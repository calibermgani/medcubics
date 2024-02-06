<?php

namespace App\Http\Controllers\Patients;

use Auth;
use View;
use Redirect;
use Log;
use Response;
use Request;
use Session;
use App\Models\Patients\Patient as Patient;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;


class ArManagementController extends Api\ArManagementApiController {

    public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'armanagement');
        View::share('heading_icon', 'fa-user');
    }

    public function index($patient_id) {
        $api_response = $this->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $claims_lists = $api_response_data->data->claims_list;
            return view('patients/armanagement/armanagement', compact('claims_lists', 'patient_id', 'patient_tabs_details', 'patient_tabs_insurance_details', 'patient_tabs_insurance_count'));
        } else {
            return Redirect::to('/patients');
        }
    }

     public function arsummary($patient_id) {      
        $api_response = $this->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        $arsummary = $api_response_data->data->arsummary;
       // dd($arsummary);
        $claim_status_wise_count = $arsummary->claim_status_wise_count;
        $claim_status_wise_sum = $arsummary->patient_claims_status;
        $ins_category_value = $arsummary->ins_category_value;
        $ins_graph_value = $arsummary->payment_graph;
        $datavaluefinal = json_decode($ins_graph_value);
        $insurance_chart_data = json_encode($datavaluefinal->datavaluefinal, true);
        $insurance_chart_label = json_encode($datavaluefinal->date_label_final, true);
        if ($api_response_data->status == 'success') {            
            return view('patients/armanagement/arsummary',
                compact('claim_status_wise_count', 'patient_id', 'claim_status_wise_sum', 'ins_category_value'))
                ->with('insurance_chart_data', $insurance_chart_data)
                ->with('insurance_chart_label', $insurance_chart_label);
        } else {
            return Redirect::to('/patients');
        }
    }

    public function lists($patient_id) {
        $api_response = $this->getListsApi($patient_id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $claims_lists = $api_response_data->data->claims_list;
			$encodeClaimIds = $api_response_data->data->encodeClaimIds;
			// Added hold reason for bulk hold option in armanagement
			// Revision 1 : MR-2786 : 4 Sep 2019
            $hold_option = $api_response_data->data->hold_options;
            if (Request::ajax()) {
                return view('patients/armanagement/claimslist', compact('claims_lists','hold_option','encodeClaimIds'));
            } else {
                $user_list = $api_response_data->data->user_list;
                $billing_provider = $api_response_data->data->billing_provider;
                $rendering_provider = $api_response_data->data->rendering_provider;
                $referring_provider = $api_response_data->data->referring_provider;
                $insurances = $api_response_data->data->insurances;
                $facility = $api_response_data->data->facility;
                $category = $api_response_data->data->category;
                $question = $api_response_data->data->question;
				$encodeClaimIds = $api_response_data->data->encodeClaimIds;
                $patient_insurance = $api_response_data->data->patient_insurance;
                return view('patients/armanagement/list', compact('claims_lists', 'patient_id', 'user_list', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'category', 'question','patient_insurance','encodeClaimIds','hold_option'));
            }
        } else {
            return Redirect::to('/patients');
        }
    }

    public function followup_list($patient_id) {
        $api_response = $this->getFollowupListsApi($patient_id);
        $api_response_data = $api_response->getData();
        $claims_lists = $api_response_data->data->claims_list;
        $user_list = $api_response_data->data->user_list;
        $billing_provider = $api_response_data->data->billing_provider;
        $rendering_provider = $api_response_data->data->rendering_provider;
        $referring_provider = $api_response_data->data->referring_provider;
        $insurances = $api_response_data->data->insurances;
        $facility = $api_response_data->data->facility;
        $category = $api_response_data->data->category;
        $question = $api_response_data->data->question;
        return view('patients/armanagement/list', compact('claims_lists', 'patient_id', 'user_list', 'billing_provider', 'rendering_provider', 'referring_provider', 'insurances', 'facility', 'category', 'question'));
    }

    public function view($patient_id) {
        $api_response = $this->getViewApi($patient_id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $claims_lists = $api_response_data->data->claims_list;
            $facilities = $api_response_data->data->facilities;
            $providers = $api_response_data->data->providers;
            $rendering_providers = $api_response_data->data->rendering_providers;
            $referring_providers = $api_response_data->data->referring_providers;
            $billing_providers = $api_response_data->data->billing_providers;
            $insurances = $api_response_data->data->insurances;
            return view('patients/armanagement/view', compact('claims_lists', 'patient_id', 'patient_tabs_details', 'rendering_providers', 'billing_providers', 'facilities', 'providers', 'patient_tabs_insurance_details', 'patient_tabs_insurance_count'));
        } else {
            return Redirect::to('/patients');
        }
    }

    public function getclaimtabdetails() {
        $api_response = $this->getclaimtabdetailsApi();
        $api_response_data = $api_response->getData();
        $added_claim_tabs = $api_response_data->added_claim_tabs;
        $remove_claim_tabs = $api_response_data->remove_claim_tabs;
        $claim_detail = $api_response_data->claim_detail;
        $practice_det = $api_response_data->practice_det;
        $user_list = $api_response_data->user_list;
        $tab_type = $api_response_data->tab_type;
        $denial_codes_arr = [];
        //$denial_codes_arr = $api_response_data->denial_codes_arr; // open on popup ajax call triggered to avoid for reduce loading time.
        /*         * *************************************************************** */
        $rendering_providers = $api_response_data->rendering_providers;
        $referring_providers = $api_response_data->referring_providers;
        $billing_providers = $api_response_data->billing_providers;
        $facilities = $api_response_data->facilities;
        $pos = $api_response_data->pos;
        $patient_notes = $api_response_data->patient_notes;
        $patient_insurance = $api_response_data->patient_insurance;
        /*         * *************************************************************** */
		$select_data = '<select class="select2 form-control  js_indivual_assign_to select2-offscreen" id="js_indivual_assign_user_id_" name="user_id" title="" data-bv-field="user_id" tabindex="-1"><option value="" selected="selected">-- Select To--</option>';
		foreach($patient_insurance as $key => $value){
			$select_data .= '<option value="'.$key.'">'.$value.'</option>';
		}
		$select_data .='<select>';
		
        $added_claim_tab_details = view('patients/armanagement/added_claim_tab_details', compact('claim_detail', 'practice_det', 'user_list', 'tab_type', 'rendering_providers', 'referring_providers', 'billing_providers', 'facilities', 'pos', 'denial_codes_arr', 'patient_notes','patient_insurance'));
        return $added_claim_tabs . "^^::^^" . $remove_claim_tabs . "^^::^^" . $added_claim_tab_details. "^^::^^" .$select_data;
    }

    public function getclaimnotesadded() {
        $api_response = $this->getclaimnotesaddedApi();
        $api_response_data = $api_response->getData();
        $created_name = $api_response_data->data->created_name;
        $created_date = $api_response_data->data->created_date;
        $claim_detail_dos = $api_response_data->data->claim_detail_dos;
        return Response::json(array('status' => 'success', 'created_name' => $created_name, 'created_date' => $created_date, 'claim_detail_dos' => $claim_detail_dos));
    }

    public function getclaimdenailnotesadded() {
        $api_response = $this->getclaimdenailnotesaddedApi();
        $api_response_data = $api_response->getData();
        $created_name = $api_response_data->data->created_name;
        $created_date = $api_response_data->data->created_date;
        $date_of_service = $api_response_data->data->date_of_service;
        $content = $api_response_data->data->content;
        $denial_insurance_name = $api_response_data->data->denial_insurance_name;
        $denial_code_result = $api_response_data->data->denial_code_result;
        return Response::json(array('status' => 'success', 'created_name' => $created_name, 'created_date' => $created_date, 'date_of_service' => $date_of_service, 'content' => $content, 'denial_insurance_name' => $denial_insurance_name, 'denial_code_result' => $denial_code_result));
    }

    public function getclaimassignadded() {
        $api_response = $this->getclaimassignaddedApi();
        return Response::json(array('status' => 'success'));
    }

    public function getclaimstatusnotesadded() {
        $api_response = $this->getclaimstatusnotesaddedApi();
        $api_response_data = $api_response->getData();
        $content = $api_response_data->content;
        $follow_up_content = $api_response_data->follow_up_content;
        $patient_notes_type = $api_response_data->patient_notes_type;
        $source_id = $api_response_data->source_id;
		Session::put('ar_source_id',$source_id);
        return view('patients/armanagement/notes_details_show', compact('content', 'follow_up_content', 'patient_notes_type'));
    }

    public function getclaimstatusfinalnotesadded() {
        $api_response = $this->getclaimstatusfinalnotesaddedApi();
        return Response::json(array('status' => 'success'));
    }

    public function getclaimchargeeditprocess() {
        $api_response = $this->getclaimchargeeditprocessApi();
        return Response::json(array('status' => 'success'));
    }

    public function getclaimchargeeditdetails($claim_id) {
        $api_response = $this->getclaimchargeeditdetailsApi($claim_id);
        $api_response_data = $api_response->getData();

        $claim_detail_val_edit = $api_response_data->claim_detail;
        $practice_det = $api_response_data->practice_det;
        $user_list = $api_response_data->user_list;
        $rendering_providers = $api_response_data->rendering_providers;
        $referring_providers = $api_response_data->referring_providers;
        $billing_providers = $api_response_data->billing_providers;
        $facilities = $api_response_data->facilities;
        $pos = $api_response_data->pos;
        return view('patients/armanagement/edit_charge_process', compact('claim_detail_val_edit', 'practice_det', 'user_list', 'rendering_providers', 'referring_providers', 'billing_providers', 'facilities', 'pos'));
    }

    public function getclaimpatientinsurance($patient_id) {
        $insurance_arr = Patient::getARPatientInsurance($patient_id);
        return Response::json(array('insurance_arr' => $insurance_arr));
    }

    public function getdenialsearchlist() {
        $api_response = $this->getdenialsearchlistApi();
        $api_response_data = $api_response->getData();
        $denial_claim_number = $api_response_data->data->denial_claim_number;
        $denial_codes_arr = $api_response_data->data->denial_codes_arr;
        $sel_code_val = $api_response_data->data->sel_code_val;
        return view('patients/armanagement/denial_codes_list', compact('denial_claim_number', 'denial_codes_arr', 'sel_code_val'));
    }

    public function claimholdprocess() {
        $api_response = $this->claimholdprocessApi();
        return Response::json(array('status' => 'success'));
    }	

	/* 
	 *
	 * Showing patient insurance
	 * @ Author  : Selvakumar
	 * @ Created : 28 FEB 18
	 */
	
	public function getpatientinsurance($patient_id) {
		
		$patient_id =  Helpers::getEncodeAndDecodeOfId(@$patient_id,'decode');
        $insurance_arr = Patient::with(['insured_detail'=>(function($query){
						      $query->select('insurance_id','patient_id','id','category', 'policy_id');
                            })])->where('id',$patient_id)->select('id')->get()->toArray();
		$patient_ins = '';
		foreach($insurance_arr[0]['insured_detail'] as $list){
			$patient_ins .= $list['category']. " : ".$list['insurance_details']['short_name']." - ".@$list['policy_id']."<br>";
		}
		if(empty($patient_ins))
			$patient_ins = 'Insurance not available';
        return $patient_ins;
    }
	
	
	public function setpatienthold($patient_id){
		$api_response = $this->setpatientholdApi($patient_id);
		return 'success';
	}
	
	public function setpatientunhold($patient_id){
		$api_response = $this->setpatientunholdApi($patient_id);
		return 'success';
	}


	/*
	 * change The claim Resposibility from ArMangment
	 * @ Request
	*/

	public function changeTheClaimResponsibility()
    {	
        try {
            $request = Request::all();
            $paymentV1 = new PaymentV1ApiController();
            $response = $paymentV1->reSubmitClaimFromArManagment($request['patientId'], $request['claimId'], $request['insuranceId']);
            $api_response_data = (array)$response->getData();
            if ($api_response_data['status'] == 'success') {
                return $api_response_data;
            } else {
                return $api_response_data;
            }
        } catch (Exception $e) {
            \Log::info("Exception occured on changeTheClaimResponsibility, Msg: ".$e->getMessage() );
			$data['status'] = 'error';
			$data['message'] = 'Something went wrong! Try again';
			return $data;
        }
    }
 /* 
 * Author : Selvakumar V
 * Desc : Showing the followup history in popup
 * Created On : 25-Apr-2018
 */	
	public function getFolloupHistoryPopup($claim_no){
		$api_response = $this->getFolloupHistoryPopupApi($claim_no);
        $api_response_data = $api_response->getData();
		$claim_detail_val = $api_response_data->data;
		return view('layouts/followup_history_popup', compact('claim_detail_val'));		
	}
	
	public function getaddedfollowupdetails(){
		$api_response = $this->getaddedfollowupdetailsApi();
		$api_response_data = $api_response->getData();
		$status = $api_response_data->status;
		return $status;
	}
}
