<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Patients\Patient as Patient;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Modifier as Modifier;
use App\Models\Pos as Pos;
use App\Models\Cpt as Cpt;
use App\Models\Medcubics\Cpt as AdminCpt;
use App\Models\Patients\PatientAuthorization as PatientAuthorization;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Icd as Icd;
use App\Models\Holdoption as Holdoption;
use Illuminate\Http\Response as Responseobj;
use App\Models\Provider_type as ProviderType;
use App\Models\Patients\PatientBudget;
use App\Models\Provider_degree as ProviderDegree;
use App\Http\Controllers\Api\CommonExportApiController;
use App\Http\Controllers\Payments\Api\PatientPaymentApiController as PatientPaymentApiController;
use App\Http\Controllers\Payments\Api\PaymentApiController as PaymentApiController;
use App\Models\Patients\PatientNote;
use App\Models\Patients\PatientInsuranceArchive as PatientInsuranceArchive;
use App\Models\Registration as Registration;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Facades\Storage;
use App\Models\Favouritecpts as Favouritecpts;
use App\Http\Controllers\Api\SuperbillsApiController;
use App\Http\Controllers\Charges\Api\ChargeApiController;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Session;
use App;
use File;
use Lang;
use Config;
use Schema;

class BillingApiController extends Controller {

    public static $unit_cal = 0;
    public static $remaining_val = 0;

