<?php

namespace App\Http\Controllers\Patients;

use Auth;
use DebugBar\DebugBar;
use View;
use Input;
use Session;
use Request;
use Redirect;
use Validator;
use App\Http\Controllers\Api\BillingApiController as BillingApiController;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Models\Patient;
use App\Models\Payments\ClaimInfoV1;
use Route;
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;
use App\Http\Helpers\Helpers as Helpers;
use Lang;
use Log;
use PDF;
use Excel;
use Response;
use App\Exports\BladeExport;

class BillingController extends Api\BillingApiController {

    public function __construct() {
        if (strpos(Route::getCurrentRoute()->uri(), 'charges') !== false) {
            View::share('heading', 'Patient');
            View::share('selected_tab', 'charges');
            View::share('heading_icon', 'fa-user');
        } else {
			
            View::share('heading', 'Patient');
            View::share('selected_tab', 'billing');			
            View::share('heading_icon', 'fa-user');
        }
    }

    public function index($patient_id) {
        $api_response = $this->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        $claims_lists = @$api_response_data->data->claims_list;
        $search_fields = @$api_response_data->data->search_fields;
        $searchUserData = @$api_response_data->data->searchUserData;

        if (Request::ajax()) {
            $id = $patient_id;
            return view('patients/billing/charges_listing', compact('claims_lists', 'id'));
        }
        if ($api_response_data->status == 'success') {
            $patients = $api_response_data->data->patients;
            return view('patients/billing/billing', compact('patients', 'claims_lists', 'patient_id', 'patient_tabs_details', 'patient_tabs_insurance_details', 'patient_tabs_insurance_count', 'search_fields', 'searchUserData'));
        } else {
            return Redirect::to('/patients');
        }
    }
    
