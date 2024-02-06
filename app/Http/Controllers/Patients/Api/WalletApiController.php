<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Patients\Patient as Patient;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Modifier as Modifier;
use App\Http\Controllers\BillingReportController as BillingReportController;
use App\Models\AddressFlag as AddressFlag;
use App\Models\Pos as Pos;
use App\Models\Cpt as Cpt;
use App\Models\Patients\PatientAuthorization as PatientAuthorization;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Patients\PatientContact as PatientContact;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Icd as Icd;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Holdoption as Holdoption;
use App\Models\Charges\BatchCharge as BatchCharge;
use Illuminate\Http\Response as Responseobj;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Session;
use App;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Facades\Storage;
use File;

class WalletApiController extends Controller {

    public function getIndexApi($patient_id) {
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claims_list = array();
        if (Patient::where('id', $patient_id)->count()) {
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('patient_id', $patient_id)->orderBy('id', 'DESC')->get();
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list')));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'No patient Available', 'data' => ''));
        }
    }

    public function getListsApi($patient_id) {
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claims_list = array();
        if (Patient::where('id', $patient_id)->count()) {
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'insurance_details', 'billing_provider', 'facility_detail')->where('patient_id', $patient_id)->orderBy('id', 'DESC')->get();
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list')));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'No patient Available', 'data' => ''));
        }
    }

    public function getViewApi($patient_id, $claim_id = null) {
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        $providers = Provider::select(DB::raw("CONCAT(provider_name) AS provider_name"), 'id')->where('status', '=', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
        $rendering_providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"), 'id')->where('status', '=', 'Active')->where('provider_types_id', '=', '1')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
        $referring_providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"), 'id')->where('status', '=', 'Active')->where('provider_types_id', '=', '2')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
        $billing_providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"), 'id')->where('status', '=', 'Active')->where('provider_types_id', '=', '5')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
        $patient_insurances = Insurance::with('patient_insurance', 'insurancetype')->whereHas('patient_insurance', function($q) use($patient_id) {
                    $q->where('patient_id', $patient_id);
                })->select('insurance_name', 'id', 'insurancetype_id', 'city', 'state', 'zipcode5', 'zipcode4')->get();
        $insurances = Insurance::with('patient_insurance', 'insurancetype')->whereHas('patient_insurance', function($q) use($patient_id) {
                    $q->where('patient_id', $patient_id)->where('category', 'Primary');
                })->select('insurance_name', 'id', 'insurancetype_id', 'city', 'state', 'zipcode5', 'zipcode4')->first();
        $insurance_data = [];
        $claims_list = array();
        if (Patient::where('id', $patient_id)->count()) {
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'employer_details', 'dosdetails')->where('id', $claim_id)->first();
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_id', 'facilities', 'providers', 'rendering_providers', 'referring_providers', 'billing_providers', 'insurances', 'patient_detail', 'modifier', 'claims_list', 'hold_options', 'insurance_data')));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'No patient Available', 'data' => ''));
        }
    }

    public function getCreateApi($patient_id, $claim_id = null) {
        $facilities = [];
        if (Patient::where('id', $patient_id)->count()) {
            $patient_detail = Patient::where('id', $patient_id)->first();
            $facilities = Facility::orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
            $providers = Provider::select(DB::raw("CONCAT(provider_name) AS provider_name"), 'id')->where('status', '=', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            $rendering_providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"), 'id')->where('status', '=', 'Active')->where('provider_types_id', '=', '1')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            $referring_providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"), 'id')->where('status', '=', 'Active')->where('provider_types_id', '=', '2')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            $billing_providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"), 'id')->where('status', '=', 'Active')->where('provider_types_id', '=', '5')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            $patient_insurances = Insurance::with('patient_insurance', 'insurancetype')->whereHas('patient_insurance', function($q) use($patient_id) {
                        $q->where('patient_id', $patient_id);
                    })->select('insurance_name', 'id', 'insurancetype_id', 'city', 'state', 'zipcode5', 'zipcode4')->get();
            $insurances = Insurance::with('patient_insurance', 'insurancetype')->whereHas('patient_insurance', function($q) use($patient_id) {
                        $q->where('patient_id', $patient_id)->where('category', 'Primary');
                    })->select('insurance_name', 'id', 'insurancetype_id', 'city', 'state', 'zipcode5', 'zipcode4')->first();
            $insurance_data = [];
            if (!empty($patient_insurances)) {
                foreach ($patient_insurances as $patient_insurances) {
                    $insurance_data[$patient_insurances->id] = $patient_insurances->patient_insurance->category . ' - ' . (($patient_insurances->patient_insurance->category == 'Others') ? $patient_insurances->insurance_name : str_limit($patient_insurances->insurance_name, 10, '..'));
                }
            }
            $modifier = Modifier::pluck('code', 'id')->all();
            $hold_options = Holdoption::where('status', 'Active')->pluck('option', 'id')->all();
            $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'employer_details', 'dosdetails')->where('id', $claim_id)->first();
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient_id', 'facilities', 'providers', 'rendering_providers', 'referring_providers', 'billing_providers', 'insurances', 'patient_detail', 'modifier', 'claims_list', 'hold_options', 'insurance_data')));
        } else {
            return response::json(array('status' => 'error', 'message' => 'No Charges Available', 'data' => ''));
        }
    }

    public function getmodifier() {
        $modifier = Modifier::pluck('code', 'id')->all();
        return response::json(array('status' => 'error', 'message' => 'No Charges Available', 'data' => compact('modifier')));
    }

    // Add aothorization from popup starts here
    public function getCreateauthorizationApi($patient_id) {
        if (Patient::where('id', $patient_id)->count()) {
            $authorization = [];
            $authorization = PatientAuthorization::with('provider_details', 'insurance_details')->where('patient_id', $patient_id)->get();
            $auth_insurances_detail = Insurance::with('patient_insurance')->whereHas('patient_insurance', function($q) use($patient_id) {
                                            $q->where('patient_id', $patient_id);
                                        })->pluck('insurance_name', 'id')->all();

            $pos = Pos::pluck('pos', 'id')->all();
            $referring_providers = Provider::where('provider_types_id', '=', '2')->where('status', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            return response::json(array('status' => 'success', 'message' => '', 'data' => compact('authorization', 'auth_insurances_detail', 'pos', 'referring_providers', 'patient_id')));
        } else {
            return response::json(array('status' => 'error', 'message' => 'No Patient Available', 'data' => ''));
        }
    }

    public function getStoreAuthApi($request = '') {
        $request = Input::all();
        $validator = Validator::make($request, ['authorization_no' => 'required', 'pos_id' => 'required']);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            $request['requested_date'] = date("Y-m-d", strtotime($request['requested_date']));
            $request['start_date'] = date("Y-m-d", strtotime($request['start_date']));
            $request['end_date'] = date("Y-m-d", strtotime($request['end_date']));
            $patient_id = $request['patient_id'];
            $Patient_authorization = PatientAuthorization::create($request);
            return Response::json(array('status' => 'success', 'message' => 'autorization added successfully', 'data' => compact('patient_id')));
        }
    }

    // Add aothorization from popup Ends here
    //Store Patient contacts as employer at popup starts here
    public function getStoreEmployerApi($request = '') {
        $request = Input::all();
        $employer = PatientContact::create($request);
        if ($employer) {
            $address_flag['type_category'] = 'employer_address';
            $address_flag['address2'] = $request['emp_address1'];
            $address_flag['city'] = $request['emp_city'];
            $address_flag['state'] = $request['emp_state'];
            $address_flag['zip5'] = $request['emp_zip5'];
            $address_flag['zip4'] = $request['emp_zip4'];
            $address_flag['is_address_match'] = $request['emp_is_address_match'];
            $address_flag['error_message'] = $request['emp_error_message'];
            $address_flag['type'] = 'patients';
            $address_flag['type_id'] = $employer->id;
            AddressFlag::checkAndInsertAddressFlag($address_flag);
            return Response::json(array('status' => 'success', 'message' => 'Patient added successfully'));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'Patient added successfully'));
        }
    }

    //Store Patient contacts as employer at popup ends here

    public function getStoreReferringProviderApi($request = '') {
        $request = Input::all();
        $last_name = $request['last_name'];
        $first_name = $request['first_name'];
        $npi = $request['npi'];
        $count = Provider::where('last_name', $last_name)->where('first_name', $first_name)->where('provider_types_id', 2)->where('npi', $npi)->count();
        if ($count > 0) {
            return Response::json(array('status' => 'error', 'message' => 'Npi already exist with provider'));
        }
        $validator = Validator::make($request, Provider::$rules, Provider::$messages);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            $request['provider_name'] = $request['last_name'] . ' ' . $request['first_name'];
            $provider = Provider::create($request);
            if (config('siteconfigs.is_enable_provider_add')) { // Just for temporary to fix database issue
                $request['practice_db_provider_id'] = $provider->id;
                /* Save image to admin database starts */
                if (Session::has('practice_dbid')) {
                    $request['practice_id'] = Session::get('practice_dbid');
                    $customer = DB::table('practices')->where('id', $request['practice_id'])->pluck('customer_id')->first();
                    $request['customer_id'] = $customer;
                }
                $admin_db_name = getenv('DB_DATABASE');
                $dbconnection = new DBConnectionController();
                //update provider id for practice db field
                $dbconnection->updatepracticedbproviderid($provider->id);
                //create provider in admin provider table
                $dbconnection->createProviderinOtherDB($request, $admin_db_name);
            }
            $provider_data['providername'] = $request['provider_name'];
            $provider_data['providerid'] = $provider->id;
            if ($provider) {
                return Response::json(array('status' => 'success', 'message' => 'Referring provider added successfully.', 'data' => compact('provider_data')));
            } else {
                return Response::json(array('status' => 'error', 'message' => ''));
            }
        }
    }

    public function getCreateBillingEmployerApi() {  // This is just made and not using now.If we want to use any data(for select box) from database will use it
        return response::json(array('status' => 'error', 'message' => 'No Patient Available', 'data' => ''));
    }

    // selectbox icons values on right side for Facility, Employer, Insurance starts here
    public function getApiselectbasedvalue($id, $model) {
        $code = '';
        $clai_no = '';
        if ($model == 'Facility') {
            $data_needed = Facility::with('pos_details', 'facility_address')->where('id', $id)->first();
            $data = $data_needed->pos_details->pos;
            $code = $data_needed->pos_details->code;
            $clai_no = $data_needed->clia_number;
        } elseif ($model == 'Employer') {
            $data_needed = PatientContact::where('id', $id)->first();
            $data = 'Emp';
        } else {
            $data_needed = Insurance::with('insurancetype')->where('id', $id)->first();
            $data = isset($data_needed->insurancetype->type_name) ? $data_needed->insurancetype->type_name : 'others';
        }
        if ($data) {
            return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('data', 'data_needed', 'code', 'clai_no')));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'no data available', 'data' => ''));
        }
    }

    // selectbox icons values on right side for Facility, Employer, Insurance ends here
    public function getReferringproviderApi($patient_id, $query = null) {
        $referring_providers = array();
        if ($patient_id != 'provider') {   // Auto search employer Details
            $referring_providers = PatientContact::select(DB::raw("CONCAT(employer_name,' ',employer_city,' ',employer_state) AS employer_detail"), 'id')->where('category', 'Employer')->where('patient_id', $patient_id)->where('employer_name', 'LIKE', '%' . $query . '%')->orWhere('employer_city', 'LIKE', '%' . $query . '%')->orWhere('employer_state', 'LIKE', '%' . $query . '%')->pluck('employer_detail', 'id')->all();
        } else {     // Auto search provider Details
            $referring_providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"), 'id')->where('provider_types_id', '=', '2')->where('provider_name', 'LIKE', '%' . $query . '%')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
        }
        return response::json(array('status' => 'success', 'message' => '', 'data' => compact('referring_providers')));
    }

    public function getProviderApiDetailpopup($value, $type) {
        $providers = Provider::with('speciality')->where('id', $value)->select('provider_name', 'npi', 'id')->first();
        return response::json(array('status' => 'success', 'message' => '', 'data' => compact('providers')));
    }

    // check icd existance on icd fill ups
    public function checkICDexistApi($icd_code) {
        if (Icd::where('icd_code', $icd_code)->count()) {
            $icd = Icd::where('icd_code', $icd_code)->select('id', 'short_description', 'icd_code')->first();
            return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('icd')));
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
    }

    // check cpt existance on icd fill ups
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

    public function checkCPTexistApi($cpt_hcpcs) {
        if (Cpt::where('cpt_hcpcs', $cpt_hcpcs)->count()) {
            $dbconnection = new DBConnectionController();
            $dbconnection->connectPracticeDB(Session::get('practice_dbid'));
            if (Cpt::where('cpt_hcpcs', $cpt_hcpcs)->count()) {
                $cpt = Cpt::where('cpt_hcpcs', $cpt_hcpcs)->select('id', 'allowed_amount', 'billed_amount')->first();
                return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('cpt')));
            } else {
                $cpt = ['allowed_amount' => 0, 'billed_amount' => 0];
                return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('cpt')));
            }
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
    }

    // Store Api details Starts Here
    public function getStoreApi($request = '') {
        //$request = Request::all();
        //dd($request);
        if (!empty($request['icd1'])) {
            for ($i = 1; $i <= 12; $i++) {
                $icd_list[$i] = !empty($request['icd' . $i]) ? Icd::getIcdIds($request['icd' . $i]) : '';
            }
            $icd_lists = array_filter($icd_list);

            $request['icd_codes'] = implode(',', $icd_lists);
        }


        //Store hold reason
        $icd = new Icd();
        $icd->connecttopracticedb();
        if (isset($request['is_hold']) && !empty($request['other_reason'])) {
            $option['option'] = $request['other_reason'];
            $option['created_by'] = Auth::user()->id;
            $option['updated_by'] = $request['other_reason'];
            $result = Holdoption::create($option);
            $request['hold_reason_id'] = $result->id;
            $request['status'] = 'Hold';
        } elseif (isset($request['is_hold']) && !empty($request['hold_reason_id'])) {
            $request['status'] = 'Hold';
        } else {
            $request['hold_reason_id'] = '';
            $request['status'] = 'Ready';
            $request['is_hold'] = '';
        }
        //Store self pay
        if (isset($request['self'])) {
            $request['self_pay'] = 'Yes';
            $request['status'] = 'Patient';
            $request['insurance_id'] = '';
            $request['insurance_category'] = '';
        }
        //Store self pay
        //Store Insurance info
        if (isset($request['insurance_id'])) {
            $request['self_pay'] = 'No';
            $insurance_category = isset($request['insurance_category']) ? explode('-', $request['insurance_category']) : '';
            $request['insurance_category'] = $insurance_category[0];
        }
        //Store insurance info
        if (!empty($request['doi']))
            $request['doi'] = date('Y-m-d', strtotime($request['doi']));
        $request['batch_date'] = date('Y-m-d', strtotime($request['batch_date']));
        $request['admit_date'] = date('Y-m-d', strtotime($request['admit_date']));
        $request['discharge_date'] = date('Y-m-d', strtotime($request['discharge_date']));
        if (!empty($request)) {
            $dos_spt_details = [];
            for ($i = 0; $i < count($request['cpt']); $i++) {
                //dd($request['dos_from'][$i]);		
                if (!empty($request['cpt'][$i])) {
                    $dos_spt_details[$i]['dos_from'] = date('Y-m-d', strtotime($request['dos_from'][$i]));
                    $dos_spt_details[$i]['dos_to'] = date('Y-m-d', strtotime($request['dos_to'][$i]));
                    $dos_spt_details[$i]['cpt_code'] = $request['cpt'][$i];
                    $dos_spt_details[$i]['modifier1'] = $request['modifier1'][$i];
                    $dos_spt_details[$i]['modifier2'] = $request['modifier2'][$i];
                    $dos_spt_details[$i]['modifier3'] = $request['modifier3'][$i];
                    $dos_spt_details[$i]['modifier4'] = $request['modifier4'][$i];
                    $dos_spt_details[$i]['charge'] = $request['charge'][$i];
                    $dos_spt_details[$i]['cpt_allowed_amt'] = $request['cpt_allowed'][$i];
                    $dos_spt_details[$i]['cpt_billed_amt'] = $request['cpt_amt'][$i];
                    $dos_spt_details[$i]['unit'] = $request['unit'][$i];
                    $dos_spt_details[$i]['cpt_icd_code'] = $request['cpt_icd_map'][$i];
                    $dos_spt_details[$i]['cpt_icd_map_key'] = $request['cpt_icd_map_key'][$i];
                    $dos_spt_details[$i]['patient_id'] = $request['patient_id'];  // Need to check DOS for each claim with patient_id
                    if ($dos_spt_details[0]['dos_from'])
                        $request['date_of_service'] = $dos_spt_details[0]['dos_from']; // To save First record of from to date
                    if ($dos_spt_details[0]['cpt_code'])
                        $request['cpt_codes'] = $dos_spt_details[0]['cpt_code']; // To save First record of from to date
                }
            }
            unset($request['dos_from']);
            unset($request['dos_to']);
            unset($request['cpt']);
            unset($request['modifier1']);
            unset($request['modifier2']);
            unset($request['modifier3']);
            unset($request['modifier4']);
            unset($request['charge']);
            unset($request['unit']);
            unset($request['cpt_icd_map']);
            $request['created_by'] = Auth::user()->id;
            if ($request['claim_id'] == '') {
                $result = ClaimInfoV1::create($request);
                $claim_no = $this->generateclaimnumber('CHR', $result->id);
                $result->update(['claim_number' => $claim_no]);
            } else {
                $result = ClaimInfoV1::find($request['claim_id']);
                if (empty($result->claim_number)) {
                    $claim_no = $this->generateclaimnumber('CHR', $request['claim_id']);
                    $request['claim_number'] = $claim_no;
                }
                $result->update($request);
            }
            for ($j = 0; $j < count($dos_spt_details); $j++) {
                $dos_spt_details[$j]['claim_id'] = $result->id;
            }
            ClaimCPTInfoV1::where('claim_id', $result->id)->forceDelete();
            ClaimCPTInfoV1::insert($dos_spt_details);
            // Store claim id to charges batches
            if (!empty($request['batch_id'])) {
                $batch = BatchCharge::find($request['batch_id']);
                $batch->claim_ids = (!empty($batch->claim_ids)) ? $batch->claim_ids . ',' . $result->id : $result->id;
                $batch->save();
            }
            if (!empty($result->document_path) && !empty($result->cmsform)) {
                $this->deleteExistingdocument($result->id);
            }
            $store_arr = $this->generatecms1500($result->id);
        }
        if ($result) {
            return Response::json(array('status' => 'success', 'message' => 'claim other details added successfully', 'data' => $result->id));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'claim other details did not added successfully', 'data' => ''));
        }
    }

    // Store Api details Ends Here
    // Payment related details on popup at billing listing page related codes
    public function getPaymentDetail($id) {
        if (ClaimInfoV1::where('id', $id)->count()) {
            $detail = ClaimInfoV1::with('rendering_provider', 'billing_provider', 'insurance_details', 'facility_detail', 'dosdetails')->where('id', $id)->first();
            return response::json(array('statuss' => 'success', 'message' => '', 'value' => compact('detail')));
        } else {
            return response::json(array('statuss' => 'error', 'message' => '', 'data' => ''));
        }
    }

    public function getDeleteApi($id) {
        if (ClaimInfoV1::where('id', $id)->count()) {
            $patient_id = ClaimInfoV1::where('id', $id)->pluck('patient_id')->first();
            if (ClaimInfoV1::where('id', $id)->delete()) {
                return Response::json(array('status' => 'success', 'message' => 'Deleted successfully', 'data' => compact('patient_id')));
            } else {
                return Response::json(array('status' => 'error', 'message' => 'Not deleted', 'data' => compact('patient_id')));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => 'No data available', 'data' => ''));
        }
    }

    public function getCmsDetailApi() {
        // Need to design and implement this page
    }

    public function generateclaimnumber($type, $claim_id) {  // Claim number generation
        $practice_id = 4;
        if (Session::get('practice_dbid')) {
            $practice_id = Session::get('practice_dbid');
        }
        $seperator = $this->generateAlphachar();
        if ($practice_id < 1000 && $claim_id < 1000) {
            
        } elseif ($practice_id < 1000 && $claim_id > 1000) {
            $claim_id = $this->generatecode($claim_id);
        } elseif ($practice_id > 1000 && $claim_id < 1000) {
            $practice_id = $this->generatecode($practice_id);
        } else {
            $claim_id = $this->generatecode($claim_id);
            $practice_id = $this->generatecode($practice_id);
        }
        return $type . $practice_id . $seperator . $claim_id;
    }

    public function generatecode($id) {  // This is to generate code from autoincrement id into 4 digit number
        return $id;
    }

    public function generateAlphachar() {   // This is to generate alpha character to seperate each ids (claim and practice)
        $length = 1;
        return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    // If a patient added with the same dos an alert will get opened and confirms with the patient
    public function checkExistingDosApi($patient_id, $dos) {
        $dos = date("Y-m-d", strtotime(base64_decode($dos)));
        /* $patient_list = DB::table('claimdoscptdetails')
          ->where('patient_id', $patient_id)
          ->Where(function($query) use ($dos)
          {
          $query->where('dos_from' , "'$dos'");
          //  ->orwhere('dos_to', "'$dos'");
          })
          ->count(); */
        //$patient_list = Claimdoscptdetail::where(DB::raw('dos_from = "'.$dos.'"'))->where('patient_id',$patient_id)->count();
        $patient_list = ClaimCPTInfoV1::where('dos_from', '=', $dos)->where('patient_id', $patient_id)->count();
        //dd($patient_list);
        if ($patient_list > 0) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function generatecms1500($claim_id) {
        $claim_data = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'dosdetails', 'patient', 'claim_details')->where('id', $claim_id)->first();
        //dd($claim_data);
        $claim_detail['id'] = $claim_data->id;
        $claim_detail['ins_type'] = isset($claim_data->insurance_details->insurancetype) ? $claim_data->insurance_details->insurancetype->type_name : '';
        $claim_detail['insured_id_number'] = isset($claim_data->insurance_details) ? "8976546" : '';
        $claim_detail['patient_name'] = $claim_data->patient->last_name . ', ' . $claim_data->patient->first_name . ',' . $claim_data->patient->middle_name;
        $claim_detail['patient_dob_m'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1970-01-01') ? date('m', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_dob_d'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1970-01-01') ? date('d', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_dob_y'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1970-01-01') ? date('y', strtotime($claim_data->patient->dob)) : '';
        $claim_detail['patient_gender'] = isset($claim_data->patient->gender) ? $claim_data->patient->gender : '';
        $claim_detail['patient_addr'] = $claim_data->patient->address1;
        $claim_detail['patient_city'] = $claim_data->patient->city;
        $claim_detail['patient_state'] = $claim_data->patient->state;
        $claim_detail['patient_zip5'] = $claim_data->patient->zip5;
        $claim_detail['patient_phone'] = $claim_data->patient->phone;
        $claim_detail['insured_relation'] = isset($claim_data->insurance_details) ? "Self" : '';
        $claim_detail['insured_name'] = isset($claim_data->insurance_details) ? "James, Robert, S" : '';
        $claim_detail['insured_addr'] = isset($claim_data->insurance_details) ? "1001 W FAYETTE ST" : '';
        $claim_detail['insured_city'] = isset($claim_data->insurance_details) ? "SYRACUSE" : '';
        $claim_detail['insured_state'] = isset($claim_data->insurance_details) ? "NY" : '';
        $claim_detail['insured_zip5'] = isset($claim_data->insurance_details) ? "2859" : '';
        $claim_detail['insured_phone'] = isset($claim_data->insurance_details) ? '(643)573-3732' : '';
        $claim_detail['reserved_nucc_box8'] = isset($claim_data->claim_details->reserved_nucc_box8) ? $claim_data->claim_details->reserved_nucc_box8 : '';
        $claim_detail['other_insured_name'] = isset($claim_data->insurance_details) ? "Josef" : '';
        $claim_detail['other_ins_policy'] = isset($claim_data->insurance_details) ? "123456565" : '';
        $claim_detail['other_insur_name'] = isset($claim_data->insurance_details) ? "Insurance Name" : '';
        $claim_detail['reserved_nucc_box9b'] = isset($claim_data->claim_details->reserved_nucc_box9b) ? $claim_data->claim_details->reserved_nucc_box9b : '';
        $claim_detail['reserved_nucc_box9c'] = isset($claim_data->claim_details->reserved_nucc_box9c) ? $claim_data->claim_details->reserved_nucc_box9c : '';
        $claim_detail['is_employment'] = isset($claim_data->claim_details->is_employment) ? $claim_data->claim_details->is_employment : '';
        $claim_detail['is_auto_accident'] = isset($claim_data->claim_details->is_autoaccident) ? $claim_data->claim_details->is_autoaccident : '';
        $claim_detail['is_other_accident'] = isset($claim_data->claim_details->is_otheraccident) ? $claim_data->claim_details->is_otheraccident : '';
        $claim_detail['accident_state'] = isset($claim_data->claim_details->autoaccident_state) ? $claim_data->claim_details->autoaccident_state : '';
        $claim_detail['insured_state'] = isset($claim_data->claim_details->autoaccident_state) ? $claim_data->claim_details->autoaccident_state : '';
        $claim_detail['ins_policy_no'] = "123456565";
        $claim_detail['claimcode'] = isset($claim_data->claim_details->claim_code) ? $claim_data->claim_details->claim_code : '';
        $claim_detail['insured_dob_m'] = '03';
        $claim_detail['insured_dob_d'] = '11';
        $claim_detail['insured_dob_y'] = '15';
        $claim_detail['insured_gender'] = 'Male';
        $claim_detail['other_claimid'] = isset($claim_data->claim_details->other_claim_id) ? $claim_data->claim_details->other_claim_id : '';
        $claim_detail['insur_name'] = 'Male';
        $claim_detail['is_another_ins'] = isset($claim_data->insurance_details) ? "Yes" : '';
        $claim_detail['patient_signed'] = 'SIGNATURE ON FILE';
        $claim_detail['signed_date'] = '12/03/2015'; //Claim Submited date.
        $claim_detail['insured_signed'] = 'SIGNATURE ON FILE';
        // If pregnency LMP was filled take its date and qualifier given at the NUCC pdf
        if (!empty($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01') {
            $claim_detail['doi_m'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01') ? date('m', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01') ? date('d', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01') ? date('y', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_qual'] = 484;
        } else {
            $claim_detail['doi_m'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01') ? date('m', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01') ? date('d', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01') ? date('y', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_qual'] = 431;
        }
        $claim_detail['other_m'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01') ? date('m', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_d'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01') ? date('d', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_y'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01') ? date('y', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_qual'] = isset($claim_data->claim_details->other_date_qualifier) ? $claim_data->claim_details->other_date_qualifier : '';
        $claim_detail['refering_provider'] = isset($claim_data->refering_provider) ? $claim_data->refering_provider->provider_name : '';
        $claim_detail['refering_provider_npi'] = isset($claim_data->refering_provider) ? $claim_data->refering_provider->npi : '';
        $claim_detail['refering_provider_qual'] = 'DN';
        $claim_detail['box18_from_m'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01') ? date('m', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_d'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01') ? date('d', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_y'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01') ? date('y', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_to_m'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01') ? date('m', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_d'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01') ? date('d', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_y'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01') ? date('y', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['admit_date_m'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01') ? date('m', strtotime($claim_data->admit_date)) : '';
        $claim_detail['admit_date_d'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01') ? date('d', strtotime($claim_data->admit_date)) : '';
        $claim_detail['admit_date_y'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01') ? date('y', strtotime($claim_data->admit_date)) : '';
        $claim_detail['discharge_date_m'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01') ? date('m', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_d'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01') ? date('d', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_y'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01') ? date('y', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['addi_claim_info'] = isset($claim_data->claim_details->additional_claim_info) ? $claim_data->claim_details->additional_claim_info : '';
        $claim_detail['outside_lab'] = isset($claim_data->claim_details->outside_lab) ? $claim_data->claim_details->outside_lab : '';
        $claim_detail['charges'] = '89787'; // This will be applied after submission of data
        $icd_codes = [];
        if (!empty($claim_data->icd_codes)) {
            $icd_codes = Icd::getIcdValues($claim_data->icd_codes);
        }
        $icd = new Icd();
        $icd->connecttopracticedb();
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
        $claim_detail['pos'] = isset($claim_data->pos_code) ? $claim_data->pos_code : '';
        $claim_detail['epsdt'] = isset($claim_data->claim_details->epsdt) ? $claim_data->claim_details->epsdt : '';
        $claim_detail['billing_provider_npi'] = !empty($claim_data->billing_provider) ? $claim_data->billing_provider->npi : '';
        if (isset($claim_data->billing_provider->etin_type) && ($claim_data->billing_provider->etin_type == 'SSN')) {
            $etin_ssn = 'X';
        } else {
            $etin_tax = 'X';
        }
        $claim_detail['etin_ssn'] = $etin_ssn;
        $claim_detail['etin_tax'] = $etin_tax;
        $claim_detail['accept_assignment'] = isset($claim_data->claim_details->accept_assignment) ? $claim_data->claim_details->accept_assignment : '';
        $claim_detail['ssn_or_taxid_no'] = isset($claim_data->billing_provider->etin_type_number) ? $claim_data->billing_provider->etin_type_number : '';
        $claim_detail['claim_no'] = $claim_data->claim_number; // send as patient account number
        $claim_detail['total'] = isset($claim_data->total_charge) ? explode('.', $claim_data->total_charge) : '';
        $claim_detail['reserved_nucc_box30'] = isset($claim_data->claim_details->reserved_nucc_box30) ? explode(',', $claim_data->claim_details->reserved_nucc_box30) : '';
        $claim_detail['rendering_provider_name'] = isset($claim_data->rendering_provider->provider_name) ? $claim_data->rendering_provider->provider_name : '';
        $claim_detail['rendering_provider_date'] = "12/03/2015";
        $claim_detail['facility_name'] = isset($claim_data->facility_detail->facility_name) ? $claim_data->facility_detail->facility_name : '';
        $claim_detail['facility_addr'] = isset($claim_data->facility_detail->facility_address->address1) ? $claim_data->facility_detail->facility_address->address1 : '';
        $claim_detail['facility_city'] = $claim_data->facility_detail->facility_address->city . ',' . $claim_data->facility_detail->facility_address->state . ',' . $claim_data->facility_detail->facility_address->pay_zip5;
        $claim_detail['facility_npi'] = isset($claim_data->facility_detail->facility_npi) ? $claim_data->facility_detail->facility_npi : '';
        $claim_detail['bill_provider_name'] = isset($claim_data->billing_provider->provider_name) ? $claim_data->billing_provider->provider_name : '';
        $claim_detail['bill_provider_addr'] = isset($claim_data->billing_provider->address_1) ? $claim_data->billing_provider->address_1 : '';
        $claim_detail['bill_provider_city'] = !empty($claim_data->billing_provider->city) ? $claim_data->billing_provider->city . ',' : '' . !empty($claim_data->billing_provider->state) ? $claim_data->billing_provider->state . ',' : '' . !empty($claim_data->billing_provider->zipcode5) ? $claim_data->billing_provider->zipcode5 : '';
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

        $this->createpdf($claim_detail);
        if ($line_item > 6) {
            $this->createpdf($claim_detail, 'second');
        }
    }

    public function createpdf($claim_data, $document_mode = 'first') {
        if (App::environment() == 'production')
            $img_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $img_path = public_path() . '/';
        $path = $img_path . "/img/background3.jpg";
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
        $content = '<html>
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
<div style="position:absolute;left:50%;margin-left:-408px;top:0px;width:820px;height:1146px;overflow:hidden">
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

<div style="position:absolute;left:719.36px;top:644px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['charges'] . '</span></div>

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

' . (isset($document_mode) && ($document_mode == 'first') ? $doc1 : $doc2) . '

<div style="position:absolute;left:24.09px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['ssn_or_taxid_no'] . '</span></div>

<div style="position:absolute;left:181.36px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['etin_ssn'] . '</span></div>
<div style="position:absolute;left:202px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['etin_tax'] . '</span></div>

<div style="position:absolute;left:240px;top:996px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['claim_no'] . '</span></div>

<div style="position:absolute;left:387px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_detail['accept_assignment']) && ($claim_detail['accept_assignment'] == 'Yes') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:436px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_detail['accept_assignment']) && ($claim_detail['accept_assignment'] == 'No') ? 'X' : '') . '</span></div>

<div style="position:absolute;left:524.17px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['total'][0]) ? $claim_data['total'][0] : '00') . '</span></div>
<div style="position:absolute;left:584.17px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['total'][1]) ? $claim_data['total'][1] : '00') . '</span></div>

<div style="position:absolute;left:727.41px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['reserved_nucc_box30'][0]) ? $claim_data['reserved_nucc_box30'][0] : '') . '</span></div>
<div style="position:absolute;left:772.41px;top:996px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['reserved_nucc_box30'][1]) ? $claim_data['reserved_nucc_box30'][1] : '') . '</span></div>

<div style="position:absolute;left:238px;top:1029px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_name'] . '</span></div>
<div style="position:absolute;left:238px;top:1044px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_addr'] . '</span></div>
<div style="position:absolute;left:238px;top:1059px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_city'] . '</span></div>

<div style="position:absolute;left:247.04px;top:1084px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['facility_npi'] . '</span></div>

<div style="position:absolute;left:502px;top:1029px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['bill_provider_name'] . '</span></div>
<div style="position:absolute;left:502px;top:1044px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['bill_provider_addr'] . '</span></div>
<div style="position:absolute;left:502px;top:1059px" class="cls_002"><span class="cls_002 med-black">' . $claim_data['bill_provider_city'] . '</span></div>

<div style="position:absolute;left:512px;top:1084px" class="cls_002"><span class="cls_002 med-black">' . (isset($claim_data['bill_provider_npi']) ? $claim_data['bill_provider_npi'] : '') . '</span></div>


</div></body></html>';
        $claim_id = $claim_data['id'];
        $type = "claim";
        $file = '';
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($content);
        $pdf->setPaper('A4', 'portrait');
        if (App::environment() == 'production')
            $pdf_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $pdf_path = public_path() . '/';
        $src_path = $pdf_path . '/media/claim/' . Auth::user()->id . '/' . $claim_id;
        $url_path = url("/") . '/media/claim/' . Auth::user()->id . '/' . $claim_id;
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
            $claim->localfilename = empty($claim->localfilename) ? $file_name : $claim->localfilename . ',' . $file_name;
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
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $claim = ClaimInfoV1::where('id', $id)->select('document_path', 'cmsform')->first();
        $chk_env_site = getenv('APP_ENV');
        if ($chk_env_site == "local") {
            $storage_disk = "s3";
        } elseif ($chk_env_site == "production") {
            $storage_disk = "s3_production";
        } else {
            $storage_disk = "s3";
        }
        $document_path = explode(',', $claim->document_path); // To check for more than one cms document file
        $document_name = explode(',', $claim->cmsform);
        if ($type == 1) {
            $file = Storage::disk($storage_disk)->get($document_path[0] . $document_name[0]);
        } elseif ($type == 2) {
            $file = Storage::disk($storage_disk)->get($document_path[1] . $document_name[1]);
        } else {
            $file = Storage::disk($storage_disk)->get($claim->document_path . $claim->cmsform);
        }
        return (new Responseobj($file, 200))->header('Content-Type', 'application/pdf');
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
            if ($chk_env_site == "production") {
                $storage_disk = "s3_production";
                $bucket_name = "medcubicsproduction";
            } else {
                $storage_disk = "s3";
                $bucket_name = "medcubicslocal";
            }
            $document = explode(',', $claim['document_path']);  // To check for more than one cmsdocument file
            $cms = explode(',', $claim['cmsform']);
            if (count($document) > 1) {
                if (Storage::disk($storage_disk)->delete([$document[0] . $cms[0], $document[1] . $cms[1]])) {
                    ClaimInfoV1::where('id', $claim_id)->update(['document_path' => '', 'cmsform' => '', 'document_domain' => '']);
                    return true;
                }
            } else {
                if (Storage::disk($storage_disk)->delete($claim['document_path'] . $claim['cmsform'])) {
                    ClaimInfoV1::where('id', $claim_id)->update(['document_path' => '', 'cmsform' => '', 'document_domain' => '']);
                    return true;
                }
            }
        }
    }

    /** When we update claim delete the existing document related details from database and s3 ends here* */
}