    public function getIndexApi($patient_id, $export = '') {
        $claims_list = array();
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if (Patient::where('id', $patient_id)->count()) {
            if ($export != '' || Request::ajax())
                $request = Request::all();
            else
                $request = [];
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                $charges = new ChargeApiController();
                $request['patient_id'] = $patient_id;  // This would be used for searching
              //  $result = $charges->getChargesSearchApi($request);
               // $claims_listn = $result["claim_list"];
                //dd($claims_list);
                if ($export != "") {
                    $claims_list_new = ClaimInfoV1::with('rendering_provider', 'facility_detail', 'insurance_details', 'billing_provider')
                                    ->where('patient_id', $patient_id)
                                    ->orderBy('id', 'DESC')->get();
                    // dd($claims_list_nw);
                    $exportparam = array(
                        'filename' => 'claim',
                        'heading' => '',
                        'fields' => array(
                            'date_of_service' => 'Date of Service',
                            'claim_number' => 'Claim Number',
                            'Patient Name' => array(
                                'table' => '', 'column' => 'patient_id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetPatientName'], 'label' => 'Patient Name'),
                            'rendering_provider_id' => array(
                                'table' => 'rendering_provider', 'column' => 'short_name', 'label' => 'Rendering Provider'
                            ),
                            'billing_provider_id' => array(
                                'table' => 'billing_provider', 'column' => 'short_name', 'label' => 'Billing Provider'
                            ),
                            'facility_id' => array(
                                'table' => 'facility_detail', 'column' => 'short_name', 'label' => 'Facility'
                            ),
                            'Billed To' => array(
                                'table' => '', 'column' => 'insurance_id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetInsuranceName'], 'label' => 'Billed To'),
                            'Unbilled' => array(
                                'table' => '', 'column' => 'id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetUnbilledCharge'], 'label' => 'Unbilled'),
                            'total_charge' => 'Total Charge',
                            'total_paid' => 'Total Paid',
                            'balance_amt' => 'Balance amount',
                            'status' => 'Status',
                    ));
                    $callexport = new CommonExportApiController();
                    return $callexport->generatemultipleExports($exportparam, $claims_list, $export);
                }
                if (Request::ajax()) {
                    return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list')));
                }
            }
            
            $chargeV1 = new ChargeV1ApiController();
            $claims_list = $chargeV1->getPatientClaimsList($patient_id);
			$ClaimController  = new ClaimControllerV1('billing');   
			$search_fields_data = $ClaimController->generateSearchPageLoad('patients_charge_listing');
			$searchUserData = $search_fields_data['searchUserData'];
			 $search_fields = $search_fields_data['search_fields'];		
            // dd($claims_list);
            $patients = Patient::with('provider_details', 'facility_details', 'ethnicity_details', 'insurance_details')
                            ->where('id', $patient_id)->first();
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patients', 'claims_list', 'search_fields', 'searchUserData')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }
    
    public function getListIndexApi($patient_id = null,$export='') {
        $request = Request::all();
        $request['patient_id'] = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $request['is_export'] = ($export != "") ? 1 : 0;
        $charge = new ChargeApiController();
        $result = $charge->getChargesSearchApi($request);
        $charges = $result["claim_list"];
        $count = $result["count"];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('charges','count')));
    }

    public function getCreateApi($patient_id, $claim_id = null, $type = null) {
        $facilities = [];
        $insurance_data = [];
        $select_ins = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if (!is_null($claim_id))
            $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        if (Patient::where('id', $patient_id)->count()) {
            $patient_detail = Patient::where('id', $patient_id)->first();
            $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
            $modifier = Modifier::where('status', 'Active')->get();
            $hold_options = Holdoption::where('status', 'Active')->pluck('option', 'id')->all();
            $pos = Pos::orderBy('code', 'ASC')->pluck('code', 'id')->all();
            //$rendering_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Rendering')],'no');	       
            //$billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')], 'no');	        
            $rendering_providers = Provider::typeBasedAllTypeProviderlist('Rendering');
            $billing_providers = Provider::typeBasedAllTypeProviderlist('Billing');
            $referring_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Referring')], 'no');

            /** Get patient insurance code starts with category * */
			
            if (!is_null($claim_id) && $type == "edit") {
                // At edit page we need to show the same insurance with same category so, we take them from archieve data
                $insurance_data = Patient::getPatientInsuranceWithCategory($patient_id, false, $claim_id);
            } else {
				if(!is_null($claim_id))
					$insurance_data = Patient::getPatientInsuranceWithCategory($patient_id, false, $claim_id);
				else
					$insurance_data = Patient::getPatientInsurance($patient_id, false);
            }
            $insured_details = PatientInsurance::with('insurance_details')->Where('category', 'Primary')->where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->get()->toArray();
            $claims_list = ClaimInfoV1::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(submited_date,"UTC","'.$practice_timezone.'") as submited_date'))->with(['rendering_provider', 'refering_provider',
                'billing_provider', 'facility_detail', 'insurance_details', 'dosdetails',
                'pos', 'anesthesia_details', 'paymentInfo'
                    => function($query){
                         $query->where('source', 'charge');
                    }, 'claim_details'
                        => function($query) {
                            $query->select('id', 'claim_id', 'original_ref_no');
                        }])
                    ->where('id', $claim_id)
                    ->first();
            $attrony_assigned = ClaimInfoV1::checkIsAttornyAssignedClaim($claim_id, $patient_id);
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_id', 'facilities', 'rendering_providers', 'referring_providers', 'billing_providers', 'patient_detail', 'modifier', 'claims_list', 'hold_options', 'insurance_data', 'pos','insured_details', 'attrony_assigned')));
        } else {
            return response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    // create aothorization from popup starts here
    public function getCreateauthorizationApi($patient_id) {
        $authorization = [];
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $registration = '';
        if (Registration::count()) {
            $registration = Registration::first();
        }
        if (Patient::where('id', $patient_id)->count()) {
            $authorization = PatientAuthorization::with('provider_details', 'insurance_details', 'pos_detail')->where('patient_id', $patient_id)->get();
            $pos = Pos::select(DB::raw("CONCAT(code,' - ',pos) AS pos_detail"), 'id')->orderBy('id', 'ASC')->pluck('pos_detail', 'id')->all();
            return response::json(array('status' => 'success', 'message' => '', 'data' => compact('authorization', 'auth_insurances_detail', 'pos', 'patient_id', 'registration')));
        } else {
            return response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    // create aothorization from popup ends here
    public function getStoreAuthApi($request = '') {
        $request = Input::all();
        $validator = Validator::make($request, ['authorization_no' => 'required', 'pos_id' => 'required']);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            $request['start_date'] = (!empty($request['start_date'])) ? date("Y-m-d", strtotime($request['start_date'])) : "";
            $request['end_date'] = (!empty($request['end_date'])) ? date("Y-m-d", strtotime($request['end_date'])) : "";
            $patient_id = $request['patient_id'];
            $request['patient_id'] = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            PatientAuthorization::create($request);
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => compact('patient_id')));
        }
    }
    /** Employer Add related codes removed on July 14 2016 **/

    /** Provider creation at popup starts here **/
    public function getproviderApi() {
        $provider_type = ProviderType::orderBy('name', 'asc')->whereNotIn('name', ['Rendering', 'Billing'])->pluck('name', 'id')->all();
        $provider_degree = ProviderDegree::orderBy('degree_name', 'asc')->pluck('degree_name', 'id')->all();
        $npiflag_columns = Schema::getColumnListing('npiflag');
        foreach ($npiflag_columns as $columns) {
            $npi_flag[$columns] = '';
        }
        return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('provider_type', 'npi_flag', 'provider_degree')));
    }

    public function getStoreReferringProviderApi($request = '') {
        $request = Input::all();
        $last_name = $request['last_name'];
        $first_name = $request['first_name'];
         $short_name = $request['short_name'];
        $npi = $request['npi'];
        $type_id = !(empty($request['provider_types_id'])) ? $request['provider_types_id'] : 2;
        $count = Provider::where('last_name', $last_name)->where('first_name', $first_name)->where('provider_types_id', $type_id)->where('npi', $npi)->count();

        $short_name_count = Provider::where('short_name',$short_name)->where('provider_types_id',$type_id)->where('npi', $npi)->count();
        if ($count > 0) {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.npi_existcheck"), 'data' => $count));
        } elseif($short_name_count > 0) {
             return Response::json(array('status' => 'error', 'message' => "Short name already exists", 'data' => $count));
        }   
        $is_valid_npi = Helpers::checknpi_valid_process($request['npi'], 'NPI-1');

        Validator::extend('check_npi_api_validator', function($attribute) use($is_valid_npi) {
            if ($is_valid_npi == 'No')
                return false;
            else
                return true;
        });        
        $rules = Provider::$rules + array('npi'=>'check_npi_api_validator');
        
        $validator = Validator::make($request, $rules, Provider::$messages +array('npi.check_npi_api_validator' => Lang::get("common.validation.npi_validcheck")));
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => $count));
        } else {
            $degree = ProviderDegree::where('id', $request['provider_degrees_id'])->pluck('degree_name')->first();
            $request['provider_name'] = $request['last_name'] . ' ' . $request['first_name'];
            $provider = Provider::create($request);
            if (config('siteconfigs.is_enable_provider_add')) { // Just for temporary to fix database issue
                $request['practice_db_provider_id'] = $provider->id;
                /* Save image to admin database starts */
                if (Session::has('practice_dbid')) {
                    $request['practice_id'] = Session::get('practice_dbid');
                    $request['customer_id'] = Auth::user()->customer_id;
                }
                $admin_db_name = getenv('DB_DATABASE', config('siteconfigs.connection_database'));
                $dbconnection = new DBConnectionController();
                //update provider id for practice db field                
                $dbconnection->updatepracticedbproviderid($provider->id);
                //create provider in admin provider table
                $dbconnection->createProviderinOtherDB($request, $admin_db_name);
            }
            $provider_data['providername'] = $request['provider_name'];
            $provider_data['providerid'] = $provider->id;
            if ($provider) {
                return Response::json(array('status' => 'success', 'message' => "Referring provider added successfully.", 'data' => compact('provider_data')));
            } else {
                return Response::json(array('status' => 'error', 'message' => '', 'data' => $count));
            }
        }
    }

    /** Provider creation at popup ends here **/
    // selectbox icons values on right side for Facility, Employer, Insurance starts here  Note: Employer removed
    public function getApiselectbasedvalue($id, $model, $category = null, $patient_id = null) {
        $code = '';
        $clai_no = '';
        if ($model == 'Facility') {
            $data_needed = Facility::with('pos_details', 'facility_address')->where('id', $id)->first();
            $data = $data_needed->pos_details->pos;
            $code = $data_needed->pos_details->id;
            $clai_no = $data_needed->clia_number;
        } else {
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
            $datainsurance = Insurance::with('insurancetype')->where('id', $id)->first();
            $data_needed = PatientInsurance::with('insurance_details')->Where('category', $category)->where('insurance_id', $id)->where('patient_id', $patient_id)->first();
            $data = isset($datainsurance->insurancetype->type_name) ? $datainsurance->insurancetype->type_name : 'others';
            // Alert on remaning amount related code removed on july 14 2016   	
        }
        if ($data) {
            return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('data', 'data_needed', 'code', 'clai_no')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    // selectbox icons values on right side for Facility, Employer, Insurance ends here
    // Auto complete search for provider Details starts here	
    public function getReferringproviderApi($patient_id, $query = null) {
       $referring_providers = App\Models\Provider::typeBasedAllReferringProviderlist($patient_id, $query);
       return response::json(array('status' => 'success', 'message' => '', 'data' => compact('referring_providers')));
    }

    // Auto complete search for provider Details ends here
    // Provider data popup starts here
    public function getProviderApiDetailpopup($value, $type) {
        $providers = Provider::with('speciality', 'provider_types', 'taxanomy', 'taxanomy2', 'degrees')->where('id', $value)->first();        
        $providers->encoded_id = Helpers::getEncodeAndDecodeOfId($providers->id, 'encode');
        return response::json(array('status' => 'success', 'message' => '', 'data' => compact('providers')));
    }

    // Provider data popup starts here
    // check icd existance on icd fill ups and icd from admin database
    public function checkICDexistApi($icd_code) {
        /* ICD referred from practice instead of master
        if (Icd::on('responsive')->where('icd_code', $icd_code)->count()) {
            $icd = Icd::on('responsive')->where('icd_code', $icd_code)->select('id', 'short_description', 'icd_code', 'age_limit_lower', 'age_limit_upper', 'medium_description')->first();
            $icd_practice = Icd::where('icd_code', $icd_code)->select('id', 'short_description', 'icd_code', 'age_limit_lower', 'age_limit_upper', 'medium_description')->first();
            //echo DB::connection()->getDatabaseName();   
            $icd = !empty($icd_practice) ? $icd_practice : $icd;
            return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('icd')));
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
        */
        //if (Icd::on('responsive')->where('icd_code', $icd_code)->count()) {
        if (Icd::where('icd_code', $icd_code)->count()) {
            $icd = Icd::where('icd_code', $icd_code)->select('id', 'short_description', 'icd_code', 'age_limit_lower', 'age_limit_upper', 'medium_description')->first();
            return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('icd')));
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
    }

    // check cpt existance on icd fill ups and icd from admin database

    public function checkCPTexistApi($cpt_hcpcs) {
        /* CPT referred from practice instead of master
        if (Cpt::on('responsive')->where('cpt_hcpcs', $cpt_hcpcs)->count()) {  // Check CPT from admin database
            $anesthesia_base_unit = Cpt::on('responsive')->where('cpt_hcpcs', $cpt_hcpcs)->pluck('anesthesia_unit');
            if (Cpt::where('cpt_hcpcs', $cpt_hcpcs)->count()) {
                $cpt = Cpt::where('cpt_hcpcs', $cpt_hcpcs)->select('id', 'allowed_amount', 'billed_amount', 'anesthesia_unit')->first(); // Billed and allowed amount from practice database          	  	
                return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('cpt')));
            } else {
                $cpt = ['allowed_amount' => 0, 'billed_amount' => 0, 'anesthesia_unit' => $anesthesia_base_unit];
                return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('cpt')));
            }
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
        */
        if (Cpt::where('cpt_hcpcs', $cpt_hcpcs)->count()) {  // Check CPT from admin database            
            $cpt = Cpt::where('cpt_hcpcs', $cpt_hcpcs)->select('id', 'allowed_amount', 'billed_amount', 'anesthesia_unit')->first(); // Billed and allowed amount from practice database                
            return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('cpt')));
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
    }

    //Check Modifier exist starts
    public function checkModifierexistApi($mod_code) {
        if (Modifier::where('code', $mod_code)->where('status', 'Active')->count()) {
            $modifier = Modifier::where('code', $mod_code)->first();
            return response::json(array('statuss' => 'success', 'message' => '', 'data' => compact('modifier')));
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
    }

    //Check Modifier exist ends
    // Store and update Api details Starts Here
    public function getStoreApi($request = '') {
        $request = Request::all();
        if (!empty($request['patient_id']))
            $request['patient_id'] = $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
        // To check for copay related data while creating charge starts here
        $check_exists = 0;
        if (!empty($request['copay_amt']) && !empty($request['check_no']) && isset($request['copay']) && $request['copay'] == 'Check' && empty($request['claim_id'])) {            
            $check_exists = PMTInfoV1::findCheckExistsOrNot($request['check_no'], "Patient", "Check", 'patientPayment', $request['patient_id']);
            if ($check_exists) {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.checkexist"), 'data' => $request['patient_id']));
            }
        }
        //DB::beginTransaction();
        // To check for copay related data while creating charge ends here
        $validator = isset($request['is_hold']) ? Validator::make($request, [], []) : Validator::make($request, ClaimInfoV1::$rules, ClaimInfoV1::$messages);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.data_add_msg"), 'data' => ''));
        } else {
            if (!empty($request['claim_id']))
                $request['claim_id'] = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');

            /*** To get ICD IDs from ICD codes starts here ***/
            if (!empty($request['icd1'])) {
                for ($i = 1; $i <= 12; $i++) {
                    $icd_list[$i] = !empty($request['icd' . $i]) ? Icd::getIcdIds($request['icd' . $i]) : '';
                }
                $icd_lists = array_filter($icd_list);
                $request['icd_codes'] = implode(',', $icd_lists);
            }
            /*** To get ICD IDs from ICD codes ends here ***/

            /*** If hold choosen claim changed as hold or else hold removed codes starts here ***/
            if (isset($request['is_hold']) && !empty($request['hold_reason_id'])) {
                $request['status'] = 'Hold';
            } else {
                $request['hold_reason_id'] = '';
                $request['status'] = (isset($request['status']) && $request['status'] != 'Hold') ? $request['status'] : 'Ready';
                $request['is_hold'] = '';
            }
            /*** If hold choosen claim changed as hold or else hold removed codes ends here ***/

            /*** Store self pay starts here ***/
            if (isset($request['self']) && $request['self'] == 1) {
                $request['self_pay'] = 'Yes';
                $request['status'] = (isset($request['status']) && $request['status'] == 'Hold') ? $request['status'] : 'Patient';
                $request['insurance_id'] = '';
                $request['insurance_category'] = '';
                $request['patient_due'] = $request['total_charge'];
                $request['insurance_due'] = 0;
            } 
            /*** Store self pay ends here ***/

            /*** Store Insurance info starts here ***/ 
            elseif (isset($request['insurance_id']) && $request['insurance_id'] != 'self' && $request['insurance_id'] != '') {
                $request['self_pay'] = 'No';
                $insurance_category = isset($request['insurance_category']) ? explode('-', $request['insurance_category']) : '';
                $request['insurance_category'] = $insurance_category[0];
                $request['insurance_due'] = $request['total_charge'];
                $request['patient_due'] = 0;
            }
            /*** Store Insurance info ends here ***/
            $request['doi'] = !empty($request['doi']) ? date('Y-m-d', strtotime($request['doi'])) : '';
            $request['admit_date'] = !empty($request['admit_date']) ? date('Y-m-d', strtotime($request['admit_date'])) : '';
            $request['discharge_date'] = !empty($request['discharge_date']) ? date('Y-m-d', strtotime($request['discharge_date'])) : '';
            $allowed_total = 0;
            /*             * * If we delete rows it makes the page to get error so we reindex array here* */
            $request = $this->reindexArray($request);
            if (!empty($request['auth_no']) && !empty(@$request['insurance_id']) && @$request['insurance_id'] != 0) {
                $this->storePatientAuth($request);
            }
            if (!empty($request)) {
                $dos_spt_details = [];
                $unit_cal = 0;
                for ($i = 0; $i < count($request['cpt']); $i++) {
                    //if (!empty($request['cpt'][$i]) && Cpt::on('responsive')->where('cpt_hcpcs', $request['cpt'][$i])->count() == 0) {  // CPT backend validation
                    if (!empty($request['cpt'][$i]) && Cpt::where('cpt_hcpcs', $request['cpt'][$i])->count() == 0) {  // CPT backend validation - CPT checked in practice instead of mater
                        return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
                    } else {
                        //$anesthesia_val = Cpt::on('responsive')->where('cpt_hcpcs', $request['cpt'][$i])->pluck('anesthesia_unit'); // CPT validated from practice instead of master
                        $anesthesia_val = Cpt::where('cpt_hcpcs', $request['cpt'][$i])->pluck('anesthesia_unit')->first();
                        if ($anesthesia_val != 0 && $unit_cal == 0) { // Add anesthesia unit to the first CPT that we have entered
                            $unit = $request['unit'][$i] + $request['anesthesia_unit'];
                            $unit_cal = 1;
                        } else {
                            $unit = 0;
                        }
                    }
                    $notempty_count = array_filter($request['cpt']);
                    if (!empty($request['cpt'][$i]) || empty($notempty_count)) {
                        if (isset($request['fromedit']) && $request['fromedit'] == 1 && isset($request['ids'][$i]) || isset($request['ids'][$i]) && !empty($request['ids'][$i]))
                            $dos_spt_details[$i]['id'] = Helpers::getEncodeAndDecodeOfId($request['ids'][$i], 'decode');
                        $insurance_payment_count = PMTInfoV1::checkpaymentDone($request['claim_id'], 'Insurance');
                        $patient_payment_count = PMTInfoV1::checkpaymentDone($request['claim_id'], 'patient');
                        $dos_spt_details[$i]['dos_from'] = date('Y-m-d', strtotime($request['dos_from'][$i]));
                        $dos_spt_details[$i]['dos_to'] = date('Y-m-d', strtotime($request['dos_to'][$i]));
                        $dos_spt_details[$i]['cpt_code'] = $request['cpt'][$i];
                        $dos_spt_details[$i]['modifier1'] = $request['modifier1'][$i];
                        $dos_spt_details[$i]['modifier2'] = $request['modifier2'][$i];
                        $dos_spt_details[$i]['modifier3'] = $request['modifier3'][$i];
                        $dos_spt_details[$i]['modifier4'] = $request['modifier4'][$i];
                        $dos_spt_details[$i]['charge'] = $request['charge'][$i];
                        if ($insurance_payment_count && isset($dos_spt_details[$i]['id'])) {                            
                            $claim_dos_data = PMTClaimCPTFINV1::where('claim_cpt_info_id', $dos_spt_details[$i]['id'])->select('insurance_balance', 'patient_balance', 'balance')->first();
                            $insurance_balance = @$claim_dos_data->insurance_balance;
                            $patient_balance = @$claim_dos_data->patient_balance;
                            $balance = @$claim_dos_data->insurance_balance + @$claim_dos_data->patient_balance; //$claim_dos_data->balance;
                        } elseif ($patient_payment_count && isset($dos_spt_details[$i]['id'])) {
                            $claim_dos_data = PMTClaimCPTFINV1::where('claim_cpt_info_id', $dos_spt_details[$i]['id'])->pluck('patient_paid')->first();
                            $patient_paid = PMTClaimCPTFINV1::where('claim_cpt_info_id', $dos_spt_details[$i]['id'])->select(DB::raw('sum(patient_paid) + sum(patient_adjusted) as paid'))->pluck('paid')->first();
                            $insurance_balance = (isset($request['self_pay']) && $request['self_pay'] == "No") ? $request['charge'][$i] : 0;
                            if ($patient_paid) {
                                $balance = $request['charge'][$i] - $patient_paid;
                            }
                            $patient_balance = ($balance <= 0) ? 0 : $balance;
                        } else {
                            $insurance_balance = (isset($request['self_pay']) && $request['self_pay'] == "No") ? $request['charge'][$i] : 0;
                            $patient_balance = (isset($request['self_pay']) && $request['self_pay'] == "Yes") ? $request['charge'][$i] : 0;
                            $balance = $request['charge'][$i];
                        }
                        $dos_spt_details[$i]['balance'] = ($balance <= 0) ? 0 : $balance;
                        //dd($dos_spt_details);
                        $dos_spt_details[$i]['patient_balance'] = ($request['insurance_id'] != "") ? 0 : $patient_balance;
                        $dos_spt_details[$i]['insurance_balance'] = ($request['insurance_id'] != "") ? $insurance_balance : 0;
                        $dos_spt_details[$i]['is_active'] = 1;
                        //$dos_spt_details[$i]['cpt_allowed_amt'] = $request['cpt_allowed'][$i];
                        $dos_spt_details[$i]['cpt_allowed_amt'] = 0;
                        $dos_spt_details[$i]['cpt_billed_amt'] = $request['cpt_amt'][$i];
                        $dos_spt_details[$i]['insurance_id'] = $request['insurance_id'];

                        if (!empty($unit) && $unit != 0) {
                            $dos_spt_details[$i]['unit'] = $unit;
                        } else {
                            $dos_spt_details[$i]['unit'] = $request['unit'][$i];
                        }
                        $dos_spt_details[$i]['cpt_icd_code'] = $request['cpt_icd_map'][$i];
                        $dos_spt_details[$i]['cpt_icd_map_key'] = $request['cpt_icd_map_key'][$i];
                        $allowed_total += $request['cpt_allowed'][$i];
                        $dos_spt_details[$i]['patient_id'] = $request['patient_id'];  // Need to check DOS for each claim with patient_id
                        if (isset($dos_spt_details[0]['dos_from']) && !empty($dos_spt_details[0]['dos_from']))
                            $request['date_of_service'] = @$dos_spt_details[0]['dos_from']; // To save First record of from to date
                        if (isset($dos_spt_details[0]['cpt_code']) && !empty($dos_spt_details[0]['cpt_code']))
                            $request['cpt_codes'] = @$dos_spt_details[0]['cpt_code']; // To save First record of from to date
                        if (empty($request['cpt'][$i]))
                            continue;
                    }
                }
                //dd($dos_spt_details);
                $claim_detail_id = $request['claim_detail_id'];
                $claim_other_detail_id = $request['claim_other_detail_id'];
                $ambulance_billing_id = $request['ambulance_billing_id'];
                unset($request['dos_from'], $request['dos_to'], $request['cpt'], $request['modifier1'], $request['modifier2'], $request['modifier3'], $request['modifier4'], $request['charge'], $request['unit'], $request['cpt_icd_map'], $request['ids'], $request['claim_detail_id'], $request['claim_other_detail_id'], $request['ambulance_billing_id']);
                $request['created_by'] = Auth::user()->id;
                //$request['total_allowed'] = $allowed_total;				
                $request['total_allowed'] = 0;
                if (!empty($dos_spt_details)) {
                    if (!empty($request['copay_amt']) && $request['copay_amt'] != 0) {
                        //PatientBudget::collect_patient_payment($request['patient_id'], $request['copay_amt']); 
                        // This is used for budget concept
                    }
                    // Claim number updation starts here							
                    if ($request['claim_id'] == '') {
                        $request['balance_amt'] = $request['total_charge'];
                        //dd($request);
                        $result = ClaimInfoV1::create($request);
                        $claim_no = $this->generateclaimnumber('CHR', $result->id);
                        $result->update(['claim_number' => $claim_no]);
                        // Default entry of claim detail starts here
                        if (empty($claim_detail_id)) {
                            $claim_detail_id = $this->DefaultClaimDetailEntryProcess($result->id);
                        }
                        $this->createChargeEntryTransaction($request, $result->id);
                        //Default entry of claim details ends here
                    } else {
                        $result = ClaimInfoV1::find($request['claim_id']);
                        if (empty($result->claim_number)) {
                            $claim_no = $this->generateclaimnumber('CHR', $request['claim_id']);
                            $request['claim_number'] = $claim_no;
                        }
                        if ($result->insurance_id != $request['insurance_id'])
                            $this->changeResponsibilityEntryProcess($request, $result, $dos_spt_details);
                        // Billed amount change option after patient payment has been done starts
                        $patient_paid_claim = $result->patient_paid;
                        $patient_adjusted_claim = $result->patient_adjusted;
                        $patient_paid_adjusted = $patient_paid_claim + $patient_adjusted_claim;
                        //dd($patient_adjusted_claim);
                        $remaining = $request['total_charge'] - $patient_paid_claim;
                        //dd($result->total_charge);
                        //dd($request['total_charge'] - $patient_paid_claim);
                        if ($request['total_charge'] > $result->total_charge && $patient_paid_claim > 0 || $request['total_charge'] != $result->total_charge && $remaining >= 0) { // when patient paid was done as well as billed amount also changed.
                            $patient_due = array_column($dos_spt_details, 'patient_balance');
                            $request['patient_due'] = ($remaining == 0) ? 0 : array_sum($patient_due);
                            $request['balance_amt'] = $this->changeBilledAmountEntryProcess($request);
                            $balance_data = array_column($dos_spt_details, 'balance');
                            $request['balance_amt'] = array_sum($balance_data);
                            if ($remaining == 0) {
                                $request['total_paid'] = DB::raw("total_paid -" . $request['total_charge']);
                                $request['patient_paid'] = DB::raw("patient_paid -" . $request['total_charge']);
                            }
                            //dd($request['balance_amt']);							
                        } elseif ($request['total_charge'] < $result->total_charge && $patient_paid_claim > 0 && $remaining >= 0) {
                            $request['balance_amt'] = $this->changeBilledAmountEntryProcess($request);
                        } elseif ($request['total_charge'] < $result->total_charge && $patient_paid_claim > 0 && $remaining < 0) {
                            $request['patient_due'] = 0;
                            $this->changeBilledAmountEntryProcess($request);
                            //$paymentobj = new PaymentApiController();
                            //$paymentobj->moveAmountToWallet($result->patient_id, $result->id, abs($remaining));
                            $request['balance_amt'] = 0;
                        } elseif ($request['total_charge'] == $result->total_charge && $patient_paid_claim > 0) {
                            $request['balance_amt'] = $remaining;
                        } else {
                            $request['balance_amt'] = $request['total_charge'];
                        }
                        // When pateint adjustemnt was available starts
                        if ($patient_adjusted_claim) {
                            $request['balance_amt'] = $request['balance_amt'] - $patient_adjusted_claim;
                        }
                        // When patient adjustment was available end here
                        // If patient paid already and change the responsibility we need to move the remaining balance to patient due

                        $request['patient_due'] = (isset($request['self']) && $request['self'] == 1) ? $request['balance_amt'] : (isset($request['patient_due']) ? $request['patient_due'] : 0);

                        // Billed amount change option after patient payment has been done ends						
                        $request['status'] = ($remaining <= 0 && isset($request['self']) && $request['self'] == 1) ? "Paid" : $request['status'];

                        if ($result->claim_submit_count > 0 && isset($request['self']) && $request['self'] == 1) {
                            $request['status'] = "Patient";
                        } elseif ($result->claim_submit_count > 0 && $request['insurance_id'] != '') {
                            $request['status'] = "Ready";
                        }
                        $prev_insurance_id = $result->insurance_id;
                        // dd($request);
                        $result->update($request);
                    }
                    // Claim number updation ends here.
                    if (!empty($claim_detail_id))
                        ClaimDetail::where('id', $claim_detail_id)->update(['claim_id' => $result->id]);
                    $dos_ids = [];
                    // This is to update line item delete on edit process
                    foreach ($dos_spt_details as $key => $dos_spt_detail) {
                        if (isset($dos_spt_detail['id']) && $dos_spt_detail['id']) {
                            $claimdosdetail = ClaimCPTInfoV1::where('id', $dos_spt_detail['id']);
                            //dd($dos_spt_detail);
                            $dos_ids = ClaimCPTInfoV1::where('claim_id', $result->id)->pluck('id')->all();
                            $not_exist_value = isset($dos_value) ? $dos_value : $dos_ids;
                            // @todo check and replace new pmt flow
                            //$claimcptdetail = $claimdosdetail->select('charge', 'patient_paid', 'patient_adjusted')->first();
                            $dos_spt_detail['claim_id'] = $result->id;
                            $billed_amt = 0; // $claimcptdetail->charge;
                            $patient_paid = 0; // $claimcptdetail->patient_paid;
                            $patient_adjusted = 0; // $claimcptdetail->patient_adjusted;
                            //$patient_paid >0 && condition has been removed because we want to know each CPT billed amount change
                            if ($billed_amt != $dos_spt_detail['charge'] && $result->insurance_id == $request['insurance_id']) {
                                $this->changeCptPaidamountandMovetoWallet($billed_amt, $patient_paid, $dos_spt_detail);
                            } elseif ($billed_amt == $dos_spt_detail['charge'] && $prev_insurance_id != $request['insurance_id']) {
                                $this->changeCptPaidamountandMovetoWallet($billed_amt, $patient_paid, $dos_spt_detail, 'patient');
                            }

                            /* if($patient_adjusted) {								
                              $dos_spt_detail['balance'] = $dos_spt_detail['charge'] - $patient_adjusted;
                              } */

                            $claimdosdetail->update($dos_spt_detail);
                            $dos_spt_details[$key]['ids'] = $dos_spt_detail['id'];
                            // After submission of Claim only edit comes and we do not delete line item data
                            $dos_value = array_diff($not_exist_value, [$dos_spt_detail['id']]);
                        } else {
                            $dos_spt_detail['claim_id'] = $result->id;
                            $cpt_result = ClaimCPTInfoV1::create($dos_spt_detail);
                            $dos_spt_details[$key]['ids'] = $cpt_result->id;
                            $this->chargelineItemEntryProcess($dos_spt_detail, $cpt_result->id, @$request['insurance_id']);
                        }
                    }
                    /** Line item delete records should be removed  starts here * */
                    if (isset($dos_value)) {
                        //$claim_dos_ids = explode(',',$request['item_ids']);
                        //$claim_id_list = array_map(function($claim_dos_ids) {  
                        //return Helpers::getEncodeAndDecodeOfId($claim_dos_ids, 'decode');  }, $claim_dos_ids);					
                        ClaimCPTInfoV1::whereIn('id', $dos_value)->delete();
                    }
                    /** Line item delete records should be removed ends here * */
                    /*                     * * This should be removed once CMS1500 form has been completed** */
                    /* if(!empty($result->document_path) && !empty($result->cmsform))
                      {
                      $this->deleteExistingdocument($result->id);
                      }
                      $store_arr = $this->generatecms1500($result->id); */
                    /*                     * * This should be removed once CMS1500 form has been completed** */
                } else {
                    return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => ''));
                }
            }
            /*             * * If copay available Payment related code starts here** */
            if ($request['claim_id'] == '' && !empty($request['copay_amt']) || !empty($request['claim_id']) && isset($request['copay']) && $request['copay_amt'] != 0) {
                $payment_data['type'] = 'charge';
                $payment_data['payment_mode'] = $request['copay'];
                $payment_data['payment_amt'] = $request['copay_amt'];
                $payment_data['check_no'] = $request['check_no'];
                $payment_data['card_type'] = $request['card_type'];
                $payment_data['check_date'] = $request['check_date'];
                $payment_data['reference'] = $request['copay_detail'];
                $payment_data['patient_id'] = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'encode');
                $claim_id = Helpers::getEncodeAndDecodeOfId($result->id, 'encode');
                $payment_data['claim_id'] = $claim_id;
                $payment_data['tot_billed_amt'] = $request['total_charge'];
                $payment_data['payment_type'] = 'Payment';
                $payment_data['payment_method'] = 'Patient';
                $payment_data['original_paid'] = $request['copay_amt'];
                self::$remaining_val = $request['copay_amt'];
                if (!empty($dos_spt_details[0]['cpt_code'])) {
                    $i = 0;
                    $arr_index = count($dos_spt_details) - 1;
                    foreach ($dos_spt_details as $key => $dos_spt_detail) {
                        // @todo check and replace new pmt flow
                        $patient_balance = PMTClaimCPTFINV1::where('claim_cpt_info_id', @$dos_spt_detail['id'])->select('balance', 'patient_paid')->first();
                        //dd($patient_paid_already);
                        //dd($dos_spt_detail);
                        $patient_balance_paid = @$patient_balance->patient_paid;
                        $balance = @$patient_balance->balance;
                        $payment_data['cpt'][$i] = $dos_spt_details[$i]['cpt_code'];
                        $payment_data['cpt_billed_amt'][$i] = $dos_spt_details[$i]['charge'];
                        $return_val = $this->amountSplitBilled($request['copay_amt'], $dos_spt_details[$i]['charge']);
                        if ($balance < $payment_data['cpt_billed_amt'][$i] && !empty($patient_balance)) {
                            $payment_data['patient_balance'][$i] = $balance - $return_val['patient_paid'];
                        } else {
                            $payment_data['patient_balance'][$i] = $return_val['patient_balance'];
                        }

                        $payment_data['patient_paid'][$i] = $return_val['patient_paid'];
                        if ($return_val['remaning_val'] > 0 && $arr_index == $key) {
                            //$return_val['patient_paid'] = $return_val['patient_paid']+$return_val['remaning_val'];

                            $payment_data['patient_balance'][$i] = -1 * $return_val['remaning_val'];
                        }
                        $payment_data['patient_due'][$i] = 0;
                        $payment_data['balance'][$i] = isset($dos_spt_details[$i]['balance']) ? $dos_spt_details[$i]['balance'] : $dos_spt_details[$i]['charge'];
                        $payment_data['paid_amt'][$i] = 0; //Initially paid amount shouold be zero
                        $payment_data['ids'][$i] = $dos_spt_details[$i]['ids'];
                        $payment_data['insurance_due'][$i] = @$dos_spt_detail['insurance_balance'];
                        $i ++;
                    }
                }
                $payment = new PatientPaymentApiController();
                //dd($payment_data);
                $payment->getStoreApi($payment_data);
            }
            /*             * * If copay available Payment related code ends here** */

            /** Store claim notes starts* */
            if (!empty($result->id) && !empty($request['note'])) {
                $this->savepatientnotes($request['note'], $result->id, $request['patient_id']);
            }
            /** Store claim notes ends* */
            if ($result) {
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $result->id));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => ''));
            }
        }
    }

    public function storePatientAuth($data) {
        $auth_count = PatientAuthorization::where('patient_id', $data['patient_id'])
                        ->where('authorization_no', $data['auth_no'])->count();
        if ($auth_count == 0) {
            $authorization_arr = [
                'patient_id' => $data['patient_id'],
                'insurance_id' => $data['insurance_id'],
                'pos_id' => $data['pos_id'],
                'authorization_no' => $data['auth_no'],
                'authorization_notes' => $data['authorization_notes'],
            ];
            PatientAuthorization::create($authorization_arr);
        }
    }

    //  @todo - check and remove this function
    public function getUpdateApi($request = '')
    {
        $request = Request::all();
        $chargev1 = new ChargeV1ApiController();
        $api_response = $chargev1->updateCharge($request);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $api_response_data->data));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => ''));
        }
    }

    function amountSplitBilled($total_claim_charge, $total_cpt_charge) {
        $remaining_amt = self::$remaining_val;
        if ($remaining_amt >= $total_cpt_charge) {
            $paid = $total_cpt_charge;
            $balance = 0;
        } elseif ($remaining_amt < $total_cpt_charge && $remaining_amt > 0) {
            $paid = $remaining_amt;
            $balance = $total_cpt_charge - $remaining_amt;
        } elseif ($remaining_amt <= 0) {
            $paid = 0;
            $balance = $total_cpt_charge;
        }
        self::$remaining_val = $remaining_amt - $total_cpt_charge;
        $return_val['remaning_val'] = self::$remaining_val;
        $return_val['patient_paid'] = $paid;
        $return_val['patient_balance'] = $balance;
        return $return_val;
    }

    // Due to delete the index may get changed for line item 
    function reindexArray($request) {
        $array_vals = ['dos_from', 'dos_to', 'cpt', 'cpt_amt', 'modifier1', 'modifier2', 'modifier3', 'modifier4', 'unit', 'charge', 'cpt_icd_map', 'cpt_allowed', 'cpt_icd_map_key'];
        foreach ($array_vals as $array_val) {
            $arrval = array_values($request[$array_val]);
            $request[$array_val] = array_combine(range(0, count($arrval) - 1), array_values($arrval));
        }
        return $request;
    }

    // Store Api details Ends Here

    /* when billed amount was changed after patient paid the necessary parts needs to be 
      updated and need an entry at payment claimdetails to change the billed amount was changed */
    public function changeBilledAmountEntryProcess($request) {
        //dd($request);		
        // @todo - check and replace new pmt flow
        $balance_amt = 0;//PaymentClaimDetail::saveClaimTransaction($request, 'billed');
        return $balance_amt;
    }

    public function createChargeEntryTransaction($request, $claim_id) {
        $savedata['claim_id'] = $claim_id;
        $savedata['patient_id'] = $request['patient_id'];
        $savedata['balance_amt'] = $request['balance_amt'];
        $savedata['transaction_type'] = "emptyamount";
        $savedata['patient_due'] = $request['patient_due'];
        $savedata['insurance_due'] = isset($request['insurance_due']) ? $request['insurance_due'] : 0;
        $savedata['insurance_id'] = @$request['insurance_id'];
        $savedata['description'] = "newcharge";
        // @todo - check and replace new pmt flow
        //$result = PaymentClaimDetail::create($savedata);

        //appointment update query starts
        $facility_id = $request['facility_id'];
        $provider_id = $request['rendering_provider_id'];
        $patient_id = $request['patient_id'];
        $dos = $request['date_of_service'];
        /*  Patient Appointment status  changed in currrent Patient, same has provider, facility  */
        $appointment = PatientAppointment::where('facility_id', $facility_id)->where('provider_id', $provider_id)->where('patient_id', $patient_id)->where('scheduled_on', $dos)->where('status', 'Scheduled');
        $appointments = $appointment->get();
        /*   Count the no.of appointment */
        if ($appointment->count() > 0) {
            foreach ($appointments as $appointment) {
                $appointment->update(['status' => 'Complete']);
                $appointment->save();
            }
        }
        //appointment update query ends
    }

    public function chargelineItemEntryProcess($data, $dos_id, $insurance_id = null) {
        // @todo check and replace new pmt flow
        /*
        $savedata['claim_id'] = $data['claim_id'];
        $savedata['patient_id'] = $data['patient_id'];
        $savedata['insurance_id'] = $insurance_id;
        $savedata['payer_insurance_id'] = $insurance_id;
        $savedata['claimdoscptdetail_id'] = $dos_id;
        $savedata['transaction_type'] = "newcharge";
        $savedata['patient_balance'] = $data['patient_balance'];
        $savedata['insurance_balance'] = $data['insurance_balance'];
        $savedata['description'] = "newcharge";
        PaymentClaimCtpDetail::create($savedata);
        */
    }

    // Payment related details on popup at billing payment listing page related codes starts here
    public function changeResponsibilityEntryProcess($request, $getdata, $dosdetails = null) {
        /** @todo - check and remove this 
          $savedata['claim_id'] = $request['claim_id'];
          $savedata['patient_id'] = $request['patient_id'];
          //$savedata['balance_amt']= (@$request['balance_amt'] == '')?0:@$request['balance_amt'];
          $get_last_details_claim = PaymentClaimDetail::with(['claims' => function($query) {
          $query->select('id', 'balance_amt', 'insurance_due');
          }])->where("claim_id", $request['claim_id'])->select('patient_due', 'insurance_due', 'id', 'claim_id')->orderBy('id', 'desc')->first();
          //dd($request);
          if ($request['insurance_id'] == '') {
          $patient_balance = isset($get_last_details_claim->claims->balance_amt) ? $get_last_details_claim->claims->balance_amt : 0;
          $insurance_balance = 0;
          } else {
          $insurance_balance = isset($request['insurance_due']) ? $request['insurance_due'] : 0;
          $patient_balance = 0;
          }
          $savedata['patient_due'] = $patient_balance;
          $savedata['insurance_due'] = $insurance_balance;
          $savedata['insurance_id'] = @$getdata->insurance_id;
          $savedata['payer_insurance_id'] = empty($request['insurance_id']) ? "" : $request['insurance_id'];
          $savedata['transaction_type'] = "responsibility";
          $savedata['description'] = "Responsibility changed to Insurance";
          $result = PaymentClaimDetail::create($savedata);
          $claim_dos_details = Claimdoscptdetail::where('claim_id', $request['claim_id'])->where('cpt_code', '!=', 'patient')->get();
          foreach ($claim_dos_details as $key => $claim_dos_detail) {
          $save_cpt_detail['claim_id'] = $request['claim_id'];
          $save_cpt_detail['patient_id'] = $request['patient_id'];
          $get_last_details = PaymentClaimCtpDetail::where("claimdoscptdetail_id", $claim_dos_detail->id)->select('insurance_balance', 'patient_balance', 'id')->orderBy('id', 'desc')->first();
          $balance = $claim_dos_detail->balance;
          if ($request['insurance_id'] == '') {
          $pateint_balance = $balance;
          $insurance_balance = 0;
          } else {
          $insurance_balance = @$dosdetails[$key]['insurance_balance'];
          //$insurance_balance = $claim_dos_detail->patient_balance;
          $pateint_balance = 0;
          }
          $patient_balance = $pateint_balance;
          $insurance_balance = $insurance_balance;
          $save_cpt_detail['patient_balance'] = $patient_balance;
          $save_cpt_detail['balance_amt'] = $balance;
          $save_cpt_detail['posting_type'] = "Patient";
          $save_cpt_detail['insurance_balance'] = $insurance_balance;
          $save_cpt_detail['insurance_id'] = @$getdata->insurance_id;
          $save_cpt_detail['payer_insurance_id'] = $savedata['payer_insurance_id'];
          $save_cpt_detail['payment_claim_detail_id'] = $result->id;
          $save_cpt_detail['transaction_type'] = "responsibility";
          $save_cpt_detail['description'] = "Responsibility changed to Insurance";
          $save_cpt_detail['claimdoscptdetail_id'] = $claim_dos_detail->id;
          PaymentClaimCtpDetail::create($save_cpt_detail);
          }
         * 
         */
    }

    /** This is used when we change cpt billed amount after patient paid we will move the remaning amount to wallet starts here* */
    public function changeCptPaidamountandMovetoWallet($billed_old, $patient_paid, $doscptdetails, $responsibility = null) {
        // @todop check and replace new pmt flow
        /*
        $billed_new = $doscptdetails['charge'];
        $save_data['claim_id'] = $doscptdetails['claim_id'];
        $save_data['claimdoscptdetail_id'] = $doscptdetails['id'];
        $save_data['patient_balance'] = $doscptdetails['patient_balance'];
        $save_data['insurance_balance'] = $doscptdetails['insurance_balance'];
        //$save_data['insurance_id'] = 1;
        $save_data['insurance_id'] = (isset($doscptdetails['insurance_id']) && !empty($doscptdetails['insurance_id'])) ? $doscptdetails['insurance_id'] : 0;
        //dd($doscptdetails)    			;
        if ($responsibility) {
            $doscptdetails['insurance_balance'] = 0;
            $save_data['transaction_type'] = "responsibility";
            $save_data['description'] = "Responsibility changed to " . $responsibility;
        } else {
            $save_data['description'] = "Billed amount changed from $billed_old to $billed_new";
            $result_payment_claim_cpt = PaymentClaimCtpDetail::create($save_data);
        }

        //dd($result_payment_claim_cpt);		
        $remaining = $billed_new - $patient_paid;
        // Move amount to wallet if have any and update the balance details in claimdoscptsdetails table starts here  
        if ($remaining < 0) {
            $doscptdetails['paid_amt'] = DB::raw("paid_amt +" . $remaining);
            $doscptdetails['patient_paid'] = DB::raw("patient_paid +" . $remaining);
            $doscptdetails['patient_balance'] = DB::raw("patient_balance +" . $remaining);
            $save_data['description'] = "Remaining amount moved to wallet" . abs($remaining);
            //PaymentClaimCtpDetail::create($save_data);
            $this->moveCPTamountToWallet($save_data, abs($remaining));
            DB::table('claimdoscptdetails')->where('claim_id', $doscptdetails['claim_id'])->where('cpt_code', 'Patient')->update(['patient_paid' => DB::raw('patient_paid - ' . abs($remaining))]); // Update CPT line item tables
        }
        if ($responsibility = "Patient") {
            $doscptdetails['insurance_balance'] = 0;
        }
        $doscptdetails['balance'] = DB::raw("balance +" . $remaining);
        //dd($doscptdetails);
        Claimdoscptdetail::where('id', $doscptdetails['id'])->update($doscptdetails);
        // Move amount to wallet if have any and update the balance details in claimdoscptsdetails table ends here  
        */
    }

    /** This is used when we change cpt billed amount after patient paid we will move the remaning amount to wallet ends here* */
    public function moveCPTamountToWallet($savedata, $refunded_amount) {
        // @todo check and replace new pmt flow
        /*
        $savecptdetail_id = $savedata['claimdoscptdetail_id'];
        $patient_paid_details = PaymentClaimCtpDetail::where('claimdoscptdetail_id', $savecptdetail_id)->where('posting_type', 'Patient')->where('patient_paid', '>', 0)->select('payment_id', 'id', 'patient_paid')->get();
        $remaining_amount = $refunded_amount;
        foreach ($patient_paid_details as $patient_paid_detail) {
            $patient_paid = $patient_paid_detail->patient_paid;
            if ($remaining_amount > $patient_paid) {
                $remaining_amount = $remaining_amount - $patient_paid;
                $payment_amount = $patient_paid;
                $payment_id = $patient_paid_detail->payment_id;
                $this->saveRefundedClaimCptData($savedata, $payment_amount, $payment_id);
            } elseif ($remaining_amount <= $patient_paid) {
                $payment_amount = $remaining_amount;
                $payment_id = $patient_paid_detail->payment_id;
                $this->saveRefundedClaimCptData($savedata, $payment_amount, $payment_id);
                break;
            }
        }
        */
    }

    public function saveRefundedClaimCptData($saveData, $amount, $payment_id) {
        $saveData['payment_id'] = $payment_id;
        $saveData['patient_paid'] = -1 * $amount; // deducting amount
        $saveData['paid_amt'] = -1 * $amount; // deducting amount            
        // @todo - check and use new pmt flow
        /*
        $payment_claim_detail = PaymentClaimCtpDetail::create($saveData);
        if ($payment_claim_detail)
            PMTInfoV1::where('id', $payment_id)->update(['amt_used' => DB::raw("amt_used -" . $amount), 'balance' => DB::raw("balance +" . $amount)]);
        */    
        // @todo - check and implement new pmt flow
        //DB::table('claim_info_v1')->where('id', $saveData['claim_id'])->update(['total_paid' => DB::raw("total_paid -" . $amount),
        //    'patient_paid' => DB::raw("patient_paid -" . $amount)]);
    }

    public function getPaymentDetail($id) {
        if (ClaimInfoV1::where('id', $id)->count()) {
            $detail = ClaimInfoV1::with(['rendering_provider', 'billing_provider', 'insurance_details', 'facility_detail', 'dosdetails', 'claim_details' => function($query) {
                            $query->select('id', 'claim_id', 'box_23', 'illness_box14');
                        }])->where('id', $id)->first();
            return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('detail')));
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
    }

    // Payment related details on popup at billing payment listing page related codes ends here

    public function getDeleteApi($id) {
        return ClaimInfoV1::deleteClaim($id);
    }

    /** Claim number generation related code starts here * */
    public function generateclaimnumber($type, $claim_id) {  // Claim number generation
        $claim_number = str_pad($claim_id, 5, '0', STR_PAD_LEFT);
        return $claim_number;
    }

    /** Claim number generation related code ends here * */
    // If a patient added with the same dos an alert will get opened and confirms with the patient
    public function checkExistingDosApi($patient_id, $dos) {
        $dateofService = date("Y-m-d", strtotime(base64_decode($dos)));
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $dosCount =  ClaimCPTInfoV1::where('dos_from', '=', $dateofService)->where('patient_id', $patient_id)->count();
        if ($dosCount > 0) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function generatecms1500($claim_id, $type = null) {
        $claim_data = ClaimInfoV1::with(['rendering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'dosdetails' => function($q) {
                        $q->where('is_active', 1);
                    }, 'patient', 'claim_details'])->where('id', $claim_id)->first();

        $insurance_data = [];
        if (!empty($claim_data['insurance_id'])) {
            $insurance_data = $this->getinsurancedetail(array($claim_data['insurance_id']), $claim_data['patient_id']);
        }
        $claim_detail['id'] = $claim_data->id;
        $claim_detail['ins_type'] = isset($claim_data->insurance_details->insurancetype) ? $claim_data->insurance_details->insurancetype->type_name : '';
        $claim_detail['insured_id_number'] = (!empty($insurance_data) && isset($insurance_data['policy_num'])) ? @$insurance_data['policy_num'] : '';
        $claim_detail['patient_name'] = Helpers::getNameformat(@$claim_data->patient->last_name, @$claim_data->patient->first_name, @$claim_data->patient->middle_name);
        $claim_detail['patient_dob_m'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1970-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('m', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_dob_d'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1970-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('d', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_dob_y'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1970-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('Y', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_gender'] = isset($claim_data->patient->gender) ? $claim_data->patient->gender : '';
        $claim_detail['insured_name'] = (!empty($insurance_data) && isset($insurance_data['insured_name'])) ? $insurance_data['insured_name'] : '';
        $claim_detail['insured_addr'] = (!empty($insurance_data) && isset($insurance_data['insured_addr'])) ? $insurance_data['insured_addr'] : '';
        $claim_detail['insured_city'] = (!empty($insurance_data) && isset($insurance_data['insured_city'])) ? $insurance_data['insured_city'] : '';
        $claim_detail['insured_state'] = (!empty($insurance_data) && isset($insurance_data['insured_state'])) ? $insurance_data['insured_state'] : '';
        $claim_detail['insured_zip5'] = (!empty($insurance_data) && isset($insurance_data['insured_zip5'])) ? $insurance_data['insured_zip5'] : '';
        $claim_detail['insured_phone'] = (!empty($insurance_data) && isset($insurance_data['insured_phone'])) ? $insurance_data['insured_phone'] : ''; // Need to keep this field			
        $claim_detail['patient_addr'] = $claim_data->patient->address1;
        $claim_detail['patient_city'] = $claim_data->patient->city;
        $claim_detail['patient_state'] = $claim_data->patient->state;
        $zip4 = isset($claim_data->patient->zip4) ? $claim_data->patient->zip4 : '';
        $claim_detail['patient_zip5'] = $claim_data->patient->zip5 . $zip4;
        $claim_detail['patient_phone'] = $claim_data->patient->phone;
        //use it preg_replace('/[^a-z0-9]/i', '', $claim_data->patient->phone);
        $claim_detail['insured_relation'] = (!empty($insurance_data) && isset($insurance_data['insured_relation'])) ? $insurance_data['insured_relation'] : '';
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
        if (isset($claim_detail['ins_type']) && $claim_detail['ins_type'] == 'Medicare') {
            $claim_detail['is_another_ins'] = (!empty($insurance_data) && isset($insurance_data['is_another_ins'])) ? $insurance_data['is_another_ins'] : 'No';
            $claim_detail['other_insured_name'] = (!empty($insurance_data) && isset($insurance_data['other_insured_name'])) ? $insurance_data['other_insured_name'] : '';
            $claim_detail['other_ins_policy'] = (!empty($insurance_data) && isset($insurance_data['other_ins_policy'])) ? $insurance_data['other_ins_policy'] : '';
            $claim_detail['other_insur_name'] = (!empty($insurance_data) && isset($insurance_data['other_insur_name'])) ? $insurance_data['other_insur_name'] : '';
        }
        $claim_detail['claimcode'] = isset($claim_data->claim_details->claim_code) ? $claim_data->claim_details->claim_code : '';
        $claim_detail['ins_policy_no'] = (!empty($insurance_data) && isset($insurance_data['ins_policy_no'])) ? $insurance_data['ins_policy_no'] : '';
        $claim_detail['insured_dob_m'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_m'])) ? $insurance_data['insured_dob_m'] : '';
        $claim_detail['insured_dob_d'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_d'])) ? $insurance_data['insured_dob_d'] : '';
        $claim_detail['insured_dob_y'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_y'])) ? $insurance_data['insured_dob_y'] : '';
        $claim_detail['insured_gender'] = (!empty($insurance_data) && isset($insurance_data['insured_gender'])) ? $insurance_data['insured_gender'] : '';  // Need to keept this field 
        $claim_detail['other_claimid'] = isset($claim_data->claim_details->otherclaimid) ? $claim_data->claim_details->otherclaimid : '';
        $claim_detail['other_claimid_qual'] = !(empty($claim_detail['other_claimid'])) ? 'Y4' : '';
        $claim_detail['insur_name'] = (!empty($insurance_data) && isset($insurance_data['insur_name'])) ? $insurance_data['insur_name'] : '';

        $claim_detail['patient_signed'] = (!empty($claim_data->claim_details) && $claim_data->claim_details->print_signature_onfile_box12 == 'No') ? 'NO SIGNATURE ON FILE' : 'SIGNATURE ON FILE';
        $claim_detail['signed_date'] = (isset($claim_data->submited_date) && $claim_data->submited_date != '0000-00-00') ? date('m d y', strtotime($claim_data->submited_date)) : ''; //Claim Submited date.
        $claim_detail['insured_signed'] = (!empty($claim_data->claim_details) && $claim_data->claim_details->print_signature_onfile_box13 == 'No') ? 'NO SIGNATURE ON FILE' : 'SIGNATURE ON FILE';

        $claim_detail['amount_paid'] = (isset($claim_data->insurance_paid) && $claim_data->is_send_paid_amount == 0) ? explode('.', $claim_data->insurance_paid) : '';
        $patient_id = $claim_data->patient_id;
        // If pregnency LMP was filled take its date and qualifier given at the NUCC pdf
        if (!empty($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01') {
            $claim_detail['doi_m'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('y', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_qual'] = 484;
        } else {
            $claim_detail['doi_m'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01') ? date('m', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01') ? date('d', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01') ? date('y', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_qual'] = 431;
        }
        $claim_detail['other_m'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_d'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_y'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_qual'] = isset($claim_data->claim_details->other_date_qualifier) ? $claim_data->claim_details->other_date_qualifier : '';

        $claim_detail['box18_from_m'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_d'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_y'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('y', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_to_m'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_d'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_y'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('y', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['refering_provider'] = isset($claim_data->refering_provider) ? $claim_data->refering_provider->provider_name : '';
        $claim_detail['refering_provider_npi'] = isset($claim_data->refering_provider) ? $claim_data->refering_provider->npi : '';
        $refering_provider_type = (isset($claim_data->referingprovidertypeid) && $claim_data->referingprovidertypeid != 0) ? $claim_data->referingprovidertypeid : '';
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
        $claim_detail['admit_date_y'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01' && $claim_data->admit_date != '0000-00-00') ? date('y', strtotime($claim_data->admit_date)) : '';
        $claim_detail['discharge_date_m'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('m', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_d'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('d', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_y'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('y', strtotime($claim_data->discharge_date)) : '';
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
        $etin_ssn = '';
        $etin_tax = '';
        $claim_detail['emergency'] = isset($claim_data->claim_details->emergency) ? $claim_data->claim_details->emergency : '';
        $claim_detail['pos'] = isset($claim_data->facility_detail->pos_details->code) ? $claim_data->facility_detail->pos_details->code : '';
        $claim_detail['epsdt'] = isset($claim_data->claim_details->epsdt) ? $claim_data->claim_details->epsdt : '';
        $claim_detail['billing_provider_npi'] = !empty($claim_data->billing_provider) ? $claim_data->billing_provider->npi : '';
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
        $claim_detail['rendering_provider_name'] = isset($claim_data->rendering_provider->provider_name) ? $claim_data->rendering_provider->provider_name : '';
        $claim_detail['rendering_provider_date'] = (isset($claim_data->submited_date) && $claim_data->submited_date != '0000-00-00') ? date('m d y', strtotime($claim_data->submited_date)) : '';
        $claim_detail['facility_name'] = isset($claim_data->facility_detail->facility_name) ? $claim_data->facility_detail->facility_name : '';
        $claim_detail['facility_addr'] = isset($claim_data->facility_detail->facility_address->address1) ? $claim_data->facility_detail->facility_address->address1 : '';
        $facility_zip_4 = isset($claim_data->facility_detail->facility_address->pay_zip4) ? $claim_data->facility_detail->facility_address->pay_zip4 : '';
        $claim_detail['facility_city'] = @$claim_data->facility_detail->facility_address->city . ' ' . @$claim_data->facility_detail->facility_address->state . ' ' . @$claim_data->facility_detail->facility_address->pay_zip5 . @$facility_zip_4;
        $claim_detail['facility_npi'] = isset($claim_data->facility_detail->facility_npi) ? $claim_data->facility_detail->facility_npi : '';
        $service_facility_qual = isset($claim_data->claim_details->service_facility_qual) ? $claim_data->claim_details->service_facility_qual : '';
        $facility_otherid = isset($claim_data->claim_details->facility_otherid) ? $claim_data->claim_details->facility_otherid : '';
        $billing_prov_zip_4 = isset($claim_data->billing_provider->zipcode4) ? $claim_data->billing_provider->zipcode4 : '';
        $claim_detail['box_32b'] = !empty($service_facility_qual && $facility_otherid) ? $service_facility_qual . $facility_otherid : '';
        $claim_detail['bill_provider_name'] = isset($claim_data->billing_provider->provider_name) ? $claim_data->billing_provider->provider_name : '';
        $claim_detail['bill_provider_addr'] = isset($claim_data->billing_provider->address_1) ? $claim_data->billing_provider->address_1 : '';
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
                $doc['row_' . $i]['from_yy'] = date('y', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_mm'] = date('m', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_dd'] = date('d', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_yy'] = date('y', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['cpt'] = $dos_detail->cpt_code;
                $doc['row_' . $i]['mod1'] = isset($dos_detail->modifier1) ? $dos_detail->modifier1 : '';
                $doc['row_' . $i]['mod2'] = isset($dos_detail->modifier2) ? $dos_detail->modifier2 : '';
                $doc['row_' . $i]['mod3'] = isset($dos_detail->modifier3) ? $dos_detail->modifier3 : '';
                $doc['row_' . $i]['mod4'] = isset($dos_detail->modifier4) ? $dos_detail->modifier4 : '';
                $doc['row_' . $i]['billed_amt'] = isset($dos_detail->cpt_billed_amt) ? explode('.', $dos_detail->cpt_billed_amt) : '';
                $doc['row_' . $i]['icd_pointer'] = isset($dos_detail->cpt_icd_map_key) ? substr(strtr($dos_detail->cpt_icd_map_key, $trans_key), 0, 4) : '';
                $doc['row_' . $i]['unit'] = isset($dos_detail->unit) ? $dos_detail->unit : 1;
            }
            $i++;
        }
        $claim_detail['box_24'] = $doc;
        $line_item = count($claim_data->dosdetails);
        if ($type == 'frompayment') {                    // From payment screen
            // @todo - check and replace new pmt flow
            $multiple_insurances = []; //Claimdoscptdetail::where('claim_id', $claim_id)->where('insurance_id', '!=', 'patient')->where('insurance_id', '!=', '')->groupBy('insurance_id')->pluck('insurance_id', 'id')->all();
            //$insurancedata = [];
            $multiple_insurances = array($claim_data['insurance_id']);
            $insurancedata = $this->getinsurancedetail($multiple_insurances, $patient_id);
            $insurancedata = array($insurancedata);
            foreach ($insurancedata as $ins) {
                $claim_detail = array_merge($claim_detail, $ins);
                if ($line_item > 6 && $line_item < 13) {
                    $this->createpdf($claim_detail, 'second');
                } elseif ($line_item > 12) {
                    $this->createpdfformorelineitem($claim_detail);
                } else {
                    $this->createpdf($claim_detail);
                }
            }
        } else {                                           // From billing
            if ($line_item > 6 && $line_item < 13) {
                $this->createpdf($claim_detail, 'second');
            } elseif ($line_item > 12) {
                $this->createpdfformorelineitem($claim_detail);
            } else {
                $this->createpdf($claim_detail);
            }
        }
    }

    public function createpdf($claim_data, $document_mode = 'first') {
        $doc1 = '<!-- 1 Starts -->
<div style="position:absolute;left:29px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['to_yy'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_1']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:784px" class="cls_002"><span class="cls_002 med-black">' . ((isset($claim_data['box_24']['row_1']) && isset($claim_data['box_24']['row_1']['billed_amt'][0])) ? $claim_data['box_24']['row_1']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:784px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['box_24']['row_1']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:767px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:767px" class="cls_007"><span class="cls_007 med-black">00</span></div>


<div style="position:absolute;left:684px;top:785px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_1']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 2 Starts -->

<div style="position:absolute;left:29px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['to_yy'] : '') . '</span></div>
<div style="position:absolute;left:203px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_2']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:821px" class="cls_002"><span class="cls_002 med-black">' . ((isset($claim_data['box_24']['row_2']) && isset($claim_data['box_24']['row_2']['billed_amt'][0])) ? $claim_data['box_24']['row_2']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:821px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['box_24']['row_2']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:804px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:804px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:822px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_2']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 3 Starts -->
<div style="position:absolute;left:29px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['to_yy'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_3']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']['row_3']) && !empty($claim_data['box_24']['row_3'] && isset($claim_data['box_24']['row_3']['billed_amt'][0])) ? $claim_data['box_24']['row_3']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:855px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['box_24']['row_3']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:838px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:838px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:856px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_3']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 4 Starts -->

<div style="position:absolute;left:29px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['to_yy'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_4']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']['row_4']) && !empty($claim_data['box_24']['row_4'] && isset($claim_data['box_24']['row_4']['billed_amt'][0])) ? $claim_data['box_24']['row_4']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:892px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['box_24']['row_4']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:875px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:875px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:893px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_4']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 5 Starts -->

<div style="position:absolute;left:29px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['to_mm'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_5']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']['row_5']) && !empty($claim_data['box_24']['row_5'] && isset($claim_data['box_24']['row_5']['billed_amt'][0])) ? $claim_data['box_24']['row_5']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:926px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['box_24']['row_5']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:926px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:909px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:909px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:927px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_5']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 6 Starts -->

<div style="position:absolute;left:29px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['from_dd'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['to_yy'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_6']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_1']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data->dosdetails[5]) ? $claim_data->dosdetails[5]->cpt_billed_amt : '') . '</span></div>
<div style="position:absolute;left:563px;top:961px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['box_24']['row_6']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:961px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:944px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:944px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:962px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_6']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>';


        $doc2 = '
<!-- 7 Starts -->
<div style="position:absolute;left:29px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['to_yy'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_7']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:784px" class="cls_002"><span class="cls_002 med-black">' . ((isset($claim_data['box_24']['row_7']) && isset($claim_data['box_24']['row_7']['billed_amt'][0])) ? $claim_data['box_24']['row_7']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:784px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['box_24']['row_7']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:784px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:767px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:767px" class="cls_007"><span class="cls_007 med-black">00</span></div>


<div style="position:absolute;left:684px;top:785px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_7']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 8 Starts -->

<div style="position:absolute;left:29px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['to_yy'] : '') . '</span></div>
<div style="position:absolute;left:203px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_8']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:821px" class="cls_002"><span class="cls_002 med-black">' . ((isset($claim_data['box_24']['row_8']) && isset($claim_data['box_24']['row_8']['billed_amt'][0])) ? $claim_data['box_24']['row_8']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:821px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['box_24']['row_8']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:821px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:804px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:804px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:822px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_8']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 9 Starts -->
<div style="position:absolute;left:29px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['to_yy'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_9']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']['row_9']) && !empty($claim_data['box_24']['row_9'] && isset($claim_data['box_24']['row_9']['billed_amt'][0])) ? $claim_data['box_24']['row_9']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:855px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['box_24']['row_9']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:855px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:538px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:538px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:856px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_9']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

<!-- 10 Starts -->

<div style="position:absolute;left:29px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['from_mm'] : '') . '</span></div>
<div style="position:absolute;left:55px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['from_dd'] : '') . '</span></div>
<div style="position:absolute;left:87px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['from_yy'] : '') . '</span></div>

<div style="position:absolute;left:115px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['to_mm'] : '') . '</span></div>
<div style="position:absolute;left:143px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['to_dd'] : '') . '</span></div>
<div style="position:absolute;left:173px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['to_yy'] : '') . '</span></div>

<div style="position:absolute;left:203px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['pos'] : '') . '</span></div>
<div style="position:absolute;left:235px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && isset($claim_data['box_24']['row_10']) ? $claim_data['emergency'] : '') . '</span></div>

<div style="position:absolute;left:274px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['cpt'] : '') . '</span></div>

<div style="position:absolute;left:337px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['mod1'] : '') . '</span></div>
<div style="position:absolute;left:368px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['mod2'] : '') . '</span></div>
<div style="position:absolute;left:398px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['mod3'] : '') . '</span></div>
<div style="position:absolute;left:428px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['mod4'] : '') . '</span></div>

<div style="position:absolute;left:468px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['icd_pointer'] : '') . '</span></div>

<div style="position:absolute;left:520px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']['row_10']) && !empty($claim_data['box_24']['row_10'] && isset($claim_data['box_24']['row_10']['billed_amt'][0])) ? $claim_data['box_24']['row_10']['billed_amt'][0] : '') . '</span></div>
<div style="position:absolute;left:563px;top:892px" class="cls_002"><span class="cls_002 med-black"></span></div>

<div style="position:absolute;left:600px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['box_24']['row_10']['unit'] : '') . '</span></div>
<div style="position:absolute;left:625px;top:892px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['epsdt'] : '') . '</span></div>

<div style="position:absolute;left:653px;top:875px" class="cls_007"><span class="cls_007 med-black">G2</span></div>
<div style="position:absolute;left:684px;top:875px" class="cls_007"><span class="cls_007 med-black"></span></div>

<div style="position:absolute;left:684px;top:893px" class="cls_007"><span class="cls_007 med-black">' . (!empty($claim_data['box_24']) && !empty($claim_data['box_24']['row_10']) ? $claim_data['billing_provider_npi'] : '') . '</span></div>

';
        $content = '';
        $page2 = "";
        $content = $this->gethtmlcontentforpdf($claim_data, $doc1);
        if ($document_mode == 'second') {
            $page2 = $this->gethtmlcontentforpdf($claim_data, $doc2);  // This is to generate second page if the line item is grater than 6
        }
        $content1 = $page2 . '</body></html>';
        $claim_id = $claim_data['id'];
        $type = "claim";
        $file = '';
        $pdf = App::make('dompdf.wrapper');
        $content = $content . '' . $content1;
        $pdf->loadHTML($content);
        $id = Auth::user()->id;
        if (App::environment() == 'production')
            $pdf_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $pdf_path = public_path() . '/';

        $src_path = $pdf_path . '/media/claim/' . $id . '/' . $claim_id;

        $url_path = url("/") . '/media/claim/' . $id . '/' . $claim_id;
        $file_name = time() . '.pdf';
        if (!file_exists($src_path)) {

            mkdir($src_path, 0777, true);
        }
        $filename = $src_path . '/' . $file_name;

        if ($pdf->save($filename)) {
            $file_store_name = md5($claim_id . strtotime(date('Y-m-d H:i:s'))) . '.pdf';
            $src_retrive = $url_path . '/' . $file_name;
            $store_arr = Helpers::amazon_server_folder_check($type, $file, $file_store_name, $src_retrive); // Move to amazon server
            $claim = ClaimInfoV1::find($claim_id);
            $claim->cmsform = empty($claim->cmsform) ? $file_store_name : $claim->cmsform . ',' . $file_store_name;
            $claim->document_path = empty($claim->document_path) ? $store_arr[0] : $claim->document_path . ',' . $store_arr[0];
            $claim->document_domain = empty($claim->document_domain) ? $store_arr[1] : $claim->document_domain . ',' . $store_arr[1];
            if ($claim->save()) {
                if (file_exists($src_path)) {
                    File::deleteDirectory($src_path); // Delete created folder at local			
                }
            }
            return true;
        } else {
            return false;
        }
    }

    function createpdfformorelineitem($claim_data) {
        $claim_id = $claim_data['id'];
        $type = "claim";
        $file = '';
        $pdf = App::make('dompdf.wrapper');
        $content = "CMS1500 will be in processing";
        $pdf->loadHTML($content);
        $id = Auth::user()->id;
        if (App::environment() == 'production')
            $pdf_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $pdf_path = public_path() . '/';

        $src_path = $pdf_path . '/media/claim/' . $id . '/' . $claim_id;

        $url_path = url("/") . '/media/claim/' . $id . '/' . $claim_id;
        $file_name = time() . '.pdf';
        if (!file_exists($src_path)) {
            mkdir($src_path, 0777, true);
        }
        $filename = $src_path . '/' . $file_name;

        if ($pdf->save($filename)) {
            $file_store_name = md5($claim_id . strtotime(date('Y-m-d H:i:s'))) . '.pdf';
            $src_retrive = $url_path . '/' . $file_name;
            $store_arr = Helpers::amazon_server_folder_check($type, $file, $file_store_name, $src_retrive); // Move to amazon server
            $claim = ClaimInfoV1::find($claim_id);
            $claim->cmsform = empty($claim->cmsform) ? $file_store_name : $claim->cmsform . ',' . $file_store_name;
            $claim->document_path = empty($claim->document_path) ? $store_arr[0] : $claim->document_path . ',' . $store_arr[0];
            $claim->document_domain = empty($claim->document_domain) ? $store_arr[1] : $claim->document_domain . ',' . $store_arr[1];
            if ($claim->save()) {
                if (file_exists($src_path)) {
                    File::deleteDirectory($src_path); // Delete created folder at local			
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /** To show the pdf from s3 server when we clicks on cms1500 starts here* */
    public function getcms1500Api($id, $type = null) {
        $claim_id = $id;
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $claim = ClaimInfoV1::where('id', $id)->select('document_path', 'cmsform')->first();
        $default_view = Config::get('siteconfigs.production.defult_production');
        $chk_env_site = getenv('APP_ENV');
        if ($chk_env_site == "local")
            $storage_disk = "s3";
        elseif ($chk_env_site == $default_view)
            $storage_disk = "s3_production";
        else
            $storage_disk = "s3";
        $document_path = explode(',', $claim->document_path); // To check for more than one cms document file
        $document_name = explode(',', $claim->cmsform);
        $file_exist = Storage::disk($storage_disk)->exists($document_path[0] . $document_name[0]);
        if ($type == 1 && Storage::disk($storage_disk)->exists($document_path[0] . $document_name[0])) {
            $file = Storage::disk($storage_disk)->get($document_path[0] . $document_name[0]);
        } elseif ($type == 2 && Storage::disk($storage_disk)->exists($document_path[0] . $document_name[0])) {
            $file = Storage::disk($storage_disk)->get($document_path[1] . $document_name[1]);
        } elseif (Storage::disk($storage_disk)->exists($document_path[0] . $document_name[0])) {
            $file = Storage::disk($storage_disk)->get($claim->document_path . $claim->cmsform);
        } else {
            return view('layouts/claim_contruction');
        }
        $filename = "document.pdf";
        return (new Responseobj($file, 200))->header('Content-Type', 'application/pdf')->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /** To show the pdf from s3 server when we clicks on cms1500 starts ends* */

    /** When we update claim delete the existing document related details from database and s3 starts here* */
    public function deleteExistingdocument($claim_id) {
        $claim = ClaimInfoV1::where('id', $claim_id)->select('id', 'document_path', 'cmsform', 'document_domain')->first();
        $main_dir_name = md5('P4');  // Statically given
        if (Session::get('practice_dbid') != '') {
            $main_dir_name = md5('P' . Session::get('practice_dbid'));
        }
        if ($main_dir_name != '') {
            $chk_env_site = getenv('APP_ENV');
            $default_view = Config::get('siteconfigs.production.defult_production');
            $chk_env_site = getenv('APP_ENV');
            if ($chk_env_site == "local")
                $storage_disk = "s3";
            elseif ($chk_env_site == $default_view)
                $storage_disk = "s3_production";
            else
                $storage_disk = "s3";
            $document = explode(',', $claim['document_path']);  // To check for more than one cmsdocument file
            $cms = explode(',', $claim['cmsform']);
            if (count($document) > 1) {
                if (Storage::disk($storage_disk)->exists($document[0] . $cms[0])) {
                    Storage::disk($storage_disk)->delete([$document[0] . $cms[0]]);
                    ClaimInfoV1::where('id', $claim_id)->update(['document_path' => '', 'cmsform' => '', 'document_domain' => '']);
                    return true;
                }
            } else {
                if (Storage::disk($storage_disk)->exists($claim['document_path'] . $claim['cmsform'])) {
                    Storage::disk($storage_disk)->delete([$claim['document_path'] . $claim['cmsform']]);
                    ClaimInfoV1::where('id', $claim_id)->update(['document_path' => '', 'cmsform' => '', 'document_domain' => '']);
                    return true;
                }
            }
        }
    }

    /** When we update claim delete the existing document related details from database and s3 ends here* */
    function gethtmlcontentforpdf($claim_data, $documentmode) {
        if (App::environment() == 'production')
            $img_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $img_path = public_path() . '/';
        $path = $img_path . "/img/background3.jpg";
        $print_data = '<html>
<head><meta http-equiv=Content-Type content="text/html; charset=UTF-8">
<style type="text/css">
<!--
span.cls_label{font-family:Arial,sans-serif;font-size:8.2px;color:rgb(227,31,37); font-weight:normal;font-style:normal;text-decoration: none}
div.cls_label{font-family:Arial,sans-serif;font-size:8.4px;color:rgb(227,31,37);font-style:normal;text-decoration: none}
span.cls_003{font-family:Arial,sans-serif;font-size:15.1px;color:rgb(227,31,37);font-weight:600;font-style:normal;text-decoration: none}
div.cls_003{font-family:Arial,sans-serif;font-size:15.1px;color:rgb(227,31,37);font-weight:600;font-style:normal;text-decoration: none}
span.cls_002{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-style:normal;text-decoration: none}
div.cls_002{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-style:normal;text-decoration: none}
span.cls_012{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_012{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_004{font-family:Arial,sans-serif;font-size:12.1px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_004{font-family:Arial,sans-serif;font-size:12.1px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_005{font-family:Arial,sans-serif;font-size:6.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_005{font-family:Arial,sans-serif;font-size:6.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_006{font-family:Arial,sans-serif;font-size:5.9px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_006{font-family:Arial,sans-serif;font-size:5.9px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_013{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_013{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_007{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_007{font-family:Arial,sans-serif;font-size:8.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_010{font-family:Arial,sans-serif;font-size:5.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_010{font-family:Arial,sans-serif;font-size:5.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_016{font-family:Arial,sans-serif;font-size:4.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_016{font-family:Arial,sans-serif;font-size:4.8px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_011{font-family:Arial,sans-serif;font-size:5.4px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_011{font-family:Arial,sans-serif;font-size:5.4px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_014{font-family:Arial,sans-serif;font-size:6.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_014{font-family:Arial,sans-serif;font-size:6.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_008{font-family:Arial,sans-serif;font-size:17.6px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_008{font-family:Arial,sans-serif;font-size:17.6px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_009{font-family:Arial,sans-serif;font-size:4.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_009{font-family:Arial,sans-serif;font-size:4.0px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_017{font-family:Arial,sans-serif;font-size:14.1px;color:rgb(249,208,204);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_017{font-family:Arial,sans-serif;font-size:14.1px;color:rgb(249,208,204);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_018{font-family:Arial,sans-serif;font-size:11px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_018{font-family:Arial,sans-serif;font-size:11px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_019{font-family:Arial,sans-serif;font-size:10px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
div.cls_019{font-family:Arial,sans-serif;font-size:8.1px;color:rgb(227,31,37);font-weight:normal;font-style:normal;text-decoration: none}
span.cls_020{font-family:"Verdana Bold Italic",sans-serif;font-size:11px;color:rgb(227,31,37);font-weight:bold;font-style:italic;text-decoration: none}
div.cls_020{font-family:"Verdana Bold Italic",sans-serif;font-size:11px;color:rgb(227,31,37);font-weight:bold;font-style:italic;text-decoration: none}
.med-black{color:#000 !important;}
.italic{font-style:italic;}
@page { margin-top: 0.3em;
            margin-left: 0.6em; }
body { margin: 0px; }
*{margin:0;padding:0}
-->
</style>
</head>
<body>
<div style="position:absolute;left:50%;margin-left:-408px;top:0px;width:820px;height:2000px;overflow:hidden">
<div style="position:absolute;left:0px;top:0px"><img src="' . $path . '" width=820 height=1146></div>
<div style="position:absolute;left:20.7px;top:76px" class="cls_003"><span class="cls_003">HEALTH INSURANCE CLAIM FORM</span></div>
<div style="position:absolute;left:22px;top:100px" class="cls_label"><span class="cls_label">APPROVED BY NATIONAL UNIFORM CLAIM COMMITTEE (NUCC) 02/12</span></div>
<div style="position:absolute;left:52px;top:116.56px" class="cls_label"><span class="cls_label">PICA</span></div>
<div style="position:absolute;left:742px;top:117.9px" class="cls_label"><span class="cls_label">PICA</span></div>
<div style="position:absolute;left:24.45px;top:136px" class="cls_label"><span class="cls_label">1.</span></div>
<div style="position:absolute;left:39.65px;top:136px" class="cls_label"><span class="cls_label">MEDICARE</span></div>
<div style="position:absolute;left:106.88px;top:136px" class="cls_label"><span class="cls_label">MEDICAID</span></div>
<div style="position:absolute;left:177.34px;top:136px" class="cls_label"><span class="cls_label">TRICARE</span></div>
<div style="position:absolute;left:264px;top:136px" class="cls_label"><span class="cls_label">CHAMPVA</span></div>
<div style="position:absolute;left:331.56px;top:136px" class="cls_label"><span class="cls_label">GROUP</span></div>
<div style="position:absolute;left:331.56px;top:143.88px" class="cls_label"><span class="cls_label">HEALTH PLAN</span></div>
<div style="position:absolute;left:408px;top:136px" class="cls_label"><span class="cls_label">FECA</span></div>
<div style="position:absolute;left:408px;top:143.88px" class="cls_label"><span class="cls_label">BLK LUNG</span></div>
<div style="position:absolute;left:468.34px;top:136px" class="cls_label"><span class="cls_label">OTHER</span></div>

<div style="position:absolute;left:501px;top:136px" class="cls_label"><span class="cls_label">1a. INSURED\'S I.D. NUMBER</span></div>
<div style="position:absolute;left:683px;top:136px" class="cls_label"><span class="cls_label">(For Program In Item 1)</span></div>

<div style="position:absolute;left:39.65px;top:152px" class="cls_label"><span class="italic">(Medicare#)</span></div>
<div style="position:absolute;left:106.88px;top:152px" class="cls_label"><span class="italic">(Medicaid#)</span></div>
<div style="position:absolute;left:176.34px;top:152px" class="cls_label"><span class="italic">(ID#/DoD#)</span></div>
<div style="position:absolute;left:260.76px;top:152px" class="cls_label"><span class="italic">(Member ID#)</span></div>
<div style="position:absolute;left:331.56px;top:152px" class="cls_label"><span class="italic">(ID#)</span></div>
<div style="position:absolute;left:408px;top:152px" class="cls_label"><span class="italic">(ID#)</span></div>
<div style="position:absolute;left:468.34px;top:152px" class="cls_label"><span class="italic">(ID#)</span></div>

<!-- Box 2 -->
<div style="position:absolute;left:24.65px;top:171px" class="cls_label"><span class="cls_label">2. PATIENT\'S NAME (Last Name, First Name, Middle Initial)</span></div>
<!-- Box 3 -->
<div style="position:absolute;left:305.55px;top:171px" class="cls_label"><span class="cls_label">3. PATIENT\'S BIRTH DATE</span></div>
<div style="position:absolute;left:442.55px;top:171px" class="cls_label"><span class="cls_label">SEX</span></div>
<div style="position:absolute;left:319.05px;top:178px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:351.05px;top:178px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:387.75px;top:178px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:412.75px;top:188px" class="cls_label"><span class="cls_label">M</span></div>
<div style="position:absolute;left:461.95px;top:188px" class="cls_label"><span class="cls_label">F</span></div>

<!-- Box 4 -->
<div style="position:absolute;left:501.95px;top:171px" class="cls_label"><span class="cls_label">4. INSURED\'S NAME (Last Name, First Name, Middle Initial)</span></div>

<!-- Box 5 -->
<div style="position:absolute;left:24.65px;top:204px" class="cls_label"><span class="cls_label">5. PATIENT\'S ADDRESS (No., Street)</span></div>
<div style="position:absolute;left:24.65px;top:240px" class="cls_label"><span class="cls_label">CITY</span></div>
<div style="position:absolute;left:269.65px;top:240px" class="cls_label"><span class="cls_label">STATE</span></div>
<div style="position:absolute;left:24.65px;top:274px" class="cls_label"><span class="cls_label">ZIP CODE</span></div>
<div style="position:absolute;left:151.45px;top:274px" class="cls_label"><span class="cls_label">TELEPHONE (Include Area Code)</span></div>
<!-- Box 6 -->
<div style="position:absolute;left:305.55px;top:204px" class="cls_label"><span class="cls_label">6. PATIENT RELATIONSHIP TO INSURED</span></div>
<div style="position:absolute;left:316px;top:223px" class="cls_label"><span class="cls_label">Self</span></div>
<div style="position:absolute;left:352px;top:223px" class="cls_label"><span class="cls_label">Spouse</span></div>
<div style="position:absolute;left:401px;top:223px" class="cls_label"><span class="cls_label">Child</span></div>
<div style="position:absolute;left:447.88px;top:223px" class="cls_label"><span class="cls_label">Other</span></div>

<!-- Box 7 -->
<div style="position:absolute;left:501.95px;top:204px" class="cls_label"><span class="cls_label">7. INSURED\'S ADDRESS (No., Street)</span></div>
<div style="position:absolute;left:501.95px;top:241px" class="cls_label"><span class="cls_label">CITY</span></div>
<div style="position:absolute;left:734px;top:241px" class="cls_label"><span class="cls_label">STATE</span></div>
<div style="position:absolute;left:501.95px;top:275px" class="cls_label"><span class="cls_label">ZIP CODE</span></div>
<div style="position:absolute;left:628.45px;top:275px" class="cls_label"><span class="cls_label">TELEPHONE (Include Area Code)</span></div>

<!-- Box 8 -->
<div style="position:absolute;left:305.55px;top:241px" class="cls_label"><span class="cls_label">8. RESERVED FOR NUCC USE</span></div>

<!-- Box 9 -->
<div style="position:absolute;left:24.65px;top:311.66px" class="cls_label"><span class="cls_label">9. OTHER INSURED\'S NAME (Last Name, First Name, Middle Initial)</span></div>
<div style="position:absolute;left:24.65px;top:345.66px" class="cls_label"><span class="cls_label">a. OTHER INSURED\'S POLICY OR GROUP NUMBER</span></div>
<div style="position:absolute;left:24.65px;top:379.55px" class="cls_label"><span class="cls_label">b. RESERVED FOR NUCC USE</span></div>
<div style="position:absolute;left:24.65px;top:415.55px" class="cls_label"><span class="cls_label">c. RESERVED FOR NUCC USE</span></div>
<div style="position:absolute;left:24.65px;top:451.85px" class="cls_label"><span class="cls_label">d. INSURANCE PLAN NAME OR PROGRAM NAME</span></div>

<!-- Box 10 -->
<div style="position:absolute;left:305px;top:311.66px" class="cls_label"><span class="cls_label">10. IS PATIENT\'S CONDITION RELATED TO:</span></div>
<div style="position:absolute;left:305px;top:345.36px" class="cls_label"><span class="cls_label">a. EMPLOYMENT? (Current or Previous)</span></div>
<div style="position:absolute;left:370.55px;top:364.66px" class="cls_label"><span class="cls_label">YES</span></div>
<div style="position:absolute;left:430px;top:364.66px" class="cls_label"><span class="cls_label">NO</span></div>
<div style="position:absolute;left:305px;top:379.55px" class="cls_label"><span class="cls_label">b. AUTO ACCIDENT?</span></div>
<div style="position:absolute;left:438.88px;top:384.88px" class="cls_label"><span class="cls_label">PLACE (State)</span></div>
<div style="position:absolute;left:370.55px;top:398.76px" class="cls_label"><span class="cls_label">YES</span></div>
<div style="position:absolute;left:430px;top:398.76px" class="cls_label"><span class="cls_label">NO</span></div>
<div style="position:absolute;left:305px;top:415.85px" class="cls_label"><span class="cls_label">c. OTHER ACCIDENT?</span></div>
<div style="position:absolute;left:370.55px;top:433.95px" class="cls_label"><span class="cls_label">YES</span></div>
<div style="position:absolute;left:430px;top:433.95px" class="cls_label"><span class="cls_label">NO</span></div>
<div style="position:absolute;left:305px;top:452px" class="cls_label"><span class="cls_label">10d. CLAIM CODES (Designated by NUCC)</span></div>
<!-- Box 11 -->
<div style="position:absolute;left:501.95px;top:311.66px" class="cls_label"><span class="cls_label">11. INSURED\'S POLICY GROUP OR FECA NUMBER</span></div>
<div style="position:absolute;left:501.95px;top:346.06px" class="cls_label"><span class="cls_label">a. INSURED\'S DATE OF BIRTH</span></div>
<div style="position:absolute;left:535.95px;top:354.06px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:565.95px;top:354.06px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:603.95px;top:354.06px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:705.95px;top:346.06px" class="cls_label"><span class="cls_label">SEX</span></div>
<div style="position:absolute;left:664.95px;top:362.76px" class="cls_label"><span class="cls_label">M</span></div>
<div style="position:absolute;left:735.95px;top:362.76px" class="cls_label"><span class="cls_label">F</span></div>
<div style="position:absolute;left:501.95px;top:382.06px" class="cls_label"><span class="cls_label">b. OTHER CLAIM ID (Designated by NUCC)</span></div>
<div style="position:absolute;left:501.95px;top:416.66px" class="cls_label"><span class="cls_label">c. INSURANCE PLAN NAME OR PROGRAM NAME</span></div>
<div style="position:absolute;left:501.95px;top:451.88px" class="cls_label"><span class="cls_label">d. IS THERE ANOTHER HEALTH BENEFIT PLAN?</span></div>
<div style="position:absolute;left:536.45px;top:470.06px" class="cls_label"><span class="cls_label">YES</span></div>
<div style="position:absolute;left:585.95px;top:470.06px" class="cls_label"><span class="cls_label">NO</span></div>
<div style="position:absolute;left:621.55px;top:470.06px" class="cls_label"><span class="italic"><b>If yes,</b></span> complete items 9, 91, and 9d</div>

<!-- Box 12 -->
<div style="position:absolute;left:120.83px;top:486px" class="cls_label"><span class="cls_label"><b>READ BACK OF FORM BEFORE COMPLETING & SIGNING THIS FORM.</b></span></div>
<div style="position:absolute;left:23.65px;top:495.74px" class="cls_label"><span class="cls_label">12. PATIENT’S OR AUTHORIZED PERSON’S SIGNATURE. I authorize the release of any medical or other information necessary</span></div>
<div style="position:absolute;left:37.45px;top:506.74px" class="cls_label"><span class="cls_label">to process this claim. I also request payment of government benefits either to myself or to the party who accepts assignment</span></div>
<div style="position:absolute;left:37.45px;top:516.89px" class="cls_label"><span class="cls_label">below.</span></div>
<div style="position:absolute;left:37.45px;top:540.55px" class="cls_label"><span class="cls_label">SIGNED</span></div>
<div style="position:absolute;left:340px;top:540.55px" class="cls_label"><span class="cls_label">DATE</span></div>

<!-- Box 13 -->
<div style="position:absolute;left:500.95px;top:486.67px" class="cls_label"><span class="cls_label">13. INSURED’S OR AUTHORIZED PERSON’S SIGNATURE I authorize</span></div>
<div style="position:absolute;left:514.87px;top:497.33px" class="cls_label"><span class="cls_label">payment of medical benefits to the undersigned physician or supplier for</span></div>
<div style="position:absolute;left:514.87px;top:507.71px" class="cls_label"><span class="cls_label">services described below.</span></div>
<div style="position:absolute;left:520.87px;top:541.55px" class="cls_label"><span class="cls_label">SIGNED</span></div>

<!-- Box 14 -->
<div style="position:absolute;left:23.65px;top:557.55px" class="cls_label"><span class="cls_label">14. DATE OF CURRENT ILLNESS, INJURY, or PREGNANCY (LMP)</span></div>
<div style="position:absolute;left:36.05px;top:565.55px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:68.05px;top:565.55px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:104.75px;top:565.55px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:137.75px;top:576.05px" class="cls_label"><span class="cls_label">QUAL</span></div>

<!-- Box 15 -->
<div style="position:absolute;left:284.65px;top:557.55px" class="cls_label"><span class="cls_label">15. OTHER DATE</span></div>
<div style="position:absolute;left:284.65px;top:572px" class="cls_label"><span class="cls_label">QUAL</span></div>
<div style="position:absolute;left:377.65px;top:565.55px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:409.65px;top:565.55px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:446.65px;top:565.55px" class="cls_label"><span class="cls_label">YY</span></div>

<!-- Box 16 -->
<div style="position:absolute;left:500.95px;top:557px" class="cls_label"><span class="cls_label">16. DATES PATIENT UNABLE TO WORK IN CURRENT OCCUPATION</span></div>
<div style="position:absolute;left:543.95px;top:565px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:574.95px;top:565px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:613.95px;top:565px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:680.95px;top:565px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:712.95px;top:565px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:750.95px;top:565px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:514.35px;top:575px" class="cls_label"><span class="cls_label">FROM</span></div>
<div style="position:absolute;left:661.35px;top:575px" class="cls_label"><span class="cls_label">TO</span></div>

<!-- Box 17 -->
<div style="position:absolute;left:23.65px;top:590.55px" class="cls_label"><span class="cls_label">17. NAME OF REFERRING PROVIDER OR OTHER SOURCE</span></div>
<div style="position:absolute;left:286.15px;top:592.55px" class="cls_label"><span class="cls_label">17a.</span></div>
<div style="position:absolute;left:286.15px;top:610px" class="cls_label"><span class="cls_label">17b.</span></div>
<div style="position:absolute;left:308.15px;top:610px" class="cls_label"><span class="cls_label">NPI</span></div>

<!-- Box 18 -->
<div style="position:absolute;left:500.95px;top:591px" class="cls_label"><span class="cls_label">18. HOSPITALIZATION DATES RELATED TO CURRENT SERVICES</span></div>
<div style="position:absolute;left:543.95px;top:599px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:574.95px;top:599px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:613.95px;top:599px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:680.95px;top:599px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:712.95px;top:599px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:750.95px;top:599px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:514.35px;top:609.77px" class="cls_label"><span class="cls_label">FROM</span></div>
<div style="position:absolute;left:661.35px;top:609.77px" class="cls_label"><span class="cls_label">TO</span></div>

<!-- Box 19 -->
<div style="position:absolute;left:23.65px;top:625.55px" class="cls_label"><span class="cls_label">19. ADDITIONAL CLAIM INFORMATION (Designated by NUCC)</span></div>

<!-- Box 20 -->
<div style="position:absolute;left:500.95px;top:625.55px" class="cls_label"><span class="cls_label">20. OUTSIDE LAB?</span></div>
<div style="position:absolute;left:659.95px;top:625.55px" class="cls_label"><span class="cls_label">$ CHARGES</span></div>
<div style="position:absolute;left:537.95px;top:645.25px" class="cls_label"><span class="cls_label">YES</span></div>
<div style="position:absolute;left:587.95px;top:645.25px" class="cls_label"><span class="cls_label">NO</span></div>

<!-- Box 21 -->
<div style="position:absolute;left:23.65px;top:661.55px" class="cls_label"><span class="cls_label">21. DIAGNOSIS OR NATURE OF ILLNESS OR INJURY  Relate A-L to service line below (24E)</span></div>
<div style="position:absolute;left:387.05px;top:666.55px" class="cls_label"><span class="cls_label">ICD Ind.</span></div>
<div style="position:absolute;left:27.65px;top:685.55px" class="cls_label"><span class="cls_label">A.</span></div>
<div style="position:absolute;left:27.65px;top:702.55px" class="cls_label"><span class="cls_label">E.</span></div>
<div style="position:absolute;left:27.65px;top:719.55px" class="cls_label"><span class="cls_label">I.</span></div>
<div style="position:absolute;left:153.65px;top:685.55px" class="cls_label"><span class="cls_label">B.</span></div>
<div style="position:absolute;left:153.65px;top:702.55px" class="cls_label"><span class="cls_label">F.</span></div>
<div style="position:absolute;left:153.65px;top:719.55px" class="cls_label"><span class="cls_label">J.</span></div>
<div style="position:absolute;left:281.65px;top:685.55px" class="cls_label"><span class="cls_label">C.</span></div>
<div style="position:absolute;left:281.65px;top:702.55px" class="cls_label"><span class="cls_label">G.</span></div>
<div style="position:absolute;left:281.65px;top:719.55px" class="cls_label"><span class="cls_label">K.</span></div>
<div style="position:absolute;left:407.65px;top:686px" class="cls_label"><span class="cls_label">D.</span></div>
<div style="position:absolute;left:407.65px;top:704px" class="cls_label"><span class="cls_label">H.</span></div>
<div style="position:absolute;left:407.65px;top:720px" class="cls_label"><span class="cls_label">L.</span></div>

<!-- Box 22 -->
<div style="position:absolute;left:500.95px;top:661.55px" class="cls_label"><span class="cls_label">22. RESUBMISSION</span></div>
<div style="position:absolute;left:514.55px;top:669.55px" class="cls_label"><span class="cls_label">CODE</span></div>
<div style="position:absolute;left:625.55px;top:669.55px" class="cls_label"><span class="cls_label">ORIGINAL REF. NO.</span></div>

<!-- Box 23 -->
<div style="position:absolute;left:500.95px;top:696.55px" class="cls_label"><span class="cls_label">23. PRIOR AUTHORIZATION NUMBER</span></div>

<!-- Box 24 -->
<div style="position:absolute;left:23.65px;top:731.95px" class="cls_label"><span class="cls_label">24. A.</span></div>
<div style="position:absolute;left:54.95px;top:742.95px" class="cls_label"><span class="cls_label">From</span></div>
<div style="position:absolute;left:143.95px;top:742.95px" class="cls_label"><span class="cls_label">To</span></div>
<div style="position:absolute;left:25.65px;top:752.95px" class="cls_label"><span class="cls_label">MM</span></div>
<div style="position:absolute;left:55.65px;top:752.95px" class="cls_label"><span class="cls_label">DD</span></div>	
<div style="position:absolute;left:85.65px;top:752.95px" class="cls_label"><span class="cls_label">YY</span></div>	
<div style="position:absolute;left:113.65px;top:752.95px" class="cls_label"><span class="cls_label">MM</span></div>	
<div style="position:absolute;left:141.15px;top:752.95px" class="cls_label"><span class="cls_label">DD</span></div>
<div style="position:absolute;left:172.15px;top:752.95px" class="cls_label"><span class="cls_label">YY</span></div>
<div style="position:absolute;left:65.95px;top:731.95px" class="cls_label"><span class="cls_label">DATE(S) OF SERVICE</span></div>
<div style="position:absolute;left:204.95px;top:731.95px" class="cls_label"><span class="cls_label">B.</span></div>
<div style="position:absolute;left:193.77px;top:742.95px" class="cls_label"><span class="cls_label" style="letter-spacing:-0.8px; font-size:7px">PLACE OF</span></div>
<div style="position:absolute;left:194.77px;top:753.95px" class="cls_label"><span class="cls_label" style="letter-spacing:-0.8px; font-size:7px">SERVICE</span></div>
<div style="position:absolute;left:234.95px;top:731.95px" class="cls_label"><span class="cls_label">C.</span></div>
<div style="position:absolute;left:230px;top:752.95px" class="cls_label"><span class="cls_label">EMG</span></div>
<div style="position:absolute;left:258.95px;top:731.95px" class="cls_label"><span class="cls_label">D. PROCEDURES, SERVICES, OR SUPPLIES</span></div>
<div style="position:absolute;left:276.95px;top:742.55px" class="cls_label"><span class="cls_label">(Explain Unusual Circumstances)</span></div>
<div style="position:absolute;left:262.95px;top:753.05px" class="cls_label"><span class="cls_label">CPT/HCPCS</span></div>
<div style="position:absolute;left:365.95px;top:753.05px" class="cls_label"><span class="cls_label">MODIFIER</span></div>
<div style="position:absolute;left:468.95px;top:731.95px" class="cls_label"><span class="cls_label">E.</span></div>
<div style="position:absolute;left:449.95px;top:742.95px" class="cls_label"><span class="cls_label">DIAGNOSIS</span></div>
<div style="position:absolute;left:453.95px;top:753.05px" class="cls_label"><span class="cls_label">POINTER</span></div>
<div style="position:absolute;left:537.95px;top:731.95px" class="cls_label"><span class="cls_label">F.</span></div>
<div style="position:absolute;left:516.95px;top:753.05px" class="cls_label"><span class="cls_label">$ CHARGES</span></div>
<div style="position:absolute;left:600px;top:731.95px" class="cls_label"><span class="cls_label">G.</span></div>
<div style="position:absolute;left:595px;top:739.95px" class="cls_label"><span class="cls_label">DAYS</span></div>
<div style="position:absolute;left:598px;top:746.95px" class="cls_label"><span class="cls_label">OR</span></div>
<div style="position:absolute;left:594px;top:753.95px" class="cls_label"><span class="cls_label">UNITS</span></div>
<div style="position:absolute;left:631px;top:731.95px" class="cls_label"><span class="cls_label">H.</span></div>
<div style="position:absolute;left:625px;top:739.95px" class="cls_label"><span class="cls_label" style="font-size:6px;letter-spacing:-0.5px;">EPSDT</span></div>
<div style="position:absolute;left:625.33px;top:747.95px" class="cls_label"><span class="cls_label" style="font-size:6.5px;letter-spacing:-0.5px;">Family</span></div>
<div style="position:absolute;left:628.33px;top:754.95px" class="cls_label"><span class="cls_label" style="font-size:6.5px;letter-spacing:-0.5px;">Plan</span></div>
<div style="position:absolute;left:657px;top:731.95px" class="cls_label"><span class="cls_label">I.</span></div>
<div style="position:absolute;left:655px;top:742.95px" class="cls_label"><span class="cls_label">ID.</span></div>
<div style="position:absolute;left:647px;top:753.95px" class="cls_label"><span class="cls_label">QUAL.</span></div>
<div style="position:absolute;left:731.66px;top:731.95px" class="cls_label"><span class="cls_label">J.</span></div>
<div style="position:absolute;left:711.66px;top:742.95px" class="cls_label"><span class="cls_label">RENDERING</span></div>
<div style="position:absolute;left:704.66px;top:753.95px" class="cls_label"><span class="cls_label">PROVIDER ID. #</span></div>


<!-- Box 25 -->
<div style="position:absolute;left:23.65px;top:977.05px" class="cls_label"><span class="cls_label">25. FEDERAL TAX I.D. NUMBER</span></div>
<div style="position:absolute;left:175.65px;top:977.05px" class="cls_label"><span class="cls_label">SSN</span></div>
<div style="position:absolute;left:198.15px;top:977.05px" class="cls_label"><span class="cls_label">EIN</span></div>

<!-- Box 26 -->
<div style="position:absolute;left:237.65px;top:977.05px" class="cls_label"><span class="cls_label">26. PATIENT’S ACCOUNT NO.</span></div>


<!-- Box 27 -->
<div style="position:absolute;left:379.65px;top:977.05px" class="cls_label"><span class="cls_label">27. ACCEPT ASSIGNMENT?</span></div>
<div style="position:absolute;left:392.33px;top:983.95px" class="cls_label"><span class="cls_label" style="font-size:7.5px;letter-spacing:-0.5px;">(For govt. claims, see back)</span></div>
<div style="position:absolute;left:399.65px;top:997.05px" class="cls_label"><span class="cls_label">YES</span></div>
<div style="position:absolute;left:449.65px;top:997.05px" class="cls_label"><span class="cls_label">NO</span></div>

<!-- Box 28 -->
<div style="position:absolute;left:500px;top:977.05px" class="cls_label"><span class="cls_label">28. TOTAL CHARGE</span></div>
<div style="position:absolute;left:506px;top:997.05px" class="cls_label"><span class="cls_label">$</span></div>

<!-- Box 29 -->
<div style="position:absolute;left:610px;top:977.05px" class="cls_label"><span class="cls_label">29. AMOUNT PAID</span></div>
<div style="position:absolute;left:615px;top:996.05px" class="cls_label"><span class="cls_label">$</span></div>

<!-- Box 30 -->
<div style="position:absolute;left:704px;top:977.05px" class="cls_label"><span class="cls_label">30. Rsvd for NUCC Use</span></div>


<!-- Box 31 -->
<div style="position:absolute;left:23.65px;top:1013.05px" class="cls_label"><span class="cls_label">31. SIGNATURE OF PHYSICIAN OR SUPPLIER</span></div>
<div style="position:absolute;left:36.65px;top:1023.55px" class="cls_label"><span class="cls_label">INCLUDING DEGREES OR CREDENTIALS</span></div>
<div style="position:absolute;left:36.65px;top:1033.55px" class="cls_label"><span class="cls_label">(I certify that the statements on the reverse</span></div>
<div style="position:absolute;left:36.65px;top:1043.55px" class="cls_label"><span class="cls_label">apply to this bill and are made a part thereof.)</span></div>
<div style="position:absolute;left:23.65px;top:1089.85px" class="cls_label"><span class="cls_label">SIGNED</span></div>
<div style="position:absolute;left:170.25px;top:1089.85px" class="cls_label"><span class="cls_label">DATE</span></div>

<!-- Box 32 -->
<div style="position:absolute;left:237.65px;top:1013.05px" class="cls_label"><span class="cls_label">32. SERVICE FACILITY LOCATION INFORMATION</span></div>


<!-- Box 33 -->
<div style="position:absolute;left:500.65px;top:1013.05px" class="cls_label"><span class="cls_label">33. BILLING PROVIDER INFO & PH #</span></div>



<div style="position:absolute;left:503px;top:152px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_id_number'] . '</span></div>

<div style="position:absolute;left:26.60px;top:152.72px" class="cls_012"><span class="cls_012 med-black">' . ((isset($claim_data['ins_type']) && $claim_data['ins_type'] == 'Medicare') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:92.90px;top:152.72px" class="cls_012"><span class="cls_012 med-black">' . ((isset($claim_data['ins_type']) && $claim_data['ins_type'] == 'Medicaid') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:161.40px;top:152.72px" class="cls_012"><span class="cls_012 med-black">' . ((isset($claim_data['ins_type']) && $claim_data['ins_type'] == 'Tricare') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:249.70px;top:152.72px" class="cls_012"><span class="cls_012 med-black">' . ((isset($claim_data['ins_type']) && $claim_data['ins_type'] == 'Champva') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:318.35px;top:152.72px" class="cls_012"><span class="cls_012 med-black">' . ((isset($claim_data['ins_type']) && $claim_data['ins_type'] == 'Group Health Plan') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:396.20px;top:152.72px" class="cls_012"><span class="cls_012 med-black">' . ((isset($claim_data['ins_type']) && $claim_data['ins_type'] == 'Feca') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:455.80px;top:152.72px" class="cls_012"><span class="cls_012 med-black">' . ((isset($claim_data['ins_type']) && $claim_data['ins_type'] != 'Medicare' && $claim_data['ins_type'] != 'Medicaid' && $claim_data['ins_type'] != 'Tricare' && $claim_data['ins_type'] != 'Champva' && $claim_data['ins_type'] != 'Group Health Plan' && $claim_data['ins_type'] != 'Feca') ? 'X' : '') . '</span></span></div>

<div style="position:absolute;left:24.09px;top:185.55px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_name'] . '</span></div>

<div style="position:absolute;left:321.78px;top:188.55px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_dob_m'] . '</span></div>

<div style="position:absolute;left:349.78px;top:188.55px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_dob_d'] . '</span></div>

<div style="position:absolute;left:388.37px;top:188.55px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_dob_y'] . '</span></div>

<div style="position:absolute;left:425.34px;top:187.95px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['patient_gender'] == 'Male') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:474.61px;top:187.95px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['patient_gender'] == 'Female') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:510.95px;top:185.55px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_name'] . '</span></div>

<div style="position:absolute;left:24.09px;top:218.08px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_addr'] . '</span></div>

<div style="position:absolute;left:24.09px;top:255.00px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_city'] . '</span></div>

<div style="position:absolute;left:277.09px;top:255.00px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_state'] . '</span></div>

<div style="position:absolute;left:24.09px;top:294.27px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_zip5'] . '</span></div>

<div style="position:absolute;left:167.27px;top:294.27px" class="cls_002"><span class="cls_002 med-black">' . substr($claim_data['patient_phone'], 1, 3) . '</span></div>

<div style="position:absolute;left:199.40px;top:294.27px" class="cls_002"><span class="cls_002 med-black">' . substr($claim_data['patient_phone'], 5) . '</span></div>

<div style="position:absolute;left:337.52px;top:223.15px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['insured_relation'] == 'Self') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:386.82px;top:223.15px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['insured_relation'] == 'Spouse') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:425.32px;top:223.15px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['insured_relation'] == 'Child') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:474.92px;top:223.15px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['insured_relation'] == 'Other') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:510.40px;top:223.08px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_addr'] . '</span></div>

<div style="position:absolute;left:510.40px;top:255.29px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_city'] . '</span></div>

<div style="position:absolute;left:737.09px;top:255.29px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_state'] . '</span></div>

<div style="position:absolute;left:510.40px;top:292.68px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_zip5'] . '</span></div>

<div style="position:absolute;left:656.36px;top:292.68px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_data['insured_phone']) ? substr($claim_data['insured_phone'], 1, 3) : '') . '</span></div>

<div style="position:absolute;left:689.99px;top:292.68px" class="cls_002"><span class="cls_002  med-black">' . (!empty($claim_data['insured_phone']) ? substr($claim_data['insured_phone'], 5) : '') . '</span></div>

<div style="position:absolute;left:315.45px;top:256.69px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['reserved_nucc_box8'] . '</span></div>

<div style="position:absolute;left:24.09px;top:329.93px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_insured_name'] . '</span></div>

<div style="position:absolute;left:24.09px;top:359.75px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_ins_policy'] . '</span></div>

<div style="position:absolute;left:24.09px;top:394.53px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['reserved_nucc_box9b'] . '</span></div>

<div style="position:absolute;left:24.09px;top:430.93px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['reserved_nucc_box9c'] . '</span></div>

<div style="position:absolute;left:24.09px;top:465.28px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_insur_name'] . '</span></div>

<div style="position:absolute;left:357.45px;top:364.69px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_employment'] == 'Yes') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:416.45px;top:364.69px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_employment'] == 'No') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:357.45px;top:399.78px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_auto_accident'] == 'Yes') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:416.45px;top:399.78px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_auto_accident'] == 'No') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:449.63px;top:399.78px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['accident_state'] . '</span></div>

<div style="position:absolute;left:357.45px;top:434.42px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_other_accident'] == 'Yes') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:416.85px;top:434.42px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_other_accident'] == 'No') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:306.45px;top:465.28px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['claimcode'] . '</span></div>

<div style="position:absolute;left:503.36px;top:325.98px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['ins_policy_no'] . '</span></div>
    
<div style="position:absolute;left:537.36px;top:366.98px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_dob_m'] . '</span></div>

<div style="position:absolute;left:566.83px;top:366.98px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_dob_d'] . '</span></div>

<div style="position:absolute;left:603.54px;top:366.98px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_dob_y'] . '</span></div>

<div style="position:absolute;left:680.15px;top:363.28px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['insured_gender'] == 'Male') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:749.55px;top:364.28px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['insured_gender'] == 'Female') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:400.17px;top:340.94px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_claimid_qual'] . '</span></div>

<div style="position:absolute;left:534.17px;top:396.94px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_claimid'] . '</span></div>


<div style="position:absolute;left:502.17px;top:430.93px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insur_name'] . '</span></div>

<div style="position:absolute;left:523.01px;top:469px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_another_ins'] == 'Yes') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:573px;top:469px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['is_another_ins'] == 'No') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:84.72px;top:536px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['patient_signed'] . '</span></div>

<div style="position:absolute;left:378.57px;top:536px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['signed_date'] . '</span></div>

<div style="position:absolute;left:569.71px;top:536px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['insured_signed'] . '</span></div>

<div style="position:absolute;left:37px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['doi_m'] . '</span></div>

<div style="position:absolute;left:69px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['doi_d'] . '</span></div>

<div style="position:absolute;left:106px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['doi_y'] . '</span></div>

<div style="position:absolute;left:170.33px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['doi_qual'] . '</span></div>

<div style="position:absolute;left:315.07px;top:575px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_qual'] . '</span></div>

<div style="position:absolute;left:377px;top:575px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_m'] . '</span></div>

<div style="position:absolute;left:410.23px;top:575px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_d'] . '</span></div>

<div style="position:absolute;left:445.83px;top:575px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['other_y'] . '</span></div>

<div style="position:absolute;left:544.96px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['box18_from_m'] . '</span></div>

<div style="position:absolute;left:574.96px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['box18_from_d'] . '</span></div>

<div style="position:absolute;left:613px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['box18_from_y'] . '</span></div>

<div style="position:absolute;left:681.95px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['box18_to_m'] . '</span></div>

<div style="position:absolute;left:712px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['box18_to_d'] . '</span></div>

<div style="position:absolute;left:751px;top:576px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['box18_to_y'] . '</span></div>

<div style="position:absolute;left:30px;top:609px" class="cls_013"><span class="cls_013 med-black">' . $claim_data['refering_provider_qual'] . '</span></div>
<div style="position:absolute;left:53.09px;top:609px" class="cls_013"><span class="cls_013 med-black">' . $claim_data['refering_provider'] . '</span></div>

<div style="position:absolute;left:330px;top:610px" class="cls_007"><span class="cls_007 med-black">' . $claim_data['refering_provider_npi'] . '</span></div>

<div style="position:absolute;left:544.96px;top:610px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['admit_date_m'] . '</span></div>

<div style="position:absolute;left:574.96px;top:610px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['admit_date_d'] . '</span></div>

<div style="position:absolute;left:613px;top:610px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['admit_date_y'] . '</span></div>

<div style="position:absolute;left:681.95px;top:610px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['discharge_date_m'] . '</span></div>

<div style="position:absolute;left:712px;top:610px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['discharge_date_d'] . '</span></div>

<div style="position:absolute;left:751px;top:610px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['discharge_date_y'] . '</span></div>

<div style="position:absolute;left:26px;top:640px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['addi_claim_info'] . '</span></div>

<div style="position:absolute;left:523.56px;top:644px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['outside_lab'] == 'Yes') ? 'X' : '') . '</span></div>
<div style="position:absolute;left:573.56px;top:644px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['outside_lab'] == 'No') ? 'X' : '') . '</span></div>
<div style="position:absolute;left:600.36px;top:644px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['outside_lab'] == 'Yes') && !empty($claim_data['lab_charge'][0]) ? $claim_data['lab_charge'][0] : '') . '</span></div>
<div style="position:absolute;left:719.36px;top:644px" class="cls_002"><span class="cls_002 med-black">' . (($claim_data['outside_lab'] == 'Yes') && !empty($claim_data['lab_charge'][1]) ? $claim_data['lab_charge'][1] : '') . '</span></div>

<div style="position:absolute;left:44px;top:682px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_A'] . '</span></div>

<div style="position:absolute;left:170px;top:682px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_B'] . '</span></div>

<div style="position:absolute;left:299px;top:682px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_C'] . '</span></div>

<div style="position:absolute;left:426px;top:684px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_D'] . '</span></div>
\
<div style="position:absolute;left:44px;top:699px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_E'] . '</span></div>

<div style="position:absolute;left:170px;top:699px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_F'] . '</span></div>
\
<div style="position:absolute;left:299px;top:699px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_G'] . '</span></div>

<div style="position:absolute;left:426px;top:701px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_H'] . '</span></div>

<div style="position:absolute;left:44px;top:717px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_I'] . '</span></div>

<div style="position:absolute;left:170px;top:717px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_J'] . '</span></div>

<div style="position:absolute;left:299px;top:717px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_K'] . '</span></div>

<div style="position:absolute;left:426px;top:717px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['icd_L'] . '</span></div>

<div style="position:absolute;left:514.00px;top:682px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['resub_code'] . '</span></div>

<div style="position:absolute;left:624.09px;top:682px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['original_ref'] . '</span></div>

<div style="position:absolute;left:514.00px;top:712.64px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['prior_auth_no'] . '</span></div>

' . $documentmode . '

<div style="position:absolute;left:24.09px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['ssn_or_taxid_no'] . '</span></div>

<div style="position:absolute;left:181.36px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['etin_ssn'] . '</span></div>
<div style="position:absolute;left:202px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['etin_tax'] . '</span></div>

<div style="position:absolute;left:240px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['claim_no'] . '</span></div>

<div style="position:absolute;left:387px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['accept_assignment']) && ($claim_data['accept_assignment'] == 'Yes') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:436px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['accept_assignment']) && ($claim_data['accept_assignment'] == 'No') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:524.17px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['total'][0]) ? $claim_data['total'][0] : '00') . '</span></div>
<div style="position:absolute;left:584.17px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['total'][1]) ? $claim_data['total'][1] : '00') . '</span></div>
<div style="position:absolute;left:620.17px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_detail['amount_paid'][0]) ? $claim_detail['amount_paid'][0] : '') . '</span></div>
<div style="position:absolute;left:680.17px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (!empty($claim_detail['amount_paid'][1]) ? $claim_detail['amount_paid'][1] : '') . '</span></div>
<div style="position:absolute;left:727.41px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['reserved_nucc_box30'][0]) ? $claim_data['reserved_nucc_box30'][0] : '') . '</span></div>
<div style="position:absolute;left:772.41px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['reserved_nucc_box30'][1]) ? $claim_data['reserved_nucc_box30'][1] : '') . '</span></div>
<div style="position:absolute;left:150.41px;top:1029px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['rendering_provider_name'] . '</span></div>
<div style="position:absolute;left:180.41px;top:1029px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['rendering_provider_date'] . '</span></div>
<div style="position:absolute;left:238px;top:1029px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_name'] . '</span></div>
<div style="position:absolute;left:238px;top:1044px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_addr'] . '</span></div>
<div style="position:absolute;left:238px;top:1059px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_city'] . '</span></div>

<div style="position:absolute;left:247.04px;top:1084px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_npi'] . '</span></div>

<div style="position:absolute;left:502px;top:1029px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['bill_provider_name'] . '</span></div>
<div style="position:absolute;left:502px;top:1044px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['bill_provider_addr'] . '</span></div>
<div style="position:absolute;left:502px;top:1059px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['bill_provider_city'] . '</span></div>

<div style="position:absolute;left:512px;top:1084px class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['bill_provider_npi']) ? $claim_data['bill_provider_npi'] : '') . '</span></div>


</div><div style="page-break-after: always"></div>';

        return $print_data;
    }

    // Get pateint insurance details related to insurance choosen at payment section
    function getinsurancedetail($insurance, $patient_id) {

        $getpatientins = PatientInsurance::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name');
                    }))->where('patient_id', $patient_id)->whereIn('insurance_id', $insurance)->get();
        $ins_data = [];
        $other_ins_detail = PatientInsurance::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name');
                    }))->where('patient_id', $patient_id)->whereNotIn('insurance_id', $insurance)->first();
        $other_ins_data = [];

        if (!empty($other_ins_detail)) {
            $insured_name = Helpers::getNameformat($other_ins_detail->last_name, $other_ins_detail->first_name, $other_ins_detail->middle_name);
            $other_ins_data = [
                'is_another_ins' => 'Yes',
                'other_insured_name' => @$insured_name,
                'other_ins_policy' => @$other_ins_detail->policy_id,
                'other_insur_name' => @$other_ins_detail->insurance_details->insurance_name,
            ];
        }

        if (!empty($getpatientins)) {
            foreach ($getpatientins as $insurance_detail) {

                $insured_name = Helpers::getNameformat($insurance_detail->last_name, $insurance_detail->first_name, $insurance_detail->middle_name);
                $insured_dob_m = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('m', strtotime($insurance_detail->insured_dob)) : '';
                $insured_dob_d = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('d', strtotime($insurance_detail->insured_dob)) : '';
                $insured_dob_y = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('Y', strtotime($insurance_detail->insured_dob)) : '';
                $zip_code = isset($insurance_detail->insured_zip5) ? $insurance_detail->insured_zip5 : '';
                $zip_code4 = isset($insurance_detail->insured_zip4) ? $insurance_detail->insured_zip4 : '';
                $ins_data = [
                    'policy_num' => $insurance_detail->policy_id,
                    'insured_dob' => $insurance_detail->insured_dob,
                    'insured_addr' => $insurance_detail->insured_address1,
                    'insured_address2' => $insurance_detail->insured_address2,
                    'insured_city' => $insurance_detail->insured_city,
                    'insured_state' => $insurance_detail->insured_state,
                    'insured_zip5' => $zip_code . $zip_code4,
                    'group_name' => $insurance_detail->group_name,
                    'group_id' => $insurance_detail->group_id,
                    'insur_name' => $insurance_detail->insurance_details->insurance_name,
                    'insured_phone' => $insurance_detail->adjustor_phm,
                    'insured_name' => $insured_name,
                    'ins_policy_no' => $insurance_detail->policy_id,
                    'insured_dob_m' => $insured_dob_m,
                    'insured_dob_d' => $insured_dob_d,
                    'insured_dob_y' => $insured_dob_y,
                    'insured_gender' => $insurance_detail->insured_gender,
                    'insured_relation' => $insurance_detail->relationship,
                    'insured_id_number' => $insurance_detail->policy_num,
                ];
            }
        } else {
            return $this->getpateintarcheiveinsurancedetail($insurance_id);
        }
        $ins_data = array_merge($other_ins_data, $ins_data);
        return $ins_data;
    }

    function getpateintarcheiveinsurancedetail($insurance_id, $category = null) {
        $pateint_detail = PatientInsuranceArchive::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name');
                    }))->where('patient_id', $patient_id)->whereIn('insurance_id', $insurance)->get();
        if (!empty($pateint_detail)) {
            $insured_name = Helpers::getNameformat($pateint_detail->last_name, $pateint_detail->first_name, $pateint_detail->middle_name);
            $insured_dob_m = !is_null($pateint_detail->insured_dob && $pateint_detail->insured_dob != '0000-00-00' && $pateint_detail->insured_dob != '1970-01-01') ? date('m', strtotime($pateint_detail->insured_dob)) : '';
            $insured_dob_d = !is_null($pateint_detail->insured_dob && $pateint_detail->insured_dob != '0000-00-00' && $pateint_detail->insured_dob != '1970-01-01') ? date('d', strtotime($pateint_detail->insured_dob)) : '';
            $insured_dob_y = !is_null($pateint_detail->insured_dob && $pateint_detail->insured_dob != '0000-00-00' && $pateint_detail->insured_dob != '1970-01-01') ? date('Y', strtotime($pateint_detail->insured_dob)) : '';
            $zip_code = isset($insurance_detail->insured_zip5) ? $insurance_detail->insured_zip5 : '';
            $zip_code4 = isset($insurance_detail->insured_zip4) ? $insurance_detail->insured_zip4 : '';
            $ins_data[$pateint_detail->category] = [
                'policy_num' => $pateint_detail->policy_id,
                'insured_dob' => $pateint_detail->insured_dob,
                'insured_addr' => $pateint_detail->insured_address1,
                'insured_address2' => $pateint_detail->insured_address2,
                'insured_city' => $pateint_detail->insured_city,
                'insured_state' => $pateint_detail->insured_state,
                'insured_zip5' => $zip_code . $zip_code4,
                'group_name' => $pateint_detail->group_name,
                'group_id' => $pateint_detail->group_id,
                'insur_name' => $pateint_detail->insurance_details->insurance_name,
                'insured_phone' => $pateint_detail->adjustor_phm,
                'insured_name' => $pateint_detail,
                'ins_policy_no' => $pateint_detail->policy_id,
                'insured_dob_m' => $insured_dob_m,
                'insured_dob_d' => $insured_dob_d,
                'insured_dob_y' => $insured_dob_y,
                'insured_gender' => $insurance_detail->insured_gender,
                'insured_relation' => $pateint_detail->relationship,
                'insured_id_number' => $pateint_detail->policy_num,
            ];
        }
        return $ins_data;
    }

    // Notes from charge entry added to the pateint notes starts here
    public function savepatientnotes($note, $claim_id, $patient_id) {
        $claim_note['content'] = $note;
        $claim_note['notes_type'] = 'patient';
        $claim_note['patient_notes_type'] = 'claim_notes';
        $claim_note['notes_type_id'] = $patient_id;
        $claim_note['title'] = 'charge';
        $claim_note['created_by'] = Auth::user()->id;
        $claim_note['claim_id'] = $claim_id;
        PatientNote::create($claim_note);
    }

    // Notes from charge entry added to the pateint notes starts ends

    public function DefaultClaimDetailEntryProcess($claim_id) {
        $claim_detail = new ClaimDetailApiController();
        $request['claim_id'] = $claim_id;
        $request['type'] = "default";
        $request['is_provider_employed'] = "No";
        $request['is_employment'] = "No";
        $request['is_autoaccident'] = "No";
        $request['is_otheraccident'] = "No";
        $request['accept_assignment'] = "Yes";
        $claim_detail_id = $claim_detail->getStoreApi($request);
        return ($claim_detail_id) ? $claim_detail_id : '';
    }

    public function getmodifier() {
        $cpt_ids = Favouritecpts::pluck('cpt_id')->all();
		$multiCpt = MultiFeeschedule::with('cptInfo')->where('status','Active')->whereIn('cpt_id', $cpt_ids)->select('id','year','insurance_id','billed_amount','allowed_amount','cpt_id')->get()->toArray();
		foreach($multiCpt as $list){
			$data[$list['year']][$list['insurance_id']]['cpt_lists'][] =  $list['cpt_info']['cpt_hcpcs'];
			$data[$list['year']][$list['insurance_id']]['cpt_billed_arr'][$list['cpt_info']['cpt_hcpcs']] = $list['billed_amount'];
		}
        $modifier = Modifier::where('status', 'Active')->pluck('code', 'id')->all();
        $cpt = Cpt::where('status', 'Active')->whereIn('id', $cpt_ids)->pluck('cpt_hcpcs', 'id')->all();
        $cpt_code = array_values($cpt);
        $cpt_billed_amt = Cpt::whereIn('cpt_hcpcs', $cpt_code)->pluck('billed_amount', 'cpt_hcpcs')->all();
        $cpt_with_modifier = DB::table('cpts as cpt')
                        ->join('modifiers as mod', 'mod.id', '=', 'cpt.modifier_id')
                        ->selectRaw('mod.code as code, cpt.cpt_hcpcs as cpt_code')
                        ->where('cpt.status', '=', 'Active')
                        ->where('cpt.deleted_at', NULL)->pluck('code', 'cpt_code')->all();
		
        return response::json(compact('modifier', 'cpt', 'cpt_billed_amt', 'cpt_with_modifier','data'));
    }

    public function getaddmoredos($i) {
        return view('patients/billing/appendrow', compact('i'));
    }

    public function findPaymentDone($claim_id) {
        $claim_status = ClaimInfoV1::where('id', $claim_id)->pluck('status')->first();
        $insurance_payment_count = PMTInfoV1::checkpaymentDone($claim_id, 'payment');
        if ($claim_status == "Submitted" || $insurance_payment_count > 0) {
            return false;
        }
        return true;
    }

    public function searchCptApi($search_key) {
        $cpt = new SuperbillsApiController();
        $cpt_values = $cpt->getTemplatesearchApi($search_key, 'charge');
        return $cpt_values;
    }

    public function getCptModifierApi($cpt_code,$year,$insurance_id) {
		/* Check default insurance or not */
		$insuranceInfo = MultiFeeschedule::where('insurance_id',$insurance_id)->count();
		if($insuranceInfo == 0)
			$insurance_id = 0;
		/* Check default insurance or not */
		
        $cpt_modifier = Cpt::with(array('multifeeSchedule'=>function($query)use($year,$insurance_id){
			$query->where('year',$year)->where('insurance_id',$insurance_id)->where('status','Active');
		}))->where('cpt_hcpcs', $cpt_code)->select('modifier_id', 'referring_provider', 'anesthesia_unit', 'short_description','id','ndc_number','unit_code','unit_cpt','unit_ndc','unit_value')->first();
		
        // If modifier not availabe get the modifier from admin cpt
        if (empty($cpt_modifier)) {
            $cpt_modifier = AdminCpt::where('cpt_hcpcs', $cpt_code)->select('modifier_id', 'referring_provider', 'anesthesia_unit', 'short_description')->first();
        }
        $refering_provider = $cpt_modifier->referring_provider;
        $anesthesia_unit = $cpt_modifier->anesthesia_unit;
        $short_description = $cpt_modifier->short_description;
        $ndc_number = $cpt_modifier->ndc_number;  
        $unit_code = $cpt_modifier->unit_code;  
        $unit_cpt = $cpt_modifier->unit_cpt;    
        $unit_ndc = $cpt_modifier->unit_ndc;    
        $unit_value = $cpt_modifier->unit_value;
		if(isset($cpt_modifier->multifeeSchedule))
			$modifier_ids = explode(',', $cpt_modifier->multifeeSchedule->modifier_id);
		else
			$modifier_ids = explode(',', $cpt_modifier->modifier_id);
        $modifier_code = Modifier::whereIn('id', $modifier_ids)->where('status', 'Active')->pluck('code')->all();
        return compact('modifier_code', 'refering_provider', 'anesthesia_unit', 'short_description','ndc_number', 'unit_code', 'unit_cpt', 'unit_ndc', 'unit_value');    
    }
	
	public function checkProviderShortNameValidationApi() {
       $request = Request::all();
       $short_name = $request['short_name'];
       $type_id = !(empty($request['providerId'])) ? $request['providerId'] : 2;

       $short_name_count = Provider::where('short_name', $short_name)->where('provider_types_id', $type_id)->count();
       return Response::json(array('status' => 'error', 'message' => "", 'provider_short_name_count' => $short_name_count));
   }

}