    public function indexTableData($patient_id = null) {
        $api_response = $this->getListIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        $claims_lists = (!empty($api_response_data->data->charges)) ? (array) $api_response_data->data->charges : [];       
        $id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
        $view_html = Response::view('patients/billing/charges_listing', compact('claims_lists','id'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['draw'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;
        return Response::json($data);
    }
    
    public function getBillingExport($patient_id='', $export=''){
        $api_response = $this->getListIndexApi($patient_id, $export);
        $api_response_data = $api_response->getData();
        $claims_lists = (!empty($api_response_data->data->charges)) ? (array) $api_response_data->data->charges : [];
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patient_Claims_List_' . $date;
        
        if ($export == 'pdf') {
            $html = view('patients/billing/billing_export_pdf', compact('claims_lists', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'patients/billing/billing_export';
            $data['claims_lists'] = $claims_lists;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        } elseif ($export == 'csv') {
            $filePath = 'patients/billing/billing_export';
            $data['claims_lists'] = $claims_lists;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');            
        }
    }
    
    public function create($patient_id, $claim_id = null) {
        $insurances_list = array();
        if (!empty($claim_id)) {
            $claim_id_val = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
            if (ClaimInfoV1::where('id', $claim_id_val)->count()) {
                $return_value = $this->findPaymentDone($claim_id_val);
                if (!$return_value) {
                    return Redirect::to('patients/' . $patient_id . '/billing/edit/' . $claim_id)->withInput();
                }
            }
        }
        $api_response = $this->getCreateApi($patient_id, $claim_id);
        $api_response_data = $api_response->getData();
        $status = @$api_response_data->status;
        if ($status == "success") {
            $facilities = @$api_response_data->data->facilities;
            $rendering_providers = @$api_response_data->data->rendering_providers;
            $referring_providers = $api_response_data->data->referring_providers;
            $billing_providers = $api_response_data->data->billing_providers;
            $insurance_data = $api_response_data->data->insurance_data;
            $patients = $api_response_data->data->patient_detail;
            $modifier = $api_response_data->data->modifier;
            $claims = $api_response_data->data->claims_list;
            $pos = $api_response_data->data->pos;
            $insured_details = $api_response_data->data->insured_details;
            $hold_option = $api_response_data->data->hold_options;
            $attrony_assigned = $api_response_data->data->attrony_assigned;
            $request = Request::all();
            $rendering_provider_id = isset($request['rendering_provider_id'])? $request['rendering_provider_id'] : '';
            $facility_id =  isset($request['facility_id'])? $request['facility_id'] : '';
            $appointment_id =  isset($request['appointment_id'])? $request['appointment_id'] : '';
            if(!empty($facility_id)){
                $pos_id = \DB::table('facilities')->where('id',base64_decode($facility_id))->pluck('pos_id')->first();
            } else {
                $pos_id='';
            }
                // Claim can create only for active patients condition included.    
            if(@$patients->status == 'Inactive') {
                return Redirect::to('patients')->with('error',"Unable to create claim for Inactive Patients");
            }

            $view = 'patients/billing/create';
            if (strpos(Route::getCurrentRoute()->uri(), 'charges') !== false) {
                $view = 'charges/charges/create_ajax';
            }
            return view($view, compact('modifier', 'appointment_id', 'facilities', 'facility_id', 'rendering_providers', 'insurance_data', 'referring_providers', 'billing_providers', 'provider_id', 'rendering_provider_id', 'referring_provider_id', 'billing_provider_id', 'patient_id', 'patients', 'claims', 'hold_option', 'pos','pos_id','insured_details', 'attrony_assigned'));
        } else {

            return Redirect::to('patients')->with('error', "Invalid Patient");
        }
    }

    public function store(Request $request) {
        $request = $request::all();
        $claim_id = $request['claim_id'];
        if (!empty($claim_id) && !isset($request['fromedit'])) {
            $claim_id_val = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
            $return_value = $this->findPaymentDone($claim_id_val);
            if (!$return_value) {
                return Redirect::to('patients/' . $request['patient_id'] . '/billing/edit/' . $claim_id)->withInput()->with('error', "Oops wrong page");
            }
        }
        //Mani WORK START
        $chargev1 = new ChargeV1ApiController();
        $api_response = '';
        if (empty($request['claim_id'])) {

            $api_response = $chargev1->createCharge($request);
        } else {
            $api_response = $chargev1->updateCharge($request);
        }
        //end --
        
        //$api_response = $this->getStoreApi($request);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
        //if (true)
            if (isset($request['is_create']) && $request['is_create'] == 1) {
                // Redirect create page for continous charge add           
                return Redirect::to('patients/' . $request['patient_id'] . '/billing/create')->with('success', $api_response_data->message);
            } elseif (isset($request['is_from_charge']) && $request['is_from_charge'] == 1) {
                // Redirection from charge entry page
                return Redirect::to('charges/create')->with('success', $api_response_data->message);
            }
            return Redirect::to('patients/' . $request['patient_id'] . '/billing')->with('success', 'Claim details updated successfully');
        } else {
            return Redirect::to('patients/' . $request['patient_id'] . '/billing/create')->withInput()->with('error', $api_response_data->message);
        }
    }

    public function update(Request $request) {
        $chargev1 = new ChargeV1ApiController();
        $request = Request::all();
       
        $api_response_data = $chargev1->updateCharge($request);
        $api_response_data = $api_response_data->getData();
        if ($api_response_data->status == 'success') {
            if (isset($request['is_create']) && $request['is_create'] == 1) {
                // Redirect create page for continous charge add           
                return Redirect::to('patients/' . $request['patient_id'] . '/billing/create')->with('success', $api_response_data->message);
            } elseif (isset($request['is_from_charge']) && $request['is_from_charge'] == 1) {
                // Redirection from charge entry page
                return Redirect::to('charges/create')->with('success', $api_response_data->message);
            }
            return Redirect::to('patients/' . $request['patient_id'] . '/billing')->with('success', 'Claim details updated successfully');
        } else {
            return Redirect::to('patients/' . $request['patient_id'] . '/billing/create')->withInput()->with('error', $api_response_data->message);
        }
    }

    public function popupauthorization($patient_id, $type = null) {
        $api_response = $this->getCreateauthorizationApi($patient_id);
        $api_response_data = $api_response->getData();
        $exist_authorizations = $api_response_data->data->authorization;
        $registration = $api_response_data->data->registration;
        $pos = $api_response_data->data->pos;

        $is_hide_process = (!is_null($type) && $type == "appointment") ? 1 : 0;
        return view('patients/billing/popup_authorization', compact('exist_authorizations', 'pos', 'patient_id', 'registration', 'is_hide_process'));
    }

    public function storeauthorization(Request $request) {
        $request = $request::all();
        $api_response = $this->getStoreAuthApi($request);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            return $api_response_data->data->patient_id;
        } else {
            return $api_response_data->status;
        }
    }

    public function addprovider() {
        $api_response = $this->getproviderApi();
        $api_response_data = $api_response->getData();
        $provider_type = $api_response_data->data->provider_type;
        $provider_degree = $api_response_data->data->provider_degree;
        $npi_flag = (array) $api_response_data->data->npi_flag;
        return view('patients/billing/popupprovider-add', compact('provider_type', 'npi_flag', 'provider_degree'));
    }

    public function storepopupprovider(Request $request) {
        $request = $request::all();
        $api_response = $this->getStoreReferringProviderApi(Request::all());
        $api_response_data = $api_response->getData();
        return $api_response;
    }

    public function getselectbasedvalues($id, $value, $category = null, $patient_id = null) {
        $sent_val = $value;
        if ($value == 'Attorney')
            $sent_val = 'Employer';
        $api_response = $this->getApiselectbasedvalue($id, $sent_val, $category, $patient_id);
        $api_response_data = $api_response->getData();
        $data_needed = $api_response_data->data->data_needed;
        $insurance_type = $api_response_data->data->data;
        $pos_code = $api_response_data->data->code;   // Changed concept wit select box 
        $clai_no = isset($api_response_data->data->data_needed->clia_number) ? $api_response_data->data->data_needed->clia_number : '';
        $id = Helpers::getEncodeAndDecodeOfId($id, 'encode');
        if ($value == 'Facility') {
            $val = '<li><span>Facility</span> : ' . $data_needed->facility_name . '</li>';
            $val .= '<li>' . $data_needed->facility_address->address1 . ', ' . $data_needed->facility_address->city . ', ' . $data_needed->facility_address->pay_zip5 . ' - ' . $data_needed->facility_address->pay_zip4 . '</li> <li> <a href="' . url("/") . '/facility/' . $id . '" target = "_blank" data-title="More Info"><i class="fa fa-info" data-placement="bottom" data-toggle="tooltip" data-original-title="More Details"></i></a></li>';
        } elseif ($value == 'Attorney') {
            $val = '<li><span>Adjuster Name</span> : ' . $data_needed->attorney_adjuster_name . '</li>';
        } else {
            $type = isset($insurance_type) ?
                    '<li><span>Type</span> : ' . @$insurance_type . '</li>' : '';
            $zip4 = !empty($data_needed->insurance_details->zipcode4) ? ' -' . $data_needed->insurance_details->zipcode4 : '';
            $val = '<li><span>Insurance</span> : ' . @$data_needed->insurance_details->insurance_name . '</li>';
            $val .= '<li><span>Policy ID</span> : ' . $data_needed->policy_id . '</li>';
            $val .= $type;
			if(Auth::user()->practice_user_type == 'customer' || Auth::user()->practice_user_type == 'practice_admin'){
				$val .= '<li>' . $data_needed->insurance_details->address_1 . ', ' . $data_needed->insurance_details->city . ' - ' . $data_needed->insurance_details->state . ', ' . $data_needed->insurance_details->zipcode5 . '' . $zip4 . '</li> <li> <a href="' . url("/") . '/insurance/' . $id . '" data-title="More Info" target = "_blank"><i class="fa fa-info" data-placement="bottom"  data-toggle="tooltip" data-original-title="More Details"></i></a></li>';
			}else{
				$val .= '<li>' . $data_needed->insurance_details->address_1 . ', ' . $data_needed->insurance_details->city . ' - ' . $data_needed->insurance_details->state . ', ' . $data_needed->insurance_details->zipcode5 . '' . $zip4 . '</li>';
			}
        }
        $data = $api_response_data->data->data . '|' . $val . '|' . $pos_code . '|' . $clai_no;
        return $data;
    }

    public function getproviderdetail($value, $type) {
        $api_response = $this->getProviderApiDetailpopup($value, $type);
        return $api_response;
    }

    public function paymentdetail($id) {
        $api_response = $this->getPaymentDetail($id);
        $api_response_data = $api_response->getData();
        $detail = $api_response_data->value->detail;
        return view('patients/billing/paymentpopup', compact('detail'));
    }

    public function cmsdetail($id) {
        return view('patients/billing/cms');
    }

    public function edit($patient_id, $claim_id = null) {
        $api_response = $this->getCreateApi($patient_id, $claim_id, 'edit');
        $api_response_data = $api_response->getData();
        $status = @$api_response_data->status;
        if ($status == "success") {
            $facilities = $api_response_data->data->facilities;
            $rendering_providers = $api_response_data->data->rendering_providers;
            $referring_providers = $api_response_data->data->referring_providers;
            $billing_providers = $api_response_data->data->billing_providers;
            $insurance_data = $api_response_data->data->insurance_data;
            $patients = $api_response_data->data->patient_detail;
            $modifier = $api_response_data->data->modifier;
            $claims = $api_response_data->data->claims_list;
            $pos = $api_response_data->data->pos;
            $hold_option = $api_response_data->data->hold_options;
            $attrony_assigned = $api_response_data->data->attrony_assigned;
            $view = 'patients/billing/edit';
            if (!empty($claim_id) && empty($claims)) {
                return Redirect::to('patients/' . $patient_id . '/billing');
            }
            return view($view, compact('modifier', 'facilities', 'facility_id', 'rendering_providers', 'insurance_data', 'referring_providers', 'billing_providers', 'provider_id', 'rendering_provider_id', 'referring_provider_id', 'billing_provider_id', 'patient_id', 'patients', 'claims', 'hold_option', 'pos', 'attrony_assigned'));
        } else {
            return Redirect::to('patients')->with('error', "Invalid Patient");
        }
    }

    public function destroy($id) {
        $api_response = $this->getDeleteApi($id);
        $api_response_data = $api_response->getData();
        $patient_id = @$api_response_data->data->patient_id;
        if (empty($patient_id)) {
            return Redirect::to('charges')->with('error', $api_response_data->message);
        } else if ($api_response_data->status == 'success') {
            return Redirect::to('patients/' . $patient_id . '/billing')->with('success', $api_response_data->message);
        } else {
            return Redirect::to('patients/' . $patient_id . '/billing')->with('error', $api_response_data->message);
        }
    }

    public function getcms1500($id, $type = null) {
        return view('layouts/under_construction');   // This is just for temporary fix         
    }

    public function searchCpt($search_keyword = null) {
        $cpts = [];
        if (!empty($search_keyword)) {
            $api_response = $this->searchCptApi($search_keyword);
            $api_response_data = $api_response->getData();
            $cpts = $api_response_data->data;
        }
        return view('patients/billing/popupcptsearch', compact('cpts', 'search_keyword'));
    }

    // This function used to find modifiers depending upon he cpt code that have been provided
    public function getCptModifier($cpt_code,$year,$insurance_id) {
        $api_response = $this->getCptModifierApi($cpt_code,$year,$insurance_id);
        return json_encode($api_response, JSON_FORCE_OBJECT);
    }
	
	public function providerShortNameValidation() {
       $api_response = $this->checkProviderShortNameValidationApi();
       $api_response_data = $api_response->getData();
       if ($api_response_data->provider_short_name_count == 0)
           return json_encode(array('valid'=>"true"));
       else
           return json_encode(array('valid'=>"false"));
   }

   public function getImoSearch() {
        return view('patients/billing/popupimosearch'); 
   }
   
   public function getPopupEmployer($patient_id) {
        return view('patients/billing/popupemployer-add', ['patient_id' => $patient_id]);
   }

}
