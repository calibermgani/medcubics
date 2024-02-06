<?php

namespace App\Http\Controllers\Charges\Api;

use App;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Patients\Api\BillingApiController;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Facility as Facility;
use App\Models\Icd as Icd;
use App\Models\Pos as Pos;
use App\Models\Patients\Patient as Patient;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Provider as Provider;
use Config;
use DB;
use Image;
use Input;
use Request;
use Response;
use Session;
use Log;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;

class ChargeApiController extends Controller {

    // Charges listing page starts here //
    public function getListIndexApi($export = '', $status = null) {
		//DB::enableQueryLog();
        $request = Request::all();
		/* Converting value to default search based */
		if(isset($request['export']) && $request['export'] == 'yes'){
			foreach($request as $key=>$value){
				if(strpos($value, ',') !== false && $key != 'patient_name'){
					$request['dataArr']['data'][$key] = json_encode(explode(',',$value));
				}else{
					$request['dataArr']['data'][$key] = json_encode($value);	
				}
			}
		}
		/* Converting value to default search based */
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
        $orderByField = 'claim_info_v1.id';
        $orderByDir = 'DESC';
        
        $rendering_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')]);
        $billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
				 
        $request['is_export'] = ($export != "") ? 1 : 0;
        $request['stats_filter'] = isset($request['stats']) ? $request['stats'] : '';
        $result = $this->getChargesSearchApi($request, $status);           
        $charges = $result["claim_list"];
        $count = $result["count"];
        //\Log::info(DB::getQueryLog());
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('charges', 'rendering_providers', 'billing_providers', 'facilities', 'count')));
    }
    
    public function getIndexApi($export = '', $status = null) {
        $request = Request::all();
           
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
        $orderByField = 'claim_info_v1.id';
        $orderByDir = 'DESC';
        $rendering_providers = Provider::typeBasedAllTypeProviderlist('Rendering');
        $billing_providers = Provider::typeBasedAllTypeProviderlist('Billing');
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        $pos = Pos::orderBy('code', 'ASC')->pluck('code', 'id')->all();
		$ClaimController  = new ClaimControllerV1("charge");
		$search_fields_data = $ClaimController->generateSearchPageLoad('charges_listing'); 
		$searchUserData = $search_fields_data['searchUserData'];
		$search_fields = $search_fields_data['search_fields'];
        $request['is_export'] = ($export != "") ? 1 : 0;
        $result = $this->getChargesSearchApi($request, $status);
        $charges = $result["claim_list"];
        $count = $result["count"];

        if ($export != "") {
            $exportparam = array(
                'filename' => 'claim',
                'heading' => '',
                'fields' => array(
                    'claim_number' => 'Claim Number',
                    'Acc No' => array('table' => 'patient', 'column' => 'account_no', 'label' => 'Acc No'),
                    'Patient Name' => array(
                        'table' => '', 'column' => 'patient_id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetPatientName'], 'label' => 'Patient Name'),
                    'date_of_service' => 'Date of Service',
                    'facility_id' => array('table' => 'facility_detail', 'column' => 'short_name', 'label' => 'Facility'),
                    'rendering_provider_id' => array('table' => 'rendering_provider', 'column' => 'short_name', 'label' => 'Rendering Provider'),
                    'billing_provider_id' => array('table' => 'billing_provider', 'column' => 'short_name', 'label' => 'Billing Provider'),
                    'Billed To' => array('table' => '', 'column' => 'insurance_id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetInsuranceName'], 'label' => 'Billed To'),
                    'Unbilled' => array('table' => '', 'column' => 'id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetUnbilledCharge'], 'label' => 'Unbilled'),
                    'total_charge' => 'Billed',
                    'total_paid' => ' Paid',
                    'balance_amt' => 'AR Due',
                    'status' => 'Status',
                )
            );
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $charges, $export);
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('charges', 'rendering_providers', 'billing_providers', 'facilities', 'pos', 'count', 'search_fields', 'searchUserData')));
    }
    
    // Charges listing page ends here //
    // Charges create page starts here //
    public function getCreateApi() {                 
       $query_data = Request::query();
       $query_url = Request::getQueryString();
       $data = [];
       $pos = Pos::orderBy('code', 'ASC')->pluck('code', 'id')->all();
       if(!empty($query_data)){
            $data['rendering_provider_id'] = Helpers::getEncodeAndDecodeOfId(@$query_data['rendering'], 'decode');
            $data['billing_provider_id'] = Helpers::getEncodeAndDecodeOfId(@$query_data['billing'], 'decode');
            $data['facility_id'] = Helpers::getEncodeAndDecodeOfId(@$query_data['fac'], 'decode');
            $data['dos_from'] = isset($query_data['dos_from']) ? $query_data['dos_from']: '';
            $data['dos_to'] = isset($query_data['dos_to']) ? $query_data['dos_to'] : '';
            $data['pos'] = Helpers::getEncodeAndDecodeOfId(@$query_data['pos'], 'decode');
            $data['pos_code'] = (isset($pos[$data['pos']])) ? $pos[$data['pos']] : '';
            $data['reference'] = isset($query_data['ref']) ? $query_data['ref'] : '';
            $data['query'] = $query_url;
        }      
        $rendering_providers = Provider::typeBasedAllTypeProviderlist('Rendering');
        $billing_providers = Provider::typeBasedAllTypeProviderlist('Billing');
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        // $charge_session_value = Session::get('charge_var');
        // $charge_session_value = (object) $charge_session_value;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('rendering_providers', 'billing_providers', 'facilities', 'pos', 'data')));
    }

    // Charges create page ends here //
    // Search patient  at charges page starts here
    public function getSearchPatientApi($type, $key) {
        $limit = Config::get('siteconfigs.charges.patientorchargelimit');
        if ($type == 'dob') {
            $key = date("Y-m-d", strtotime(base64_decode($key)));
            $patient_list = Patient::where($type, '=', $key)->where('status', 'Active')->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn', 'status')->take($limit)->get();
        } elseif (!empty($type) && $type == "policy_id") {
            $patient_list = Patient::where('status', 'Active')->whereHas('patient_insurance', function($q) use ($key) {
                        $q->where('policy_id', '=', trim($key));
                    })->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn', 'status')->where('status', 'Active')->take($limit)->get();
        } elseif (!empty($type) && ($type != 'name')) {
            $patient_list = Patient::where($type, 'like', '%' . $key . '%')->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn', 'status')->where('status', 'Active')->take($limit)->get();
        } elseif (!empty($type) && ($type == 'account_no') || ($type == 'ssn')) {
            $patient_list = Patient::where($type, $key)->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn', 'status')->where('status', 'Active')->take($limit)->get();
        } elseif (!empty($type) && ($type == 'name')) {
            if (strpos($key, ',')) {
                $key = str_replace(' ', '', str_replace(',', '', $key));
            } else {
                $key = str_replace(' ', '', $key);
            }
            $patient_list = Patient::where('status', 'Active')->where(DB::raw("REPLACE(CONCAT(last_name,first_name,middle_name),' ','')"), 'like', "%$key%")->get();
        } else {
            $patient_list = Patient::where('status', 'Active')->where(function($query) use ($key) {
                        $query->where('last_name', 'like', '%' . $key . '%')->orwhere('first_name', 'like', '%' . $key . '%');
                    })->select('id', 'account_no', 'last_name', 'middle_name', 'first_name', 'dob', 'ssn', 'status')->take($limit)->get();
        }
        if (!empty($patient_list)) {
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_list')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.no_record"), 'data' => ''));
        }
    }

    // Search patient  at charges page ends here
    // Store function starts here
    public function getStoreApi($request = '') {
        if (empty($request))
            $request = Request::all();
        $viCharges = new ChargeV1ApiController();
        $responseData = $viCharges->createCharge($request);
        return $responseData;
    }

    // Store function ends here

    public function getEditApi($claim_id) {
        $patient_id = ClaimInfoV1::where('id', $claim_id)->value('patient_id');

        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'encode');
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'encode');
        $charges = new BillingApiController();
        return $charges->getCreateApi($patient_id, $claim_id);
    }

    public function getUpdateApi($request = '') {
        if (empty($request))
            $request = Request::all();
        $charges = new BillingApiController();
        return $charges->getUpdateApi($request);
    }

    public function getDeleteApi($id) {
        //$charges = new BillingApiController();
        $resp = ClaimInfoV1::deleteClaim($id);
        return $resp; //$charges->getDeleteApi($id);
    }

    public function getChargesSearchApi($request, $status = null) {
        $request = (!empty($request)) ? $request : Request::all();
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
        $orderField = $orderByField = 'claim_info_v1.id';
        $orderByDir = 'DESC';
        if(isset($request['is_export']) && $request['is_export'] == 1)
            $orderByDir = 'DESC';
        
        $claim_qry = ClaimInfoV1::where('claim_info_v1.id', '<>', 0)
                ->join('patients','patients.id', '=', 'claim_info_v1.patient_id')
                ->join('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')
                ->leftjoin('insurances', 'insurances.id', '=', 'claim_info_v1.insurance_id')
                ->leftjoin('facilities', 'facilities.id', '=', 'claim_info_v1.facility_id')
                ->leftjoin('facilityaddresses', 'facilityaddresses.facilityid', '=', 'facilities.id')
                ->leftjoin('providers as rendering_provider', 'rendering_provider.id', '=', 'claim_info_v1.rendering_provider_id')
                ->leftjoin('providers as billing_provider', 'billing_provider.id', '=', 'claim_info_v1.billing_provider_id')
                ->leftJoin('claim_sub_status', 'claim_sub_status.id', '=', 'claim_info_v1.sub_status_id');
		
        if (isset($request['patient_id']) && $request['patient_id'] != '') {
            $claim_qry->where('claim_info_v1.patient_id', $request['patient_id']);
        }
       
        if ($status != '' && $status != 'All') {
            $status = explode(",", $status);                // print_r($status);
            $claim_qry->whereIn('claim_info_v1.status', $status);
        }


        if (!empty($request['order'])) {
            $orderField = (isset($request['order'][0]['column'])) ? $request['order'][0]['column'] : 'claim_info_v1.id';                        
            $refUrl = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
            // Same function using in different listing, based on pagewise order by assigned
            switch ($orderField) {
                case '0':   // claim_number
                    $orderByField = (strpos($refUrl,'/patients/') != false) ? 'claim_info_v1.date_of_service' : 'claim_info_v1.claim_number';
                    break;

                case '1':   // Account no
                    $orderByField = (strpos($refUrl,'/patients/') != false) ? 'claim_info_v1.claim_number': 'patients.account_no';
                    break;

                case '2':   //'patient_name';
                    $orderByField = (strpos($refUrl,'/patients/') != false) ? 'rendering_provider.short_name': 'patients.last_name';
                    break;

                case '3':   // DOS
                    $orderByField = (strpos($refUrl,'/patients/') != false) ? 'billing_provider.short_name' : 'claim_info_v1.date_of_service';
                    break;

                case '4':   // Facility
                    $orderByField = 'facilities.short_name';                
                    break;

                case '5':   // Rendering
                    $orderByField = (strpos($refUrl,'/patients/') != false) ? 'insurances.short_name' : 'rendering_provider.short_name'; 
                    break;

                case '6':   // Billing
                    $orderByField = (strpos($refUrl,'/patients/') != false) ? 'claim_info_v1.total_charge' : 'billing_provider.short_name';          
                    break;

                case '7':   // Payer
                    $orderByField = 'insurances.short_name';                
                    break;
                case '13':  // AR Bal
                    $orderByField = (strpos($refUrl,'/patients/') != false) ? 'tot_ar' : 'claim_info_v1.status';  
                    break;

                case '14':
                    $orderByField = 'claim_info_v1.status';                 // status
                    break;

                default:
                    $orderByField = 'claim_info_v1.id';
                    break;
            }            
            $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
        }
        if(!empty($request['dataArr']))
            $claim_qry = $this->searchFilterApi($claim_qry, $request);

        $result['count'] = $claim_qry->distinct('claim_info_v1.id')->count();
        //$claim_qry->groupBy('claim_info_v1.id');

        $claim_qry->selectRaw('claim_info_v1.id,
            claim_info_v1.id as claim_id,
            claim_info_v1.claim_number,
            claim_info_v1.date_of_service,
            claim_info_v1.insurance_id,
            claim_info_v1.total_charge,
            claim_info_v1.status,
            claim_info_v1.claim_submit_count,
            claim_info_v1.charge_add_type,
            claim_info_v1.rendering_provider_id,
            claim_info_v1.refering_provider_id,
            claim_info_v1.billing_provider_id,
            claim_info_v1.facility_id,
            claim_info_v1.insurance_id,
            claim_info_v1.icd_codes,
            patients.id as patient_id,
            patients.account_no,
            patients.first_name,
            patients.last_name,
            patients.middle_name,
            patients.title,
            patients.dob,
            patients.gender,
            patients.address1,
            patients.city,
            patients.state,
            patients.zip5,
            patients.zip4,
            patients.is_self_pay,
            patients.phone,
            patients.mobile,
            
            facilities.facility_name,
            facilities.short_name as facility_short_name,
            facilityaddresses.address1 as facility_address1,
            facilityaddresses.city as facility_city,
            facilityaddresses.state as facility_state,
            facilityaddresses.pay_zip5 as facility_pay_zip5,
            facilityaddresses.pay_zip4 as facility_pay_zip4,

            billing_provider.short_name as billing_short_name,
            billing_provider.provider_name as billing_full_name,
            billing_provider.provider_dob as billing_dob,
            billing_provider.gender as billing_gender,
            billing_provider.etin_type as billing_etin_type,
            billing_provider.etin_type_number as billing_etin_no,
            billing_provider.npi as billing_npi,

            rendering_provider.short_name as rendering_short_name,
            rendering_provider.provider_name as rendering_full_name,
            rendering_provider.provider_dob as rendering_dob,
            rendering_provider.gender as rendering_gender,            
            rendering_provider.etin_type as rendering_etin_type,
            rendering_provider.etin_type_number as rendering_etin_no,
            rendering_provider.npi as rendering_npi,
             
            pmt_claim_fin_v1.insurance_due,
            pmt_claim_fin_v1.patient_due,
            pmt_claim_fin_v1.patient_adj,
            pmt_claim_fin_v1.insurance_adj,
            pmt_claim_fin_v1.insurance_paid,
            pmt_claim_fin_v1.patient_paid,
            pmt_claim_fin_v1.withheld,
            claim_sub_status.sub_status_desc,
            (claim_info_v1.total_charge-(pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.withheld)) as balance_amt,
            (pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.withheld) as totalAdjustment,
            (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_paid) as total_paid,
            IF(claim_info_v1.claim_submit_count > 0,"false","true") as unbilled
            ');

        
        // Revision 1 - 26-08-2019 - Kannan
        // MR-2717 - If Sort by DOS we should consider claim no. to be Descending too
        if($orderByField == 'claim_info_v1.date_of_service')
            $claim_qry->orderBy($orderByField, $orderByDir)->orderBy('claim_info_v1.id', $orderByDir);
        else
            $claim_qry->orderBy($orderByField, $orderByDir);
        // End of Revision
        if (isset($request['is_export']) && $request['is_export'] == 1) {
            // For export data no need to take limit            
            if(isset($request['stats_filter']) && $request['stats_filter'] != ''){               
                $status_arr = explode(",", trim($request['stats_filter']));
                $claim_qry->whereIn('claim_info_v1.status', $status_arr);            
            }
        } else {
           $claim_qry->skip($start)->take($len);
        }
        $claim_lists = $claim_qry->get();
        // New pmt flow integration start
        /*for ($m = 0; $m < count($claim_lists); $m++) {
            if (!empty($claim_lists)) {
                //if any payment made against claims unbilled 0 and claimsubmitted count> 0
                if ($claimSubmittedCount > 0){
                    $claim_lists[$m]['unbilled'] = false;
                } else {
                    $claim_lists[$m]['unbilled'] = true;
                }*/
                //$patientAdjusted  =  $claim_lists[$m]['patient_adj'];
                //$insurance_adjusted  =  $claim_lists[$m]['insurance_adj'];
                //$withheld = $claim_lists[$m]['withheld'];
                //$totalPaid = $claim_lists[$m]['patient_paid'] + $claim_lists[$m]['insurance_paid'];
                //$totalAdjustment =$patientAdjusted+ $insurance_adjusted;
                //$balance = $claim_lists[$m]['total_charge'] - ($totalPaid + $totalAdjustment + $withheld);
                //$claim_lists[$m]['total_paid'] = $totalPaid;
                //$claim_lists[$m]['balance_amt'] = $balance;
                //$claim_lists[$m]['totalAdjustment'] = $totalAdjustment+$withheld;
                //$claim_lists[$m]['patient_due'] = $claim_lists[$m]['patient_due'];
                //$claim_lists[$m]['insurance_due'] = $claim_lists[$m]['insurance_due'];
				// For show icd codes on hover claim number
                /*if ($claim_lists[$m]->icd_codes) {
                    $selected_icd = Icd::getIcdValues($claim_lists[$m]->icd_codes, 'yes');                    
                }                
                $claim_lists[$m]['selected_icd'] = (!empty($selected_icd)) ? implode(", ", array_unique(array_values($selected_icd))) : '' ;
            }
        }*/
        // New pmt flow integration end  
        $result['claim_list'] = $claim_lists;
        return $result;
    }

    public function searchFilterApi($claim_query, $request = []){        
        $practice_timezone = Helpers::getPracticeTimeZone();
		$claim_query->join(DB::raw("(SELECT      
          claim_id,     
          total_charge,patient_due, insurance_due,
          sum(total_charge)-(sum(patient_paid)+sum(insurance_paid) + sum(withheld) + sum(patient_adj) + sum(insurance_adj)) as tot_ar,
          SUM(pmt_claim_fin_v1.insurance_due) as total_ins_due,
          SUM(pmt_claim_fin_v1.patient_due) as total_pat_due,
          (pmt_claim_fin_v1.patient_due+pmt_claim_fin_v1.insurance_due) as total_due,
          (pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid) as tot_paid
          FROM pmt_claim_fin_v1
          WHERE pmt_claim_fin_v1.deleted_at IS NULL
          GROUP BY pmt_claim_fin_v1.claim_id
          ) as fin"), function($join) {
            $join->on('fin.claim_id', '=', 'claim_info_v1.id');
        })->selectRaw('fin.tot_ar,
            fin.total_ins_due,
            fin.total_pat_due,
            fin.total_due,
            fin.tot_paid
            ');

        if (!empty(json_decode(@$request['dataArr']['data']['claim_number'])))
            $claim_query->where('claim_info_v1.claim_number', 'LIKE', '%' . json_decode($request['dataArr']['data']['claim_number']) . '%');
        
        if (!empty(json_decode(@$request['dataArr']['data']['acc_no'])))
            $claim_query->where('patients.account_no', 'LIKE', '%'. json_decode($request['dataArr']['data']['acc_no']) .'%');
		
        if (!empty(json_decode(@$request['dataArr']['data']['patient_name']))) {
			$dynamic_name = $search = trim(json_decode(@$request['dataArr']['data']['patient_name']));
		    // Rev.1 - Ref. MEDV2-605 - Ravi - 23-12-2019
            // Charges Listing:Search Patient name filter by using lastname and firstname record not display.
            $claim_query->Where(function ($query) use ($dynamic_name) {
                $query = $query->orWhere(DB::raw('CONCAT(patients.last_name,", ", patients.first_name)'),  'like', "%{$dynamic_name}%" );
            });

		}

		if(!empty(json_decode(@$request['dataArr']['data']['date_of_service']))){
			$date = explode('-',json_decode($request['dataArr']['data']['date_of_service']));
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1]));
            $claim_query->whereBetween('claim_info_v1.date_of_service', [ $from,  $to]);
        }

		if(!empty(json_decode(@$request['dataArr']['data']['transaction_date']))){
			$date = explode('-',json_decode($request['dataArr']['data']['transaction_date']));
            $from = date("Y-m-d", strtotime($date[0]));
			if($from == '1970-01-01'){
				$from = '0000-00-00';
			}
            $to = date("Y-m-d", strtotime($date[1]));
            $claim_query->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'");
        }

        if (!empty(json_decode(@$request['dataArr']['data']['facility']))){
			if(is_array(json_decode(@$request['dataArr']['data']['facility'])))
				$claim_query->whereIn('claim_info_v1.facility_id', json_decode($request['dataArr']['data']['facility']));
			else
				$claim_query->where('claim_info_v1.facility_id', json_decode($request['dataArr']['data']['facility']));
		}
        
        if (!empty(json_decode(@$request['dataArr']['data']['rendering']))){
            if(is_array(json_decode(@$request['dataArr']['data']['rendering'])))
				$claim_query->whereIn('claim_info_v1.rendering_provider_id', json_decode($request['dataArr']['data']['rendering']));
			else
				$claim_query->where('claim_info_v1.rendering_provider_id', json_decode($request['dataArr']['data']['rendering']));
		}

		if (!empty(json_decode(@$request['dataArr']['data']['refering']))){
			if(is_array(json_decode(@$request['dataArr']['data']['refering'])))
				$claim_query->whereIn('claim_info_v1.refering_provider_id', json_decode($request['dataArr']['data']['refering']));
			else
				$claim_query->where('claim_info_v1.refering_provider_id', json_decode($request['dataArr']['data']['refering']));
		}
        
        if (!empty(json_decode(@$request['dataArr']['data']['billing']))){
			if(is_array(json_decode(@$request['dataArr']['data']['billing'])))
				$claim_query->whereIn('claim_info_v1.billing_provider_id', json_decode($request['dataArr']['data']['billing']));
			else
				$claim_query->where('claim_info_v1.billing_provider_id', json_decode($request['dataArr']['data']['billing']));
		}
       
        $ins_data = isset($request['dataArr']['data']['insurance_id']) ? (array)json_decode(@$request['dataArr']['data']['insurance_id']) : [];
        if (!empty($ins_data)){			
            $claim_query->whereIn('claim_info_v1.insurance_id', $ins_data);
        }
        
        if(!empty(json_decode(@$request['dataArr']['data']['status']))){
            $statusArr = is_string(json_decode(@$request['dataArr']['data']['status'])) ? explode(',',json_decode(@$request['dataArr']['data']['status'])) : json_decode(@$request['dataArr']['data']['status']);
            if(in_array("All", $statusArr)){
                $claim_query->whereIn('claim_info_v1.status', ['Hold','Pending','Ready','Patient','Submitted','Paid','Denied','Rejection']);                
            }else{
                $claim_query->whereIn('claim_info_v1.status', $statusArr);
            }
            if(in_array("Hold", $statusArr)){
                if(!empty(json_decode(@$request['dataArr']['data']['hold_reason']))){
                    $holdReasonArr = is_string(json_decode(@$request['dataArr']['data']['hold_reason'])) ? explode(',',json_decode(@$request['dataArr']['data']['hold_reason'])) : json_decode(@$request['dataArr']['data']['hold_reason']);
                    $claim_query->whereIn('claim_info_v1.hold_reason_id', $holdReasonArr);                    
                }
            }            
        }

        if (!empty(json_decode(@$request['dataArr']['data']['status_reason']))) {
            if(is_array(json_decode(@$request['dataArr']['data']['status_reason'])))
                $claim_query->whereIn('claim_info_v1.sub_status_id', json_decode($request['dataArr']['data']['status_reason']));
            else
                $claim_query->where('claim_info_v1.sub_status_id', json_decode($request['dataArr']['data']['status_reason']));
        }
        if(!isset($request['patient_id']))
        if (count(array_filter(json_decode(@$request['dataArr']['data']['status_reason'])))!=count(json_decode(@$request['dataArr']['data']['status_reason']))) {
            $claim_query->orWhereNull('claim_info_v1.sub_status_id');
        }
		if (isset($request['dataArr']['data']['unbilledamt'])) {  
            $unbilledamt = json_decode($request['dataArr']['data']['unbilledamt']);
            $unbilledamt_con = '=';
            if (preg_match('/</', $unbilledamt)){
                $exp = explode('<',$unbilledamt);
                $unbilledamt_con = '<=';
                $unbilledamt = $exp[1];
            }
            if (preg_match('/>/', $unbilledamt)){
                $exp = explode('>',$unbilledamt);
                $unbilledamt_con = '>=';
                $unbilledamt = $exp[1];
            }
            if($unbilledamt!==''){
                $claim_query->where('claim_info_v1.total_charge', $unbilledamt_con,$unbilledamt);
            }
            $claim_query->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', 0);
        }

		if (isset($request['dataArr']['data']['billedamt'])) {
            $billedamt = json_decode($request['dataArr']['data']['billedamt']);
            $billedamt_con = '=';
            if(preg_match('/</', $billedamt)){
                $exp = explode('<',$billedamt);
                $billedamt_con = '<=';
                $billedamt = $exp[1];
            }
            if(preg_match('/>/', $billedamt)){
                $exp = explode('>',$billedamt);
                $billedamt_con = '>=';
                $billedamt = $exp[1];
            }
            if($billedamt !== '') {
                $claim_query->where('claim_info_v1.total_charge', $billedamt_con,$billedamt);
                $claim_query->where(function($qry){
                    $qry->where(function($query){ 
                        $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                    })->orWhere('claim_info_v1.insurance_id',0);
                });
            }
        }

		if (isset($request['dataArr']['data']['paid_amt'])) {
             $paid_amt = json_decode($request['dataArr']['data']['paid_amt']);
            $paid_amt_con = '=';
            if(preg_match('/</', $paid_amt)){
                $exp = explode('<',$paid_amt);
                $paid_amt_con = '<=';
                $paid_amt = $exp[1];
            }
            if(preg_match('/>/', $paid_amt)){
                $exp = explode('>',$paid_amt);
                $paid_amt_con = '>=';
                $paid_amt = $exp[1];
            }
            if($paid_amt !== '')
                $claim_query->where('tot_paid', $paid_amt_con,$paid_amt);
            //$claim_query->where('tot_paid', 'LIKE',"%".json_decode($request['dataArr']['data']['paid_amt'])."%");	
        }

		if (isset($request['dataArr']['data']['pat_bal'])) {
            $pat_amt = json_decode($request['dataArr']['data']['pat_bal']);
            $pat_amt_con = '=';
            if(preg_match('/</', $pat_amt)){
                $exp = explode('<',$pat_amt);
                $pat_amt_con = '<=';
                $pat_amt = $exp[1];
            }
            if(preg_match('/>/', $pat_amt)){
                $exp = explode('>',$pat_amt);
                $pat_amt_con = '>=';
                $pat_amt = $exp[1];
            }
            if($pat_amt !== '')
                $claim_query->where('pmt_claim_fin_v1.patient_due', $pat_amt_con,$pat_amt);
            //$claim_query->where('pmt_claim_fin_v1.patient_due', 'LIKE',"%".json_decode($request['dataArr']['data']['pat_bal'])."%");
        }

		if (isset($request['dataArr']['data']['ins_bal'])) {
            $ins_amt = json_decode($request['dataArr']['data']['ins_bal']);
            $ins_amt_con = '=';
            if(preg_match('/</', $ins_amt)){
                $exp = explode('<',$ins_amt);
                $ins_amt_con = '<=';
                $ins_amt = $exp[1];
            }
            if(preg_match('/>/', $ins_amt)){
                $exp = explode('>',$ins_amt);
                $ins_amt_con = '>=';
                $ins_amt = $exp[1];
            }
            if($ins_amt !== '')
                $claim_query->where('pmt_claim_fin_v1.insurance_due', $ins_amt_con,$ins_amt);
            //$claim_query->where('pmt_claim_fin_v1.insurance_due', 'LIKE',"%".json_decode($request['dataArr']['data']['ins_bal'])."%");					
        }

		if (isset($request['dataArr']['data']['ar_bal'])) {
            $ar_amt = json_decode($request['dataArr']['data']['ar_bal']);
            $ar_amt_con = '=';
            if(preg_match('/</', $ar_amt)){
                $exp = explode('<',$ar_amt);
                $ar_amt_con = '<=';
                $ar_amt = $exp[1];
            }
            if(preg_match('/>/', $ar_amt)){
                $exp = explode('>',$ar_amt);
                $ar_amt_con = '>=';
                $ar_amt = $exp[1];
            }
            if($ar_amt !== '')
                $claim_query->where('tot_ar', $ar_amt_con,$ar_amt);
            //$claim_query->where('tot_ar', 'LIKE',"%".json_decode($request['dataArr']['data']['ar_bal'])."%");			
        }
        return $claim_query;
    }
    
    public function getcmsdataApi($claim_id) {
        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        $claim_data = ClaimInfoV1::with(['rendering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'dosdetails' => function($q) {
                        $q->where('is_active', 1);
                    }, 'patient', 'claim_details', 'pos'])->where('id', $claim_id)->first();
        $insurance_data = [];
        if (!empty($claim_data['insurance_id'])) {
            $insurance_data = $this->getinsurancedetail(array($claim_data['insurance_id']), $claim_data['patient_id']);
        }
        $claim_detail['id'] = $claim_data->id;
        $claim_detail['insurance_address1'] = @$insurance_data['insurance_address1'];
        $claim_detail['insurance_address2'] = @$insurance_data['insurance_address2'];
        $claim_detail['insurance_city'] = @$insurance_data['insurance_city'];
        $claim_detail['insurance_state'] = @$insurance_data['insurance_state'];
        $claim_detail['insurance_zipcode5'] = @$insurance_data['insurance_zipcode5'];
        $claim_detail['insurance_zipcode4'] = @$insurance_data['insurance_zipcode4'];
        $claim_detail['insurance_address_concat'] = @$insurance_data['insurance_city'] . ' ' . @$insurance_data['insurance_state'] . ' ' . @$insurance_data['insurance_zipcode5'];
        if (!empty(@$insurance_data['insurance_zipcode4'])) {
            $claim_detail['insurance_address_concat'] = @$insurance_data['insurance_city'] . ' ' . @$insurance_data['insurance_state'] . ' ' . @$insurance_data['insurance_zipcode5'] . '-' . @$insurance_data['insurance_zipcode4'];
        }
        $claim_detail['ins_type'] = isset($claim_data->insurance_details->insurancetype) ? $claim_data->insurance_details->insurancetype->type_name : '';
        $claim_detail['insured_id_number'] = (!empty($insurance_data) && isset($insurance_data['policy_num'])) ? @$insurance_data['policy_num'] : '';
        $claim_detail['patient_name'] = Helpers::getNameformat(@$claim_data->patient->last_name, @$claim_data->patient->first_name, @$claim_data->patient->middle_name);

        $claim_detail['patient_dob_m'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1901-01-01' && $claim_data->patient->dob != '0000-00-00' ) ? date('m', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_dob_d'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1901-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('d', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_dob_y'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1901-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('Y', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_gender'] = isset($claim_data->patient->gender) ? $claim_data->patient->gender : '';
        $claim_detail['insured_name'] = (!empty($insurance_data) && isset($insurance_data['insured_name'])) ? $insurance_data['insured_name'] : '';

        $claim_detail['insured_addr'] = (!empty($insurance_data) && isset($insurance_data['insured_addr'])) ? $insurance_data['insured_addr'] : '';
        $claim_detail['insured_city'] = (!empty($insurance_data) && isset($insurance_data['insured_city'])) ? $insurance_data['insured_city'] : '';
        $claim_detail['insured_state'] = (!empty($insurance_data) && isset($insurance_data['insured_state'])) ? $insurance_data['insured_state'] : '';
        $zip4_data = (isset($insurance_data['insured_zip4']) && !empty($insurance_data['insured_zip4'])) ? '-' . $insurance_data['insured_zip4'] : '';
        $claim_detail['insured_zip5'] = (!empty($insurance_data) && isset($insurance_data['insured_zip5'])) ? $insurance_data['insured_zip5'] . $zip4_data : '';
        $claim_detail['insured_phone'] = (!empty($insurance_data) && !empty($claim_data->patient->mobile)) ? preg_replace('/[^0-9]/', '', $claim_data->patient->mobile) : ''; // Need to keep this field 

        $claim_detail['patient_addr'] = $claim_data->patient->address1;
        $claim_detail['patient_city'] = $claim_data->patient->city;
        $claim_detail['patient_state'] = $claim_data->patient->state;
        $zip4 = isset($claim_data->patient->zip4) ? '-' . $claim_data->patient->zip4 : '';
        $claim_detail['patient_zip5'] = $claim_data->patient->zip5 . $zip4;
        if ($claim_data->patient->phone != '') {
            $claim_detail['patient_phone'] = preg_replace('/[^0-9]/', '', $claim_data->patient->phone);
        } elseif (($claim_data->patient->mobile != '')) {
            $claim_detail['patient_phone'] = preg_replace('/[^0-9]/', '', $claim_data->patient->mobile);
        } else {
            $claim_detail['patient_phone'] = '';
        }
        $claim_detail['insured_relation'] = (!empty($insurance_data) && isset($insurance_data['insured_relation'])) ? @$insurance_data['insured_relation'] : '';
        $claim_detail['reserved_nucc_box8'] = '';
        $claim_detail['reserved_nucc_box9b'] = '';
        $claim_detail['reserved_nucc_box9c'] = '';
        
        $claim_detail['is_employment'] = (isset($claim_data->claim_details->is_employment) && $claim_data->claim_details->is_employment == 'Yes') ? $claim_data->claim_details->is_employment : 'No';
        $claim_detail['is_auto_accident'] = (isset($claim_data->claim_details->is_autoaccident) && $claim_data->claim_details->is_autoaccident == 'Yes') ? $claim_data->claim_details->is_autoaccident : 'No';
        $claim_detail['is_other_accident'] = (isset($claim_data->claim_details->is_otheraccident) && $claim_data->claim_details->is_otheraccident == 'Yes') ? $claim_data->claim_details->is_otheraccident : 'No';
        $claim_detail['accident_state'] = isset($claim_data->claim_details->autoaccident_state) ? $claim_data->claim_details->autoaccident_state : '';
        $claim_detail['is_another_ins'] = 'No';
        $claim_detail['other_insured_name'] = '';
        $claim_detail['other_ins_policy'] = '';
        $claim_detail['other_insur_name'] = '';
        // if(isset($claim_detail['ins_type']) && $claim_detail['ins_type'] == 'Medicare')
        //{
        $claim_detail['is_another_ins'] = (!empty($insurance_data) && isset($insurance_data['is_another_ins'])) ? $insurance_data['is_another_ins'] : 'No';
        $claim_detail['other_insured_name'] = (!empty($insurance_data) && isset($insurance_data['other_insured_name'])) ? $insurance_data['other_insured_name'] : '';
        $claim_detail['other_ins_policy'] = (!empty($insurance_data) && isset($insurance_data['other_ins_policy'])) ? $insurance_data['other_ins_policy'] : '';
        $claim_detail['other_insur_name'] = (!empty($insurance_data) && isset($insurance_data['other_insur_name'])) ? $insurance_data['other_insur_name'] : '';
        // }           
        $claim_detail['claimcode'] = isset($claim_data->claim_details->claim_code) ? $claim_data->claim_details->claim_code : '';
        $claim_detail['ins_policy_no'] = (!empty($insurance_data) && isset($insurance_data['ins_policy_no'])) ? $insurance_data['ins_policy_no'] : '';

        if (@$insurance_data['insured_relation'] == "Self") {
            $claim_detail['insured_dob_m'] = ($insurance_data['primary_ins_type'] != "Medicare") ? $claim_detail['patient_dob_m'] : "";
            $claim_detail['insured_dob_d'] = ($insurance_data['primary_ins_type'] != "Medicare") ? $claim_detail['patient_dob_d'] : "";
            $claim_detail['insured_dob_y'] = ($insurance_data['primary_ins_type'] != "Medicare") ? $claim_detail['patient_dob_y'] : "";
        } else {
            $claim_detail['insured_dob_m'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_m']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_dob_m'] : '';
            $claim_detail['insured_dob_d'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_d']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_dob_d'] : '';
            $claim_detail['insured_dob_y'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_y']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_dob_y'] : '';
        }
        $claim_detail['insured_gender'] = (!empty($insurance_data) && isset($insurance_data['insured_gender']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_gender'] : '';  // Need to keept this field 
        $claim_detail['other_claimid'] = isset($claim_data->claim_details->otherclaimid) ? $claim_data->claim_details->otherclaimid : '';
        $claim_detail['other_claimid_qual'] = !(empty($claim_detail['other_claimid'])) ? 'Y4' : '';
        $claim_detail['insur_name'] = (!empty($insurance_data) && isset($insurance_data['insur_name']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insur_name'] : '';

        $claim_detail['patient_signed'] = (!empty($claim_data->claim_details) && $claim_data->claim_details->print_signature_onfile_box12 == 'No') ? 'NO SIGNATURE ON FILE' : 'SIGNATURE ON FILE';
        $claim_detail['signed_date'] = (isset($claim_data->submited_date) && $claim_data->submited_date != '0000-00-00') ? date('m/d/Y', strtotime($claim_data->submited_date)) : date('m/d/Y'); //Claim Submited date.
        $claim_detail['insured_signed'] = (!empty($claim_data->claim_details) && $claim_data->claim_details->print_signature_onfile_box13 == 'No') ? 'NO SIGNATURE ON FILE' : 'SIGNATURE ON FILE';
        $claim_detail['amount_paid'] = (isset($claim_data->insurance_paid) && $claim_data->is_send_paid_amount == "Yes") ? explode('.', $claim_data->insurance_paid) : '';
        $patient_id = $claim_data->patient_id;
        // If pregnency LMP was filled take its date and qualifier given at the NUCC pdf
        if (!empty($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') {
            $claim_detail['doi_m'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_qual'] = 484;
        } else {
            $claim_detail['doi_m'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01' && $claim_data->doi != '0000-00-00 00:00:00') ? date('m', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01' && $claim_data->doi != '0000-00-00 00:00:00') ? date('d', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01' && $claim_data->doi != '0000-00-00 00:00:00') ? date('Y', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_qual'] = ($claim_detail['doi_y'] != "") ? 431 : "";
        }
        $claim_detail['other_m'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_d'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_y'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_qual'] = isset($claim_data->claim_details->other_date_qualifier) ? $claim_data->claim_details->other_date_qualifier : '';

        $claim_detail['box18_from_m'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_d'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_y'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_to_m'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_d'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_y'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['refering_provider'] = isset($claim_data->refering_provider) ? $claim_data->refering_provider->provider_name : '';
        $claim_detail['refering_provider_npi'] = isset($claim_data->refering_provider) ? $claim_data->refering_provider->npi : '';
        $refering_provider_type = (isset($claim_data->refering_provider->provider_types_id) && $claim_data->refering_provider->provider_types_id != 0) ? $claim_data->refering_provider->provider_types_id : '';
        $provider_qual = '';
        if ($refering_provider_type == config('siteconfigs.providertype.Supervising')) {
            $provider_qual = 'DQ';
        } elseif ($refering_provider_type == config('siteconfigs.providertype.Referring')) {
            $provider_qual = 'DN';
        } elseif ($refering_provider_type == config('siteconfigs.providertype.Ordering')) {
            $provider_qual = 'DK';
        }
        $claim_detail['refering_provider_qual'] = $provider_qual;
        $claim_detail['provider_qual'] = (isset($claim_data->claim_details->provider_qualifier) && !empty($claim_detail['refering_provider'])) ? $claim_data->claim_details->provider_qualifier : '';
        $claim_detail['provider_otherid'] = (isset($claim_data->claim_details->provider_otherid) && !empty($claim_detail['refering_provider'])) ? $claim_data->claim_details->provider_otherid : '';

        $claim_detail['admit_date_m'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01' && $claim_data->admit_date != '0000-00-00') ? date('m', strtotime($claim_data->admit_date)) : '';
        $claim_detail['admit_date_d'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01' && $claim_data->admit_date != '0000-00-00') ? date('d', strtotime($claim_data->admit_date)) : '';
        $claim_detail['admit_date_y'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01' && $claim_data->admit_date != '0000-00-00') ? date('Y', strtotime($claim_data->admit_date)) : '';
        $claim_detail['discharge_date_m'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('m', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_d'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('d', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_y'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('Y', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['addi_claim_info'] = isset($claim_data->claim_details->additional_claim_info) ? $claim_data->claim_details->additional_claim_info : '';
        $claim_detail['outside_lab'] = isset($claim_data->claim_details->outside_lab) ? $claim_data->claim_details->outside_lab : '';
        $claim_detail['lab_charge'] = isset($claim_data->claim_details->lab_charge) ? explode('.', $claim_data->claim_details->lab_charge) : ''; // This will be applied after submission of data   
        $icd_codes = [];
        if (!empty($claim_data->icd_codes)) {
            $icd_codes = Icd::getIcdValues($claim_data->icd_codes);
        }
        //$icd = new Icd();
        //$icd->connecttopracticedb();
        $claim_detail['icd_A'] = isset($icd_codes[1]) ? $icd_codes[1] : '';
        $claim_detail['icd_B'] = isset($icd_codes[2]) ? $icd_codes[2] : '';
        $claim_detail['icd_C'] = isset($icd_codes[3]) ? $icd_codes[3] : '';
        $claim_detail['icd_D'] = isset($icd_codes[4]) ? $icd_codes[4] : '';
        $claim_detail['icd_E'] = isset($icd_codes[5]) ? $icd_codes[5] : '';
        $claim_detail['icd_F'] = isset($icd_codes[6]) ? $icd_codes[6] : '';
        $claim_detail['icd_G'] = isset($icd_codes[7]) ? $icd_codes[7] : '';
        $claim_detail['icd_H'] = isset($icd_codes[8]) ? $icd_codes[8] : '';
        $claim_detail['icd_I'] = isset($icd_codes[9]) ? $icd_codes[9] : '';
        $claim_detail['icd_J'] = isset($icd_codes[10]) ? $icd_codes[10] : '';
        $claim_detail['icd_K'] = isset($icd_codes[11]) ? $icd_codes[11] : '';
        $claim_detail['icd_L'] = isset($icd_codes[12]) ? $icd_codes[12] : '';
        $claim_detail['resub_code'] = isset($claim_data->claim_details->resubmission_code) ? $claim_data->claim_details->resubmission_code : '';
        $claim_detail['original_ref'] = isset($claim_data->claim_details->original_ref_no) ? $claim_data->claim_details->original_ref_no : '';
        $claim_detail['prior_auth_no'] = isset($claim_data->claim_details->box_23) ? $claim_data->claim_details->box_23 : '';
        if (empty($claim_detail['prior_auth_no'])) {
            $claim_detail['prior_auth_no'] = $claim_data->auth_no;
        }
        $etin_ssn = '';
        $etin_tax = '';
        $claim_detail['emergency'] = isset($claim_data->claim_details->emergency) ? $claim_data->claim_details->emergency : '';
        $claim_detail['pos'] = isset($claim_data->facility_detail->pos_details->code) ? $claim_data->facility_detail->pos_details->code : '';
        $claim_detail['epsdt'] = isset($claim_data->claim_details->epsdt) ? $claim_data->claim_details->epsdt : '';
        $claim_detail['billing_provider_npi'] = !empty($claim_data->billing_provider) ? $claim_data->billing_provider->npi : '';
        $claim_detail['rendering_provider_npi'] = !empty($claim_data->rendering_provider) ? $claim_data->rendering_provider->npi : '';
        if (isset($claim_data->billing_provider->etin_type) && ($claim_data->billing_provider->etin_type == 'SSN')) {
            $etin_ssn = 'X';
        } else {
            $etin_tax = 'X';
        }
        $claim_detail['etin_ssn'] = $etin_ssn;
        $claim_detail['etin_tax'] = $etin_tax;
        $claim_detail['accept_assignment'] = (isset($claim_data->claim_details->accept_assignment) && $claim_data->claim_details->accept_assignment == "No") ? $claim_data->claim_details->accept_assignment : 'Yes';
        $claim_detail['ssn_or_taxid_no'] = isset($claim_data->billing_provider->etin_type_number) ? preg_replace('/[^a-z0-9]/i', '', $claim_data->billing_provider->etin_type_number) : '';
        $claim_detail['claim_no'] = $claim_data->claim_number; // send as patient account number
        $claim_detail['total'] = isset($claim_data->total_charge) ? explode('.', $claim_data->total_charge) : '';
        $claim_detail['reserved_nucc_box30'] = '';
        $rendering_provider_id = $claim_data->rendering_provider->id;
        $renderingProviderName = Provider::getProviderNamewithDegree($rendering_provider_id);
        $claim_detail['rendering_provider_name'] = isset($renderingProviderName) ? $renderingProviderName : '';
        $claim_detail['rendering_provider_date'] = (isset($claim_data->submited_date) && $claim_data->submited_date != '0000-00-00') ? date('m/d/Y', strtotime($claim_data->submited_date)) : '';
        if (empty($claim_detail['rendering_provider_date']))
            $claim_detail['rendering_provider_date'] = date('m/d/Y');
        $claim_detail['facility_name'] = isset($claim_data->facility_detail->facility_name) ? $claim_data->facility_detail->facility_name : '';
        $claim_detail['facility_addr'] = isset($claim_data->facility_detail->facility_address->address1) ? $claim_data->facility_detail->facility_address->address1 : '';
        $facility_zip_4 = isset($claim_data->facility_detail->facility_address->pay_zip4) ? '-' . $claim_data->facility_detail->facility_address->pay_zip4 : '';
        $claim_detail['facility_city'] = @$claim_data->facility_detail->facility_address->city . ' ' . @$claim_data->facility_detail->facility_address->state . ' ' . @$claim_data->facility_detail->facility_address->pay_zip5 . @$facility_zip_4;
        $claim_detail['facility_npi'] = isset($claim_data->facility_detail->facility_npi) ? $claim_data->facility_detail->facility_npi : '';
        $service_facility_qual = isset($claim_data->claim_details->service_facility_qual) ? $claim_data->claim_details->service_facility_qual : '';
        $facility_otherid = isset($claim_data->claim_details->facility_otherid) ? $claim_data->claim_details->facility_otherid : '';
        $billing_prov_zip_4 = isset($claim_data->billing_provider->zipcode4) ? '-' . $claim_data->billing_provider->zipcode4 : '';
        $claim_detail['box_32b'] = !empty($service_facility_qual && $facility_otherid) ? $service_facility_qual . $facility_otherid : '';
        $billing_provider_id = $claim_data->billing_provider->id;
        $billingProviderName = Provider::getProviderNamewithDegree($billing_provider_id);
        $claim_detail['bill_provider_name'] = isset($billingProviderName) ? $billingProviderName : '';
        $claim_detail['bill_provider_addr'] = isset($claim_data->billing_provider->address_1) ? $claim_data->billing_provider->address_1 : '';
        $claim_detail['bill_provider_phone'] = isset($claim_data->billing_provider->phone) ? str_replace(array('(', ')'), '', $claim_data->billing_provider->phone) : '';
        $claim_detail['bill_provider_city'] = !empty($claim_data->billing_provider->city) ? $claim_data->billing_provider->city . ' ' : '' . !empty($claim_data->billing_provider->state) ? $claim_data->billing_provider->state . ' ' : '' . !empty($claim_data->billing_provider->zipcode5) ? $claim_data->billing_provider->zipcode5 : '' . $billing_prov_zip_4;
        $billing_provider_qual = isset($claim_data->claim_details->billing_provider_qualifier) ? $claim_data->claim_details->billing_provider_qualifier : '';
        $billing_provider_otherid = isset($claim_data->claim_details->billing_provider_otherid) ? $claim_data->claim_details->billing_provider_otherid : '';
        $claim_detail['box_33b'] = !empty($billing_provider_qual && $billing_provider_otherid) ? $billing_provider_qual . $billing_provider_otherid : '';
        $claim_detail['box_24I'] = "G2"; // Need to make as dynamic
        $claim_detail['box_24J'] = "987878978";
        $i = 1;
        $trans_key = ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E', '6' => 'F', '7' => 'G', '8' => 'H', '9' => 'I', '10' => 'J', '11' => 'K', '12' => 'L', ',' => ''];
        $doc = [];
        foreach ($claim_data->dosdetails as $dos_detail) {
            if (!empty($dos_detail)) {
                $doc['row_' . $i]['from_mm'] = date('m', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['from_dd'] = date('d', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['from_yy'] = date('Y', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_mm'] = date('m', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_dd'] = date('d', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_yy'] = date('Y', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['cpt'] = $dos_detail->cpt_code;
                $doc['row_' . $i]['mod1'] = isset($dos_detail->modifier1) ? $dos_detail->modifier1 : '';
                $doc['row_' . $i]['mod2'] = isset($dos_detail->modifier2) ? $dos_detail->modifier2 : '';
                $doc['row_' . $i]['mod3'] = isset($dos_detail->modifier3) ? $dos_detail->modifier3 : '';
                $doc['row_' . $i]['mod4'] = isset($dos_detail->modifier4) ? $dos_detail->modifier4 : '';
                $doc['row_' . $i]['billed_amt'] = isset($dos_detail->charge) ? explode('.', $dos_detail->charge) : '';
                $doc['row_' . $i]['icd_pointer'] = isset($dos_detail->cpt_icd_map_key) ? substr(strtr($dos_detail->cpt_icd_map_key, $trans_key), 0, 4) : '';
                $doc['row_' . $i]['unit'] = isset($dos_detail->unit) ? $dos_detail->unit : 1;
                $doc['row_' . $i]['rendering_provider_npi'] = (!empty($dos_detail->cpt_code)) ? $claim_detail['rendering_provider_npi'] : "";
                $doc['row_' . $i]['pos'] = (!empty($claim_data->pos_details->code)) ? $claim_data->pos_details->code : "";
                $doc['row_' . $i]['emergency'] = (!empty($dos_detail->cpt_code)) ? $claim_detail['emergency'] : "";
                $doc['row_' . $i]['epsdt'] = substr(@$claim_detail['epsdt'], 0, 1);
            }
            $i++;
        }
        $claim_detail['box_24'] = $doc;
        $box_count = count((array)$claim_detail['box_24']);
        return Response::json(array('status' => '', 'data' => compact('claim_detail', 'box_count'), 'message' => ''));
    }

    function getinsurancedetail($insurance, $patient_id) {
        $getpatientins = PatientInsurance::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'insurancetype_id')->with('insurancetype');
                    }, 'patient'))->where('patient_id', $patient_id)->whereIn('insurance_id', $insurance)->get();
        $ins_data = [];
        $other_ins_detail = PatientInsurance::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'insurancetype_id')->with('insurancetype');
                    }, 'patient'))->where('patient_id', $patient_id)->whereNotIn('insurance_id', $insurance)->first();
        $other_ins_data = [];

        if (!empty($other_ins_detail)) {
            $insured_name = Helpers::getNameformat($other_ins_detail->last_name, $other_ins_detail->first_name, $other_ins_detail->middle_name);
            $other_ins_data = [
                'is_another_ins' => 'Yes',
                'other_insured_name' => @$insured_name,
                'other_ins_policy' => @$other_ins_detail->policy_id,
                'other_insur_name' => @$other_ins_detail->insurance_details->insurance_name,
                'other_insur_type' => @$other_ins_detail->insurance_details->insurancetype->type_name,
            ];
        }
        if (!empty($getpatientins) && count((array)$getpatientins)) {
            foreach ($getpatientins as $insurance_detail) {
                $insurance_type = @$insurance_detail->insurance_details->insurancetype->type_name;
                $insured_name = Helpers::getNameformat($insurance_detail->last_name, $insurance_detail->first_name, $insurance_detail->middle_name);
                $patient_name = Helpers::getNameformat($insurance_detail->patient->last_name, @$insurance_detail->patient->first_name, @$insurance_detail->patient->middle_name);
                $insured_dob_m = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('m', strtotime($insurance_detail->insured_dob)) : '';
                $insured_dob_d = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('d', strtotime($insurance_detail->insured_dob)) : '';
                $insured_dob_y = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('Y', strtotime($insurance_detail->insured_dob)) : '';
                $zip_code = isset($insurance_detail->insured_zip5) ? $insurance_detail->insured_zip5 : '';
                $zip_code4 = (!empty($insurance_detail->insured_zip4)) ? '-' . $insurance_detail->insured_zip4 : '';
                $insured_name = ($insurance_detail->relationship == "Self") ? $patient_name : $insured_name;
                $ins_data = [
                    'policy_num' => $insurance_detail->policy_id,
                    'insured_dob' => $insurance_detail->insured_dob,
                    'insured_addr' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->address1 : $insurance_detail->insured_address1,
                    'insured_address2' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->address2 : $insurance_detail->insured_address2,
                    'insured_city' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->city : $insurance_detail->insured_city,
                    'insured_state' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->state : $insurance_detail->insured_state,
                    'insured_zip5' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->zip5 : $insurance_detail->insured_zip5,
                    'insured_zip4' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->zip4 : $insurance_detail->insured_zip4,
                    'group_name' => $insurance_detail->group_name,
                    'insured_relation' => $insurance_detail->relationship,
                    'group_id' => $insurance_detail->group_id,
                    'insur_name' => $insurance_detail->insurance_details->insurance_name,
                    'insured_phone' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->phone : $insurance_detail->insurance_details->insured_phone,
                    'insured_name' => ($insurance_type != "Medicare") ? $insured_name : "",
                    'ins_policy_no' => ($insurance_type != "Medicare" && isset($other_ins_data['other_insur_type']) && $other_ins_data['other_insur_type'] == "Medicare") ? @$other_ins_data['other_ins_policy'] : "",
                    'insured_dob_m' => $insured_dob_m,
                    'insured_dob_d' => $insured_dob_d,
                    'insured_dob_y' => $insured_dob_y,
                    'insured_gender' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->gender : $insurance_detail->insured_gender,
                    'insured_relation' => $insurance_detail->relationship,
                    'insured_id_number' => $insurance_detail->policy_num,
                    'insurance_address1' => $insurance_detail->insurance_details->address_1,
                    'insurance_address2' => $insurance_detail->insurance_details->address_2,
                    'insurance_city' => $insurance_detail->insurance_details->city,
                    'insurance_state' => $insurance_detail->insurance_details->state,
                    'insurance_zipcode5' => $insurance_detail->insurance_details->zipcode5,
                    'insurance_zipcode4' => $insurance_detail->insurance_details->zipcode4,
                    'primary_ins_type' => $insurance_type];
            }
        } else {
            // return $this->getpateintarcheiveinsurancedetail($insurance, $patient_id); // We wont delete insurance if it used at claims so this was no need
        }
        $ins_data = array_merge($other_ins_data, $ins_data);
        //dd($ins_data);
        return $ins_data;
    }

    function getpateintarcheiveinsurancedetail($insurance_id, $patient_id) {
        //dd($insurance_id);
        $pateint_detail = PatientInsuranceArchive::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4');
                    }, 'patient'))->where('patient_id', $patient_id)->whereIn('insurance_id', $insurance_id)->get();
        if (!empty($pateint_detail)) {
            $insured_name = Helpers::getNameformat($pateint_detail->patient->last_name, $pateint_detail->insurance_id->first_name, $pateint_detail->insurance_id->middle_name);
            $insured_dob_m = !is_null($pateint_detail->insured_dob && $pateint_detail->insured_dob != '0000-00-00' && $pateint_detail->insured_dob != '1970-01-01') ? date('m', strtotime($pateint_detail->insured_dob)) : '';
            $insured_dob_d = !is_null($pateint_detail->insured_dob && $pateint_detail->insured_dob != '0000-00-00' && $pateint_detail->insured_dob != '1970-01-01') ? date('d', strtotime($pateint_detail->insured_dob)) : '';
            $insured_dob_y = !is_null($pateint_detail->insured_dob && $pateint_detail->insured_dob != '0000-00-00' && $pateint_detail->insured_dob != '1970-01-01') ? date('Y', strtotime($pateint_detail->insured_dob)) : '';
            $zip_code = isset($insurance_detail->insured_zip5) ? $insurance_detail->insured_zip5 : '';
            $zip_code4 = isset($insurance_detail->insured_zip4) ? $insurance_detail->insured_zip4 : '';
            $ins_data[$pateint_detail->category] = [
                'policy_num' => $pateint_detail->policy_id,
                'insured_dob' => $pateint_detail->insured_dob,
                'insured_addr' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->address1 : $pateint_detail->insured_address1,
                'insured_address2' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->address2 : $pateint_detail->insured_address2,
                'insured_city' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->city : $pateint_detail->insured_city,
                'insured_state' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->state : $pateint_detail->insured_state,
                'insured_zip5' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->zip5 : $zip_code,
                'insured_zip4' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->zip4 : $zip_code4,
                'group_name' => $pateint_detail->group_name,
                'insured_relation' => $insurance_detail->relationship,
                'group_id' => $pateint_detail->group_id,
                'insur_name' => $pateint_detail->insurance_details->insurance_name,
                'insured_phone' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->phone : $pateint_detail->insured_phone,
                'insured_name' => $pateint_detail,
                'ins_policy_no' => $pateint_detail->policy_id,
                'insured_dob_m' => $insured_dob_m,
                'insured_dob_d' => $insured_dob_d,
                'insured_dob_y' => $insured_dob_y,
                'insured_gender' => (@$insurance_detail->relationship == "Self") ? $pateint_detail->patient->gender : $insurance_detail->insured_gender,
                'insured_relation' => $insurance_detail->relationship,
                'insured_id_number' => $insurance_detail->policy_num,
                'insurance_address1' => $insurance_detail->insurance_details->address_1,
                'insurance_address2' => $insurance_detail->insurance_details->address_2,
                'insurance_city' => $insurance_detail->insurance_details->city,
                'insurance_state' => $insurance_detail->insurance_details->state,
                'insurance_zipcode5' => $insurance_detail->insurance_details->zipcode5,
                'insurance_zipcode4' => $insurance_detail->insurance_details->zipcode4,
            ];
        }
        return $ins_data;
    }

}