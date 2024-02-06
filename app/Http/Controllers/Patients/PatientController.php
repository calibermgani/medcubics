<?php

namespace App\Http\Controllers\Patients;

use Auth;
use View;
use Input;
use Session;
use Request;
use Response;
use Redirect;
use Validator;
use App\Http\Controllers\Api\PatientApiController as PatientApiController;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Uploadpatient;
use App;
use Config;
use SSH; 
use PDF;
use Excel;
use File;

use App\Traits\ClaimUtil;
use App\Exports\BladeExport;

class PatientController extends Api\PatientApiController {

    use ClaimUtil;

    public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'patients');
        View::share('heading_icon', 'fa-user');
    }

    public $totalLines;
    public $hl_count;
    public $line_count;
    public $segment_separator;
    public $iea_02;
    public $separator;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $patients = $insurance_list = [];
        $total_rec = $all_patients = 1; 
        $ClaimController  = new ClaimControllerV1();   
        $search_fields_data = $ClaimController->generateSearchPageLoad('patients_listing');
        $searchUserData = $search_fields_data['searchUserData']; 
        $search_fields = $search_fields_data['search_fields']; 
        return view('patients/patients/patients', compact('patients', 'insurance_list', 'total_rec', 'all_patients', 'search_fields', 'searchUserData'));
    }

    public function indexTableData($type = '', $appCheck = '') {
        ($type != 'all' ) ? null : $type;
        ($appCheck == '') ? null : $appCheck;

        $api_response = $this->getIndexApi('', $type, $appCheck);
        $api_response_data = $api_response->getData();
        $patients = (array) $api_response_data->data->patients;
        $insurance_list = (array) $api_response_data->data->insurances;
        $view_html = Response::view('patients/patients/patients_list', compact('patients', 'insurance_list'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['order'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;

        return Response::json($data);
    }

    /* 	patient list export	 */

    public function getPatientExport($export = '', $type = '', $appCheck = '') {
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', -1);
        ($type != 'all' ) ? null : $type;
        ($appCheck == '') ? null : $appCheck;

        $api_response = $this->getIndexApi($export, $type, $appCheck);
        $api_response_data = $api_response->getData();
        $patients = (array) $api_response_data->data->patients;
        $insurance_list = (array) $api_response_data->data->insurances;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patients_list_' . $date;
        
        if ($export == 'pdf') {
            $html = view('patients/patients/patients_list_export_pdf', compact('patients', 'insurance_list', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'patients/patients/patients_list_export';
            $data['patients'] = $patients;
            $data['insurance_list'] = $insurance_list;
            $data['export'] = $export;            
            $data['file_path'] = $filePath;

            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xlsx');
        } elseif ($export == 'csv') {
            $filePath = 'patients/patients/patients_list_export';
            $data['patients'] = $patients;
            $data['insurance_list'] = $insurance_list;
            $data['export'] = $export;            
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

    public function create() {
        $api_response = $this->getCreateApi();
        $api_response_data = $api_response->getData();

        $countries = $api_response_data->data->countries;
        $ethnicity = $api_response_data->data->ethnicity;
        $languages = $api_response_data->data->languages;
        $providers = $api_response_data->data->providers;
        $referringProviders = $api_response_data->data->referringProviders;
        $facilities = $api_response_data->data->facilities;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['pia'] = (array) $address_flags['pia'];
        $address_flag['poa'] = (array) $address_flags['poa'];
        $registration = $api_response_data->data->registration;
        $selectbox = $api_response_data->data->selectbox;
        $country_id = $api_response_data->data->country_id;
        $ethnicity_id = $api_response_data->data->ethnicity_id;
        $language_id = $api_response_data->data->language_id;
        $provider_id = $api_response_data->data->provider_id;
        $facility_id = $api_response_data->data->facility_id;
        $referring_provider_id = $api_response_data->data->referring_provider_id;
        $employe_status = $api_response_data->data->employe_status;

        $gu_self_check = 'No';
        $emer_relationship  = '';
        $tab = 'demo';
        $gu_relationship = $gu_last_name = $gu_first_name = $gu_middle_name = '';
        $stmt_category = isset($api_response_data->data->stmt_category) ? $api_response_data->data->stmt_category : [];
        $stmt_holdreason = isset($api_response_data->data->stmt_holdreason) ? $api_response_data->data->stmt_holdreason : [];

        return view('patients/patients/create', compact('tab', 'countries', 'ethnicity', 'languages', 'providers', 'referringProviders', 'facilities', 'address_flag', 'registration', 'selectbox', 'country_id', 'ethnicity_id', 'language_id', 'provider_id', 'facility_id', 'referring_provider_id', 'employe_status', 'gu_relationship', 'gu_last_name', 'gu_first_name', 'gu_middle_name', 'stmt_category', 'stmt_holdreason','gu_self_check','emer_relationship'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($patient_id = '') {
        $api_response = $this->getStoreApi($patient_id);
        $api_response_data = $api_response->getData();
        // print_r($api_response);exit;
        if ($api_response_data->status == 'failure') {
            return Redirect::to('patients')->with('message', $api_response_data->message);
        }

        $id = $api_response_data->data;
        if ($api_response_data->status == 'success') {
            if (Request::input('next_tab') == 'no') {
                return Redirect::to('patients/' . $api_response_data->data . '#personal-info')->with('success', $api_response_data->message);
            } else {
                return Redirect::to('patients/' . $api_response_data->data . '/edit/insurance')->with('success', $api_response_data->message);
            }
        } else {
            if ($patient_id != '') {
                return Redirect::to('patients/' . $api_response_data->data . '/edit#personal-info')->withInput()->withErrors($api_response_data->message);
            } else {
                return Redirect::to('patients/create')->withInput()->withErrors($api_response_data->message);
            }
            exit;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id, $tab = 'demo', $addmore = '') {      
        $api_response = $this->getEditApi($id, $tab);
        if (!isset($api_response) || empty($api_response->getData())) {
            return Redirect::to('patients')->with('message', 'Invalid Patient!!!');
        }
        $api_response_data = $api_response->getData();
        $status = $api_response_data->status;
        if ($api_response_data->status == 'failure') {
            return Redirect::to('patients')->with('message', $api_response_data->message);
        }
        $patients = $api_response_data->data->patient;
        $registration = $api_response_data->data->registration;
        $selectbox = $api_response_data->data->selectbox;
        $selectbox_count = $api_response_data->data->selectbox_count;
        $eligibility = $api_response_data->data->eligibility;       
        $stmt_category = isset($api_response_data->data->stmt_category) ? $api_response_data->data->stmt_category : [];
        $stmt_holdreason = isset($api_response_data->data->stmt_holdreason) ? $api_response_data->data->stmt_holdreason : [];
        
        if ($status == "success") {
            $insurance_concat = [];
            $authorization_concat = [];
            $contact_concat = [];

            /*             * *  Starts - Insurance Tab ** */
            if ($tab == 'insurance') {
                $insurances = $api_response_data->data->insurances;
                $patient_insurances = $api_response_data->data->patient_insurances;
                $primary_ins_id = $api_response_data->data->primary_ins_id;
                $secondary_ins_id = $api_response_data->data->secondary_ins_id;
                $tertiary_ins_id = $api_response_data->data->tertiary_ins_id;
                $workerscomp_ins_id = $api_response_data->data->workerscomp_ins_id;
                $autoaccident_ins_id = $api_response_data->data->autoaccident_ins_id;
                $attorney_ins_id = $api_response_data->data->attorney_ins_id;
                $insurancetypes = $api_response_data->data->insurancetypes;
                $medical_secondary_list = $api_response_data->data->medical_secondary_list;
                $insurance_policy = @$api_response_data->data->insurance_policy ;
                $insurance_ssn = @$api_response_data->data->insurance_ssn;
                $insurance_concat = ['insurances', 'patient_insurances', 'primary_ins_id', 'secondary_ins_id', 'tertiary_ins_id', 'workerscomp_ins_id', 'autoaccident_ins_id', 'attorney_ins_id', 'insurancetypes', 'medical_secondary_list','insurance_policy','insurance_ssn'];
            }
            /*             * * Ends - Insurance Tab ** */

            /*             * *  Starts - Contact Tab ** */
            //echo '<pre>';print_r($api_response_data->data->contacts);exit;
            if ($tab == 'contact') {
                $contacts = $api_response_data->data->contacts;
                $claims_list = $api_response_data->data->claims_list;//dd($claims_list);
                $contact_concat = ['contacts', 'eligibility', 'claims_list'];
            }

            /*             * * Ends - Contact Tab ** */

            /*             * *  Starts - Authorization Tab ** */
            if ($tab == 'authorization') {
                $authorizations = $api_response_data->data->authorizations;
                $patient_insurances = $api_response_data->data->patient_insurances;
                $pos = $api_response_data->data->pos;
                $authorization_policy_ids = $api_response_data->data->authorization_policy_ids;
                $authorization_auth = @$api_response_data->data->authorization_auth;
                $authorization_concat = ['authorizations', 'patient_insurances', 'pos', 'authorization_policy_ids', 'eligibility'];
            }
            /*             * * Ends - Authorization Tab ** */

            if (Request::ajax()) {
                if ($tab == 'insurance') {
                    return View('patients/patients/insurance-info', compact('id', 'patients', 'insurances', 'patient_insurances', 'registration', 'selectbox', 'selectbox_count', 'addmore', 'primary_ins_id', 'secondary_ins_id', 'tertiary_ins_id', 'workerscomp_ins_id', 'autoaccident_ins_id', 'attorney_ins_id', 'insurancetypes', 'medical_secondary_list', 'eligibility','insurance_policy','insurance_ssn'));
                } elseif ($tab == 'contact') {
                    return View('patients/patients/contact-info', compact('id', 'patients', 'contacts', 'registration', 'selectbox', 'selectbox_count', 'addmore', 'eligibility','claims_list'));
                } elseif ($tab == 'authorization') {
                    return View('patients/patients/authorization-info', compact('id', 'patients', 'authorizations', 'registration', 'selectbox', 'selectbox_count', 'pos', 'patient_insurances', 'addmore', 'authorization_policy_ids','authorization_auth'));
                }
            } else {
                $countries = $api_response_data->data->countries;
                $ethnicity = $api_response_data->data->ethnicity;
                $languages = $api_response_data->data->languages;
                $referringProviders = $api_response_data->data->referringProviders;
                $providers = $api_response_data->data->providers;
                $facilities = $api_response_data->data->facilities;
                $country_id = $api_response_data->data->country_id;
                $ethnicity_id = $api_response_data->data->ethnicity_id;
                $language_id = $api_response_data->data->language_id;
                $provider_id = $api_response_data->data->provider_id;
                $facility_id = $api_response_data->data->facility_id;
                $referring_provider_id = $api_response_data->data->referring_provider_id;
                $employe_status = $api_response_data->data->employe_status;
                $address_flags = (array) $api_response_data->data->addressFlag;
                $address_flag['pia'] = (array) $address_flags['pia'];
                $address_flag['poa'] = (array) $address_flags['poa'];
                $practice_user_type = $api_response_data->data->practice_user_type;
                $claims_count = $api_response_data->data->claims_count;
                $emer_last_name = $api_response_data->data->emer_last_name;
                $emer_first_name = $api_response_data->data->emer_first_name;
                $emer_mi_name = $api_response_data->data->emer_mi_name;
                $emer_cell_phone = $api_response_data->data->emer_cell_phone;
                $emer_email = $api_response_data->data->emer_email;
                $emer_relationship = @$api_response_data->data->emer_relationship;
                $patient_alert_note = $api_response_data->data->patient_alert_note;
                $gu_relationship = $api_response_data->data->gu_relationship;
                $gu_self_check = @$api_response_data->data->gu_self_check;
                $gu_first_name = $api_response_data->data->gu_first_name;
                $gu_last_name = $api_response_data->data->gu_last_name;
                $gu_middle_name = $api_response_data->data->gu_middle_name;
                $emp_relationship = $api_response_data->data->emp_relationship;
                $employer_name = $api_response_data->data->employer_name;
                //$emp_organization = $api_response_data->data->emp_organization;
                $emp_occupation = $api_response_data->data->emp_occupation;
                $emp_student_status = @$api_response_data->data->emp_student_status;
                $emp_work_phone = $api_response_data->data->emp_work_phone;
                $emp_phone_ext = $api_response_data->data->emp_phone_ext;

                /* $patient_tabs_api_response 		= $this->getPatientTabsDetails($patients->id);
                  $patient_tabs_api_res_data 		= $patient_tabs_api_response->getData();
                  $patient_tabs_details			= $patient_tabs_api_res_data->data->patients;
                  $patient_tabs_insurance_count	= $patient_tabs_api_res_data->data->patient_insurance_count;
                  $patient_tabs_insurance_details	= json_decode(json_encode($patient_tabs_api_res_data->data->patient_insurance), true); */

                // For set page title
                $details['account_no'] = $patients->account_no;
                App\Http\Helpers\Helpers::setPageTitle('patients', $details);
                $documents_ssn = $api_response_data->data->documents_ssn;
                $documents_licence = $api_response_data->data->documents_licence;
                return view('patients/patients/edit', compact('id', 'tab', 'patients', 'countries', 'ethnicity', 'languages', 'providers', 'referringProviders', 'facilities', 'address_flag', 'registration', 'selectbox', 'selectbox_count', 'country_id', 'ethnicity_id', 'language_id', 'provider_id', 'facility_id', 'referring_provider_id', 'employe_status', 'addmore', 'patient_tabs_details', 'patient_tabs_insurance_details', 'patient_tabs_insurance_count', 'practice_user_type', 'claims_count', 'emer_last_name', 'emer_first_name', 'emer_cell_phone', 'emer_email','emer_relationship', 'patient_alert_note', 'gu_relationship', 'gu_first_name', 'gu_last_name', 'gu_middle_name', 'emp_relationship', 'employer_name', 'emp_occupation', 'emp_student_status', 'emp_work_phone', 'emp_phone_ext', 'eligibility', 'emer_mi_name', 'stmt_category', 'stmt_holdreason','gu_self_check','documents_ssn','documents_licence') + compact($insurance_concat) + compact($contact_concat) + compact($authorization_concat));
            }
        } else {
            if (!Request::ajax())
                return Redirect::to('patients')->with('error', $api_response_data->message);
            else
                return "Error";
        }
    }

    public function getAddMoreFields($addmore_type, $cur_count, $id = '') {
        $api_response = $this->getAddMoreFieldsApi($addmore_type, $cur_count, $id);
        $api_response_data = $api_response->getData();

        if ($addmore_type == 'insurance') {
            $patients = $api_response_data->data->patient;
            $insurances = $api_response_data->data->insurances;
            $registration = $api_response_data->data->registration;
            $patient_insurance = $api_response_data->data->patient_insurance;
            $eligibility = $api_response_data->data->eligibility;
            $count = $api_response_data->data->count;
            return view('patients/patients/insurance-form', compact('patients', 'insurances', 'patient_insurance', 'registration', 'cur_count', 'count', 'id', 'eligibility'));
        } elseif ($addmore_type == 'contact') {
            $patients = $api_response_data->data->patient;
            $contact = [];
            $registration = $api_response_data->data->registration;
            $selectbox = $api_response_data->data->selectbox;
            $count = $api_response_data->data->count;
            return view('patients/patients/contact-form', compact('patients', 'contact', 'registration', 'selectbox', 'cur_count', 'count', 'id'));
        } elseif ($addmore_type == 'authorization') {
            $patients = $api_response_data->data->patient;
            $authorizations = $api_response_data->data->authorizations;
            $patient_insurances = $api_response_data->data->patient_insurances;
            $pos = $api_response_data->data->pos;
            $registration = $api_response_data->data->registration;
            $count = $api_response_data->data->count;
            return view('patients/patients/authorization-form', compact('patients', 'authorizations', 'patient_insurances', 'pos', 'registration', 'cur_count', 'count', 'id'));
        }
    }

    public function updatePatientOtherTabs($id, $tab) {
        $api_response = $this->updatePatientOtherTabsApi($id, $tab);
        $api_response_data = $api_response->getData();
        $status = $api_response_data->status;
        if ($status == 'success')
            return 'success';
        else
            return 'error';
        //$patients = $api_response_data->data->patient;
    }

    public function show($id) {
        $api_response = $this->getShowApi($id);
        $api_response_data = $api_response->getData();
        $status = $api_response_data->status;
        if ($status == "success") {
            $patients = $api_response_data->data->patients;
            $registration = $api_response_data->data->registration;
            $selectbox = $api_response_data->data->selectbox;

            /* $patient_tabs_api_response 		= $this->getPatientTabsDetails($patients->id);
              $patient_tabs_api_res_data 		= $patient_tabs_api_response->getData();
              $patient_tabs_details			= $patient_tabs_api_res_data->data->patients;
              $patient_tabs_insurance_count	= $patient_tabs_api_res_data->data->patient_insurance_count;
              $patient_tabs_insurance_details	= json_decode(json_encode($patient_tabs_api_res_data->data->patient_insurance), true); */

            return view('patients/patients/show', compact('id', 'patients', 'registration', 'selectbox', 'patient_tabs_details', 'patient_tabs_insurance_details', 'patient_tabs_insurance_count'));
        } else {
            return Redirect::to('patients')->with('message', $api_response_data->message);
        }
    }

    public function getContactCategory($category, $cur_count) {
        return view('patients/patients/contact-category-form', compact('category', 'cur_count'));
    }

    //Auto Search employer_name
    public function employername($id) {
        $api_response = $this->getEmployerName($id);
        $api_response_data = $api_response->getData();
        $message = json_encode($api_response_data->message);
        print_r($message);
        exit;
    }

    // Populate patient statement category details
    public function getPatientStmtCategoryDetails($id) {
        $api_response = $this->getPatientStmtCateogyDetails($id);
        $api_response_data = $api_response->getData();//dd($api_response_data->data->stmtCatDetails);
        $catDetails = json_encode($api_response_data->data->stmtCatDetails);
        print_r($catDetails);
        exit;
    }

    //patient status change via function.js
    public function changeStatus($id, $status_value) {
        $api_response = $this->getchangeStatus($id, $status_value);
        $api_response_data = $api_response->getData();
        $status = json_encode($api_response_data->message);
        print_r($status);
        exit;
    }

    public function sel_patientinsurance_address($sel_insurance_id) {
        $api_response = $this->api_sel_patientinsurance_address($sel_insurance_id);
        echo $api_response;
        exit;
    }

    public function ContactModuleProcess() {
        $api_response = $this->ContactModuleProcessApi();
        $api_response_data = $api_response->getData();
        $status = $api_response_data->status;
        return $status;
    }

    public function insuranceModuleProcess() {
        $api_response = $this->insuranceModuleProcessApi();
        $api_response_data = $api_response->getData();
        $status = $api_response_data->status;
        echo $status;
        exit;
    }

    public function authorizationModuleProcess() {
        $api_response = $this->authorizationModuleProcessApi();
        $api_response_data = $api_response->getData();
        $status = $api_response_data->status;
        return $status;
    }

    public function checkInsurancetype() {
        $api_response_status = $this->getCheckInsurancetypeApi();
        echo $api_response_status;
        exit;
    }

    public function delete_patient_picture($id) {
        $api_response_status = $this->deletepatientpictureApi($id);
        echo $api_response_status;
        exit;
    }

    // Get patient questionnaries list
    public function getQuestionnaires($id) {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'patientshistory');
        View::share('heading_icon', 'fa-user');

        $api_response = $this->getQuestionnairesApi($id);
        $api_response_data = $api_response->getData();
        $questionaries = $api_response_data->data->questionaries;
        $registration = $api_response_data->data->registration;

        return view('patients/patients/questionaries', compact('registration', 'id', 'questionaries'));
    }

    public function destroy($id) {
        $api_response = $this->getDeleteApi($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'failure') {
            return Redirect::to('patients')->with('error', $api_response_data->message);
        }
        return Redirect::to('patients')->with('success', $api_response_data->message);
    }

    // Get patient archiveinsurance list
    public function getarchiveinsurance($id) {
        $api_response = $this->getarchiveinsuranceApi($id);
        $api_response_data = $api_response->getData();
        $archiveinsurance = $api_response_data->data->archiveinsurance;
        $patientdetails = $api_response_data->data->patientinfo;

        return view('patients/patients/archiveinsurance', compact('archiveinsurance', 'id', 'patientdetails'));
    }
    
    public function archiveInsuranceExport($id = '', $export = '') {
        $api_response = $this->getarchiveinsuranceApi($id);
        $api_response_data = $api_response->getData();
        $archiveinsurance = $api_response_data->data->archiveinsurance;
        $patientdetails = $api_response_data->data->patientinfo;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patient_Insurance_Archive_' . $date;
        
        if ($export == 'pdf') {
            $html = view('patients/patients/archiveinsurance_export_pdf', compact('archiveinsurance', 'patientdetails'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'patients/patients/archiveinsurance_export';
            $data['archiveinsurance'] = $archiveinsurance;
            $data['patientdetails'] = $patientdetails;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'patients/patients/archiveinsurance_export';
            $data['archiveinsurance'] = $archiveinsurance;
            $data['patientdetails'] = $patientdetails;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

    /*     * * Move archiveinsurance to insurance starts ** */

    public function moveArchchivetoInsurance($patient_id, $arc_id) {
        $api_response = $this->moveArchchivetoInsuranceApi($patient_id, $arc_id);
        $api_response_data = $api_response->getData();
        $status = json_encode($api_response_data);
        print_r($status);
        exit;
    }

    /*     * * Move archiveinsurance to insurance end ** */

    /**
     * Display a move insurance from archive to insurance.
     * params for insurance id, archive insurance id.
     * @return Move insurance form.
     */
    public function getMoveArcInsuranceForm($patid, $arcid) {
        $api_response = $this->getMoveArcInsuranceFormApi($patid, $arcid);
        $api_response_data = $api_response->getData();
        $archiveinsurance = $api_response_data->data->archive_ins;
        $insurances = $api_response_data->data->insurances;
        $patients = $api_response_data->data->patient;
        $category = $api_response_data->data->category;
        $insurancetypes = $api_response_data->data->insurancetypes;
        $primary_ins_id = $api_response_data->data->primary_ins_id;
        $secondary_ins_id = $api_response_data->data->secondary_ins_id;
        $tertiary_ins_id = $api_response_data->data->tertiary_ins_id;
        $workerscomp_ins_id = $api_response_data->data->workerscomp_ins_id;
        $autoaccident_ins_id = $api_response_data->data->autoaccident_ins_id;
        $attorney_ins_id = $api_response_data->data->attorney_ins_id;
        $registration = $api_response_data->data->registration;
        $medical_secondary_list = $api_response_data->data->medical_secondary_list;

        return view('patients/patients/movearchiveinsurance', compact('registration', 'category', 'archiveinsurance', 'primary_ins_id', 'secondary_ins_id', 'tertiary_ins_id', 'workerscomp_ins_id', 'autoaccident_ins_id', 'attorney_ins_id', 'insurances', 'patients', 'insurancetypes', 'medical_secondary_list'));
    }

    public function ajax_loading_demographics($id) {
        $tab = 'demo';

        $api_response = $this->getAjaxdataApi($id, $tab);
        $api_response_data = $api_response->getData();
        $patients = $api_response_data->data->patient;
        $registration = $api_response_data->data->registration;
        $selectbox = $api_response_data->data->selectbox;

        $insurance_concat = [];
        $authorization_concat = [];
        $contact_concat = [];

        $countries = $api_response_data->data->countries;
        $ethnicity = $api_response_data->data->ethnicity;
        $languages = $api_response_data->data->languages;
        $providers = $api_response_data->data->providers;
        $facilities = $api_response_data->data->facilities;
        $country_id = $api_response_data->data->country_id;
        $ethnicity_id = $api_response_data->data->ethnicity_id;
        $language_id = $api_response_data->data->language_id;
        $provider_id = $api_response_data->data->provider_id;
        $facility_id = $api_response_data->data->facility_id;
        $employe_status = $api_response_data->data->employe_status;
        $address_flags = (array) $api_response_data->data->addressFlag;
        $address_flag['pia'] = (array) $address_flags['pia'];
        $address_flag['poa'] = (array) $address_flags['poa'];        
        $practice_user_type = $api_response_data->data->practice_user_type;
        $claims_count = $api_response_data->data->claims_count;
        $emer_last_name = $api_response_data->data->emer_last_name;
        $emer_first_name = $api_response_data->data->emer_first_name;
        $emer_cell_phone = $api_response_data->data->emer_cell_phone;
        $emer_mi_name = $api_response_data->data->emer_mi_name;
        $emer_email = $api_response_data->data->emer_email;
        $patient_alert_note = $api_response_data->data->patient_alert_note;
        $gu_relationship = $api_response_data->data->gu_relationship;
        $gu_first_name = $api_response_data->data->gu_first_name;
        $gu_last_name = $api_response_data->data->gu_last_name;
        $gu_middle_name = $api_response_data->data->gu_middle_name;

        return view('patients/patients/personal-info-ajax', compact('id', 'tab', 'patients', 'countries', 'ethnicity', 'languages', 'providers', 'facilities', 'address_flag', 'registration', 'selectbox', 'country_id', 'ethnicity_id', 'language_id', 'provider_id', 'facility_id', 'employe_status', 'addmore', 'patient_tabs_details', 'patient_tabs_insurance_details', 'patient_tabs_insurance_count', 'practice_user_type', 'claims_count', 'emer_last_name', 'emer_first_name', 'emer_mi_name', 'emer_cell_phone', 'emer_email', 'patient_alert_note', 'gu_relationship', 'gu_first_name', 'gu_last_name', 'gu_middle_name') + compact($insurance_concat) + compact($contact_concat) + compact($authorization_concat));
    }

    /*
     * 	Patient eligibility check module
     */

    public function patient_egbty() {

        $pat_id = 'MQ==';
        $ins_id = '1';

        $this->separator = "*";
        $this->segment_separator = "~";
        $this->hl_count = 1;
        $this->line_count = 1;

        $api_response = $this->getEligibilityApi($pat_id, $ins_id);
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance{0};
        $patients = $api_response_data->data->patients{0};

        // creating ISA header
        $header = $this->create_ISA_segment();

        // creating GS header
        $header .= $this->create_GS_segment();

        // creating ST header
        $header .= $this->create_ST_segment();

        // creating BHT header
        $header .= $this->create_BHT_segment();

        // creating Payer information segment 
        $header .= $this->create_payer_info($insurance);

        // creating Information receiver segment
        $header .= $this->create_information_receiver();

        // creating subscriber information
        $header .= $this->create_subscriber_segment($patients, $insurance);

        // Creating SE header end
        $header .= $this->create_SE_segment();

        // creating GE header end
        $header .= $this->create_GE_segment();

        // creating ISA header end
        $header .= $this->create_IEA_segment();
        $this->writeMultipleSegments($header);
    }

    public function create_ISA_segment() {
        $segment = "ISA" . $this->separator;

        // segment inner data ISA01
        $segment .= "00" . $this->separator;

        // segment authorization data ISA02
        $segment .= "          " . $this->separator;

        // segment inner data ISA03 (00 -> No Security Information)
        $segment .= "00" . $this->separator;

        // segment inner data ISA04
        $segment .= "          " . $this->separator;

        // segment inner data ISA05 (interchange sender ID selected)
        $segment .= "ZZ" . $this->separator;

        // segment inner data ISA06 (Identical to GS02 segment)
        $segment .= "520608         " . $this->separator;

        // segment inner data ISA07 ( Mutually Defined)
        $segment .= "ZZ" . $this->separator;

        // segment inner data ISA08 (Receiver id)
        $segment .= "OFFALLY        " . $this->separator;

        // segment inner data ISA09 (Interchange date)
        $date = date("ymd");
        $segment .= $date . $this->separator;

        // segment inner data ISA10 (Interchange time)
        $time = date("hi");
        $segment .= $time . $this->separator;

        // segment inner data ISA11 (Delimiter)
        $segment .= "^" . $this->separator;

        // segment inner data ISA12 (interchange control version)
        $segment .= "00501" . $this->separator;

        // segment inner data ISA13 (rand number isa and iea)
        $this->iea_02 = rand(100000000, 999999999);
        $segment .= $this->iea_02 . $this->separator;

        // segment inner data ISA14 (interchange acknowledgment)
        $segment .= "0" . $this->separator;

        // segment inner data ISA15 (Production or test)
        $segment .= "T" . $this->separator;

        // segment inner data ISA16 (component element separator)
        $component_separator = "^";
        $segment .= $component_separator . $this->segment_separator;
        return $segment;
    }

    public function create_GS_segment() {
        $segment = "GS" . $this->separator;

        // GS01  function identifier code
        $segment .= "HS" . $this->separator;

        // GS02  Application sender code ISA06
        $segment .= "520608" . $this->separator;

        // GS03  Application receiver code ISA08
        $segment .= "OFFALLY" . $this->separator;

        // GS04  date segment
        $date = date("Ymd");
        $segment .= $date . $this->separator;

        // GS05  time segment
        $time = date("hi");
        $segment .= $time . $this->separator;

        // GS06  group control number
        $segment .= "01" . $this->separator;

        // GS07 Agency code
        $segment .= "X" . $this->separator;

        // GS08 verision identifier
        $segment .= "005010X279A1" . $this->segment_separator;

        $this->line_count = $this->line_count + 1;
        return $segment;
    }

    public function create_GE_segment() {
        $segment = "GE" . $this->separator;

        // GE01 number of transcation
        $segment .= "1" . $this->separator;

        // GE02 group control number
        $segment .= "1" . $this->segment_separator;

        $this->line_count = $this->line_count + 1;
        return $segment;
    }

    public function create_IEA_segment() {
        $segment = "IEA" . $this->separator;
        $segment .= "1" . $this->separator;
        $segment .= $this->iea_02 . $this->segment_separator;

        $this->line_count = $this->line_count + 1;

        return $segment;
    }

    public function create_ST_segment() {

        // Start the segement
        $segment = "ST" . $this->separator;

        // To find transcation type
        $segment .= "270" . $this->separator;

        // setting transcation control number
        $segment .= "0001" . $this->separator;

        // setting transcation control number
        $segment .= "0001" . $this->segment_separator;

        $this->line_count = $this->line_count + 1;
        return $segment;
    }

    public function create_BHT_segment() {
        // BHT segment position two 13 means bht count 05 and  position two 01 means bht count 06
        $segment = "BHT" . $this->separator . "0022" . $this->separator . "13" . $this->separator;

        $segment .= $this->separator;

        // current date in this position
        $segment .= date("Ymd") . $this->separator;

        // current time in this position
        $segment .= date("hi") . $this->separator;

        $segment .= $this->separator . $this->segment_separator;

        $this->line_count = $this->line_count + 1;
        return $segment;
    }

    public function create_payer_info($insurance) {
        /*
         * Payer information level
         */
        $segment = "HL" . $this->separator . $this->hl_count . $this->separator . "" . $this->separator . "20" . $this->separator;   // HL segment 
        $segment .= $this->separator;
        $segment .= $this->separator . $this->segment_separator;
        $this->line_count = $this->line_count + 1;

        /*
         * Payer Information section
         */

        $segment .= "NM1" . $this->separator;

        // Payer information section
        $segment .= "PR" . $this->separator;

        if ($insurance->relationship == 'Self') {
            // Payer type
            $segment .= "2" . $this->separator;

            // Insurance or orgazitation name
            $segment .= $insurance->insurance_details->insurance_name . $this->separator;
            $segment .= $this->separator;
            $segment .= $this->separator;
        } else {
            // Payer type
            $segment .= "1" . $this->separator;

            // Insurance or orgazitation name
            $segment .= $insurance->last_name . $this->separator;
            $segment .= $insurance->first_name . $this->separator;
            $segment .= $insurance->middle_name . $this->separator;
        }

        $segment .= $this->separator;
        $segment .= $this->separator;

        // Payer selection segment
        $segment .= "PI" . $this->separator;

        // Payer id
        $segment .= $insurance->insurance_details->payerid . $this->separator . $this->segment_separator;

        $this->line_count = $this->line_count + 1;
        $this->hl_count = $this->hl_count + 1;
        return $segment;
    }

    public function create_information_receiver() {
        // parent id for reference HL
        $parent_number = $this->hl_count - 1;

        // HL segment for information receiver
        $segment = "HL" . $this->separator . $this->hl_count . $this->separator . $parent_number . $this->separator . "21" . $this->separator;
        $segment .= "1" . $this->separator;
        $segment .= $this->separator . $this->segment_separator;

        $this->line_count = $this->line_count + 1;

        // Receiver information		
        $segment .= "NM1" . $this->separator;

        // identifier code 1p (provider)
        $segment .= "1p" . $this->separator;

        // type qualifier 1 (person)
        $segment .= "1" . $this->separator;

        // last name for provider
        $segment .= "jones" . $this->separator;

        // first name for provider 
        $segment .= "marcus" . $this->separator;

        // middle name for provider
        $segment .= $this->separator;

        // prefix name for provider
        $segment .= $this->separator;

        // suffix name for provider
        $segment .= "Md" . $this->separator;

        // service provider number
        $segment .= "SV" . $this->separator;

        // service provider identification code
        $segment .= "1871519884" . $this->separator . $this->segment_separator;

        // Increment the hirechy level 
        $this->hl_count = $this->hl_count + 1;

        $this->line_count = $this->line_count + 1;
        return $segment;
    }

    public function create_subscriber_segment($patients, $insurance) {
        $segment = "HL" . $this->separator;
        $segment .= "3" . $this->separator;
        $segment .= "2" . $this->separator;
        $segment .= "22" . $this->separator;
        $segment .= "1" . $this->segment_separator;
        $this->line_count = $this->line_count + 1;
        // identifier code
        $segment .= "NM1" . $this->separator;

        // patient or insured details 
        $segment .= "1L" . $this->separator;

        // entity type qulifier
        $segment .= "1" . $this->separator;

        // patient last name 
        $segment .= $patients->last_name . $this->separator;

        // patient first name
        $segment .= $patients->first_name . $this->separator;

        // prefix name
        $segment .= $patients->middle_name . $this->separator;

        $segment .= $this->separator;
        $segment .= $this->separator;

        // member identification code
        $segment .= "MI" . $this->separator;

        // identification code
        $segment .= $insurance->policy_id . $this->segment_separator;
        $this->line_count = $this->line_count + 1;
        return $segment;
    }

    public function create_SE_segment() {
        $this->line_count = $this->line_count + 1;
        $segment = "SE" . $this->separator;
        $segment .= $this->line_count . $this->separator;
        $segment .= "123456" . $this->separator . $this->segment_separator;
        return $segment;
    }

    public function writeMultipleSegments($spReturnValue) {
        $tempString = '';
        $parts = explode("~", $spReturnValue);
        $parts_length = count($parts) - 1;
        for ($i = 0; $i < $parts_length; $i++) {
            $tempString = $parts[$i] . "~";
            $this->writeToFile($tempString, true);
        }
    }

    public function writeToFile($ediLine, $linecount) {
        if (App::environment() == Config::get('siteconfigs.production.claim_server'))
            $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $path_medcubic = public_path() . '/';
        $path = $path_medcubic . 'media/eligibility/';
        //$path = $this->path;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $date = "Y-m-d H:i:s";
        $date = date("Y-m-d H:i:s");
        $day = date('d', strtotime($date));
        $hour = date('H', strtotime($date));
        $minute = date('i', strtotime($date));
        $second = date('s', strtotime($date));
        $myfile = $path . "EDI270_" . $day . "_" . $hour . "_" . $minute . "_" . $second . ".txt";
        ;

        if (!file_exists($myfile))
            fopen($myfile, "w");

        if ($linecount == true)
            $this->totalLines = $this->totalLines + 1;

        $current = file_get_contents($myfile);
        $current .= $ediLine;
        file_put_contents($myfile, $current);
    }

    /*
     * 	Patient eligibility check module
     */

    public function patient_egbty_show() {
        $local_path = public_path('media/eligibility_response/');
        foreach (glob($local_path . '*.txt') as $list) {
            $file_content = file($list);
            $file_full_content = implode('~', $file_content);
            $file_full_content = explode('~', $file_full_content);
            $symb_check = implode('', $file_full_content);
            $first_segment = $file_full_content[0];

            if (count(explode('|', $symb_check)) > 1) {
                $separate = "|";
            } elseif (count(explode('*', $symb_check)) > 1) {
                $separate = "*";
            }

            $spl_symb = explode($separate, $first_segment);
            $spl_separate = $spl_symb[16];
            foreach ($file_full_content as $key => $segment) {
                if (substr($segment, 0, 3) == 'HL' . $separate) {
                    $temp = explode($separate, $segment);
                }
                if (substr($segment, 0, 4) == 'NM1' . $separate) {
                    $temp = explode($separate, $segment);
                    echo "<pre>";
                    print_r($temp);
                }
            }
        }
    }

    /*
     * Patient import function for pms
     * Reading data form excel 
     * Author: 		Selvakumar 
     * Created on: 	22Sep2017 
     */

    public function import_xls_data() {
        //$api_response = $this->getEXLDataApi();
		//$api_response = $this->getEXLDataApiICD();        
        //$api_response = $this->getEXLDataApiDRLord(); - For Dr. Lord Practice
        // $api_response = $this->getEXLDataApiRVU();   // - For Dr. Lord Practice
        $api_response = $this->getEXLDataApiRPGPatients();
        $api_response_data = $api_response->getData();
        $patients = $api_response_data->data->data;
        dd($patients);
    }

    /*
     * Patient SSN validation  
     * Author: 		Selvakumar 
     * Created on: 	06Apr2018 
     */

    public function patientSsnValidation() {
        $api_response = $this->checkPatientSsnValidationApi();
        $api_response_data = $api_response->getData();
        //  dd($api_response_data->ssncount);
        if ($api_response_data->ssncount == 0)
            return json_encode(array('valid' => "true"));
        else
            return json_encode(array('valid' => "false"));
    }

    /*
     * Getting Stored Patient Name and DOB validation  
     * Author: 		Selvakumar 
     * Created on: 	18Apr2018 
     */

    public function patient_check() {
        $api_response = $this->patientCheckApi();
        $api_response_data = $api_response->getData();
        $data['patient_status'] = $api_response_data->avble_status;
        $data['msg'] = $api_response_data->msg;
        return $data;
    }
	
	
	/* Patient Uploaded Listing */
	
	public function getUploadedPatient(){
        $total_rec = 1; 
        $ClaimController  = new ClaimControllerV1();   
        $search_fields_data = $ClaimController->generateSearchPageLoad('uploadedListing');
        $searchUserData = $search_fields_data['searchUserData']; 
        $search_fields = $search_fields_data['search_fields']; 
        return view('patients/patients/uploadedpatient', compact('total_rec', 'search_fields', 'searchUserData'));
	}
	
	
	public function getUploadedPatientAjax(){
        $api_response = $this->getUploadedPatientApiAjax();
        $api_response_data = $api_response->getData();
        $uploadInfo = (array) $api_response_data->data->uploadInfo;
        $view_html = Response::view('patients/patients/uploadedpatient_list', compact('uploadInfo'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['order'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->pagination_count;
        $data['recordsFiltered'] = $api_response_data->data->pagination_count;

        return Response::json($data);
	}
	
	public function uploaded_patient(){
		$request = Request::all();
		if(Input::file('filefield')){
			$file = Input::file('filefield');
			$dataArr['org_filename'] = $file->getClientOriginalName();
			$md5Name = md5_file($file->getRealPath()).time();
			$dataArr['file_name'] = $md5Name.".csv";
			$dataArr['total_patients'] = 0;
			$dataArr['msg'] = $request['msg'];
			$dataArr['created_by'] = Auth::user()->id;
			$guessExtension = $file->guessExtension();		  
			$destinationPath = storage_path().'/uploadPatient';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
			$file->move($destinationPath,$md5Name.".csv");
			///$resp = $this->getEXLDataApiRVU($md5Name.".csv");
			Uploadpatient::create($dataArr);            
		}
		return Redirect::to('/uploadedpatients');
	}
	
	public function getUploadedFile($id){
        if(!is_numeric($id))
            $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');

		$downloadLink = Uploadpatient::where('id',$id)->first();
        if(!empty($downloadLink)) {
            $docFile = storage_path()."/uploadPatient/".$downloadLink->resp_filename;
            $file_name = $downloadLink['file_name'];
            if ($file_name != '' && File::exists($docFile)) {
    		      $downloadPath = storage_path()."/uploadPatient/".$downloadLink->file_name;
                return response()->download($downloadPath,$downloadLink->org_filename);
            } else {
                 echo "<h1>File Not Exists</h1>";     
            }   
        } else {
            echo "<h1>Invalid ID</h1>";
        }
	}

    public function getUploadedResponseFile($id){
        if(!is_numeric($id))
            $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');

        $downloadLink = Uploadpatient::where('id',$id)->first();
        if(!empty($downloadLink)) {
            $docFile = storage_path()."/uploadPatient/".$downloadLink->resp_filename;
            $file_name = $downloadLink['resp_filename'];
            if ($file_name != '' && File::exists($docFile)) {
                $downloadPath = storage_path()."/uploadPatient/".$downloadLink->resp_filename;
                return response()->download($downloadPath,$downloadLink->org_filename);
            } else {
                echo "<h1>File Not Exists</h1>";   
            }
        } else {
            echo "<h1>Invalid ID</h1>";
        }
    }

    public function processUploadedFile($id){
        $api_response = $this->processUploadedSheet($id); 
        $api_response_data = $api_response->getData();
        // print_r($api_response);exit;
        if ($api_response_data->status == 'failure') {
            return Redirect::to('uploadedpatients')->with('message', $api_response_data->message);
        }        
        return Redirect::to('uploadedpatients')->with('success', $api_response_data->message);
    }

    public function getUploadStatus() {
        $statusList = Uploadpatient::where('deleted_at', NULL)->orderBy('id', 'desc')->take(5)
                    ->select('id', 'total_patients', 'completed_patients', 'status')->get();
        $status = json_encode($statusList);
        print_r($status);
        exit;
    }
	
    // Download upload patient template file.
    public function getDownloadTemplateFile(){    
        $file_name = 'medcubics_upload_template.csv';
        $docFile = storage_path()."/uploadPatient/".$file_name;        
        if ($file_name != '' && File::exists($docFile)) {
            $downloadPath = $docFile;
            return response()->download($downloadPath,$file_name);
        } else {
            echo "<h1>File Not Exists</h1>";
        }
    }
	/* Patient Uploaded Listing */

}