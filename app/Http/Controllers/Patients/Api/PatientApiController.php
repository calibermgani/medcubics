<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\Patient as Patient;
use App\Models\Country as Country;
use App\Models\Ethnicity as Ethnicity;
use App\Models\Language as Language;
use App\Models\Provider as Provider;
use App\Models\Provider_degree as Provider_degree;
use App\Models\MedicalSecondary as MedicalSecondary;
use App\Models\Facility as Facility;
use App\Models\Registration as Registration;
use App\Models\AddressFlag as AddressFlag;
use App\Models\Insurance as Insurance;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Patients\PatientContact as PatientContact;
use App\Models\Patients\PatientAuthorization as PatientAuthorization;
use App\Models\Patients\PatientInsuranceArchive as PatientInsuranceArchive;
use App\Models\Patients\PatientNote as PatientNote;
use App\Models\Patients\PatientOtherAddress as PatientOtherAddress;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Pos as Pos;
use App\Models\Cpt as Cpt;
use App\Models\Icd as Icd;
use App\Models\Modifier as Modifier;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use App\Models\Insurancetype as InsuranceType;
use App\Models\QuestionnariesAnswer as QuestionnariesAnswer;
use App\Models\QuestionnariesOption as QuestionnariesOption;
use App\Models\Document as Document; // Need For Document table update
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Medcubics\Users as Users;
use App\Models\Employer as Employer;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Models\Payments\ClaimCPTTXDESCV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController as ChargeV1ApiController;
use App\Models\Scheduler\PatientAppointment;
use App\Models\Patients\PatientEligibility;
use App\Models\STMTCategory;
use App\Models\STMTHoldReason;
use App\Models\Uploadpatient;
use DateTime;
use Config;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Session;
use Image;
use App;
use View;
use Lang;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ViewExport;

class PatientApiController extends Controller {

    public $valueArr = array();
    public $patient_count = 0;
    public $prev_key;
    public $primary_insured_name;
    public $secondary_insured_name;
    public $city_state_count = 0;
    public $patientMobile = 0;
    public $address = 0;
    public $patient_name_Arr = array();
    public $uploadData = array();    


    public function getIndexApi($export = "", $typeSearch = '', $appCheck = '') {
       
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
        $practice_timezone = Helpers::getPracticeTimeZone();   
        /* Converting value to default search based */
        //if($export == '') {
            $insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceList();
//        } else {
//            $insurances = App\Models\Patients\PatientInsurance::getAllPatientInsuranceFullNameList();
//        }
        $start = 0;
        $len = 50;
        $search = $searchFor = '';
        $orderByField = 'patients.account_no'; // created_at
        $orderByDir = 'DESC';
        if(isset($export) && !empty($export))
        $orderByDir = 'DESC';
     
        if (count($request) > 0) {
            $start = isset($request['start']) ?  $request['start'] : 0;
            $len = isset($request['length']) ? $request['length'] : 50;
            if (!empty($request['search']['value'])) {
                $search = trim($request['search']['value']);
            }
            if (!empty($request['columns'])) {
                foreach ($request['columns'] as $columns) {
                    if ($columns['searchable'] == 'true') {
                        $searchFor = $columns['data'];
                    }
                }
            }

            if (!empty($request['order'])) {
                $orderByField = (isset($request['order'][0]['column'])) ? $request['order'][0]['column'] : 'percentage';

                switch ($orderByField) {
                    case '0':
                        $orderByField = 'account_no';
                        break;

                    case '1':
                        $orderByField = 'last_name';
                        break;           

                    case '2':
                        $orderByField = 'mobile';
                        break;

                    case '3':
                        $orderByField = 'gender';
                        break;

                    case '4':
                        $orderByField = 'dob';
                        break;
                    
                    case '5':
                        $orderByField = 'ssn';
                        break;
                        
                    case '6':
                        $orderByField = 'percentage';
                        break;

                    case '7':
                        $orderByField = 'total_pat_due';
                        break;

                    case '8':
                        $orderByField = 'total_ins_due';
                        break;

                    case '9':
                        $orderByField = 'total_ar';
                        break;

                  case '10':
                        $orderByField = 'patients.created_at';
                        break; 

                    default:
                        $orderByField = 'patients.account_no';
                        break;
                }
                $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';
            }
        }
        //  $patients = Patient::orderBy('percentage','ASC')->get();             
        //$patient_qry = $patient_qry->leftJoin('claims', 'claims.patient_id', '=', 'patients.id');
        // '1.Acc No','2.Patient Name','3.Gender','4.DOB','5.SSN','6.Payer','7.Patient Due','8.Insurance Due','9.AR Due','10.Created On'                        
        $patient_qry = Patient::where('patients.id', '<>', 0);
        // All Patient checked 
        // @todo replace with new tables
        // $patient_qry->with(['patient_claim_fin']);       
          
        $patient_qry->leftjoin('claim_info_v1', function($join) {
            $join->on('claim_info_v1.patient_id', '=', 'patients.id');
            $join->on('claim_info_v1.status', '<>', DB::raw("'Hold'"));
        });

        $patient_qry->leftjoin(DB::raw("(SELECT
          pmt_claim_fin_v1.patient_id,
          sum(total_charge)-(sum(patient_paid)+sum(insurance_paid) + sum(withheld) + sum(patient_adj) + sum(insurance_adj)) as total_ar,
          SUM(pmt_claim_fin_v1.insurance_due) as total_ins_due,
          SUM(pmt_claim_fin_v1.patient_due) as total_pat_due
          FROM pmt_claim_fin_v1
          WHERE pmt_claim_fin_v1.deleted_at IS NULL
          GROUP BY pmt_claim_fin_v1.patient_id
          ) as pmt_claim_fin_v1"), function($join) {
            $join->on('pmt_claim_fin_v1.patient_id', '=', 'patients.id');
        });

        // Join related ends    
        $patient_qry->Where(function ($patient_qry) use ($search, $searchFor, $insurances) {
            $search = trim($search);
            if (!empty($search)) {
                if ($searchFor == '11') { // For all 
                    $patient_qry = $patient_qry->where('account_no', 'LIKE', '%' . $search . '%')
                            ->orWhere('gender', 'LIKE', $search . '%')
                            ->orWhere('ssn', 'LIKE', '%' . $search . '%');
//                            ->orWhere('total_ar', 'LIKE', '%' . $search . '%')
//                            ->orWhere('total_ins_due', 'LIKE', '%' . $search . '%')
//                            ->orWhere('total_pat_due', 'LIKE', '%' . $search . '%');

                    // To handle comma separted value given in search keyword                   
                    if (strpos(strtolower($search), ",") !== false) {
                        $searchValues = array_filter(explode(",", $search));
                        foreach ($searchValues as $value) {
                            if ($value !== '') {
                                $patient_qry = $patient_qry->orWhere('last_name', 'like', "%{$value}%")
                                        ->orWhere('middle_name', 'like', "%{$value}%")
                                        ->orWhere('first_name', 'like', "%{$value}%")
                                        ->orWhere('title', 'like', "%{$value}%");
                            }
                        }
                    } else {
                        $patient_qry = $patient_qry->orWhere('last_name', 'LIKE', '%' . $search . '%')
                                ->orWhere('middle_name', 'LIKE', '%' . $search . '%')
                                ->orWhere('first_name', 'LIKE', '%' . $search . '%')
                                ->orWhere('title', 'LIKE', '%' . $search . '%');
                    }

                    // For AR due, insurance due, patient due search condition
                    /*
                      $patient_qry->orWhereHas('patient_claim_fin', function($patient_qry) use ($search) {
                      $patient_qry = $patient_qry->whereRaw("sum(insurance_due) like %?% ", [$search])
                      // ->orWhere('sum(patient_due)', 'LIKE', '%' . $search . '%')
                      ->orwhereRaw("sum(insurance_due) like %?% ", [$search]);
                      });
                     */

                    if (strpos(strtolower($search), "/") !== false) {
                        // if date string given then check with DOB and created at date                         
                        $dateSearch = date("Y-m-d", strtotime(@$search));
                        $patient_qry = $patient_qry->orWhere('patients.created_at', 'LIKE', '%' . $dateSearch . '%');
                        $patient_qry = $patient_qry->orWhere('dob', 'LIKE', '%' . $dateSearch . '%');
                    } else {
                        $patient_qry = $patient_qry->orWhere('patients.created_at', 'LIKE', '%' . $search . '%');
                        $patient_qry = $patient_qry->orWhere('dob', 'LIKE', '%' . $search . '%');
                    }

                    if (strpos(strtolower($search), "self") !== false) {
                        $patient_qry = $patient_qry->orWhere('is_self_pay', '=', 'Yes');
                    } else {
                        if (!empty($insurances['all'])) {
                            $result = array_filter($insurances['all'], function ($item) use ($search) {
                                if (stripos($item, $search) !== false) {
                                    return true;
                                }
                                return false;
                            });
                            $patIds = array_keys($result);
                            if (!empty($patIds)) {
                                $patient_qry = $patient_qry->orWhere(function($query) use ($patIds) {
                                    $query = $query->whereIn('patients.id', $patIds)->Where('is_self_pay', '=', 'No');
                                });
                            } else {                                
                                $query = $query->where('patients.id', 0);
                            }
                        }
                    }
                } else {
                    //'1.Acc No','2.Patient Name','3.Gender','4.DOB','5.SSN','6.Payer','7.Patient Due','8.Insurance Due','9.AR Due','10.Created On' 
                    if ($searchFor == '0') { // account no search
                        $patient_qry = $patient_qry->where('account_no', 'LIKE', '%' . $search . '%');
                    } elseif ($searchFor == '1') { // patient name search
                        // To handle comma separted value given in search keyword
                        if (strpos(strtolower($search), ",") !== false) {
                            $searchValues = array_filter(explode(",", $search));
                            foreach ($searchValues as $value) {
                                if ($value !== '') {
                                    $patient_qry = $patient_qry->orWhere('last_name', 'like', "%{$value}%")
                                            ->orWhere('middle_name', 'like', "%{$value}%")
                                            ->orWhere('first_name', 'like', "%{$value}%");
                                }
                            }
                        } else {
                            $patient_qry = $patient_qry->where('last_name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('middle_name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('first_name', 'LIKE', '%' . $search . '%')
                                    ->orWhere('title', 'LIKE', '%' . $search . '%');
                        }
                    } elseif ($searchFor == '2') { //  gender search
                        $patient_qry = $patient_qry->orWhere('gender', '=', $search);
                    } elseif ($searchFor == '3') { // date of birth search                          
                        $dateSearch = date("Y-m-d", strtotime(@$search));
                        $patient_qry = $patient_qry->orWhere('dob', 'LIKE', '%' . $dateSearch . '%');
                    } elseif ($searchFor == '4') {  // ssn search
                        $patient_qry = $patient_qry->orWhere('ssn', 'LIKE', '%' . $search . '%');
                    } elseif ($searchFor == '5') { // Insurance checking condition
                        if (strpos(strtolower($search), "self") !== false) {
                            $patient_qry = $patient_qry->orWhere('is_self_pay', '=', 'Yes');
                        } else {                           
                            if (!empty($insurances['all'])) {
                                $result = array_filter($insurances['all'], function ($item) use ($search) {
                                    if (stripos($item, $search) !== false) {
                                        return true;
                                    }
                                    return false;
                                });
                                $patIds = array_keys(@$result);
                                if (!empty($patIds)) {
                                    $patient_qry = $patient_qry->Where('is_self_pay', '=', 'No')->whereIn('patients.id', $patIds);
                                } else {
                                    $patient_qry = $patient_qry->where('patients.id', 0);
                                }
                            }
                        }
                    } elseif ($searchFor == '6' OR $searchFor == '7' OR $searchFor == '8') { // Patient due, insurance due, AR due checking
                        $patient_qry = $patient_qry->WhereExists(function($query) use ($search, $searchFor) {

//                            if ($searchFor == '6')
//                                $patient_qry = $patient_qry->Where('total_pat_due', 'LIKE', '%' . $search . '%');
//                            if ($searchFor == '7')
//                                $patient_qry = $patient_qry->Where('total_ins_due', 'LIKE', '%' . $search . '%');
//                            if ($searchFor == '8')
//                                $patient_qry = $patient_qry->Where('total_ar', 'LIKE', '%' . $search . '%');

                            /*
                              $patient_qry->orWhereHas('patient_claim_fin', function($patient_qry) use ($search) {
                              if ($searchFor == '6')
                              $patient_qry = $patient_qry->Where('total_pat_due', 'LIKE', '%' . $search . '%');
                              if ($searchFor == '7')
                              $patient_qry = $patient_qry->Where('total_ins_due', 'LIKE', '%' . $search . '%');
                              if ($searchFor == '8')
                              $patient_qry = $patient_qry->Where('total_ar', 'LIKE', '%' . $search . '%');
                              });
                              $query = $query->where('status', '<>', 'Hold');
                             */
                        });
                    } elseif ($searchFor == '9') { // created date searching                        
                        $dateSearch = date("Y-m-d", strtotime(@$search));
                        $patient_qry = $patient_qry->where('patients.created_at', 'LIKE', '%' . $dateSearch . '%');
                    }
                }
            }
        });
        // 
        $patient_qry = $patient_qry->selectRaw('DISTINCT(patients.id), total_ar, total_ins_due, total_pat_due, patients.*');
        /*
         * Patient search option Default optionClaim count 0, patient create date,  
         */
        /* Start for patients listing by Thilagavathy */
        if(!empty(json_decode(@$request['dataArr']['data']['ssn']))){
            $ssn = trim(json_decode(@$request['dataArr']['data']['ssn']));
            $patient_qry->where('patients.ssn', 'LIKE', '%' . $ssn . '%');
        } 

        if(!empty(json_decode(@$request['dataArr']['data']['patient_name']))){
            $dynamic_name = trim(json_decode($request['dataArr']['data']['patient_name']));
			$dynamic_name = str_replace("'",'',$dynamic_name);
			$dynamic_name = str_replace('"','',$dynamic_name);
            $patient_qry->Where(function ($query) use ($dynamic_name) {
                if (strpos($dynamic_name, ",") !== false) {
                    $nameArr = explode(",", $dynamic_name);
                    $temp = explode(" ", @trim($nameArr[1]));
                    $nameStr = trim($nameArr[0]);
                    if(isset($temp[0])) 
                        $nameStr =$nameStr.", ".trim($temp[0]).((isset($temp[1])) ? " ".trim($temp[1]) :'');
                    $patient_qry = $query->orWhere(DB::raw('CONCAT(patients.last_name,", ", patients.first_name, " ", patients.middle_name)'),  'like', "%{$nameStr}%" );
                } else {
                    // Patient name search
                    $patient_qry = $query->orWhere(function ($query) use ($dynamic_name) {
                        $sub_sql = '';
                        $searchValues = array_filter(explode(" ", $dynamic_name));
                        if(isset($searchValues[2])){
                            $nameStr =trim(@$searchValues[0]).", ".trim(@$searchValues[1])." ".trim(@$searchValues[2]);
                            $patient_qry = $query->orWhere(DB::raw('CONCAT(patients.last_name,", ", patients.first_name, " ", patients.middle_name)'),  'like', "%{$nameStr}%" );
                        } else {
                            $sub_sql = '';
                            foreach ($searchValues as $searchKey) {
                                $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                                $sub_sql .= "patients.last_name LIKE '%$searchKey%' OR patients.first_name LIKE '%$searchKey%' OR patients.middle_name LIKE '%$searchKey%'";
                            }
                            if ($sub_sql != '')
                                $query->whereRaw($sub_sql);
                        }
                    });
                }
            });
        }
        if(!empty(json_decode(@$request['dataArr']['data']['account_no']))){
            $account_num =  trim(json_decode($request['dataArr']['data']['account_no']));
            $patient_qry->where('patients.account_no', 'LIKE', '%' . $account_num . '%');
        }
       
        if(!empty(json_decode(@$request['dataArr']['data']['gender']))){
            $gender =json_decode(@$request['dataArr']['data']['gender']);// trim(json_decode(@$request['dataArr']['data']['gender']));            
            if(is_array($gender)){               
                $patient_qry->whereIn('patients.gender', $gender);                
            }else{                
                $patient_qry->where('patients.gender', json_decode(@$request['dataArr']['data']['gender']));
            }
        }
        // Drop down list is not showing issue MEDV2- 612 Thilagavathy
        if(!empty(json_decode(@$request['dataArr']['data']['payer']))){

            $patient_qry->Where(function ($patient_qry) use ($insurances, $request) {
                $search = json_decode(@$request['dataArr']['data']['payer']);            
                $search_name = [];
                $result = [];
                if(in_Array('0',$search)){
                    if(count($search) > 0){
                        $search = array_diff_key($search, ["0"]);
                        foreach($search as $value){
                            $search_name[] = Insurance::getInsuranceshortName($value);
                        }
                        foreach($search_name as $search){
                            $result[] = array_filter($insurances['all'], function ($item) use ($search) {
                                if (stripos($item, $search) !== false) {
                                    return true;
                                }
                                return false;
                            });
                        }
                        $singleArray = [];
                        foreach ($result as $childArray){
                            foreach($childArray as $key=>$test){
                                $singleArray[$key] = $test;
                            }
                        }
                        $patIds = array_keys($singleArray);
                        if (!empty($patIds) && COUNT($patIds) > 0) {
                            $patient_qry = $patient_qry->Where(function($query) use ($patIds) {
                                $query = $query->whereIn('patients.id', $patIds);
                            });
                        }
                        $patient_qry = $patient_qry->orwhere('is_self_pay', '=', 'Yes'); 
                    }else{
                        $patient_qry = $patient_qry->Where('is_self_pay', '=', 'Yes'); 
                    }
                }else{
                    $patient_qry = $patient_qry->Where('is_self_pay', '=', 'No');
                    $search = json_decode(@$request['dataArr']['data']['payer']);
                    foreach($search as $value){
                        $search_name[] = Insurance::getInsuranceshortName($value);                    
                    }
                    foreach($search_name as $search){
                        $result[] = array_filter($insurances['all'], function ($item) use ($search) {
                            if (stripos($item, $search) !== false) {
                                return true;
                            }
                            return false;
                        });
                    }
                    $singleArray = [];
                    foreach ($result as $childArray){
                        foreach($childArray as $key=>$test){
                            $singleArray[$key] = $test;
                        }
                    }
                    $patIds = array_keys($singleArray);
                    if (!empty($patIds)) {
                        $patient_qry = $patient_qry->Where(function($query) use ($patIds) {
                            $query = $query->whereIn('patients.id', $patIds)->Where('is_self_pay', '=', 'No');
                        });
                    } else {
                        $patient_qry = $patient_qry->where('patients.id', 0);
                    }
                }
            });
        }
        
        if(isset($request['dataArr']['data']['pat_due'])) {
            $pat_due = json_decode(@$request['dataArr']['data']['pat_due']);
            $pat_due_con = '=';
            if(preg_match('/</', $pat_due)){
                $exp = explode('<',$pat_due);
                $pat_due_con = '<=';
                $pat_due = $exp[1];
            }
            if(preg_match('/>/', $pat_due)){
                $exp = explode('>',$pat_due);
                $pat_due_con = '>=';
                $pat_due = $exp[1];
            }
            if($pat_due !== '')
                $patient_qry = $patient_qry->where('total_pat_due', $pat_due_con,$pat_due);
        }
        
        if(isset($request['dataArr']['data']['ins_due'])) {
            $ins_due = json_decode(@$request['dataArr']['data']['ins_due']);
            $ins_due_con = '=';
            if(preg_match('/</', $ins_due)){
                $exp = explode('<',$ins_due);
                $ins_due_con = '<=';
                $ins_due = $exp[1];
            }
            if(preg_match('/>/', $ins_due)){
                $exp = explode('>',$ins_due);
                $ins_due_con = '>=';
                $ins_due = $exp[1];
            }
            if($ins_due !== '')
                $patient_qry = $patient_qry->where('total_ins_due', $ins_due_con,$ins_due);
        }
        
        if(isset($request['dataArr']['data']['ar_due'])) {
            $ar_due = json_decode(@$request['dataArr']['data']['ar_due']);
            $ar_due_con = '=';
            if(preg_match('/</', $ar_due)){
                $exp = explode('<',$ar_due);
                $ar_due_con = '<=';
                $ar_due = $exp[1];
            }
            if(preg_match('/>/', $ar_due)){
                $exp = explode('>',$ar_due);
                $ar_due_con = '>=';
                $ar_due = $exp[1];
            }
            if($ar_due !== '')
                $patient_qry = $patient_qry->where('total_ar', $ar_due_con,$ar_due);
        }
        
//        if(!empty(json_decode(@$request['dataArr']['data']['pat_due']))){
//             $search = json_decode(@$request['dataArr']['data']['pat_due']);
//            $patient_qry = $patient_qry->Where('total_pat_due', 'LIKE', '%' . $search . '%');
//        }
//        if(!empty(json_decode(@$request['dataArr']['data']['ins_due']))){
//             $search = json_decode(@$request['dataArr']['data']['ins_due']);
//            $patient_qry = $patient_qry->Where('total_ins_due', 'LIKE', '%' . $search . '%');
//        }
//        if(!empty(json_decode(@$request['dataArr']['data']['ar_due']))){
//            $search = json_decode(@$request['dataArr']['data']['ar_due']);
//            $patient_qry = $patient_qry->Where('total_ar', 'LIKE', '%' . $search . '%');
//        }
        if(!empty(json_decode(@$request['dataArr']['data']['mobile']))){
            $search = trim(json_decode(@$request['dataArr']['data']['mobile']));
            $patient_qry = $patient_qry->Where('mobile', 'LIKE', '%' . $search . '%');
        }

        //for select option 
        if(!empty(json_decode(@$request['dataArr']['data']['patient_type']))){          
            $patient_type = trim(json_decode(@$request['dataArr']['data']['patient_type']));
            if($patient_type == 'App'){
                 $patient_qry->where('patient_from','app');                
            }else if($patient_type == 'New'){
                $createdDate = date('Y-m-d');
                $patient_qry->where('claim_count', '=', 0)->whereRaw("patients.created_at >= $createdDate");
            } else {
                 $patient_qry->whereIn('patient_from', ['app', 'web']);
            }                                       
                 
        }else{ //for default listing
            if ($export == "") {
                $createdDate = date('Y-m-d');
                $patient_qry->where('claim_count', '=', 0)->whereRaw("patients.created_at >= $createdDate");
            }
        }
       
      // print_r(@$request['dataArr']['data']);
      if(!empty(json_decode(@$request['dataArr']['data']['dob_search']))){
            $date = explode('-',json_decode($request['dataArr']['data']['dob_search']));
          
            $from = date("Y-m-d", strtotime($date[0]));
            if($from == '1970-01-01'){
                $from = '0000-00-00';
            }

            $to = date("Y-m-d", strtotime($date[1]));         
            $patient_qry->where(DB::raw('DATE(patients.dob)'),'>=',$from)->where(DB::raw('DATE(patients.dob)'),'<=',$to);        
        }

       if(!empty(json_decode(@$request['dataArr']['data']['created_on']))){
            $date = explode('-',json_decode($request['dataArr']['data']['created_on']));          
            $from = date("Y-m-d", strtotime($date[0]));
            if($from == '1970-01-01'){
                $from = '0000-00-00';
            }           
            $to = date("Y-m-d", strtotime($date[1]));           
            $patient_qry->whereRaw("DATE(CONVERT_TZ(patients.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(patients.created_at,'UTC','".$practice_timezone."')) <= '".$to."'"); 
        }
        /* end for patients listing by Thilagavathy */

        /* @todo need to check and remove since not going to use anymore
        $createdDate = date('Y-m-d');
        $patient_qry->where('claim_count', '=', 0)->whereRaw("patients.created_at >= $createdDate");
        if (($typeSearch != 'all') && ($appCheck != 'app')) {
            $createdDate = date('Y-m-d');
            $patient_qry->where('claim_count', '=', 0)->whereRaw("patients.created_at >= $createdDate"); //orNotWhere('created_at','>',$createdDate);
        } elseif (($typeSearch != 'all') && ($appCheck == 'app')) {
            $patient_qry->where('patient_from', $appCheck);
        } elseif (($typeSearch == 'all') && ($appCheck == 'app')) {
            $patient_qry->whereIn('patient_from', ['app', 'web']);
        }
        */
        $count = $patient_qry->count(DB::raw('DISTINCT(patients.id)'));
        if ($export == "") {
            $patient_qry = $patient_qry->groupBy('patients.id')->skip($start)->take($len);
        } else {
            $patient_qry = $patient_qry->groupBy('patients.id');
        }
        
        $patients = $patient_qry->orderBy($orderByField, $orderByDir)->get();

        /* if ($export != "") {
          $exportparam = array(
          'filename' => 'Patients',
          'heading' => 'Patients',
          'fields' => array(
          'account_no' => 'Acc No.',
          'Patient Name' => array(
          'table' => '', 'column' => ['last_name', 'first_name'], 'label' => 'Patient Name'),
          'gender' => 'Gender',
          'dob' => 'DOB',
          'ssn' => 'SSN',
          'Insurance Name' => array(
          'table' => '', 'column' => 'id', 'use_function' => ['App\Models\Patients\PatientInsurance', 'CheckAndReturnInsuranceName'], 'label' => 'Payer'),
          'tot_patient_due' => 'Pat Due',
          'tot_insurance_due' => 'Ins Due',
          'tot_balance_amt' => 'AR Due',
          /* 'PatientDue'   => array(
          'table'=>'','column' => 'id', 'use_function' => ['App\Models\Patients\Patient','getPatientAR','patient_due'], 'label' => 'Patient Due'),
          'InsuranceDue'    => array(
          'table'=>'','column' => 'id', 'use_function' => ['App\Models\Patients\Patient','getInsuranceAR','insurance_ar'], 'label' => 'Insurance Due'),
          'ARDue'           => array(
          'table'=>'','column' => 'id', 'use_function' => ['App\Models\Patients\Patient','getPatienttabARData','total_ar'], 'label' => 'AR Due'),
          'created_at' => 'Created On',
          'status' => 'Status',
          'percentage' => '%',
          )
          );

          $callexport = new CommonExportApiController();
          return $callexport->generatemultipleExports($exportparam, $patients, $export);
          } */
         $ClaimController  = new ClaimControllerV1();   
         $search_fields_data = $ClaimController->generateSearchPageLoad('patients_listing');
         $searchUserData = $search_fields_data['searchUserData']; 
         $search_fields = $search_fields_data['search_fields'];
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patients', 'count', 'insurances','search_fields','searchUserData')));
    }

    public function getPatientStmtCateogyDetails($cat_id) {
        $stmtCatDetails = STMTCategory::where('status', 'Active')->where('id', $cat_id)->first()->toArray();    
        if(isset($stmtCatDetails['hold_release_date'])){
            if($stmtCatDetails['hold_release_date'] != "0000-00-00")
                $stmtCatDetails['hold_release_date'] =  date('m/d/Y', strtotime(@$stmtCatDetails['hold_release_date']));
            else     
                $stmtCatDetails['hold_release_date'] = "";
        }
        return Response::json(array('data' => compact('stmtCatDetails')));
    }

    public function getCreateApi() {        
        $countries = Country::orderBy('name', 'asc')->pluck('name', 'id')->all(); /// Get Country  List
        $ethnicity = Ethnicity::orderBy('name', 'asc')->pluck('name', 'id')->all(); /// Get Ethinity List
        $languages = Language::where('language', '!=', '')->orderBy('language', 'ASC')->pluck('language', 'id')->all(); /// Get Language List
        $providers = Provider::getBillingAndRenderingProvider('yes'); /// Get Provider List
        $referringProviders = Provider::getReferringProviderList(); /// Get Referring provider List
        $facilities = Facility::where('status', 'Active')->orderBy('facility_name', 'asc')->pluck('facility_name', 'id')->all(); /// Get Facility List
        
        // Get statement category list
        $stmt_category = STMTCategory::where('status', 'Active')->pluck('category','id')->all();
        $stmt_holdreason = STMTHoldReason::where('status', 'Active')->pluck('hold_reason','id')->all();
        /// Get address for usps ///
        $addressFlag['pia']['address1'] = '';
        $addressFlag['pia']['city'] = '';
        $addressFlag['pia']['state'] = '';
        $addressFlag['pia']['zip5'] = '';
        $addressFlag['pia']['zip4'] = '';
        $addressFlag['pia']['is_address_match'] = '';
        $addressFlag['pia']['error_message'] = '';
        $addressFlag['poa']['address1'] = '';
        $addressFlag['poa']['city'] = '';
        $addressFlag['poa']['state'] = '';
        $addressFlag['poa']['zip5'] = '';
        $addressFlag['poa']['zip4'] = '';
        $addressFlag['poa']['is_address_match'] = '';
        $addressFlag['poa']['error_message'] = '';

        /// Get dynamic fields to display in form ///
        $registration = '';
        $selectbox = [];
        if (Registration::count()) {
            $registration = Registration::first();
            if ($registration != '') {
                if ($registration->guarantor == 1)
                    $selectbox['Guarantor'] = 'Guarantor';
                if ($registration->emergency_contact == 1)
                    $selectbox['Emergency Contact'] = 'Emergency Contact';
                if ($registration->employer == 1)
                    $selectbox['Employer'] = 'Employer';
            }
        }

        $country_id = 215;
        $ethnicity_id = $language_id = $provider_id = $facility_id = $referring_provider_id = '';
        $employe_status = 'Unknown';

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('countries', 'ethnicity', 'languages', 'providers','referringProviders', 'facilities', 'addressFlag', 'registration', 'selectbox', 'country_id', 'ethnicity_id', 'language_id', 'provider_id', 'facility_id', 'referring_provider_id', 'employe_status', 'stmt_category', 'stmt_holdreason')));
    }

    public function getStoreApi($id = '') {
        $patient_id = '';
        if ($id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');

        $request = Request::all();
        unset($request['age']);

        if ($request['dob'] != "1901-01-01" && $request['dob'] != "" && $request['ssn'] != "") {
            Validator::extend('chk_ssn_dob_unique', function($attribute, $value, $parameters) {
                $ssn_un = Input::get($parameters[0]);
                $dob_un = date('Y-m-d', strtotime(Input::get($parameters[1])));
                $patient_id_un = $parameters[2];
                if ($patient_id_un != '')
                    $count = Patient::where('ssn', $ssn_un)->where('id', '!=', $patient_id_un)->count();
                else
                    $count = Patient::where('ssn', $ssn_un)->count();
                if ($count > 0)
                    return false;
                else
                    return true;
            });
            $rules = Patient::$rules + array('ssn' => 'chk_ssn_dob_unique:ssn,dob,' . @$patient_id, 'medical_chart_no' => 'nullable|unique:patients,medical_chart_no,' . @$patient_id. ',id,deleted_at,NULL');
        } elseif ($request['dob'] == "" && $request['ssn'] != "") {
            Validator::extend('chk_ssn_dob_unique', function($attribute, $value, $parameters) {
                $ssn_un = Input::get($parameters[0]);
                $dob_un = date('Y-m-d', strtotime(Input::get($parameters[1])));
                $patient_id_un = $parameters[2];
                if ($patient_id_un != '')
                    $count = Patient::where('ssn', $ssn_un)->where('id', '!=', $patient_id_un)->count();
                else
                    $count = Patient::where('ssn', $ssn_un)->count();
                if ($count > 0)
                    return false;
                else
                    return true;
            });
            $rules = Patient::$rules + array('ssn' => 'chk_ssn_dob_unique:ssn,dob,' . @$patient_id, 'medical_chart_no' => 'nullable|unique:patients,medical_chart_no,' . @$patient_id. ',id,deleted_at,NULL');
        } else {
            if(!empty($request['medical_chart_no'])){
                $rules = Patient::$rules + array('medical_chart_no' => 'nullable|unique:patients,medical_chart_no,' . @$patient_id .',id,deleted_at,NULL');
            }
            else{
                $rules = [];
            }
        }
        $validator = Validator::make($request, $rules + array('filefield' => Config::get('siteconfigs.customer_image.defult_image_size')), Patient::$messages + array('filefield.mimes' => Config::get('siteconfigs.customer_image.defult_image_message')));

        if ($request['dob'] == "" || $request['dob'] == "01/01/1901")
            $request['dob'] = "1901-01-01";
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => $id));
        } else {
            if ($request['dob'] != "1901-01-01")
                $request['dob'] = date('Y-m-d', strtotime($request['dob']));

            if (($request['deceased_date'] != "0000-00-00") && ($request['deceased_date'] != ""))
                $request['deceased_date'] = date('Y-m-d', strtotime($request['deceased_date']));

            // Statement set as hold then only handle hold reason and hold release date values
            if ($request['statements'] == 'Hold' && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
                $request['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
            } else {
                $request['hold_release_date'] = "0000-00-00";
            }

            if ($request['statements'] != 'Hold'){
                $request['hold_reason'] = 0;
            }

            if ($request['employment_status'] != "Employed" && $request['employment_status'] != "Self Employed") {
                $request['organization_name'] = "";
                $request['occupation'] = "";
            }
            
            if ($request['employment_status'] != "Student") {
                $request['student_status'] = "Unknown";
            }
           

            if ($patient_id == '') { 

                $request['percentage'] = '60';
                $request['created_by'] = Auth::user()->id;
                $request['is_self_pay'] = 'Yes';
                $request['demo_percentage'] = '60';              
                $result = Patient::create($request);
                if ($request['employment_status'] != '') {

                    if ($request['employment_status'] == "Employed" || $request['employment_status'] == "Self Employed") {
                        $def_emp_arr = array('patient_id' => $result->id, 'category' => 'Employer', 'employer_status' => $request['employment_status'], 'employer_occupation' => @$request['occupation'], 'employer_name' => $request['employer_name'], 'employer_work_phone' => $request['work_phone'], 'employer_phone_ext' => $request['work_phone_ext']);                        
                    } elseif($request['employment_status'] == "Retired" || $request['employment_status'] == "Unknown"){
                        $def_emp_arr = array('patient_id' => $result->id, 'category' => 'Employer', 'employer_status' => $request['employment_status']);
                    } else {
                        $def_emp_arr = array('patient_id' => $result->id, 'category' => 'Employer', 'employer_status' => $request['employment_status'],'employer_work_phone' => $request['work_phone'], 'employer_phone_ext' => $request['work_phone_ext']);
                    }
                    $def_emp_arr['exist_emp_id'] = @$request['exist_emp_id'] ? $request['exist_emp_id'] : '';
                    $this->check_create_def_employer($def_emp_arr); //created default employer
                }
                if (isset($request['guarantor_last_name']) && isset($request['guarantor_first_name'])) {
                    $def_gua_arr = array('patient_id' => $result->id, 'category' => 'Guarantor', 'guarantor_last_name' => @$request['guarantor_last_name'], 'guarantor_first_name' => @$request['guarantor_first_name'], 'guarantor_middle_name' => @$request['guarantor_middle_name'], 'guarantor_relationship' => @$request['guarantor_relationship']);

                    $this->check_create_def_guarantor($def_gua_arr); //created default guarantor
                }
                if ($request['emer_last_name'] != "" && $request['emer_first_name'] != "") {
                    $emr_cnt_arr = array('patient_id' => $result->id, 'category' => 'Emergency Contact', 'emergency_last_name' => @$request['emer_last_name'], 'emergency_first_name' => @$request['emer_first_name'], 'emergency_cell_phone' => @$request['emer_cell_phone'], 'emergency_email' => @$request['emer_email'], 'emergency_relationship' => @$request['emergency_relationship'], 'emergency_middle_name' => @$request['emer_middle_name']);
                    $this->check_create_def_emergency($emr_cnt_arr); //created emergency contact 
                }
                if (trim($request['patient_alert_note']) != "") {
                    PatientNote::insert(['title' => 'Patient alert notes', 'notes_type' => 'patient', 'patient_notes_type' => 'alert_notes', 'notes_type_id' => $result->id, 'content' => $request['patient_alert_note'], 'created_by' => Auth::user()->id, 'created_at' => Date('Y-m-d H:i:s')]);
                }
                if (Input::hasFile('filefield') || $request['webcam_image'] == 1) {
                    $filename = rand(11111, 99999);
                    $extension = Input::hasFile('filefield') ? Input::file('filefield')->getClientOriginalExtension() : '.jpg';
                    $filestoreName = $filename . '.' . $extension;
                    $resize = array('150', '150');

                    if (Input::hasFile('filefield') && $request['upload_type'] != 'webcam') {
                        $image = Input::file('filefield');
                        Helpers::mediauploadpath('', 'patient', $image, $resize, $filestoreName);
                    } elseif ($request['webcam_image'] == 1 && !empty($request['webcam_filename'])) {
                        $default_view = Config::get('siteconfigs.production.defult_production');
                        if (App::environment() == $default_view)
                            $image_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
                        else
                            $image_path = public_path() . '/';
                        $src = $image_path . '/media/patient/' . Auth::user()->id . '/' . $request['webcam_filename'];
                        $image = Input::file($src);
                        Helpers::mediauploadpath('', 'patient', $image, $resize, $filestoreName);
                    }

                    $result->avatar_name = $filename;
                    $result->avatar_ext = $extension;
                }
                $result->account_no = $this->create_patient_accno($result->id); //create patient account number
                $result->save();

                if (isset($request['temp_doc_id']) && $request['temp_doc_id'] != "") {
                    Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $result->id, 'temp_type_id' => '']);
                }

                /// Starts - Personal info address flag update ///
                $address_flag = array();
                $address_flag['type'] = 'patients';
                $address_flag['type_id'] = $result->id;
                $address_flag['type_category'] = 'personal_info_address';
                $address_flag['address2'] = $request['pia_address1'];
                $address_flag['city'] = $request['pia_city'];
                $address_flag['state'] = $request['pia_state'];
                $address_flag['zip5'] = $request['pia_zip5'];
                $address_flag['zip4'] = $request['pia_zip4'];
                $address_flag['is_address_match'] = $request['pia_is_address_match'];
                $address_flag['error_message'] = $request['pia_error_message'];
                AddressFlag::checkAndInsertAddressFlag($address_flag);

                $patient_id = Helpers::getEncodeAndDecodeOfId($result->id, 'encode');
                
                ### if Other Address of patient create other address record
                if ($request['send_statement_to'] != "Patient Address") {
                    $pat_other_address = [];

                    $pat_other_address['patient_id'] = $result->id;
                    $pat_other_address['address1'] = $request['other_address1'];
                    $pat_other_address['address2'] = $request['other_address2'];
                    $pat_other_address['city'] = $request['other_city'];
                    $pat_other_address['state'] = $request['other_state'];
                    $pat_other_address['zip5'] = $request['other_zip5'];
                    $pat_other_address['zip4'] = $request['other_zip4'];
                    $pat_other_address['status'] = 'Active';                   
                    $results = PatientOtherAddress::create($pat_other_address);

                   /// Starts - Personal info Other address flag update ///
                    $Oth_address_flag = array();
                    $Oth_address_flag['type'] = 'patients';
                    $Oth_address_flag['type_id'] = $result->id;
                    $Oth_address_flag['type_category'] = 'personal_other_address';
                    $Oth_address_flag['address2'] = $request['poa_address1'];
                    $Oth_address_flag['city'] = $request['poa_city'];
                    $Oth_address_flag['state'] = $request['poa_state'];
                    $Oth_address_flag['zip5'] = $request['poa_zip5'];
                    $Oth_address_flag['zip4'] = $request['poa_zip4'];
                    $Oth_address_flag['is_address_match'] = $request['poa_is_address_match'];
                    $Oth_address_flag['error_message'] = $request['poa_error_message'];
                    AddressFlag::checkAndInsertAddressFlag($Oth_address_flag);
                }
                /*** user activity ***/
                $action = "add";
                $get_name = $request['last_name'] . ', ' . $request['first_name'] . ' ' . $request['middle_name'];
                $url = Request::url();
                $split_url = explode("/store", $url);
                $fetch_url = $split_url[0] . '/' . $patient_id . '#personal-info';
                $module = "patients";
                $submodule = "patient";
                $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
                /*** user activity ***/

                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $patient_id));
            } else {
                if (Patient::where('id', $patient_id)->count() > 0 && is_numeric($patient_id)) {
                    $patients = Patient::findOrFail($patient_id);
                    if (Input::hasFile('filefield') || $request['webcam_image'] == 1 || $request['scanner_image'] == 1) {
                        $old_filename = $patients->avatar_name;
                        $old_extension = $patients->avatar_ext;
                        $filestoreoldName = $old_filename . '.' . $old_extension;

                        $filename = rand(11111, 99999);
                        $extension = Input::hasFile('filefield') ? Input::file('filefield')->getClientOriginalExtension() : '.jpg';
                        $filestoreName = $filename . '.' . $extension;

                        $resize = array('150', '150');

                        if (Input::hasFile('filefield') && $request['upload_type'] != 'webcam') {
                            $image = Input::file('filefield');
                            Helpers::mediauploadpath('', 'patient', $image, $resize, $filestoreName, $filestoreoldName);
                        } elseif ($request['webcam_image'] == 1 && !empty($request['webcam_filename'])) {
                            $default_view = Config::get('siteconfigs.production.defult_production');
                            if (App::environment() == $default_view)
                                $image_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
                            else
                                $image_path = public_path() . '/';

                            $src = $image_path . '/media/patient/' . Auth::user()->id . '/' . $request['webcam_filename'];
                            $image = Input::file($src);
                            Helpers::mediauploadpath('', 'patient', $image, $resize, $filestoreName, $filestoreoldName);
                        }

                        $patients->avatar_name = $filename;
                        $patients->avatar_ext = $extension;
                    }
                    $patOtherAdd = PatientOtherAddress::where('patient_id',$patient_id)->get()->toArray();
                    
                    if ($request['send_statement_to'] != "Patient Address") {
                        $pat_other_address = [];

                        $pat_other_address['patient_id'] = $patient_id;
                        $pat_other_address['address1'] = $request['other_address1'];
                        $pat_other_address['address2'] = $request['other_address2'];
                        $pat_other_address['city'] = $request['other_city'];
                        $pat_other_address['state'] = $request['other_state'];
                        $pat_other_address['zip5'] = $request['other_zip5'];
                        $pat_other_address['zip4'] = $request['other_zip4'];
                        $pat_other_address['status'] = 'Active';

                        /// Starts - Personal info Other address flag update ///
                        $Oth_address_flag = array();
                        $Oth_address_flag['type'] = 'patients';
                        $Oth_address_flag['type_id'] = $patient_id;
                        $Oth_address_flag['type_category'] = 'personal_other_address';
                        $Oth_address_flag['address2'] = $request['poa_address1'];
                        $Oth_address_flag['city'] = $request['poa_city'];
                        $Oth_address_flag['state'] = $request['poa_state'];
                        $Oth_address_flag['zip5'] = $request['poa_zip5'];
                        $Oth_address_flag['zip4'] = $request['poa_zip4'];
                        $Oth_address_flag['is_address_match'] = $request['poa_is_address_match'];
                        $Oth_address_flag['error_message'] = $request['poa_error_message'];
                        AddressFlag::checkAndInsertAddressFlag($Oth_address_flag);
                      
                        if(!empty($patOtherAdd)) {
                            $PatOtherAddress = PatientOtherAddress::where('patient_id',$patient_id)->update($pat_other_address);
                        }else{
                            $results = PatientOtherAddress::create($pat_other_address);
                        }
                    }else{
                        if($patOtherAdd != '') {
                         $PatOtherAddress = PatientOtherAddress::where('patient_id',$patient_id)->update(['status'=>'InActive']);
                        }                      
                    }
                    $request['updated_by'] = Auth::user()->id;
                    // If account number not generated then 
                    if($patients->account_no == '')
                        $request['account_no'] = $this->create_patient_accno($patient_id); //create patient account number
                    
                    $patients->update($request);
                 

                    if (isset($request['employment_status'] )) {
                        $def_emp_arr = array('patient_id' => $patient_id, 'category' => 'Employer', 'employer_status' => $request['employment_status'], 'employer_occupation' => @$request['occupation'], 'employer_name' => @$request['employer_name'], 'employer_work_phone' => @$request['work_phone'] ? $request['work_phone'] : '', 'employer_phone_ext' => @$request['work_phone_ext'] ? $request['work_phone_ext'] : '','exist_emp_id' => @$request['exist_emp_id'] ? $request['exist_emp_id'] : '');
                        $this->check_create_def_employer($def_emp_arr); //created default employer
                    }
 
                    if (isset($request['guarantor_relationship'])) {
                        if($request['guarantor_relationship'] == 'Self'){
                            $request['guarantor_first_name'] = $request['first_name'];
                            $request['guarantor_last_name'] = $request['last_name'];
                            $request['guarantor_middle_name'] = $request['middle_name'];
                        }
                        $def_gua_arr = array('patient_id' => $patient_id, 'category' => 'Guarantor', 'guarantor_last_name' => @$request['guarantor_last_name'], 'guarantor_first_name' => @$request['guarantor_first_name'], 'guarantor_middle_name' => @$request['guarantor_middle_name'], 'guarantor_relationship' => @$request['guarantor_relationship']);
                       
                        $this->check_create_def_guarantor($def_gua_arr); //created default guarantor
                    }

                    if (isset($request['emer_last_name']) && isset($request['emer_first_name'])) {
                        $emr_cnt_arr = array('patient_id' => $patient_id, 'category' => 'Emergency Contact', 'emergency_last_name' => @$request['emer_last_name'], 'emergency_first_name' => @$request['emer_first_name'], 'emergency_cell_phone' => @$request['emer_cell_phone'], 'emergency_email' => @$request['emer_email'], 'emergency_middle_name' => @$request['emer_middle_name'], 'emergency_relationship' => @$request['emergency_relationship']);
                        $this->check_create_def_emergency($emr_cnt_arr); //created emergency contact 
                    }

                    if (trim($request['patient_alert_note']) != "") {
                        if (PatientNote::where('notes_type', 'patient')->where('patient_notes_type', 'alert_notes')->where('status', 'Active')->where('notes_type_id', $patient_id)->count() == 0) {
                            PatientNote::insert(['title' => 'Patient alert notes', 'notes_type' => 'patient', 'patient_notes_type' => 'alert_notes', 'notes_type_id' => $patient_id, 'content' => $request['patient_alert_note'], 'created_by' => Auth::user()->id, 'created_at' => Date('Y-m-d H:i:s')]);
                        } else {
                            PatientNote::insert(['title' => 'Patient alert notes', 'notes_type' => 'patient', 'patient_notes_type' => 'alert_notes', 'notes_type_id' => $patient_id, 'content' => $request['patient_alert_note'], 'created_by' => Auth::user()->id, 'created_at' => Date('Y-m-d H:i:s')]);
                        }
                    } else {
                        //PatientNote::where('notes_type','patient')->where('patient_notes_type','alert_notes')->where('notes_type_id',$patient_id)->delete();
                    }
                    /// Starts - Personal info address flag update ///
                    $address_flag = array();
                    $address_flag['type'] = 'patients';
                    $address_flag['type_id'] = $patient_id;
                    $address_flag['type_category'] = 'personal_info_address';
                    $address_flag['address2'] = $request['pia_address1'];
                    $address_flag['city'] = $request['pia_city'];
                    $address_flag['state'] = $request['pia_state'];
                    $address_flag['zip5'] = $request['pia_zip5'];
                    $address_flag['zip4'] = $request['pia_zip4'];
                    $address_flag['is_address_match'] = $request['pia_is_address_match'];
                    $address_flag['error_message'] = $request['pia_error_message'];

                    AddressFlag::checkAndInsertAddressFlag($address_flag);

                    /*                     * * user activity ** */
                    $action = "edit";
                    $get_name = $patients->last_name . ', ' . $patients->first_name . ' ' . $patients->middle_name;
                    $url = Request::url();
                    $split_url = explode("/store", $url);
                    $fetch_url = $split_url[0] . '/' . $id . '#personal-info';
                    $module = "patients";
                    $submodule = "patient";
                    $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
                    /*                     * * user activity ** */

                    /* Ends - Pay to address */

                    /* Remove claim error message */
                    ClaimInfoV1::ClearingClaimErrors($patient_id, 'Patient');
                    /* Remove claim error message */

                    return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.update_msg"), 'data' => $id));
                } else {
                    return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
                }
            }
        }
    }

    public function getEditApi($id, $tab = 'demo') {

        $patient_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ((isset($patient_id) && is_numeric($patient_id)) && (Patient::where('id', $patient_id)->count()) > 0) {
            /// Fetch patient information ///
            $patient = Patient::findOrFail($patient_id);

            /// Get dynamic fields to display in form ///
            $registration = $selectbox = $selectbox_count = [];
            
            $patient_contact_det = PatientContact::where('patient_id', $patient_id)->where('deleted_at', NULL);
            $patient_contact_det = $patient_contact_det->select(DB::raw('sum(case when category = "Guarantor" then 1 else 0 end) AS guarantor_count'), DB::raw('sum(case when category = "Employer" then 1 else 0 end) AS emply_count'), DB::raw('sum(case when category = "Emergency Contact" then 1 else 0 end) AS emer_count'), DB::raw('sum(case when category = "Attorney" then 1 else 0 end) AS attorney_count'))->first();
             $patient_other_address = PatientOtherAddress::where('patient_id', $patient_id)->where('status','Active')->get()->toArray();
             if(!empty($patient_other_address)){
                 $patient['other_status'] = 'Other Address';
                 $patient['other_address1'] = isset($patient_other_address[0]['address1']) ? $patient_other_address[0]['address1'] :'';
                 $patient['other_address2'] = isset($patient_other_address[0]['address2']) ? $patient_other_address[0]['address2'] :'';
                 $patient['other_city'] = isset($patient_other_address[0]['city']) ? $patient_other_address[0]['city'] :'';
                 $patient['other_state'] = isset($patient_other_address[0]['state']) ? $patient_other_address[0]['state'] :'';
                 $patient['other_zip5'] = isset($patient_other_address[0]['zip5']) ? $patient_other_address[0]['zip5'] :'';
                 $patient['other_zip4'] = isset($patient_other_address[0]['zip4']) ? $patient_other_address[0]['zip4'] :'';               
             }else{
                 $patient['other_status'] = 'Patient Address';
             }
             
            // dd($patient);
            //gurantor count 
            //$guarantor_count = PatientContact::where('patient_id',$patient_id)->where('category','Guarantor')->where('deleted_at',NULL)->get()->count();
            $guarantor_count = ($patient_contact_det->guarantor_count) ? $patient_contact_det->guarantor_count : 0;
            // employer count
            //$emply_count = PatientContact::where('patient_id',$patient_id)->where('category','Employer')->where('deleted_at',NULL)->get()->count();
            $emply_count = ($patient_contact_det->emply_count) ? $patient_contact_det->emply_count : 0;
            $attorney_count = ($patient_contact_det->attorney_count) ? $patient_contact_det->attorney_count : 0;

            // emergency contact count  
            //$emer_count = PatientContact::where('patient_id',$patient_id)->where('category','Emergency Contact')->where('deleted_at',NULL)->get()->count();
            $emer_count = ($patient_contact_det->emer_count) ? $patient_contact_det->emer_count : 0;
            $eligibility = PatientEligibility::with('insurance_details', 'user')->where('patients_id', $patient_id)->orderBy('id', 'DESC')->skip(0)->take(3)->get();
            if (Registration::count()) {
                $registration = Registration::first();
                if ($registration != '') {
                    if ($registration->guarantor == 1) {
                        $selectbox['Guarantor'] = 'Guarantor';
                        $selectbox_count['Guarantor'] = $guarantor_count;
                    }
                    if ($registration->emergency_contact == 1) {
                        $selectbox['Emergency Contact'] = 'Emergency Contact';
                        $selectbox_count['Emergency_Contact'] = $emer_count;
                    }
                    if ($registration->employer == 1) {
                        $selectbox['Employer'] = 'Employer';
                        $selectbox_count['Employer'] = $emply_count;
                    }
                    if ($registration->attorney == 1) {
                        $selectbox['Attorney'] = 'Attorney / Adjuster Name';
                        $selectbox_count['Attorney'] = $attorney_count;
                    }
                }
            }
            $insurance_concat = $authorization_concat = $contact_concat = [];
            /*             * *  Starts - Insurance Tab ** */
            if ($tab == 'insurance') {
                // Get insurance list for dropdown
                $insurances = Insurance::where('status', 'Active')->selectRaw('CONCAT(id,"::",insurancetype_id) as concatid, insurance_name')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'concatid')->all();
                $patient_insurances = PatientInsurance::with(['insurance_details'])->where('patient_id', $patient_id)->whereIn('category', ['Primary', 'Secondary', 'Tertiary'])->orderBy('category', 'asc')->get();

                $eligibility = PatientEligibility::with('insurance_details', 'user')->where('patients_id', $patient_id)->orderBy('id', 'DESC')->skip(0)->take(3)->get();

                $patient_insurances_cate_arr = PatientInsurance::where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->pluck('id', 'category')->all();

                $primary_ins_id = @$patient_insurances_cate_arr['Primary'];
                $secondary_ins_id = @$patient_insurances_cate_arr['Secondary'];
                $tertiary_ins_id = @$patient_insurances_cate_arr['Tertiary'];
                $workerscomp_ins_id = @$patient_insurances_cate_arr['Workerscomp'];
                $autoaccident_ins_id = @$patient_insurances_cate_arr['Autoaccident'];
                $attorney_ins_id = @$patient_insurances_cate_arr['Attorney'];

                $insurancetypes = InsuranceType::orderBy('type_name', 'ASC')->pluck('type_name', 'id')->all();
                $medical_secondary_list = MedicalSecondary::orderBy('code', 'ASC')->pluck('description', 'code')->all();
                $insurance_policy = Document::where('document_type','patients')->where('category','Patient_Documents_Insurance_Card_Copy')->where('type_id',$patient_id)->first();
                $insurance_ssn = Document::where('document_type','patients')->where('category','insured_ssn')->where('type_id',$patient_id)->first();
                if (Request::ajax()) {
                    return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient', 'insurances', 'patient_insurances', 'registration', 'selectbox', 'selectbox_count', 'primary_ins_id', 'secondary_ins_id', 'tertiary_ins_id', 'workerscomp_ins_id', 'autoaccident_ins_id', 'attorney_ins_id', 'insurancetypes', 'medical_secondary_list', 'eligibility','insurance_policy','insurance_ssn')));
                }
                $insurance_concat = ['insurances', 'patient_insurances', 'primary_ins_id', 'secondary_ins_id', 'tertiary_ins_id', 'workerscomp_ins_id', 'autoaccident_ins_id', 'attorney_ins_id', 'insurancetypes', 'medical_secondary_list', 'eligibility'];
            }
            /*             * * Ends - Insurance Tab ** */

            /*             * * Starts - Contact Tab ** */
            if ($tab == 'contact') {
                $contacts = PatientContact::where('patient_id', $patient_id)->orderBy('id', 'desc')->get();
                $claims_list = ClaimInfoV1::where('patient_id', $patient_id)->pluck('claim_number','id')->all();
                if (Request::ajax()) {
                    return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient', 'contacts', 'registration', 'selectbox', 'selectbox_count', 'eligibility','claims_list')));
                }              
                $contact_concat = ['contacts', 'eligibility','claims_list'];
            }
            /*             * *  Ends - Contact Tab ** */

            /*             * *  Starts - Authorization Tab ** */
            if ($tab == 'authorization') {
                /// Get Patient Insurance list
                $patient_insurances = Insurance::has('patient_insurance')->whereHas('patient_insurance', function($q) use($patient_id) {
                            $q->where('patient_id', $patient_id);
                        })->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
                $pos = Pos::select(DB::raw("CONCAT(code,' - ',pos) AS pos_detail"), 'id')->orderBy('id', 'ASC')->pluck('pos_detail', 'id')->all();
                $authorizations = PatientAuthorization::where('patient_id', $patient_id)->orderBy('id', 'asc')->get();
                $authorization_policy_ids_arr = PatientAuthorization::where('patient_id', $patient_id)->selectRaw('CONCAT(id,"::",authorization_no) as concatid, id')->pluck('concatid', 'id')->all();
                $authorization_policy_ids = implode(',', $authorization_policy_ids_arr);
                $authorization_auth = Document::where('document_type','patients')->where('category','Authorization_Documents_Pre_Authorization_Letter')->where('type_id',$patient_id)->first();

                if (Request::ajax()) {
                    return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient', 'authorizations', 'registration', 'selectbox', 'selectbox_count', 'pos', 'patient_insurances', 'authorization_policy_ids', 'eligibility','authorization_auth')));
                }
                $authorization_concat = ['authorizations', 'patient_insurances', 'pos', 'authorization_policy_ids', 'eligibility'];
            }
            /*             * * Ends - Authorization Tab ** */

            if (!Request::ajax()) {
                $countries = Country::orderBy('name', 'asc')->pluck('name', 'id')->all(); /// Get Country  List
                $ethnicity = Ethnicity::orderBy('name', 'asc')->pluck('name', 'id')->all(); /// Get Ethinity List
                $languages = Language::where('language', '!=', '')->orderBy('language', 'ASC')->pluck('language', 'id')->all(); /// Get Language List
                $providers = Provider::getBillingAndRenderingProvider('yes'); /// Get Provider List
				$referringProviders = Provider::getReferringProviderList(); /// Get Referring provider List
				
                $facilities = Facility::where('status', 'Active')->orderBy('facility_name', 'asc')->pluck('facility_name', 'id')->all(); /// Get Facility List

                // Get statement category list
                $stmt_category = STMTCategory::where('status', 'Active')->pluck('category','id')->all();
                $stmt_holdreason = STMTHoldReason::where('status', 'Active')->pluck('hold_reason','id')->all();

                /// Get address for usps ///
                $pia_address_flag = AddressFlag::getAddressFlag('patients', @$patient_id, 'personal_info_address');
                $addressFlag['pia'] = $pia_address_flag;

                $poa_address_flag = AddressFlag::getAddressFlag('patients', @$patient_id, 'personal_other_address');
                $addressFlag['poa'] = $poa_address_flag;
              //  dd($addressFlag);
                $country_id = $patient->country_id;
                $ethnicity_id = $patient->ethnicity_id;
                $language_id = $patient->language_id;
                $provider_id = $patient->provider_id;
                $facility_id = $patient->facility_id;
                $referring_provider_id = $patient->referring_provider_id;
                $employe_status = 'Unknown';
                $practice_user_type = Users::where('id', Auth::user()->id)->value("practice_user_type");
                $claims_count = ClaimInfoV1::where('patient_id', $patient_id)->count();


                if (PatientContact::where('patient_id', $patient_id)->where('category', 'Emergency Contact')->count() == 0) {
                    $emer_last_name = $emer_first_name = $emer_mi_name = $emer_cell_phone = $emer_email = "";
                } else {
                    $emer_cont_arr = PatientContact::where('patient_id', $patient_id)->where('category', 'Emergency Contact')->orderBy('id', 'DESC')->select('emergency_last_name', 'emergency_first_name', 'emergency_middle_name', 'emergency_cell_phone', 'emergency_email','emergency_relationship')->take(1)->get()->toArray();
                    $emer_last_name = $emer_cont_arr[0]['emergency_last_name'];
                    $emer_first_name = $emer_cont_arr[0]['emergency_first_name'];
                    $emer_mi_name = $emer_cont_arr[0]['emergency_middle_name'];
                    $emer_cell_phone = $emer_cont_arr[0]['emergency_cell_phone'];
                    $emer_email = $emer_cont_arr[0]['emergency_email'];
                    $emer_relationship = $emer_cont_arr[0]['emergency_relationship'];
                }

                if (PatientContact::where('patient_id', $patient_id)->where('category', 'Guarantor')->count() == 0) {
                    $gu_relationship = $gu_first_name = $gu_last_name = $gu_middle_name = "";
                } else {
                    $gut_cont_arr = PatientContact::where('patient_id', $patient_id)->where('category', 'Guarantor')->orderBy('id', 'DESC')->select('guarantor_relationship', 'guarantor_first_name', 'guarantor_last_name', 'guarantor_middle_name')->get()->toArray();
                    $gu_self_check = 'No';
                    if(!empty($gut_cont_arr[1])){
                        if($gut_cont_arr[1]['guarantor_relationship'] == 'Self'){
                            $gu_self_check = 'Yes';
                        }else{
                            $gu_self_check = 'No';
                        }
                    }else{
                       $gu_self_check = 'No';
                    }
                    $gu_relationship = $gut_cont_arr[0]['guarantor_relationship'];
                    $gu_first_name = $gut_cont_arr[0]['guarantor_first_name'];
                    $gu_last_name = $gut_cont_arr[0]['guarantor_last_name'];
                    $gu_middle_name = $gut_cont_arr[0]['guarantor_middle_name'];
                }
                /* Employer Contact details views */
                if (PatientContact::where('patient_id', $patient_id)->where('category', 'Employer')->count() == 0) {
                    $emp_relationship = $employer_name = $emp_occupation = $emp_work_phone = $emp_phone_ext = $employer_student_status = "";
                } else {
                    $emp_cont_arr = PatientContact::where('patient_id', $patient_id)->where('category', 'Employer')->orderBy('updated_at', 'DESC')->select('employer_status', 'employer_occupation', 'employer_name', 'employer_student_status', 'employer_work_phone', 'employer_phone_ext')->take(1)->get()->toArray();
                    $emp_relationship = $emp_cont_arr[0]['employer_status'];
                    $employer_name = $emp_cont_arr[0]['employer_name'];
                    //$emp_organization             = $emp_cont_arr[0]['employer_organization_name'];
                    $emp_occupation = $emp_cont_arr[0]['employer_occupation'];
                    $emp_student_status = $emp_cont_arr[0]['employer_student_status'];
                    $emp_work_phone = $emp_cont_arr[0]['employer_work_phone'];
                    $emp_phone_ext = $emp_cont_arr[0]['employer_phone_ext'];
                }

                $patient_alert_note = PatientNote::where('notes_type_id', $patient_id)->where('notes_type', 'patient')->where('status', 'Active')->where('patient_notes_type', 'alert_notes')->select("created_by", "content")->first();
                
                /* Remove claim error message */
                ClaimInfoV1::ClearingClaimErrors($patient_id, 'Patient');
                /* Remove claim error message */
                $documents_ssn = Document::where('document_type','patients')->where('category','ssn')->where('type_id',$patient_id)->first();
                $documents_licence = Document::where('document_type','patients')->where('category','Patient_Documents_Driving_License')->where('type_id',$patient_id)->first(); 

                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('id', 'patient', 'countries', 'ethnicity', 'languages', 'providers', 'referringProviders', 'facilities', 'addressFlag', 'registration', 'selectbox', 'selectbox_count', 'country_id', 'ethnicity_id', 'language_id', 'provider_id', 'facility_id', 'referring_provider_id', 'employe_status', 'practice_user_type', 'claims_count', 'emer_last_name', 'emer_first_name', 'emer_cell_phone', 'emer_email','emer_relationship', 'patient_alert_note', 'gu_relationship', 'gu_first_name', 'gu_last_name', 'gu_middle_name', 'emp_relationship', 'employer_name', 'emp_occupation', 'emp_student_status', 'emp_work_phone', 'emp_phone_ext', 'eligibility', 'emer_mi_name', 'stmt_category', 'stmt_holdreason','gu_self_check','documents_ssn','documents_licence') + compact($insurance_concat) + compact($contact_concat) + compact($authorization_concat) ));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    public function getAjaxdataApi($id, $tab = 'demo') {

        $patient_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ((isset($patient_id) && is_numeric($patient_id)) && (Patient::where('id', $patient_id)->count()) > 0) {
            /// Fetch patient information ///
            $patient = Patient::findOrFail($patient_id);

            /// Get dynamic fields to display in form ///
            $registration = '';
            $selectbox = [];

            $patient_contact_det = PatientContact::where('patient_id', $patient_id)->where('deleted_at', NULL);
            $patient_contact_det = $patient_contact_det->select(DB::raw('sum(case when category = "Guarantor" then 1 else 0 end) AS guarantor_count'), DB::raw('sum(case when category = "Employer" then 1 else 0 end) AS emply_count'), DB::raw('sum(case when category = "Emergency Contact" then 1 else 0 end) AS emer_count'))->first();

            //gurantor count 
            //$guarantor_count = PatientContact::where('patient_id',$patient_id)->where('category','Guarantor')->where('deleted_at',NULL)->get()->count();
            $guarantor_count = ($patient_contact_det->guarantor_count) ? $patient_contact_det->guarantor_count : 0;
            // employer count
            //$emply_count = PatientContact::where('patient_id',$patient_id)->where('category','Employer')->where('deleted_at',NULL)->get()->count();
            $emply_count = ($patient_contact_det->emply_count) ? $patient_contact_det->emply_count : 0;
            // emergency contact count  
            //$emer_count = PatientContact::where('patient_id',$patient_id)->where('category','Emergency Contact')->where('deleted_at',NULL)->get()->count();
            $emer_count = ($patient_contact_det->emer_count) ? $patient_contact_det->emer_count : 0;

            if (Registration::count()) {
                $registration = Registration::first();
                if ($registration != '') {
                    if ($registration->guarantor == 1) {
                        if ($guarantor_count < 2)
                            $selectbox['Guarantor'] = 'Guarantor';
                    }
                    if ($registration->emergency_contact == 1)
                        if ($emer_count < 2)
                            $selectbox['Emergency Contact'] = 'Emergency Contact';
                    if ($registration->employer == 1)
                        if ($emply_count < 2)
                            $selectbox['Employer'] = 'Employer';
                }
            }
            $insurance_concat = $authorization_concat = $contact_concat = [];

            $countries = Country::orderBy('name', 'asc')->pluck('name', 'id')->all(); /// Get Country  List
            $ethnicity = Ethnicity::orderBy('name', 'asc')->pluck('name', 'id')->all(); /// Get Ethinity List
            $languages = Language::where('language', '!=', '')->orderBy('language', 'ASC')->pluck('language', 'id')->all(); /// Get Language List
            $providers = Provider::getBillingAndRenderingProvider('yes'); /// Get Provider List
            $facilities = Facility::where('status', 'Active')->orderBy('facility_name', 'asc')->pluck('facility_name', 'id')->all(); /// Get Facility List
            /// Get address for usps ///
            $pia_address_flag = AddressFlag::getAddressFlag('patients', @$patient_id, 'personal_info_address');
            $addressFlag['pia'] = $pia_address_flag;
             $pia_address_flag = AddressFlag::getAddressFlag('patients', @$patient_id, 'personal_other_address');
            $addressFlag['poa'] = $pia_address_flag;

            $country_id = $patient->country_id;
            $ethnicity_id = $patient->ethnicity_id;
            $language_id = $patient->language_id;
            $provider_id = $patient->provider_id;
            $facility_id = $patient->facility_id;
            $employe_status = 'Unknown';
            $practice_user_type = Users::where('id', Auth::user()->id)->pluck("practice_user_type")->all();
            $claims_count = ClaimInfoV1::where('patient_id', $patient_id)->count();

            if (PatientContact::where('patient_id', $patient_id)->where('category', 'Emergency Contact')->count() == 0) {
                $emer_last_name = $emer_first_name = $emer_mi_name = $emer_cell_phone = $emer_email = "";
            } else {
                $emer_cont_arr = PatientContact::where('patient_id', $patient_id)->where('category', 'Emergency Contact')->orderBy('id', 'DESC')->select('emergency_last_name', 'emergency_middle_name', 'emergency_first_name', 'emergency_cell_phone', 'emergency_email')->take(1)->get()->toArray();
                $emer_last_name = $emer_cont_arr[0]['emergency_last_name'];
                $emer_first_name = $emer_cont_arr[0]['emergency_first_name'];
                $emer_mi_name = $emer_cont_arr[0]['emergency_middle_name'];
                $emer_cell_phone = $emer_cont_arr[0]['emergency_cell_phone'];
                $emer_email = $emer_cont_arr[0]['emergency_email'];
            }

            if (PatientContact::where('patient_id', $patient_id)->where('category', 'Guarantor')->count() == 0) {
                $gu_relationship = $gu_first_name = $gu_last_name = $gu_middle_name = "";
            } else {
                $gut_cont_arr = PatientContact::where('patient_id', $patient_id)->where('category', 'Guarantor')->orderBy('id', 'DESC')->select('guarantor_relationship', 'guarantor_first_name', 'guarantor_last_name', 'guarantor_middle_name')->take(1)->get()->toArray();
                $gu_relationship = $gut_cont_arr[0]['guarantor_relationship'];
                $gu_first_name = $gut_cont_arr[0]['guarantor_first_name'];
                $gu_last_name = $gut_cont_arr[0]['guarantor_last_name'];
                $gu_middle_name = $gut_cont_arr[0]['guarantor_middle_name'];
            }

            $patient_alert_note = PatientNote::where('notes_type_id', $patient_id)->where('notes_type', 'patient')->where('status', 'Active')->where('patient_notes_type', 'alert_notes')->pluck("content")->all();

            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('id', 'patient', 'countries', 'ethnicity', 'languages', 'providers', 'facilities', 'addressFlag', 'registration', 'selectbox', 'country_id', 'ethnicity_id', 'language_id', 'provider_id', 'facility_id', 'employe_status', 'practice_user_type', 'claims_count', 'emer_last_name', 'emer_first_name', 'emer_mi_name', 'emer_cell_phone', 'emer_email', 'patient_alert_note', 'gu_relationship', 'gu_first_name', 'gu_last_name', 'gu_middle_name') + compact($insurance_concat) + compact($contact_concat) + compact($authorization_concat)));
        }
    }

    public function getAddMoreFieldsApi($addmore_type, $cur_count, $id = '') {
        $patient_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');

        $registration = Registration::first();

        $cur_count = $cur_count + 1;

        if ($addmore_type == 'insurance') {
            /// Fetch patient information ///
            $patient = Patient::findOrFail($patient_id);
            $insurances = Insurance::where('status', 'Active')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
            $eligibility = PatientEligibility::with('insurance_details', 'user')->where('patients_id', $patient_id)->orderBy('id', 'DESC')->skip(0)->take(3)->get();
            $patient_insurance = [];
            $count = $cur_count;
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient', 'insurances', 'registration', 'patient_insurance', 'cur_count', 'count', 'eligibility')));
        } elseif ($addmore_type == 'contact') {
            /// Fetch patient information ///
            $patient = Patient::findOrFail($patient_id);
            $contacts = PatientContact::where('patient_id', $patient_id)->get();
            $count = $cur_count;

            $selectbox = '';
            if ($registration != '') {
                if ($registration->guarantor == 1)
                    $selectbox['Guarantor'] = 'Guarantor';
                if ($registration->emergency_contact == 1)
                    $selectbox['Emergency Contact'] = 'Emergency Contact';
                if ($registration->employer == 1)
                    $selectbox['Employer'] = 'Employer';
            }
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient', 'contacts', 'registration', 'selectbox', 'cur_count', 'count')));
        } elseif ($addmore_type == 'authorization') {
            /// Fetch patient information ///
            $patient = Patient::findOrFail($patient_id);

            $patient_insurances = Insurance::has('patient_insurance')->whereHas('patient_insurance', function($q) use($patient_id) {
                        $q->where('patient_id', $patient_id);
                    })->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
            $pos = Pos::select(DB::raw("CONCAT(code,' - ',pos) AS pos_detail"), 'id')->orderBy('id', 'ASC')->pluck('pos_detail', 'id')->all();
            $authorizations = PatientAuthorization::where('patient_id', $patient_id)->get();
            $count = $cur_count;
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('patient', 'authorizations', 'registration', 'patient_insurances', 'pos', 'cur_count', 'count')));
        }
    }

    public function doArchivePatientInsurance($insurance, $status) {
        $user_id = Auth::user()->id;
        $insurance_archive = new PatientInsuranceArchive;
        $insurance_archive->patient_id = $insurance->patient_id;
        $insurance_archive->insurance_id = $insurance->insurance_id;
        $insurance_archive->medical_secondary_code = $insurance->medical_secondary_code;
        $insurance_archive->category = $insurance->category;
        $insurance_archive->relationship = $insurance->relationship;
        $insurance_archive->insured_phone = $insurance->insured_phone;
        $insurance_archive->insured_gender = $insurance->insured_gender;
        $insurance_archive->last_name = $insurance->last_name;
        $insurance_archive->first_name = $insurance->first_name;
        $insurance_archive->middle_name = $insurance->middle_name;
        $insurance_archive->insured_ssn = $insurance->insured_ssn;
        $insurance_archive->insured_dob = $insurance->insured_dob;
        $insurance_archive->insured_address1 = $insurance->insured_address1;
        $insurance_archive->insured_address2 = $insurance->insured_address2;
        $insurance_archive->insured_city = $insurance->insured_city;
        $insurance_archive->insured_state = $insurance->insured_state;
        $insurance_archive->insured_zip5 = $insurance->insured_zip5;
        $insurance_archive->insured_zip4 = $insurance->insured_zip4;
        $insurance_archive->policy_id = $insurance->policy_id;
        $insurance_archive->group_name = $insurance->group_name;
        $insurance_archive->effective_date = $insurance->effective_date;
        $insurance_archive->termination_date = $insurance->termination_date;
        $insurance_archive->adjustor_ph = $insurance->adjustor_ph;
        $insurance_archive->adjustor_fax = $insurance->adjustor_fax;
        $insurance_archive->document_save_id = $insurance->document_save_id;
        $insurance_archive->eligibility_verification = $insurance->eligibility_verification;
        $insurance_archive->same_patient_address = $insurance->same_patient_address;
        $insurance_archive->active_from = (@$insurance->active_from != "0000-00-00 00:00:00" && @$insurance->active_from != "") ? @$insurance->active_from : $insurance->created_at;
        $insurance_archive->active_to = date("Y-m-d H:i:s");
        $insurance_archive->created_reason = $status;
        $insurance_archive->created_by = $user_id;
        $insurance_archive->save();
    }

    //patient status change via function.js
    public function getchangeStatus($id, $status_value) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (isset($id) && is_numeric($id)) {
            //update patient status in patients table
            $patients = Patient::findOrFail($id);
            $patients->timestamps = false;
            $patients->status = $status_value;
            $patients->save();
            /*                     * * user activity ** */
            $action = "status";
            $get_name = $patients->last_name . ', ' . $patients->first_name . ' ' . $patients->middle_name;
            $url = Request::url();
            $split_url = explode("/store", $url);
            $fetch_url = $split_url[0] . '/' . $id;
            $module = "patients";
            $submodule = "patient";
            $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
            /*                     * * user activity ** */
            if ($status_value == "Active")
                $msg = 1;
            else
                $msg = 0;
            return Response::json(array('status' => 'success', 'message' => $msg, 'data' => ''));
        }
    }

    public function getDeletePatientApi($id, $type, $div_id, $type_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');

        if ($type == 'insurance') {
            $document_type_id = $patient_id . $div_id;
            Document::where('type_id', $document_type_id)->where('document_type', 'patients')->where('document_sub_type', 'insurance')->delete();
            PatientInsurance::find($type_id)->delete();
        } elseif ($type == 'contact') {
            PatientContact::find($type_id)->delete();
        } elseif ($type == 'authorization') {
            $document_type_id = $patient_id . $div_id;
            Document::where('type_id', $document_type_id)->where('document_type', 'patients')->where('document_sub_type', 'Authorization')->delete();
            PatientAuthorization::find($type_id)->delete();
        }
        return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.delete_msg"), 'data' => ''));
    }

    //Dropdown List in employer name 
    public function getEmployerName($id) {
        $request = Request::all();
        $employer_name = $request["name"];
        $country = Patient::where('employer_name', 'LIKE', '%' . $employer_name . '%')->pluck('employer_name', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => $country, 'data' => ''));
    }

    public function api_sel_patientinsurance_address($sel_insurance_id) {
        $address_part_arr = Insurance::where('id', $sel_insurance_id)->first();
        $address_part = $address_part_arr->address_1 . ", " . $address_part_arr->city;
        return $address_part;
    }

    public function getinsurance_details_modal($serach_keyword, $serach_category) {
        $sub_sql = '';
        if ($serach_category == 'payerid') {
            $sub_sql = "payerid LIKE '%$serach_keyword%' or payerid LIKE '%$serach_keyword' or payerid LIKE '$serach_keyword%'";
        } elseif ($serach_category == 'address') {
            $serach_keywords = array_map("trim", explode(',', $serach_keyword));
            foreach ($serach_keywords as $serach_keyword) {
                if ($serach_keyword != "") {
                    $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                    $sub_sql .= "address_1 LIKE '%$serach_keyword%' or city LIKE '%$serach_keyword%' or state LIKE '%$serach_keyword%' or zipcode5 LIKE '%$serach_keyword%' or zipcode4 LIKE '%$serach_keyword%'";
                }
            }
        } else {
            //$sub_sql = "insurance_name LIKE '%$serach_keyword%' or insurance_name LIKE '%$serach_keyword' or insurance_name LIKE '$serach_keyword%'";         
            $serach_keywords = array_map("trim", explode(',', $serach_keyword));
            foreach ($serach_keywords as $srch_keyword) {
                if ($srch_keyword != "") {
                    $sub_sql = ($sub_sql <> "") ? $sub_sql . " or " : $sub_sql;
                    $sub_sql .= "insurance_name LIKE '%$srch_keyword%' or insurance_name LIKE '%$srch_keyword' or insurance_name LIKE '$srch_keyword%'";
                }
            }
        }
        $insurances = Insurance::whereRaw("($sub_sql)")->orderBy('insurance_name', 'asc')->get();
        $insurances = json_decode(json_encode($insurances), true);
        $total_insurance = count($insurances);
        return view('patients/patients/insurance_details_modal', compact('insurances', 'total_insurance'));
    }

    public static function getPatientTabsDetails($patient_id) {
        $practice_timezone = Helpers::getPracticeTimeZone();     
        $patients = Patient::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with(['patient_insurance' => function($query) {
                        $query->whereRaw('(category = "Primary" or category = "Secondary")')->orderBy('orderby_category', 'asc');
                    }, 'patient_insurance.insurance_details'])->where('id', $patient_id)->first();
        $patient_insurance_count = isset($patients->patient_insurance) ? count(json_decode(json_encode($patients->patient_insurance), true)) : 0; // Changed by revathi on April 22 2016
        if ($patient_insurance_count)
            $patient_insurance = $patients->patient_insurance->keyBy('category');
        else
            $patient_insurance = array();

        return Response::json(array('data' => compact('patients', 'patient_insurance_count', 'patient_insurance')));
    }

    public function getswitchpatient_details_modal() {
        $request = Request::all();
        $searchby = $request['search_by'];
        $searchkeyword = $request['search_keyword'];
        $query = Patient::with('patient_claim_fin')->where('status', 'Active');

        $adsa = strtolower($searchkeyword);
        
        if ($searchby == 'patient_name') {
            $query->Where(function ($query) use ($searchkeyword) {
                if (strpos(strtolower($searchkeyword), ",") !== false) {
                    $searchValues = array_filter(explode(",", $searchkeyword));
                    if(count($searchValues) == 1 && isset($searchValues[0])){
                        $query = $query->Where('last_name', 'LIKE', trim(@$searchValues[0]).'%');
                    }
                    if(count($searchValues) == 2 && isset($searchValues[0]) && $searchValues[1]){
                        $query = $query->Where('last_name', 'LIKE', trim(@$searchValues[0]).'%')
                                    ->Where('first_name', 'LIKE', trim(@$searchValues[1]).'%');
                    }
                    if(count($searchValues) == 3 && isset($searchValues[0]) && isset($searchValues[1]) && isset($searchValues[2]) ) {
                        $query = $query->Where('last_name', 'LIKE', trim(@$searchValues[0]).'%')
                                    ->Where('first_name', 'LIKE', trim(@$searchValues[1]).'%')
                                     ->Where('middle_name', 'LIKE', trim(@$searchValues[2]).'%');
                    }     
                } else {
                    $query = $query->orWhere('last_name', 'LIKE', '%' . $searchkeyword . '%')
                            ->orWhere('middle_name', 'LIKE', '%' . $searchkeyword . '%')
                            ->orWhere('first_name', 'LIKE', '%' . $searchkeyword . '%');
                }
            });
        } elseif ($searchby == 'act_no') {
            $query->whereRaw("account_no LIKE '%$searchkeyword%'");
        } elseif ($searchby == 'dob') {
            $searchkeyword = date('Y-m-d', strtotime($searchkeyword));
            $query->whereRaw("dob LIKE '%$searchkeyword%'");
        } elseif ($searchby == 'ssn') {
            $query->whereRaw("ssn LIKE '%$searchkeyword%'");
        }
        $patients = $query->orderBy('percentage', 'ASC')->get();
        return view('patients/patients/patient_details_modal', compact('patients'));
    }

    public function ContactModuleProcessApi() {
        $request = Request::all();
        $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
        $user_id = Auth::user()->id;
        $patient = Patient::where('id', $patient_id)->first();
        if ($request['current_option'] == "contact_delete") {
            $patient_contact = PatientContact::where('id', $request['contact_id'])->first();
            if(!empty($patient_contact)) {
                if ($patient_contact->category == 'Guarantor') {
                    $guarantor_count = PatientContact::where('patient_id', $patient_id)->where('category', 'Guarantor')->where('deleted_at', NULL)->get()->count();
                    $patient->age = date_diff(date_create(@$patient->dob), date_create('today'))->y;
                    if ($patient->age < 18 && $guarantor_count == 1) {
                        return Response::json(array('status' => 'error', 'message' => null, 'data' => ''));
                    } else {
                        PatientContact::find($request['contact_id'])->delete();
                        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
                    }
                } else {
                    PatientContact::find($request['contact_id'])->delete();
                    return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
                }
            } else {
                return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
            }
        }
        // print_r($request);
        $address_flag = array();
        if ($request['current_option'] == 'guarantor') {
          //  print_r($request);
            $contact_arr = [
                'patient_id' => $patient_id,
                'category' => 'Guarantor',
                'guarantor_last_name' => $request['guarantor_last_name'],
                'guarantor_middle_name' => $request['guarantor_middle_name'],
                'guarantor_first_name' => $request['guarantor_first_name'],
                'guarantor_relationship' => $request['guarantor_relationship'],
                'guarantor_home_phone' => $request['guarantor_home_phone'],
                'guarantor_cell_phone' => $request['guarantor_cell_phone'],
                'guarantor_email' => $request['guarantor_email'],
                'guarantor_address1' => $request['guarantor_address1'],
                'guarantor_address2' => $request['guarantor_address2'],
                'guarantor_city' => $request['guarantor_city'],
                'guarantor_state' => $request['guarantor_state'],
                'guarantor_zip5' => $request['guarantor_zip5'],
                'guarantor_zip4' => $request['guarantor_zip4']
            ];
            $address_flag['address2'] = $request['guarantor_general_address1'];
            $address_flag['city'] = $request['guarantor_general_city'];
            $address_flag['state'] = $request['guarantor_general_state'];
            $address_flag['zip5'] = $request['guarantor_general_zip5'];
            $address_flag['zip4'] = $request['guarantor_general_zip4'];
            $address_flag['is_address_match'] = $request['guarantor_general_is_address_match'];
            $address_flag['error_message'] = $request['guarantor_general_error_message'];
        } elseif ($request['current_option'] == 'emergency_contact') {
            $contact_arr = [
                'patient_id' => $patient_id,
                'category' => 'Emergency Contact',
                'emergency_last_name' => $request['emergency_last_name'],
                'emergency_first_name' => $request['emergency_first_name'],
                'emergency_middle_name' => $request['emergency_middle_name'],
                'emergency_relationship' => $request['emergency_relationship'],
                'emergency_home_phone' => $request['emergency_home_phone'],
                'emergency_cell_phone' => $request['emergency_cell_phone'],
                'emergency_email' => $request['emergency_email'],
                'emergency_address1' => $request['emergency_address1'],
                'emergency_address2' => $request['emergency_address2'],
                'emergency_city' => $request['emergency_city'],
                'emergency_state' => $request['emergency_state'],
                'emergency_zip5' => $request['emergency_zip5'],
                'emergency_zip4' => $request['emergency_zip4']
            ];
            $address_flag['address2'] = $request['emergency_contact_general_address1'];
            $address_flag['city'] = $request['emergency_contact_general_city'];
            $address_flag['state'] = $request['emergency_contact_general_state'];
            $address_flag['zip5'] = $request['emergency_contact_general_zip5'];
            $address_flag['zip4'] = $request['emergency_contact_general_zip4'];
            $address_flag['is_address_match'] = $request['emergency_contact_general_is_address_match'];
            $address_flag['error_message'] = $request['emergency_contact_general_error_message'];
        } elseif ($request['current_option'] == 'employer') {
            $employer_count = Employer::where('employer_name', '=', $request['employer_name'])->count();
            $emp = [];
            /* IF employer count is Zero Create new employer in Employer table */
            if ($employer_count == 0) {
                $emp['employer_name'] = $request['employer_name'];
                $emp['employer_occupation'] = $request['employer_occupation'];
                $emp['employer_status'] = $request['employer_status'];
                $emp['employer_student_status'] = $request['employer_student_status'];
                $emp['address1'] = $request['employer_address1'];
                $emp['address2'] = $request['employer_address2'];
                $emp['city'] = $request['employer_city'];
                $emp['state'] = $request['employer_state'];
                $emp['zip5'] = $request['employer_zip5'];
                $emp['zip4'] = $request['employer_zip5'];
                $emp['work_phone'] = $request['employer_work_phone'];
                $emp['work_phone_ext'] = $request['employer_phone_ext'];
                $emp['created_by'] = Auth::user()->id;
                $emp['created_at'] = date('Y-m-d h:i:s');
                Employer::create($emp);
            }
            if ($request['employer_status'] != "Employed" && $request['employer_status'] != "Self Employed") {
                //$request['employer_organization_name']    = "";
                $request['employer_occupation'] = "";
            }
            if ($request['employer_status'] != "Student") {
                $request['employer_student_status'] = "Unknown";
            }
            $contact_arr = [
                'patient_id' => $patient_id,
                'category' => 'Employer',
                'employer_status' => $request['employer_status'],
                //'employer_organization_name' => @$request['employer_organization_name'],
                'employer_occupation' => @$request['employer_occupation'],
                'employer_student_status' => @$request['employer_student_status'],
                'employer_name' => $request['employer_name'],
                'employer_work_phone' => $request['employer_work_phone'],
                'employer_phone_ext' => $request['employer_phone_ext'],
                'employer_address1' => $request['employer_address1'],
                'employer_address2' => $request['employer_address2'],
                'employer_city' => $request['employer_city'],
                'employer_state' => $request['employer_state'],
                'employer_zip5' => $request['employer_zip5'],
                'employer_zip4' => $request['employer_zip4']
            ];
            $address_flag['address2'] = $request['employer_general_address1'];
            $address_flag['city'] = $request['employer_general_city'];
            $address_flag['state'] = $request['employer_general_state'];
            $address_flag['zip5'] = $request['employer_general_zip5'];
            $address_flag['zip4'] = $request['employer_general_zip4'];
            $address_flag['is_address_match'] = $request['employer_general_is_address_match'];
            $address_flag['error_message'] = $request['employer_general_error_message'];
        } elseif ($request['current_option'] == 'attorney') {
            $contact_arr = [
                'patient_id' => $patient_id,
                'category' => 'Attorney',
                'attorney_adjuster_name' => $request['attorney_adjuster_name'],
                'attorney_doi' => (($request['attorney_doi'] != "0000-00-00") && ($request['attorney_doi'] != "")) ? date("Y-m-d", strtotime($request['attorney_doi'])) : '',
                'attorney_claim_num' => !empty($request['attorney_claim_number']) ? implode(',',$request['attorney_claim_number']) : '',
                'attorney_work_phone' => $request['attorney_work_phone'],
                'attorney_phone_ext' => $request['attorney_phone_ext'],
                'attorney_fax' => $request['attorney_fax'],
                'attorney_email' => $request['attorney_email'],
                'attorney_address1' => $request['attorney_address1'],
                'attorney_address2' => $request['attorney_address2'],
                'attorney_city' => $request['attorney_city'],
                'attorney_state' => $request['attorney_state'],
                'attorney_zip5' => $request['attorney_zip5'],
                'attorney_zip4' => $request['attorney_zip4']
            ];
            $address_flag['address2'] = $request['attorney_general_address1'];
            $address_flag['city'] = $request['attorney_general_city'];
            $address_flag['state'] = $request['attorney_general_state'];
            $address_flag['zip5'] = $request['attorney_general_zip5'];
            $address_flag['zip4'] = $request['attorney_general_zip4'];
            $address_flag['is_address_match'] = $request['attorney_general_is_address_match'];
            $address_flag['error_message'] = $request['attorney_general_error_message'];
        }

        if (isset($request['same_as_patient_address']) && @$request['same_as_patient_address'] == "on") {
            $contact_arr['same_patient_address'] = "yes";
        } else {
            $contact_arr['same_patient_address'] = "no";
        }

        /* Employer is empty on status based */
        if ($request['employer_status'] == 'Retired') {
            $contact_arr['employer_occupation'] = '';
            $contact_arr['employer_name'] = '';
            $contact_arr['employer_work_phone'] = '';
            $contact_arr['employer_phone_ext'] = '';
            $contact_arr['employer_address1'] = '';
            $contact_arr['employer_address2'] = '';
            $contact_arr['employer_city'] = '';
            $contact_arr['employer_state'] = '';
            $contact_arr['employer_zip5'] = '';
            $contact_arr['employer_zip4'] = '';
        } elseif ($request['employer_status'] == 'Active Military Duty' || $request['employer_status'] == 'Unknown') {
            $contact_arr['employer_occupation'] = '';
            $contact_arr['employer_name'] = '';
        }
        /* New patient contact  store or update */
        if ($request['add_type'] == "new") {
            $patient_contact = PatientContact::create($contact_arr);
            $patient_contact->created_by = Auth::user()->id;
            $patient_contact->save();
        } elseif ($request['add_type'] == "edit") {
            $patient_contact = PatientContact::find($request['edit_type_id']);
            $patient_contact->update($contact_arr);
            $patient_contact->updated_by = Auth::user()->id;
            $patient_contact->save();
        }
        /* Patient detail uplode the last element details */
        $patient_emp = [];
        $patient_emp['employer_status'] = $request['employer_status'];
        $patient_emp['employer_name'] = $request['employer_name'];
        $patient_emp['occupation'] = $request['employer_occupation'];
        $patient_emp['student_status'] = $request['employer_student_status'];
        $patient_emp['work_phone'] = $request['employer_work_phone'];
        $patient_emp['work_phone_ext'] = $request['employer_phone_ext'];

        $employer_patient = Patient::find($patient_id);
        $employer_patient->update($patient_emp);

        /* Patient details upload end  */
        $address_flag['type'] = 'patients';
        $address_flag['type_id'] = $patient_contact->id;
        $address_flag['type_category'] = 'patient_contact_address';

        AddressFlag::checkAndInsertAddressFlag($address_flag);

        $action = "edit";
        $get_name = $patient->last_name . ', ' . $patient->first_name . ' ' . $patient->middle_name;
        $url = Request::url();
        $split_url = explode("/edit", $url);
        $fetch_url = $split_url[0] . '#contact-info';
        $module = "patients";
        $submodule = "contact";
        $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);

        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    // Insert the insurance record information.
    public function insuranceModuleProcessApi() {
        $request = Request::all();
        $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
        $user_id = Auth::user()->id;
        $patient = Patient::where('id', $patient_id)->first();

        // Update the record when change self pay or insurance.
        if ($request['current_option'] == "insurance_responsible") {
            $pat_ins_cnt = 0;
            if ($request['is_self_pay_val'] == "Yes") {
                $patient_insurance = PatientInsurance::where('patient_id', $patient_id)->get();
                if (count($patient_insurance) > 0) {

                    // Move insurance to others if change the method to self pay.
                    foreach ($patient_insurance as $insurance) {
                        /* if($insurance['category']=='Primary' || $insurance['category']=='Secondary' || $insurance['category']=='Tertiary'){
                          $this->doArchivePatientInsurance($insurance,'Self Pay');
                          } */
                        if ($insurance['category'] != 'Others') {
                            PatientInsurance::where('id', $insurance['id'])->update(['category' => 'Others', 'orderby_category' => 8, 'active_from' => $insurance['created_at'], 'active_to' => date("Y-m-d H:i:s")]);
                        }
                    }
                }

                if ($patient->ins_percentage == 0) {
                    $patient->ins_percentage = 40;                    
                    $patient->percentage = 100;
                }
            } else {
                $pat_ins_cnt = PatientInsurance::where('patient_id', $patient_id)->where('category', 'Others')->count();
                if ($patient->ins_percentage > 0 && $pat_ins_cnt == 0) {
                    $patient->ins_percentage = 0;
                    $patient->percentage -= 40;
                }
            }
            $patient->is_self_pay = $request['is_self_pay_val'];
            $patient->save();
            return Response::json(array('status' => $pat_ins_cnt, 'message' => '', 'data' => ''));
        }

        // Delete the insurance record.
        if ($request['current_option'] == "insurance_delete") {
            $patient_insurance = PatientInsurance::with('patient_authorization')->find($request['insurance_id']);
            /* if($patient_insurance->category=='Primary' || $patient_insurance->category=='Secondary' || $patient_insurance->category=='Tertiary'){
              $this->doArchivePatientInsurance($patient_insurance,'Deleted');
              } */
            // check the claim exist or not for the particular insurance.
            if (ClaimInfoV1::where('patient_id', $patient_id)->where('insurance_id', $patient_insurance->insurance_id)->where('insurance_category', $patient_insurance->category)->count() == 0) {
                $patient_insurance->patient_authorization()->delete();
                $patient_insurance->delete();
                Document::where('type_id', $request['insurance_id'])->where('document_type', 'patients')->where('document_sub_type', 'insurance')->delete();
                $pat_ins_cnt = PatientInsurance::where('patient_id', $patient_id)->count();
                if ($pat_ins_cnt == 0 && $patient->ins_percentage > 0) {
                    $patient->ins_percentage = 0;
                    $patient->percentage -= 40;
                    $patient->save();
                }
                return Response::json(array('status' => 'Deleted successfully', 'message' => '', 'data' => ''));
            } else {
                return Response::json(array('status' => 'Already used this Insurance in claim', 'message' => '', 'data' => ''));
            }
        }

        if ($request['current_option'] == "check_ins_policy") {
            $policy_ids = 'no';

            // Return policy_id based on patient and insurance.
            if (PatientInsurance::where('patient_id', $patient_id)->where('insurance_id', $request['sel_ins_id'])->where('id', '!=', $request['cur_ins_id'])->count() > 0) {
                $policy_id = PatientInsurance::where('patient_id', $patient_id)->where('insurance_id', $request['sel_ins_id'])->where('id', '!=', $request['cur_ins_id'])->pluck('policy_id')->all();
                $policy_ids = implode(",", $policy_id);
            }
            return Response::json(array('status' => $policy_ids));
        }

        $address_flag = array();
        $orderby_category = "";
        if ($request['category'] == "Primary")
            $orderby_category = 1;
        elseif ($request['category'] == "Secondary")
            $orderby_category = 2;
        elseif ($request['category'] == "Tertiary")
            $orderby_category = 3;
        elseif ($request['category'] == "Workers Comp")
            $orderby_category = 4;
        elseif ($request['category'] == "Liability")
            $orderby_category = 5;
        elseif ($request['category'] == "Autoaccident")
            $orderby_category = 6;
        elseif ($request['category'] == "Attorney")
            $orderby_category = 7;
        else
            $orderby_category = 8;
        $insurance_id_arr = (isset($request['insurance_id']) && !empty($request['insurance_id'])) ? explode("::", $request['insurance_id']) : [];
        if (isset($request['same_as_patient_address']) && @$request['same_as_patient_address'] == "on") {
            $same_patient_address = "yes";
			/* Added patient address if same as address checked */
			$request['insured_address1'] = (isset($request['insured_address1']) && !empty($request['insured_address1'])) ? $request['insured_address1'] : $patient->address1;
			$request['insured_address2'] = (isset($request['insured_address2']) && !empty($request['insured_address2'])) ? $request['insured_address2'] : $patient->address2;
			$request['insured_city'] = (isset($request['insured_city']) && !empty($request['insured_city'])) ? $request['insured_city'] : $patient->city;
			$request['insured_state'] = (isset($request['insured_state']) && !empty($request['insured_state'])) ? $request['insured_state'] : $patient->state;
			$request['insured_zip5'] = (isset($request['insured_zip5']) && !empty($request['insured_zip5'])) ? $request['insured_zip5'] : $patient->zip5;
			$request['insured_zip4'] = (isset($request['insured_zip4']) && !empty($request['insured_zip4'])) ? $request['insured_zip4'] : $patient->zip4;
			
        } else {
            $same_patient_address = "no";
        }
        $arrData = array(
            "patient_id" => $patient_id,
            "category" => $request['category'],
            "medical_secondary_code" => $request['medical_secondary_code'],
            "insurance_id" => @$insurance_id_arr[0],
            "relationship" => $request['relationship'],
            "last_name" => $request['insured_last_name'],
            "first_name" => $request['insured_first_name'],
            "middle_name" => $request['insured_middle_name'],
            "insured_gender" => @$request['gender'],
            "insured_ssn" => @$request['insured_ssn'],
            "insured_dob" => ((@$request['insured_dob'] != "0000-00-00") && (@$request['insured_dob'] != "")) ? date("Y-m-d", strtotime(@$request['insured_dob'])) : '',
            "insured_address1" => $request['insured_address1'],
            "insured_address2" => $request['insured_address2'],
            "insured_city" => $request['insured_city'],
            "insured_state" => $request['insured_state'],
            "insured_zip5" => $request['insured_zip5'],
            "insured_zip4" => $request['insured_zip4'],
            "policy_id" => $request['policy_id'],
            "group_name" => @$request['group_name'],
            "effective_date" => (($request['effective_date'] != "0000-00-00") && ($request['effective_date'] != "")) ? date("Y-m-d", strtotime($request['effective_date'])) : '',
            "termination_date" => (($request['termination_date'] != "0000-00-00") && ($request['termination_date'] != "")) ? date("Y-m-d", strtotime($request['termination_date'])) : '',
            "adjustor_ph" => $request['adjustor_ph'],
            "adjustor_fax" => $request['adjustor_fax'],
            "orderby_category" => $orderby_category,
            "document_save_id" => "",
            "same_patient_address" => $same_patient_address,
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $user_id,
            "updated_by" => $user_id
        );

        $address_flag['address2'] = $request['general_address1'];
        $address_flag['city'] = $request['general_city'];
        $address_flag['state'] = $request['general_state'];
        $address_flag['zip5'] = $request['general_zip5'];
        $address_flag['zip4'] = $request['general_zip4'];
        $address_flag['is_address_match'] = $request['general_is_address_match'];
        $address_flag['error_message'] = $request['general_error_message'];

        $insurancetype_id_exists = Insurance::where('id', $insurance_id_arr[0])->pluck('insurancetype_id')->first();

        $status = 'Success';
        // Create a new insurance record.
        if ($request['current_option'] == "new") {
            $status = '0';
            if ($request['category'] == 'Primary' || $request['category'] == 'Secondary' || $request['category'] == 'Tertiary') {
                $arrData['active_from'] = date("Y-m-d H:i:s");
                $status = '1';
            }
            if ($request['category'] == 'Workers Comp' || $request['category'] == 'Auto Accident' || $request['category'] == 'Attorney' || $request['category'] == 'Others') {
                $arrData['active_from'] = date("Y-m-d H:i:s");
                $arrData['active_to'] = date("Y-m-d H:i:s");
             }
            $patient_insurance = PatientInsurance::create($arrData);
            $address_flag['type_id'] = $patient_insurance->id;

            if ($patient->ins_percentage == 0) {
                $patient->ins_percentage = 40;
                $patient->percentage = 100;
                $patient->save();
            }
        } elseif ($request['current_option'] == "move") {
            $patient_insurance = PatientInsurance::find($request['patient_insurance_id']);
            $address_flag['type_id'] = $patient_insurance->id;
            $status = '0';
            if ($request['category'] == 'Primary' || $request['category'] == 'Secondary' || $request['category'] == 'Tertiary') {
                $arrData['active_from'] = date("Y-m-d H:i:s");
                $status = '1';
            }

            $patient_insurance->update($arrData);
            if ($patient->ins_percentage == 0) {
                $patient->ins_percentage = 40;
                $patient->percentage = 100;
                $patient->save();
            }
        } elseif ($request['current_option'] == "edit") {
            $patient_insurance = PatientInsurance::find($request['edit_insurance_id']);
            $old_insur_category = $patient_insurance['category'];
            $address_flag['type_id'] = $patient_insurance->id;
            $arrData['updated_by'] = Auth::user()->id;
            if (($old_insur_category == 'Primary' || $old_insur_category == 'Secondary' || $old_insur_category == 'Tertiary') && ($old_insur_category != $request['category'] || $patient_insurance['insurance_id'] != $request['insurance_id'])) {

                 if($patient_insurance->active_from != "0000-00-00 00:00:00"){
                    //
                 }else{
                     $arrData['active_from'] = date("Y-m-d H:i:s"); 
                 }

                // update "active to" option for how many days it was active.
                if ($request['category'] != 'Primary' && $request['category'] != 'Secondary' && $request['category'] != 'Tertiary') {
                    $arrData['active_to'] = date("Y-m-d H:i:s");
                }
                //$this->doArchivePatientInsurance($patient_insurance,'Changed');
            }
            $status = '0';
            if ($request['category'] == 'Primary' || $request['category'] == 'Secondary' || $request['category'] == 'Tertiary') {
                $status = '1';
            }

            $patient_insurance->update($arrData);

            if ($patient->ins_percentage == 0) {
                $patient->ins_percentage = 40;
                $patient->percentage = 100;
                $patient->save();
            }

            /* Remove claim error message */
            ClaimInfoV1::ClearingClaimErrors($request['edit_insurance_id'], 'Insurance');
            /* Remove claim error message */
            
            /* Remove claim error message */
            ClaimInfoV1::ClearingClaimErrors($request['edit_insurance_id'], 'Patient');
            /* Remove claim error message */
        }

        /*         * * Check start insurance eligibility status ** */
        if (@$patient_insurance->patient_id != '' && @$patient_insurance->insurance_id != '' && @$patient_insurance->policy_id != '') {
            $check_inseligibilityinfo = Helpers::checkInsEligiblity($patient_insurance->patient_id, $patient_insurance->insurance_id, $patient_insurance->policy_id);

            if ($check_inseligibilityinfo == 1) {
                $inseligiblitystatus = 'None';
            } elseif ($check_inseligibilityinfo == 2) {
                $inseligiblitystatus = 'Active';
            } elseif ($check_inseligibilityinfo == 3) {
                $inseligiblitystatus = 'Inactive';
            }
        }

        // Check eligibility verification for primary, secondary, Tertiary only.
        if ($request['category'] == 'Primary' || $request['category'] == 'Secondary' || $request['category'] == 'Tertiary') {
            PatientInsurance::where('id', '=', $patient_insurance->id)->update(['eligibility_verification' => $inseligiblitystatus]);
        }

        if ($request['category'] == 'Primary') {
            Patient::where('id', $patient_insurance->patient_id)->update(['eligibility_verification' => $inseligiblitystatus]);
        }
        /*** insurance eligibility status end ***/

        if ($request['insurancetype_id'] != '' && $request['insurancetype_id'] != 0 && $insurancetype_id_exists == 0 && ($request['current_option'] == "new" || $request['current_option'] == "edit")) {
            Insurance::where('id', $insurance_id_arr[0])->update(['insurancetype_id' => $request['insurancetype_id']]);
        }

        $address_flag['type'] = 'patients';
        $address_flag['type_category'] = 'patient_insurance_address';

        AddressFlag::checkAndInsertAddressFlag($address_flag);

        /*         * * user activity ** */
        $action = "edit";
        $get_name = $patient->last_name . ', ' . $patient->first_name . ' ' . $patient->middle_name;
        $url = Request::url();
        $split_url = explode("/edit", $url);
        $fetch_url = $split_url[0] . '#insurance-info';
        $module = "patients";
        $submodule = "Insurance";
        $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
        /*         * * user activity ** */

        return Response::json(array('status' => $status, 'message' => null, 'data' => ''));
    }

    public function authorizationModuleProcessApi() {
        $request = Request::all();
        $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
        $user_id = Auth::user()->id;
        $patient = Patient::where('id', $patient_id)->first();
        if ($request['current_option'] == "authorization_delete") {
             $authorization = [
            'updated_by' => $user_id            
            ];

             PatientAuthorization::find($request['authorization_id'])->update($authorization);
        } 
        if ($request['current_option'] == "authorization_delete") {
            Document::where('type_id', $request['authorization_id'])->where('document_type', 'patients')->where('document_sub_type', 'Authorization')->delete();
            PatientAuthorization::find($request['authorization_id'])->delete();
          /*  $authorization_arr = [
            'updated_by' => $user_id            
            ];
             PatientAuthorization::find($request['authorization_id'])->update($authorization_arr);*/
            return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
        }
         
        $authorization_arr = [
            'patient_id' => $patient_id,
            'insurance_id' => $request['auth_insurance_id'],
            'pos_id' => $request['pos_id'],
            'authorization_no' => $request['authorization_no'],
            'start_date' => ($request['start_date'] != '') ? Helpers::dateFormat($request['start_date'], 'datedb') : '0000-00-00',
            'end_date' => ($request['end_date'] != '') ? Helpers::dateFormat($request['end_date'], 'datedb') : '0000-00-00',
            'alert_appointment' => @$request['alert_appointment'],
            'allowed_visit' => @$request['allowed_visit'],
            'authorization_notes' => @$request['authorization_notes']
        ];

        if ($request['current_option'] == "new") {
            $authorization_arr['created_by'] = $user_id;
            $patient_authorization = PatientAuthorization::create($authorization_arr);
        } elseif ($request['current_option'] == "edit") {
            $patient_authorization = PatientAuthorization::find($request['edit_authorization_id']);
            $authorization_arr['updated_by'] = $user_id;
            $patient_authorization->update($authorization_arr);
        }

        /*         * * user activity ** */
        $action = "edit";
        $get_name = $patient->last_name . ', ' . $patient->first_name . ' ' . $patient->middle_name;
        $url = Request::url();
        $split_url = explode("/edit", $url);
        $fetch_url = $split_url[0] . '#authorization';
        $module = "patients";
        $submodule = "authorization";
        $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
        /*         * * user activity ** */

        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    public function getPatientContactDeatilsApi($contact_id, $patient_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $contacts = PatientContact::where('patient_id', $patient_id)->where('id', $contact_id)->first();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => $contacts));
    }

    //created default employer
    public function check_create_def_employer($def_emp_arr) {
        $def_emp_arr['employer_work_phone'] = @$def_emp_arr['employer_work_phone'] ? $def_emp_arr['employer_work_phone'] : '';
        $def_emp_arr['employer_phone_ext'] = @$def_emp_arr['employer_phone_ext'] ? $def_emp_arr['employer_phone_ext'] : '';
        /* New patient contact details added here */
        if (PatientContact::where('patient_id', $def_emp_arr['patient_id'])->where('category', 'Employer')->count() == 0) {
            if($def_emp_arr['employer_status'] != ""){
                $patient_contact = PatientContact::create($def_emp_arr);
                $patient_contact->created_by = Auth::user()->id;
                $patient_contact->save();
            }
        } else {
            /* Already added patient detailedit here */
            $emp_cont_id = PatientContact::where('patient_id', $def_emp_arr['patient_id'])->where('category', 'Employer')->orderBy('id', 'DESC')->pluck('id')->first();
                     
            if($def_emp_arr['employer_status'] != ""){
                $patient_contact = PatientContact::find($emp_cont_id);
                $patient_contact->update($def_emp_arr);
                $patient_contact->updated_by = Auth::user()->id;
                $patient_contact->save();
           }else{
                PatientContact::where('patient_id',$def_emp_arr['patient_id'])->where('category', 'Employer')->delete();
           }           
            
        }
        if($def_emp_arr['exist_emp_id'] == "create"){
            $def_emp_arr['work_phone'] = @$def_emp_arr['employer_work_phone'] ? $def_emp_arr['employer_work_phone'] : '';
            $def_emp_arr['work_phone_ext'] = @$def_emp_arr['employer_phone_ext'] ? $def_emp_arr['employer_phone_ext'] : '';
            $data = Employer::create($def_emp_arr);
            $user = Auth::user ()->id;
            $data->created_by = $user;
            $data->save();
        }
        return true;
    }

    //created default guarantor
    public function check_create_def_guarantor($def_gua_arr) {
        if (PatientContact::where('patient_id', $def_gua_arr['patient_id'])->where('category', $def_gua_arr['category'])->count() == 0) {
             if($def_gua_arr['guarantor_last_name'] != "" && $def_gua_arr['guarantor_first_name']){
                $patient_contact = PatientContact::create($def_gua_arr);
                $patient_contact->created_by = Auth::user()->id;
                $patient_contact->save();
            }
        } else {
            $gua_cont_id = PatientContact::where('patient_id', $def_gua_arr['patient_id'])->where('category', $def_gua_arr['category'])->orderBy('id', 'DESC')->pluck('id')->first();
            if($def_gua_arr['guarantor_last_name'] != "" && $def_gua_arr['guarantor_first_name']){
                $patient_contact = PatientContact::find($gua_cont_id);
                $patient_contact->update($def_gua_arr);
                $patient_contact->updated_by = Auth::user()->id;
                $patient_contact->save();
            }else{
                PatientContact::where('patient_id',$def_gua_arr['patient_id'])->where('category', $def_gua_arr['category'])->delete(); 
            }
        }
        return true;
    }

    //created default emergency
    public function check_create_def_emergency($emr_cnt_arr) {
        if (PatientContact::where('patient_id', $emr_cnt_arr['patient_id'])->where('category', $emr_cnt_arr['category'])->count() == 0) {
             if($emr_cnt_arr['emergency_relationship'] != ""){
                $patient_emergency = PatientContact::create($emr_cnt_arr);
                $patient_emergency->created_by = Auth::user()->id;
                $patient_emergency->save();
             }
        } else {
            $emer_cont_id = PatientContact::where('patient_id', $emr_cnt_arr['patient_id'])->where('category', $emr_cnt_arr['category'])->orderBy('id', 'DESC')->pluck('id')->first();
            if($emr_cnt_arr['emergency_relationship'] != ""){
                $patient_contact = PatientContact::find($emer_cont_id);
                $patient_contact->update($emr_cnt_arr);
                $patient_contact->updated_by = Auth::user()->id;
                $patient_contact->save();
            }else{
                 PatientContact::where('patient_id',$emr_cnt_arr['patient_id'])->where('category', $emr_cnt_arr['category'])->delete(); 
            }
        }
        return true;
    }

    public function getCheckInsurancetypeApi() {
        $request = Request::all();
        $insurance_type_id = $request['insurance_type_id'];
        return self::InsurancetypeCheck($insurance_type_id);
    }

    public static function InsurancetypeCheck($insurance_type_id) {
        $ins_type_code = InsuranceType::where("id", $insurance_type_id)->pluck("code")->first();
        $medicare_insurance_code = Config::get('siteconfigs.medicare_insurance_type_code');

        if (in_array($ins_type_code, $medicare_insurance_code)) {
            return "error";
        } else {
            return "success";
        }
    }

    public function deletepatientpictureApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $delete_avr = Patient::where('id', $id)->first();
        $delete_avr->avatar_name = "";
        $delete_avr->avatar_ext = "";
        $delete_avr->save();
        return "success";
    }

    public function getDeleteApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Patient::where('id', $id)->count() > 0 && is_numeric($id)) {
            $patients = Patient::where('id', $id)->first();
            /*                     * * user activity ** */
            $action = "delete";
            $get_name = $patients->last_name . ', ' . $patients->first_name . ' ' . $patients->middle_name;
            $url = Request::url();
            $split_url = explode("/store", $url);
            $fetch_url = $split_url[0] . '/' . $id . '#personal-info';
            $module = "patients";
            $submodule = "patient";
            $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
            /*                     * * user activity ** */
            Patient::where('id', $id)->delete();
            PatientAppointment::where('patient_id', $id)->delete();
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.delete_msg"), 'data' => ''));
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
        }
    }

    // Check insurance eligible or not
    public function checkInsuranceApi($patientid, $insuranceid, $policyid) {
        if (!is_numeric($patientid) && $patientid != '') {
            $patientid = Helpers::getEncodeAndDecodeOfId($patientid, 'decode');
        }

        $insurance_id = explode("::", $insuranceid);

        if (count($insurance_id) > 0) {
            $insuranceid = $insurance_id[0];
        }
        return DBConnectionController::checkInsEligiblity($patientid, $insuranceid, $policyid);
    }

    // Get patient questionnaries list
    public function getQuestionnairesApi($patientid, $export = '') {
        $patientid = Helpers::getEncodeAndDecodeOfId($patientid, 'decode');
        $registration = Registration::first();
        $questionaries = QuestionnariesAnswer::with('questionnaries_template', 'usercreated')->where('patient_id', $patientid)->orderBy('id', 'ASC')->get();

        if ($export != '') {
            $patient_r = $patient_list = array();
            foreach ($questionaries as $key => $answer) {
                $patient_r['question'] = $answer->questionnaries_template->question;
                $patient_r['answertype'] = $answer->questionnaries_template->answer_type;
                if ($answer->questionnaries_template->answer_type == 'checkbox' or $answer->questionnaries_template->answer_type == 'radio') {
                    $patient_r['answer'] = Self::getQuestionnariesOption(@$answer->questionnaries_option_id);
                } else {
                    $patient_r['answer'] = $answer->answer;
                }

                $patient_r['created'] = Helpers::dateFormat(@$answer->created_at, 'date');

                $patient_list[$key] = $patient_r;
            }
            $get_patientres = json_decode(json_encode($patient_list));

            $exportparam = array('filename' => 'Patient Questionnaries',
                'heading' => '',
                'fields' => array('question' => 'Question',
                    'answertype' => 'Answer Type',
                    'answer' => 'Answer',
                    'created' => 'Created On'));
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $get_patientres, $export);
        }

        return Response::json(array('data' => compact('registration', 'questionaries')));
    }

    // Get questionnaires answer for check box and radio button.
    public static function getQuestionnariesOption($optionid) {
        $get_optionarray = explode(',', $optionid);
        $get_value = QuestionnariesOption::select(DB::raw('group_concat(`option`) AS options'))->whereIn('id', $get_optionarray)->get();
        return $get_value[0]->options;
    }

    // Get patient archiveinsurance list
    public function getarchiveinsuranceApi($patientid, $export = '') {
        $patientid = Helpers::getEncodeAndDecodeOfId($patientid, 'decode');
        $practice_timezone = Helpers::getPracticeTimeZone();
        $archiveinsurance = PatientInsurance::select('*',DB::raw('CONVERT_TZ(active_from,"UTC","'.$practice_timezone.'") as active_from'),DB::raw('CONVERT_TZ(active_to,"UTC","'.$practice_timezone.'") as active_to'))->with(['insurance_details'])->where('patient_id', $patientid)->whereNotIn('category', ['Primary', 'Secondary', 'Tertiary'])->orderBy('id', 'DESC')->get();
        $patientinfo = Patient::where('id', $patientid)->first();
        // Export the archive details
        if ($export != '') {
            $ins_arc_r = $ins_arc_list = array();
            foreach ($archiveinsurance as $key => $archiveinsurance_val) {
                $ins_arc_r['insurance_name'] = $archiveinsurance_val->insurance_details->insurance_name;
                $ins_arc_r['category'] = $archiveinsurance_val->category;
                $ins_arc_r['relationship'] = $archiveinsurance_val->relationship;
                $ins_arc_r['policy_id'] = $archiveinsurance_val->policy_id;
                $ins_arc_r['from_to'] = ($archiveinsurance_val->active_from != '0000-00-00 00:00:00') ? "[ " . Helpers::dateFormat(@$archiveinsurance_val->active_from, 'date') . " To " . Helpers::dateFormat(@$archiveinsurance_val->active_to, 'date') . " ]" : '-';
                $ins_arc_list[$key] = $ins_arc_r;
            }
            $get_ins_archlist = json_decode(json_encode($ins_arc_list));

            $exportparam = array(
                'filename' => 'Insurance Archive',
                'heading' => '',
                'fields' => array(
                    'insurance_name' => 'Insurance Name',
                    'category' => 'Category',
                    'relationship' => 'Relationship',
                    'policy_id' => 'Policy Id',
                    'from_to' => 'From / To'
                )
            );
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $get_ins_archlist, $export);
        }

        return Response::json(array('data' => compact('archiveinsurance', 'patientinfo')));
    }

    //create patient account number
    public function create_patient_accno($pat_id) {
        /*
        //$acc_no = 'ACC'.date('Y').$pat_id;
        // Append prefix practice
        $practice_name = App\Models\Practice::getPracticeName();        
        $practice_Arr = explode(" ", $practice_name);
        if(COUNT($practice_Arr) > 2 ) {
            $practice_prefix = $practice_Arr[0][0].$practice_Arr[1][0].$practice_Arr[2][0];    
        } elseif(COUNT($practice_Arr) > 1 ) {
            $practice_prefix = $practice_Arr[0][0].substr($practice_Arr[1],0,2);
        } elseif(COUNT($practice_Arr) == 1) {
            $practice_prefix = substr($practice_Arr[0],0,2);
        } else {
            $practice_prefix = ''; // Default
        }
        $acc_no = strtoupper($practice_prefix).str_pad($pat_id, 5, '0', STR_PAD_LEFT);
        */
        $acc_no = App\Models\Patients\Patient::generatePatientAccNo($pat_id);
        return $acc_no;
    }

    /*     * * Check Insurance Category exist or not if exist it will deleted function start ** */

    public function checkInsuranceCategoryExist($pat_id, $category) {
        $category_arr = PatientInsurance::where("patient_id", $pat_id)->whereIn("category", ['Primary', 'Secondary', 'Tertiary'])->pluck("category")->all();
        if (in_array($category, $category_arr)) {
            $patient_insurance = PatientInsurance::where("patient_id", $pat_id)->where("category", $category)->first();
            if ($patient_insurance->category == 'Primary' || $patient_insurance->category == 'Secondary' || $patient_insurance->category == 'Tertiary') {
                $this->doArchivePatientInsurance($patient_insurance, 'Deleted');
            }
            Document::where('type_id', $patient_insurance->id)->where('document_type', 'patients')->where('document_sub_type', 'insurance')->delete();
            $patient_insurance->delete();

            /* $patient     = Patient::where('id',$pat_id)->first();
              if(PatientInsurance::where('patient_id',$pat_id)->count() ==0 && $patient->ins_percentage>0){
              $patient->ins_percentage = 0;
              $patient->percentage -= 40;
              $patient->save();
              } */
            return true;
        }
        return true;
    }

    /*     * * Check Insurance Category exist or not if exist it will deleted function end ** */

    /*     * * Check Insurance Policy ID with Insurance function start ** */

    public function checkPolicyIdwithIns($pat_id, $ins_id, $policy_id) {
        return (PatientInsurance::where('patient_id', $pat_id)->where('insurance_id', $ins_id)->where('policy_id', $policy_id)->count() > 0) ? false : true;
    }

    /*     * * Check Insurance Policy ID with Insurance function end ** */

    /*     * * Move archiveinsurance to insurance function starts ** */
    /* public function moveArchchivetoInsuranceApi($patientid,$arcid)
      {
      $patient_id           = Helpers::getEncodeAndDecodeOfId($patientid,'decode');
      $arc_id           = Helpers::getEncodeAndDecodeOfId($arcid,'decode');
      if ((isset($patient_id) && is_numeric($patient_id)) && (Patient::where('id', $patient_id)->count()) > 0)
      {
      if ((isset($arc_id) && is_numeric($arc_id)) && (PatientInsuranceArchive::where('id',$arc_id)->count()) > 0)
      {
      $archive_ins_arr = PatientInsuranceArchive::where('id',$arc_id)->first()->toArray();
      $ins_policy_exist = $this->checkPolicyIdwithIns($patient_id,$archive_ins_arr['insurance_id'],$archive_ins_arr['policy_id']);
      if($ins_policy_exist)
      {
      $ins_cat_exist = $this->checkInsuranceCategoryExist($patient_id,$archive_ins_arr['category']);
      if($ins_cat_exist)
      {
      $archive_ins_arr['active_from']   = date("Y-m-d H:i:s");
      if($archive_ins_arr['category']=="Primary")
      $archive_ins_arr['orderby_category'] = 1;
      elseif($archive_ins_arr['category']=="Secondary")
      $archive_ins_arr['orderby_category'] = 2;
      elseif($archive_ins_arr['category']=="Tertiary")
      $archive_ins_arr['orderby_category'] = 3;
      else
      $archive_ins_arr['orderby_category'] = 8;

      $patient_insurance = PatientInsurance::create($archive_ins_arr);
      $patient  = Patient::where('id',$patient_id)->first();
      if($patient->ins_percentage==0)
      {
      $patient->ins_percentage = 40;
      $patient->percentage += 40;
      $patient->save();
      }
      $address_flag = array();
      $address_flag['type'] = 'patients';
      $address_flag['type_id'] = $patient_insurance->id;
      $address_flag['type_category'] = 'patient_insurance_address';
      $address_flag['address2'] = $archive_ins_arr['insured_address1'];
      $address_flag['city'] = $archive_ins_arr['insured_city'];
      $address_flag['state'] = $archive_ins_arr['insured_state'];
      $address_flag['zip5'] =$archive_ins_arr['insured_zip5'];
      $address_flag['zip4'] = $archive_ins_arr['insured_zip4'];
      $address_flag['is_address_match'] = '';
      $address_flag['error_message'] = '';
      AddressFlag::checkAndInsertAddressFlag($address_flag);
     */
    /*     * * user activity ** */
    /* $action      = "edit";
      $get_name = $patient->last_name.', '.$patient->first_name.' '.$patient->middle_name;
      $url      = Request::url();
      $split_url    = explode("/archiveinsurance",$url);
      $fetch_url    = $split_url[0].'/edit/insurance';
      $module   = "patients";
      $submodule    = "Insurance";
      $this->user_activity($module,$action,$get_name,$fetch_url,$submodule); */
    /*     * * user activity ** */

    /*  return Response::json(array('status' => 'success', 'message' =>Lang::get("common.validation.create_msg"), 'data' => 'null'));
      }
      }
      else
      {
      $message = '<div class="med-orange" >'.$archive_ins_arr['policy_id']."</div> This ".Lang::get("practice/patients/patients.validation.policyid_unique");
      return Response::json(array('status' => 'failure', 'message' =>$message, 'data' => 'null'));
      }
      }
      else
      {
      return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
      }
      }
      else
      {
      return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
      }
      } */
    /*     * * Move archiveinsurance to insurance function end ** */

    /*     * * Move insurance details from archive Params : patient id, patient insurance id ** */

    public function getMoveArcInsuranceFormApi($patientid, $arcid) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patientid, 'decode');
        $arc_id = Helpers::getEncodeAndDecodeOfId($arcid, 'decode');

        // Check the patient exist or not.
        if ((isset($patient_id) && is_numeric($patient_id)) && (Patient::where('id', $patient_id)->count()) > 0) {
            // Check the patient insurance exist or not.
            if ((isset($arc_id) && is_numeric($arc_id)) && (PatientInsurance::where('id', $arc_id)->count()) > 0) {
                $archive_ins = PatientInsurance::where('id', $arc_id)->first();
                $insurances = Insurance::where('status', 'Active')->selectRaw('CONCAT(id,"::",insurancetype_id) as concatid, insurance_name,id')->orderBy('insurance_name', 'ASC')->pluck('insurance_name', 'id')->all();
                $patient = Patient::findOrFail($patient_id);
                $registration = Registration::first();

                $patient_insurances_cate_arr = PatientInsurance::where('patient_id', $patient_id)->orderBy('orderby_category', 'asc')->pluck('id', 'category')->all();

                // Pass all category id to check category exist or not
                $primary_ins_id = @$patient_insurances_cate_arr['Primary'];
                $secondary_ins_id = @$patient_insurances_cate_arr['Secondary'];
                $tertiary_ins_id = @$patient_insurances_cate_arr['Tertiary'];
                $workerscomp_ins_id = @$patient_insurances_cate_arr['Workerscomp'];
                $autoaccident_ins_id = @$patient_insurances_cate_arr['Autoaccident'];
                $attorney_ins_id = @$patient_insurances_cate_arr['Attorney'];

                $insurancetypes = InsuranceType::orderBy('type_name', 'ASC')->pluck('type_name', 'id')->all();
                $medical_secondary_list = MedicalSecondary::orderBy('code', 'ASC')->pluck('description', 'code')->all();

                // Pass category for constant into the form.
                $category = ['' => '-- Select --', 'Primary' => 'Primary', 'Secondary' => 'Secondary', 'Tertiary' => 'Tertiary', 'Workers Comp' => 'Workers Comp', 'Auto Accident' => 'Auto Accident', 'Attorney' => 'Attorney', 'Others' => 'Others'];

                $collect_category = PatientInsurance::where('patient_id', $patient_id)->whereIn('category', ['Primary', 'Secondary', 'Tertiary'])->pluck('category', 'id')->all();
                // Remove already added category using the function.
                $category = array_diff($category, $collect_category);

                if ($archive_ins->effective_date != '0000-00-00') {
                    $archive_ins->effective_date = Helpers::dateFormat($archive_ins->effective_date, 'claimdate');
                } else {
                    $archive_ins->effective_date = '';
                }

                if ($archive_ins->termination_date != '0000-00-00') {
                    $archive_ins->termination_date = Helpers::dateFormat($archive_ins->termination_date, 'claimdate');
                } else {
                    $archive_ins->termination_date = '';
                }
                /*
                  If self insurance the insured name set empty details
                 */
                if ($archive_ins->relationship == 'Self') {
                    $archive_ins->first_name = '';
                    $archive_ins->last_name = '';
                    $archive_ins->middle_name = '';
                    $archive_ins->insured_ssn = '';
                    $archive_ins->insured_dob = '';
                    $archive_ins->insured_address1 = '';
                    $archive_ins->insured_address2 = '';
                    $archive_ins->insured_city = '';
                    $archive_ins->insured_state = '';
                    $archive_ins->insured_zip5 = '';
                    $archive_ins->insured_zip4 = '';
                }
                // return registration, category, patient insurance, insurance, patients, insurance types, medical secondary table records and also each insurance id details.
                return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('registration', 'category', 'archive_ins', 'insurances', 'patient', 'insurancetypes', 'medical_secondary_list', 'primary_ins_id', 'secondary_ins_id', 'tertiary_ins_id', 'workerscomp_ins_id', 'autoaccident_ins_id', 'attorney_ins_id')));
            } else {
                return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
            }
        } else {
            return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => 'null'));
        }
    }

    /* Employer Search in */

    public static function employerSearchApi() {
        /* Request is employer name based search  */
        $request = Request::all();
        $emp_name = $request['term'];
        //list of matching key are store in employer
        $employers = Employer::where('employer_name', 'like', '%' . $emp_name . '%')->pluck('employer_name', 'id')->all();
        $data = array();
        /* Employer key and value assigned */
        foreach ($employers as $key => $employer) {
            $data[] = array('value' => $employer, 'id' => $key);
        }
        /* Employer Encode the data is not empty */
       // if (count($data))
            return json_encode($data);
    }

    /* Select key based match employer */

    public static function employerAddressApi() {
        /* Selected key is came in request */
        $request = Request::all();
        $emp_name = $request['term']['label'];
        $employers = Employer::where('employer_name', 'like', '%' . $emp_name . '%')->select('id', 'employer_name', 'address1', 'address2', 'city', 'State', 'zip5', 'zip4', 'work_phone', 'work_phone_ext')->first();
        /* Employer Encode the values  */
        if (count((array)$employers))
            return json_encode($employers);
    }

    /* getting eligibility details start  */

    public function getEligibilityApi($pat_id, $ins_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($pat_id, 'decode');
        $patients = Patient::where('id', $patient_id)->select('id', 'last_name', 'middle_name', 'first_name', 'dob')->get();
        $insurance = PatientInsurance::with(['insurance_details'])->where('patient_id', $patient_id)->where('id', $ins_id)->select('relationship', 'last_name', 'first_name', 'middle_name', 'policy_id', 'insurance_id')->get();

        return Response::json(array('data' => compact('insurance', 'patients')));
    }

    /* getting eligibility details end */

    function __destruct() {
        
    }

    /*
     * Patient import function for pms
     * Reading data form excel 
     * Author:      Selvakumar 
     * Created on:  22Sep2017 
     */


    public function startsWith($haystack, $needle) {
         $length = strlen($needle);
         return (substr($haystack, 0, $length) === $needle);
    }

    public function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public function importExcelData($file) {
        $resultArr = [];
        try {
            
            $pat = 0;
            //$handle = fopen(Input::file('file'),"r");
            //$data = fgetcsv($handle);
            ini_set('auto_detect_line_endings',TRUE);

            //$file = './Daniel/Daniel Fisher - Patient List with insurance info.csv';// File path
            $handle = fopen(@$file, "r");
            //$handle = $this->utf8_fopen_read("billinglog_missings.xlsx"); 
            if($handle) {
                $i = 0;

                $unsetArr = array('Primary Care Provider (PCP)', 'Date Last Seen PCP', 'Supervising Physician', 'Symptom Type',
                    'Symptom Date', 'Work Phone', 'Similar Symptom', 'Responsible', 'Similar Symptom Date', 'Status',
                    'Lab Charges', 'Lab Charges Amount', 'Next Recall Date', 'Co-Pay', 'Patient Code', 'Primary Type', 
                    'Patient Type', 'Managed Care Plan', 'Managed Care Payment', 'Primary Accept Assign',  
                    'New Pat. Date', 'Primary Claim Number', 'Fax Phone', 'Pager Phone', 'Secondary Type', 'Other Phone',
                    'Work E-Mail', 'Secondary Authorization', 'Secondary Accept Assign', 'Secondary Claim Number',
                    'Tertiary Type', 'Facility', 'Tertiary Authorization', 'Referring Pysician', 'Tertiary Accept Assign',
                    'Referring Patient', 'Referral Date', 'Attorney');

               // while(($csvDatas = fgetcsv($handle, 1000, ",")) !== false) {
                while(($csvDatas = fgetcsv($handle, 1000, ",")) !== false) {

                    $respArr = array_values(array_filter($csvDatas)); // read csv line convert into key value pair with out empty 
                    
                    
                    if(!empty($respArr)) {               
                        if (strpos($respArr[0], '(041') !== false) {    
                            $pat++;        
                            $resultArr[$pat]['name'] = $respArr[0];    
                        } else {
                            if($this->endsWith($respArr[0],":")) {         
                                $key = str_replace(":","",$respArr[0]);                       
                                if (!in_array($key, $unsetArr)) {
                                    $resultArr[$pat][$key] = isset($respArr[1]) ? $respArr[1] : '';    
                               }
                            }                        
                        }
                        //dd("stop");
                    }
                    $i++;                    
                }
            } else {
                echo "Invalid File";
            }    
            //echo "<pre>";  print_r($resultArr);
            //dd("stop");

            $importedData = $this->importPatientEntry($resultArr);
            //dd($importedData);
            ini_set('auto_detect_line_endings',FALSE);
            // For get unique key array
            $uniqArr = [];
            foreach($resultArr AS $pat) {
                $uniqArr = array_unique($uniqArr + array_keys($pat));
            }
           // echo "<pre>"; print_r($uniqArr);
        } catch(Exception $e) {
            die("Error occured while import ". $e->getMessage() );
        }            
        return $resultArr;
    }

    public function checkInsExist($patient_id) {
        $ins = PatientInsurance::where('patient_id', $patient_id)->count();        
        if($ins > 0){
            return 'No';
        } else {
            return 'Yes';
        }
    }

    public function importPatientEntry($patrequest) {
        //dd($patrequest);
        $patient_details = [];
        $pat_codes = [];
        try{
            foreach($patrequest as $request) {            
                
                //echo "<pre>"; print_r($request);
                if($request['name'] != ''){
                    $names_arr = explode(", ", $request['name']);

                    $pat_details['last_name'] = @$names_arr[0];

                    // Bill Cycle
                    $first_letter = strtoupper(substr($pat_details['last_name'], 0, 1));
                    $bill_cycle_arr = ['A-G', 'H-M', 'N-S', 'T-Z'];
                    for ($i = 0; $i < count($bill_cycle_arr); $i++) {
                        $str_arr = explode('-', $bill_cycle_arr[$i]);
                        if ($first_letter >= $str_arr[0] && $first_letter <= $str_arr[1]) {
                            $pat_details['bill_cycle'] = $str_arr[0] . " - " . $str_arr[1];
                        }
                    }

                    $firstnames = (isset($names_arr[1])) ? explode(" ", trim($names_arr[1])) : [];
                    $pat_details['first_name'] = isset($firstnames[0]) ? $firstnames[0] : '';
                    if( isset($firstnames[1]) ) {
                      if($this->endsWith($firstnames[1],")" ))  {
                        $pat_details['middle_name'] = '';
                        $pat_details['temp_id'] = str_replace(")","", str_replace("(","", @$firstnames[1]));
                      } else {
                        $pat_details['middle_name'] = isset($firstnames[1]) ? $firstnames[1] : '';
                        $pat_details['temp_id'] = str_replace(")","", str_replace("(","", @$firstnames[2]));    
                      }
                    } 
                }
                //  [2] => Address 1            @@@ address1    
                $pat_details['address1'] =  (!empty($request['Address 1'])) ? $request['Address 1'] : " ";  
                // [4] => Address 2            @@@ address2    
                $pat_details['address2'] =  (!empty($request['Address 2'])) ? $request['Address 2'] : " ";  

                //       [6] => City             @@@ city    
                if(isset($request['City']))
                    $pat_details['city'] = $request['City'];

                //[7] => State            @@@ state   
                if(isset($request['State']))
                    $pat_details['state'] = $request['State'];
                                
                //[8] => Zip Code         @@@ zip5/zip4  
                $pat_details['zip5'] = $pat_details['zip4'] ="";
                if(isset($request['Zip Code'])) {
                    $zips = explode("-", $request['Zip Code']);
                    $pat_details['zip5'] = isset($zips[0]) ? $zips[0] : '';
                    $pat_details['zip4'] = isset($zips[1]) ? $zips[1] : '';
                }
                
                // [10] => Home Phone              Phone
                if(isset($request['Home Phone']))
                    $pat_details['phone'] = $request['Home Phone'];   
                
                // [16] => Active/Inactive         status
                if(isset($request['Active/Inactive']))
                    $pat_details['status'] = $request['Active/Inactive'];

                // [18] => Social Security #           ssn
                if(isset($request['Social Security #']))
                    $pat_details['ssn'] = $request['Social Security #'];
                
                
                if (isset($request['Date of Death']))
                    $request['deceased_date'] = date('Y-m-d', strtotime($request['Date of Death'])); 
                
                //  [20] => Birth Date              dob
                if(isset($request['Birth Date']))
                    $pat_details['dob'] = date('Y-m-d', strtotime($request['Birth Date']));

                //[22] => Sex                 gender
                if(isset($request['Sex']))
                    $pat_details['gender'] = $request['Sex'];

                // [24] => Marital Status          marital_status
                if(isset($request['Marital Status']))
                    $pat_details['marital_status'] = $request['Marital Status'];

                //[25] => Employment Status           employment_status   
                if(isset($request['Employment Status']))
                    $pat_details['employment_status'] = $request['Employment Status'];

                //[67] => Provider                    @@@ patients -> provider_id
                if(isset($request['Provider'])) {
                    $prvDet = explode(",", $request['Provider']);
                    $provider_name = @trim($prvDet[0]);
                    $provider_degree = @trim($prvDet[1]);        
                    $pat_details['provider_id'] = $this->checkAndGetProviderId($provider_name, $provider_degree);
                }

              //  dd($pat_details);
                //DB::table('patients')->insert($pat_details);
                //$pat_id = DB::getPdo()->lastInsertId();

                if(!in_array($pat_details['temp_id'], $pat_codes)) {
                      
                    

                    $pat_codes[] = $pat_details['temp_id'];
                    echo "<pre>"; print_r($pat_details);
                    unset($pat_details['temp_id']);
                    $result = Patient::create($pat_details);
                 
                    
                    $patient = Patient::where('id', $result->id)->first();
                    /*
                    if (!empty($patient_data['primary_insurance'])) {
                        $patient_data['primary_insurance']['patient_id'] = $result->id;
                        $patient_insurance = PatientInsurance::create($patient_data['primary_insurance']);
                        if ($patient->ins_percentage == 0) {
                            $patient->ins_percentage = 40;
                            $patient->percentage += 40;
                            $patient->save();
                        }
                    }
                    if (!empty($patient_data['secondary_insurance'])) {
                        $patient_data['secondary_insurance']['patient_id'] = $result->id;
                        $patient_insurance = PatientInsurance::create($patient_data['secondary_insurance']);
                    }
                    */

                    $patient->account_no = $this->create_patient_accno($result->id);
                    $patient->save();

                    $patID = $result->id;
                    //echo "<br>##".$patID;

                    if(isset($request['Primary Insurance'])) {
                        $pri_insurance = trim($request['Primary Insurance']);
                        $primary_details['insurance_id'] = $this->checkAndGetInsuranceID($pri_insurance);
                        $primary_details['patient_id'] = $patID;
                        $primary_details['category'] = 'Primary';
                        $primary_details['group_name'] = isset($request['Primary Group Number']) ? $request['Primary Group Number'] : '';
                        $primary_details['policy_id'] = $request['Primary ID'];
                        $primary_details['last_name'] = $primary_details['first_name'] = $primary_details['first_name'] = '';
                        if(isset($request['Primary Insured']) && $request['Primary Insured'] != '' && $request['Primary Insured'] != 'Self') {
                            $insName = explode(" ", trim($request['Primary Insured']));
                            $primary_details['last_name'] = isset($insName[0]) ? $insName[0] : '';
                            $primary_details['first_name'] = isset($insName[1]) ? $insName[1] : '';
                            $primary_details['middle_name'] = isset($insName[2]) ? $insName[2] : '';
                        }
                        $primary_details['relationship'] = isset($request['Primary Insured Relation']) ? $request['Primary Insured Relation'] : 'Self';
                        $pri_insurance = PatientInsurance::create($primary_details);
                        $patient->is_self_pay = 'No';
                    }


                    if(isset($request['Secondary Insurance']) && $request['Secondary Insurance'] != '') {
                        $sec_insurance = trim($request['Secondary Insurance']);
                        $secondary_details = [];    
                        $primary_details['insurance_id'] = $this->checkAndGetInsuranceID($sec_insurance);
                        $secondary_details['patient_id'] = $patID;
                        $secondary_details['category'] = 'Secondary';
                        $secondary_details['group_name'] = isset($request['Secondary Group Number']) ? $request['Secondary Group Number'] : '';
                        $secondary_details['last_name'] = $secondary_details['first_name'] = $secondary_details['first_name'] = '';
                        if(isset($request['Secondary Insured']) && $request['Secondary Insured'] != '' && $request['Secondary Insured'] != 'Self') {
                            $insName = explode(" ", trim($request['Secondary Insured']));
                            $secondary_details['last_name'] = isset($insName[0]) ? $insName[0] : '';
                            $secondary_details['first_name'] = isset($insName[1]) ? $insName[1] : '';
                            $secondary_details['middle_name'] = isset($insName[2]) ? $insName[2] : '';
                        }
                        $secondary_details['relationship'] = isset($request['Secondary Insured Relation']) ? $request['Secondary Insured Relation'] : 'Self';
                        $patient_insurance = PatientInsurance::create($secondary_details);
                        $patient->is_self_pay = 'No';
                    }
                    

                    if(isset($request['Tertiary Insurance']) && $request['Tertiary Insurance'] != '') {
                        $tri_insurance = trim($request['Tertiary Insurance']);
                        $tertiary_details = [];    
                        $primary_details['insurance_id'] = $this->checkAndGetInsuranceID($tri_insurance);
                        $tertiary_details['patient_id'] = $patID;
                        $tertiary_details['category'] = 'Tertiary';
                        $tertiary_details['group_name'] = isset($request['Tertiary Group Number']) ? $request['Tertiary Group Number'] : '';

                        $tertiary_details['last_name'] = $tertiary_details['first_name'] = $tertiary_details['middle_name'] = '';
                        if(isset($request['Tertiary Insured']) && $request['Tertiary Insured'] != '' && $request['Tertiary Insured'] != 'Self') {
                            $insName = explode(" ", trim($request['Tertiary Insured']));
                            $tertiary_details['last_name'] = isset($insName[0]) ? $insName[0] : '';
                            $tertiary_details['first_name'] = isset($insName[1]) ? $insName[1] : '';
                            $tertiary_details['middle_name'] = isset($insName[2]) ? $insName[2] : '';
                        }
                        $tertiary_details['relationship'] = isset($request['Tertiary Insured Relation']) ?  $request['Tertiary Insured Relation'] : 'Self';
                        $patient_insurance = PatientInsurance::create($tertiary_details);
                        $patient->is_self_pay = 'No';
                    }    
                    $patient->save();
                }                
            }
        } catch (Exception $e)  {
            dd("Exception occured ".$e->getMessage() );

        }
        dd($request);
    }

    public function checkAndGetInsuranceID($ins_name) {
        $insurance = Insurance::where('insurance_name', $ins_name)->first();
        if(!empty($insurance)) {
            return $insurance->id;
        } else {
            $data = new Insurance;
            $data->insurance_name = $ins_name;
            $data->save();
            return $data->id; 
        }

    }

    public function checkAndGetProviderId($name, $degree) {
        $providerDegree = Provider_degree::where('degree_name', $degree)->first();
        if(!empty($providerDegree)) {
            $provider_degrees_id = $providerDegree->id;
        } else {
            $data = new Provider_degree;
            $data->degree_name = $degree;
            $data->save();
            $provider_degrees_id = $data->id;
        }

        $provider = Provider::where('provider_name', $name)->first();
        if(!empty($provider)) {
            return $provider->id;
        } else {
            $provder_data = new Provider;
            $provder_data->provider_name = $name;
            $namesArr = explode(".", $name);
            if(isset($namesArr[0])) {
                $lnames = explode(" ", $namesArr[0]);
                $provder_data->last_name = $last_name = @$lnames[0];
                $provder_data->middle_name = $middle_name = @$lnames[1];
            }            
            $provder_data->first_name = $first_name = @$namesArr[1]; 
            $provder_data->short_name = strtoupper(substr($first_name, 0, 1)).strtoupper(substr($middle_name, 0, 1)).strtoupper(substr(@$last_name, 0, 1));
            $provder_data->provider_types_id = 1;
            $provder_data->save();
            return $provder_data->id;
        }
    }
   
    public function getEXLDataApi() {
        ini_set('max_execution_time', 0);
        $file_name = "9001-10000_1.xlsx";
        if (App::environment() == "production")
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/media/import/';
        else
            $file_path = public_path('media/import/');
        $dataKey = $dataArr = [];

        $keyArr = [];
        $valueArr = [];
        
        try { 
            foreach (glob($file_path . $file_name) as $list) {  
                Excel::load($list, function($reader) { 
                    $reader->noHeading();
                    $reader->ignoreEmpty();
                    $reader->setSeparator('~');
                    $reader->each(function($sheet) {
                        $sheet->each(function($row) {
                            
                             if (!empty($row) && ($row != "Case Description:")) {
                                if ($row == "Patient Name:")
                                    $this->patient_count++;
                                if ($this->endsWith($row, ":") || $row == 'Date of Birth') {
                                    $this->arryset($row, "key", $this->patient_count);
                                } else {
                                    $this->arryset($row, 'value', $this->patient_count);
                                }
                            }
                        });
                    });
                });
            }
        } catch (Exception $e) {
            echo "Errors : " . $e->getMessage();
        }
        
        $final_patient_array = array_diff_key($this->valueArr, $this->patient_name_Arr);
        echo "full Array" . count($this->valueArr) . "<br>";
        echo "<pre>";
        //print_r($this->valueArr);
        echo "rejected Array" . count($this->patient_name_Arr) . "<br>";
        echo "<pre>";
       // print_r($this->patient_name_Arr);
        echo "final Array" . count($final_patient_array) . "<br>";
        echo "<pre>";
        //print_r($final_patient_array);
        echo "Success";
        //Log::error(print_r($this->patient_name_Arr, TRUE));
        Log::useFiles(storage_path().'/logs/patientErrorLog.log');
        Log::info('Patient Error Log');
        Log::info('File Name:'.$file_name);
        Log::info($this->patient_name_Arr);
        Log::useFiles(storage_path().'/logs/patientSuccessLog.log');
        Log::info('Patient Success Log');
        Log::info('File Name:'.$file_name);
        Log::info($final_patient_array);
        //dd(\DB::getDatabaseName());
        //$this->patient_import($final_patient_array);
        $this->patient_update($this->valueArr);
        echo "Success";
        die;
    }

    public function arryset($value, $type, $patient_count) {
        if ($type == "key") {
            switch ($value) {
                case 'Patient Name:':
                    $this->prev_key = "patient_name";
                    $this->city_state_count = 0;
                    $this->patientMobile = 0;
                    $this->address = 0;
                    break;
                case 'Date of Birth':
                    $this->prev_key = 'dob';
                    break;
                case 'Address:':
                    $this->prev_key = 'address1';
                    break;
                case 'Sex:':
                    $this->prev_key = 'gender';
                    break;
                case 'City, State, Zip:':
                    if ($this->city_state_count == 0) {
                        $this->prev_key = 'city_state_zip';
                        $this->city_state_count = 1;
                    }
                    break;
                case 'SSN:':
                    $this->prev_key = 'ssn';
                    break;
                case 'Marital Status:':
                    $this->prev_key = 'marital_status';
                    break;
                case 'Telephone:':
                    if($this->patientMobile == 0){
                        $this->prev_key = 'mobile';
                        $this->patientMobile = 1;
                    }
                    break;
                case 'Assigned Provider:':
                    $this->prev_key = 'provider_id';
                    break;
                case 'Last Visit Date:':
                    $this->prev_key = 'last_vist_date';
                    break;
                case 'Guarantor:':
                    $this->prev_key = 'gurantor_name';
                    break;
                case 'Insured 1 Name:':
                    $this->prev_key = 'insured1_name';
                    break;
                case 'Insurance 1 Name:':
                    $this->prev_key = 'insurance_name';
                    break;
                case 'Policy Number:':
                    $this->prev_key = 'insurance_policy_id';
                    break;
                case 'Ins Start Date:':
                    $this->prev_key = 'effective_date';
                    break;
                case 'Ins End Date:':
                    $this->prev_key = 'termination_date';
                    break;
                case 'Insured 2 Name:':
                    $this->prev_key = 'insured2_name';
                    break;
                case 'Insurance 2 Name:':
                    $this->prev_key = 'secondary_insurance_name';
                    break;
                default:
                    $this->prev_key = $value;
            }
        } elseif ($type == 'value') {
            switch ($this->prev_key) {
                case 'patient_name':
                    $name = explode(',', $value);
                    $this->valueArr[$patient_count]['last_name'] = trim($name[0]);
                    $this->valueArr[$patient_count]['first_name'] = trim($name[1]);
                    $this->primary_insured_name = '';
                    break;

                case 'dob':
                    if (!empty($value)) {
                        $this->valueArr[$patient_count]['dob'] = (isset($value)) ? date('Y-m-d', strtotime($value)) : '0000-00-00';
                    }
                    break;

                case 'address1':

                    if ($this->address == 0) {
                        if (!empty($value)) {
                            $this->valueArr[$patient_count]['address1'] = $value;
                        } else {
                            $this->valueArr[$patient_count]['address1'] = $value;
                            $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                            $this->patient_name_Arr[$patient_count]['error_desc'] = 'Patient Address Missing';
                        }
                        $this->address = 1;
                    }
                    break;

                case 'city_state_zip':
                    $city = explode(',', $value);
                    if (count($city) > 2) {
                        $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                        $this->patient_name_Arr[$patient_count]['error_desc'] = 'Patient city state zipcode confused';
                    } else {
                        if (!empty($city[1]))
                            $state = explode(' ', trim($city[1]));
                        if (!empty($state[1]))
                            $zip = explode('-', trim($state[1]));
                        if (!empty($city[0]))
                            $this->valueArr[$patient_count]['city'] = $city[0];
                        if (!empty($state[0]))
                            $this->valueArr[$patient_count]['state'] = $state[0];
                        if (!empty($zip[0]))
                            $this->valueArr[$patient_count]['zip5'] = $zip[0];
                        if (!empty($zip[1]))
                            $this->valueArr[$patient_count]['zip4'] = $zip[1];
                    }
                    break;

                case 'gender':
                    $this->valueArr[$patient_count][$this->prev_key] = $value;
                    break;  

                case 'ssn':
                    if (!empty($value)) {
                        $this->valueArr[$patient_count][$this->prev_key] = str_replace('-', '', $value);
                        $patient_counts = Patient::where('ssn', str_replace('-', '', $value))->count();
                        if ($patient_counts != 0) {
                            $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                            $this->patient_name_Arr[$patient_count]['error_desc'] = 'Patient Already Exits';
                        }
                    } elseif (!empty($this->valueArr[$patient_count]['last_name']) && !empty($this->valueArr[$patient_count]['first_name'])) {
                        $this->valueArr[$patient_count][$this->prev_key] = str_replace('-', '', $value);
                        $patient_counts = Patient::where('last_name', $this->valueArr[$patient_count]['last_name'])->where('first_name', $this->valueArr[$patient_count]['first_name'])->count();
                        if ($patient_counts != 0) {
                            $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                            $this->patient_name_Arr[$patient_count]['error_desc'] = 'Patient Already Exits';
                        }
                    }
                    break;

                case 'mobile':
                    $this->valueArr[$patient_count][$this->prev_key] = (strlen($value) == 13 && strpos($value, '-') !== false) ? $value : '';
                    break;

                case 'provider_id':
                    $provider = explode(',', $value);
                    $provider_name = explode(' ', $provider[0]);
                    $provider_degree_id = Provider_degree::where('degree_name', trim($provider[1]))->pluck('id')->first(); 
                    $provider_id = Provider::where('provider_degrees_id', $provider_degree_id)->where('last_name', trim($provider_name[1]))->where('first_name', trim($provider_name[0]))->where('provider_types_id', '1')->pluck('id')->first();
                    $this->valueArr[$patient_count][$this->prev_key] = (empty($provider_id)) ? 1 : $provider_id;
                    if (empty($provider_id)) {
                        $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                        $this->patient_name_Arr[$patient_count]['error_desc'] = 'Invalid Provider';
                    }
                    break;

                case 'last_vist_date':
                    $date1 = date_create(date('Y-m-d'));
                    $date2 = date_create(date('Y-m-d', strtotime($value)));
                    $diff = date_diff($date1, $date2);
                    $year = $diff->format("%y");
                    if ($year > 3) {
                        $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                        $this->patient_name_Arr[$patient_count]['error_desc'] = 'More than three years patient';
                    }
                    break;

                case 'gurantor_name':
                    $gurantor_name = explode(',', $value);
                    if ($this->valueArr[$patient_count]['last_name'] != trim($gurantor_name[0]) && $this->valueArr[$patient_count]['first_name'] != trim($gurantor_name[1])) {
                        $this->valueArr[$patient_count][$this->prev_key]['last_name'] = trim($gurantor_name[0]);
                        $this->valueArr[$patient_count][$this->prev_key]['first_name'] = trim($gurantor_name[1]);
                    }
                    break;
                    
                case 'insured1_name':
                    $insured_name = explode(',', $value);
                    if ($this->valueArr[$patient_count]['last_name'] == trim($insured_name[0]) && $this->valueArr[$patient_count]['first_name'] == trim($insured_name[1])) {
                        $this->primary_insured_name = 'primary_insurance';
                        $this->secondary_insured_name = '';
                        $this->valueArr[$patient_count]['primary_insurance']['relationship'] = 'Self';
                        $this->valueArr[$patient_count]['primary_insurance']['orderby_category'] = '1';
                        $this->valueArr[$patient_count]['primary_insurance']['category'] = 'Primary';
                        $this->valueArr[$patient_count]['primary_insurance']['same_patient_address'] = 'yes';
                        $this->valueArr[$patient_count]['primary_insurance']['last_name'] = @$this->valueArr[$patient_count]['last_name'];
                        $this->valueArr[$patient_count]['primary_insurance']['first_name'] = @$this->valueArr[$patient_count]['first_name'];
                        $this->valueArr[$patient_count]['primary_insurance']['insured_address1'] = @$this->valueArr[$patient_count]['address1'];
                        $this->valueArr[$patient_count]['primary_insurance']['insured_city'] = isset($this->valueArr[$patient_count]['city']) ?  @$this->valueArr[$patient_count]['city'] : "";
                        $this->valueArr[$patient_count]['primary_insurance']['insured_state'] = @$this->valueArr[$patient_count]['state'];
                        $this->valueArr[$patient_count]['primary_insurance']['insured_zip5'] = @$this->valueArr[$patient_count]['zip5'];
                        $this->valueArr[$patient_count]['primary_insurance']['insured_zip4'] = (isset($this->valueArr[$patient_count]['zip4'])) ? $this->valueArr[$patient_count]['zip4'] : '0' ;
                        $this->valueArr[$patient_count]['is_self_pay'] = 'No';
                    } else {
                        $this->primary_insured_name = '';
                        $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                        $this->patient_name_Arr[$patient_count]['error_desc'] = 'Primary insurance not self';
                    }
                    break;

                case 'insurance_name':
                    if ($this->primary_insured_name != '') {
                        $insurance_id = Insurance::where('insurance_name', $value)->pluck('id')->first();
                        $this->valueArr[$patient_count]['primary_insurance']['insurance_id'] = $insurance_id;
                        if (empty($insurance_id)) { 
                            $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                            $this->patient_name_Arr[$patient_count]['Insurance_name'] = @$value;
                            $this->patient_name_Arr[$patient_count]['error_desc'] = 'Invalid Primary Insurance Details';
                        }
                    }
                    break;

                case 'insurance_policy_id':
                    if ($this->primary_insured_name != '' && $this->secondary_insured_name == '')
                        $this->valueArr[$patient_count]['primary_insurance']['policy_id'] = $value;
                    elseif ($this->secondary_insured_name != '')
                        $this->valueArr[$patient_count]['secondary_insurance']['policy_id'] = $value;
                    break;
                case 'effective_date':
                    if ($this->primary_insured_name != '' && $value != '' && $this->secondary_insured_name == '')
                        $this->valueArr[$patient_count]['primary_insurance']['effective_date'] = $value;
                    elseif ($value != '' && $this->secondary_insured_name != '')
                        $this->valueArr[$patient_count]['secondary_insurance']['effective_date'] = $value;
                    break;
                case 'termination_date':
                    if ($this->primary_insured_name != '' && $value != '' && $this->secondary_insured_name == '')
                        $this->valueArr[$patient_count]['primary_insurance']['termination_date'] = $value;
                    elseif ($value != '' && $this->secondary_insured_name != '')
                        $this->valueArr[$patient_count]['secondary_insurance']['termination_date'] = $value;
                    break;

                case 'insured2_name':
                    $insured2_name = explode(',', $value);
                    if ($this->valueArr[$patient_count]['last_name'] == trim($insured2_name[0]) && $this->valueArr[$patient_count]['first_name'] == trim($insured2_name[1])) {
                        $this->secondary_insured_name = 'secondary_insurance';
                        $this->valueArr[$patient_count]['secondary_insurance']['relationship'] = 'Self';
                        $this->valueArr[$patient_count]['secondary_insurance']['orderby_category'] = '2';
                        $this->valueArr[$patient_count]['secondary_insurance']['category'] = 'Secondary';
                        $this->valueArr[$patient_count]['secondary_insurance']['patient_id'] = '1';
                        $this->valueArr[$patient_count]['secondary_insurance']['same_patient_address'] = 'yes';
                        $this->valueArr[$patient_count]['secondary_insurance']['last_name'] = @$this->valueArr[$patient_count]['last_name'];
                        $this->valueArr[$patient_count]['secondary_insurance']['first_name'] = @$this->valueArr[$patient_count]['first_name'];
                        $this->valueArr[$patient_count]['secondary_insurance']['insured_address1'] = @$this->valueArr[$patient_count]['address1'];
                        $this->valueArr[$patient_count]['secondary_insurance']['insured_city'] = isset($this->valueArr[$patient_count]['city']) ?  @$this->valueArr[$patient_count]['city'] : "";
                        $this->valueArr[$patient_count]['secondary_insurance']['insured_state'] = @$this->valueArr[$patient_count]['state'];
                        $this->valueArr[$patient_count]['secondary_insurance']['insured_zip5'] = @$this->valueArr[$patient_count]['zip5'];
                        $this->valueArr[$patient_count]['secondary_insurance']['insured_zip4'] = (isset($this->valueArr[$patient_count]['zip4'])) ? $this->valueArr[$patient_count]['zip4'] : '0' ;
                    } else {
                        $this->secondary_insured_name = '';
                        $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                        $this->patient_name_Arr[$patient_count]['error_desc'] = 'Secondary Insurance not self';
                    }
                    break;

                case 'secondary_insurance_name':
                    if ($this->primary_insured_name != '') {
                        $insurance_id = Insurance::where('insurance_name', $value)->pluck('id')->first();
                        $this->valueArr[$patient_count]['secondary_insurance']['insurance_id'] = $insurance_id;
                        if (empty($insurance_id)) {
                            $this->patient_name_Arr[$patient_count]['patient_name'] = $this->valueArr[$patient_count]['last_name'] . ", " . $this->valueArr[$patient_count]['first_name'];
                            $this->patient_name_Arr[$patient_count]['Insurance_name'] = @$value;
                            $this->patient_name_Arr[$patient_count]['error_desc'] = 'Invalid Secondary Insurance Details';
                        }
                    }

                    break;

                default:
                    //$this->valueArr[$patient_count][$this->prev_key] = $value;
                    $this->prev_key = '';
            }
        }
    }

    public function patient_import($dataArr) {
        
        DB::beginTransaction();
        try {
            foreach ($dataArr as $key => $patient_data) {
                $request['last_name'] = $patient_data['last_name'];
                $first_letter = strtoupper(substr($request['last_name'], 0, 1));
                $bill_cycle_arr = ['A-G', 'H-M', 'N-S', 'T-Z'];
                for ($i = 0; $i < count($bill_cycle_arr); $i++) {
                    $str_arr = explode('-', $bill_cycle_arr[$i]);
                    if ($first_letter >= $str_arr[0] && $first_letter <= $str_arr[1]) {
                        $request['bill_cycle'] = $str_arr[0] . " - " . $str_arr[1];
                    }
                }
                $request['first_name'] = @$patient_data['first_name'];
                if (!empty($patient_data['dob']))
                    $request['dob'] = @$patient_data['dob'];
                if (!empty($patient_data['address1']) && !empty($patient_data['city']) && !empty($patient_data['state']) && !empty($patient_data['zip5'])) {
                    $request['address1'] = @$patient_data['address1'];
                    $request['city'] = @$patient_data['city'];
                    $request['state'] = @$patient_data['state'];
                    $request['zip5'] = @$patient_data['zip5'];
                }
                if (!empty($patient_data['ssn']))
                    $request['ssn'] = @$patient_data['ssn'];
                $request['mobile'] = @$patient_data['mobile'];
                $request['gender'] = @$patient_data['gender'];
                $request['provider_id'] = @$patient_data['provider_id'];
                $request['percentage'] = '60';
                $request['created_by'] = Auth::user()->id;
                $request['demo_percentage'] = '60';
                $request['status'] = 'Active';
                $request['email_notification'] = 'No';
                $request['phone_reminder'] = 'No';
                $request['statements'] = 'Yes';
                $request['preferred_communication'] = '';
                $request['language_id'] = '5';              
                
                if (empty($patient_data['primary_insurance']) && empty($patient_data['secondary_insurance'])) {                 
                    $req['is_self_pay'] = 'Yes';
                } else {
                    $req['is_self_pay'] = 'No';
                }
                
                $result = Patient::create($request);
                $patient = Patient::where('id', $result->id)->first();
                if (!empty($patient_data['primary_insurance'])) {
                    $patient_data['primary_insurance']['patient_id'] = $result->id;
                    $patient_insurance = PatientInsurance::create($patient_data['primary_insurance']);
                    if ($patient->ins_percentage == 0) {
                        $patient->ins_percentage = 40;
                        $patient->percentage = 100;
                        $patient->save();
                    }
                } 
                if (!empty($patient_data['secondary_insurance'])) {
                    $patient_data['secondary_insurance']['patient_id'] = $result->id;
                    $patient_insurance = PatientInsurance::create($patient_data['secondary_insurance']);
                }
                
                $patient->account_no = $this->create_patient_accno($result->id);
                $patient->save();
                if (!empty($patient_data['address1']) && !empty($patient_data['city']) && !empty($patient_data['state']) && !empty($patient_data['zip5'])) {
                    $address_flag = array();
                    $address_flag['type'] = 'patients';
                    $address_flag['type_id'] = $result->id;
                    $address_flag['type_category'] = 'personal_info_address';
                    $address_flag['address2'] = $patient_data['address1'];
                    $address_flag['city'] = $patient_data['city'];
                    $address_flag['state'] = $patient_data['state'];
                    $address_flag['zip5'] = $patient_data['zip5'];
                    $address_flag['zip4'] = '';
                    $address_flag['is_address_match'] = '';
                    $address_flag['error_message'] = '';
                    AddressFlag::checkAndInsertAddressFlag($address_flag);
                }               
            }
            DB::commit();
        } catch (\Exception $e) {
            echo 'Error message: ' . $e->getMessage();
            DB::rollback();
        }
    }

    /* Santucci Patient mobile number update function  */
    public function patient_update($dataArr){
        DB::beginTransaction();
        count($dataArr);
        try {
            foreach ($dataArr as $key => $patient_data) {
                $patient_Details = Patient::where('last_name', $patient_data['last_name'])->where('first_name', $patient_data['first_name']);
                if(isset($patient_data['ssn']))
                    $patient_Details->where('ssn',$patient_data['ssn']);
                $patientCount =  $patient_Details->count();
                $patientInfo = $patient_Details->get();
                foreach($patientInfo as $list){
                    if(!isset($patient_data['mobile']))
                        $patient_data['mobile'] = '';
                    Patient::where('id',$list->id)->update(['mobile'=>$patient_data['mobile']]);
                }
            }
            DB::commit();
        }catch (\Exception $e) {
            echo 'Error message: ' . $e->getMessage();
            DB::rollback();
        }
    }   
    
    /*
     * Patient SSN validation  
     * Author:      Selvakumar 
     * Created on:  06Apr2018 
     */
    
    public function checkPatientSsnValidationApi(){
        $request = Request::all();
        if(isset($request['patient_id'])){
            $ssn = Patient::where('ssn',$request['ssn'])->where('id',$request['patient_id'])->get()->count();
            if($ssn == '1'){
                $patient_snn ='0'; 
            }else{
                 $patient_snn = Patient::where('ssn',$request['ssn'])->get()->count();
            }
        }else{
            $patient_snn = Patient::where('ssn',$request['ssn'])->get()->count();
        }
        return Response::json(array('status' => 'success', 'message' => '', 'ssncount' => $patient_snn));
    }
    
    /*
     * Getting Stored Patient Name and DOB validation  
     * Author:      Selvakumar 
     * Created on:  18Apr2018 
     */
     
    public function patientCheckApi() {
        $request = Request::all();
        $patient_id = Helpers::getEncodeAndDecodeOfId(@$request['encode_patient_id'], 'decode');
        $msg = $avble_status = '';
        $dob = (!empty($request['dob'])) ? date('Y-m-d', strtotime($request['dob'])) : "";
        if (isset($patient_id) && !empty($patient_id)) {
            $patient_count = Patient::where('dob', $dob)->where('last_name', $request['last_name'])->where('first_name', $request['first_name'])->where('id', '<>', $patient_id)->get();
            if ($patient_count->count() > 0) {
                $avble_status = 'true';
                $msg = 'Acct – ';
                foreach ($patient_count as $list) {
                    $msg .= $list->account_no . ',';
                }
                $msg = rtrim($msg, ',');
                $msg .= ' - Patient already exists';
            } else {
                $avble_status = 'false';
                $msg = '';
            }
        } else {
            $patient_count = Patient::where('dob', $dob)
                    ->where('last_name', $request['last_name'])
                    ->where('first_name', $request['first_name'])
                    ->get();
            if ($patient_count->count() > 0) {
                $avble_status = 'true';
                $msg = 'Acc No : ';
                foreach ($patient_count as $list) {
                    $msg .= $list->account_no . ',';
                }
                $msg = rtrim($msg, ',');
                $msg .= ' - Patient already exist';
            } else {
                $avble_status = 'false';
                $msg = '';
            }
        }
        return Response::json(array('status' => 'success', 'message' => '', 'avble_status' => $avble_status, 'msg' => $msg));
    }

    // RPG Practice Patient Import Start

    public function getEXLDataApiRPGPatients() {       

        ini_set('max_execution_time', 0);

        if (App::environment() == "production")
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/media/import/';
        else
            $file_path = public_path('media/import/');

        $dataArr = $keyArr = [];        
        
        $file = $file_name = $file_path.'RPG_patients.csv';

        $resultArr = $pat_det = $other_det = $icd_lists = $cpt_lists = $patIds = $claimIds = []; 
        try {
            
            $pat = 0;
            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen(@$file, "r");            
            if($handle) {
                $i = 0; $prePtID = 0; $totalPatients = 0;
               // while(($csvDatas = fgetcsv($handle, 1000, ",")) !== false) {
                while(($csvDatas = fgetcsv($handle, 5000, ",")) !== false) {
                    $respArr = array_values($csvDatas); // read csv line convert into key value pair with out empty 

                    if(!empty($respArr)) {
                        if($i == 0) {
                            $keyArr = array_values($respArr);
                        } else {
                            $totalPatients+=1;
                            $pat++; 
                            $resultArr[$pat]['last_name'] = isset($respArr[0]) ? $respArr[0] :'';    
                            $resultArr[$pat]['first_name'] = isset($respArr[1]) ? $respArr[1] :'';    
                            $resultArr[$pat]['dob'] = isset($respArr[2]) ? date('Y-m-d', strtotime($respArr[2])) : '';  
                            $resultArr[$pat]['address1'] = isset($respArr[3]) ? $respArr[3] :'';  
                            $resultArr[$pat]['address2'] = isset($respArr[4]) ? $respArr[4] :'';  
                            $resultArr[$pat]['city'] = isset($respArr[5]) ? $respArr[5] :'';  
                            $resultArr[$pat]['state'] = isset($respArr[6]) ? $respArr[6] :'';  
                            $resultArr[$pat]['zip5'] = isset($respArr[7]) ? $respArr[7] :'';  
                        }
                        $i++;
                    }                    
                }
            }            
            $resp = $this->patient_import_rpg($resultArr);
            echo "Success";
            die;
        } catch(Exception $e) {
            \Log::info("Error Occured While import patient record to RPG Error:".$e->getMessage() );
            echo "Error ".$e->getMessage();
        }
    }

    public function patient_import_rpg($dataArr) {
        
        DB::beginTransaction();
        $oldPatient = $newPatient = $result = [];
        try {
            $inc = 0;
            foreach ($dataArr as $key => $patient_data) {
                // Check patient SSN if not exist add it, otherwise write into existing patient log 

                $patient_counts = Patient::where('last_name', ucwords(strtolower($patient_data['last_name'])))
                                    ->where('first_name', ucwords(strtolower($patient_data['first_name'])))
                                    ->where('dob', $patient_data['dob'])
                                    ->count();

                if ($patient_data['first_name'] != '' && $patient_data['last_name']  && $patient_counts != 0) {
                    $oldPatient[$inc]['patient_name'] = $patient_data['last_name'] . ", " . $patient_data['first_name'];
                    $oldPatient[$inc]['error_desc'] = 'Patient Already Exits';
                } else {
                    //$newPatient[$inc] = $patient_data['last_name'] . ", " . $patient_data['first_name'];
                    // Add new patient
                    
                    $request['last_name'] = $patient_data['last_name'];
                    $first_letter = strtoupper(substr($request['last_name'], 0, 1));
                    $bill_cycle_arr = ['A-G', 'H-M', 'N-S', 'T-Z'];
                    for ($i = 0; $i < count($bill_cycle_arr); $i++) {
                        $str_arr = explode('-', $bill_cycle_arr[$i]);
                        if ($first_letter >= $str_arr[0] && $first_letter <= $str_arr[1]) {
                            $request['bill_cycle'] = $str_arr[0] . " - " . $str_arr[1];
                        }
                    }
                    $request['first_name'] = @$patient_data['first_name'];

                    if (!empty($patient_data['dob']))
                        $request['dob'] = @$patient_data['dob'];

                    if (!empty($patient_data['address1']) && !empty($patient_data['city']) && !empty($patient_data['state']) && !empty($patient_data['zip5'])) {
                        $request['address1'] = @$patient_data['address1'];
                        $request['address2'] = @$patient_data['address2'];
                        $request['city'] = @$patient_data['city'];
                        $request['state'] = @$patient_data['state'];
                        $request['zip5'] = @$patient_data['zip5'];
                    }
                    
                    $request['percentage'] = '60';
                    $request['created_by'] = Auth::user()->id;
                    $request['demo_percentage'] = '60';
                    $request['status'] = 'Active';
                    $request['email_notification'] = 'No';
                    $request['phone_reminder'] = 'No';
                    $request['statements'] = 'Yes';
                    $request['preferred_communication'] = '';
                    $request['language_id'] = '5'; 

                    $result = Patient::create($request);
                    $patID  = $result->id;
                    $patient = Patient::where('id', $patID)->first();

                    $patient->account_no = $this->create_patient_accno($result->id);
                    $patient->save();

                    $newPatient[$inc]['patient_name'] = $patient_data['last_name'] . ", " . $patient_data['first_name'];
                    $newPatient[$inc]['desc'] = 'Patient Added #'.$patID;

                    echo "<br>### Patient Created ".$patID;
                    
                    /*
                    if (!empty($patient_data['address1']) && !empty($patient_data['city']) && !empty($patient_data['state']) && !empty($patient_data['zip5'])) {
                        $address_flag = array();
                        $address_flag['type'] = 'patients';
                        $address_flag['type_id'] = $patID;
                        $address_flag['type_category'] = 'personal_info_address';
                        $address_flag['address2'] = $patient_data['address1'];
                        $address_flag['city'] = $patient_data['city'];
                        $address_flag['state'] = $patient_data['state'];
                        $address_flag['zip5'] = $patient_data['zip5'];
                        $address_flag['zip4'] = $patient_data['zip4'];
                        $address_flag['is_address_match'] = '';
                        $address_flag['error_message'] = '';
                        AddressFlag::checkAndInsertAddressFlag($address_flag);
                    }          
                    */
                }
                $inc++;
            }            
            
            \Log::info("Existing Patients. Total ".count($oldPatient));
            \Log::info($oldPatient);
            
            \Log::info("New Patients. Total ".count($newPatient));
            \Log::info($newPatient);

            DB::commit();

            $result['new_patients'] = $newPatient;
            $result['old_patients'] = $oldPatient;
            //\Log::info("Failed Patient Insurance"); \Log::info($failed_patient);
            
            return $result;
        } catch (\Exception $e) {
            echo 'Error message: ' . $e->getMessage();
            \Log::info($e);
            DB::rollback();
        }
    }

    // RPG Practice Patient Import End

    // Lord Practice Patient Import Start
    public function getEXLDataApiDRLord() {
        ini_set('max_execution_time', 0);        
        if (App::environment() == "production")
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/media/import/';
        else
            $file_path = public_path('media/import/');

        $dataKey = $dataArr = $keyArr =  $valueArr = [];
        
        $file = $file_name = $file_path.'Dr Lord Demographics.csv';

        $resultArr = [];
        try {
            
            $pat = 0;
            //$handle = fopen(Input::file('file'),"r");
            //$data = fgetcsv($handle);
            ini_set('auto_detect_line_endings',TRUE);

            //$file = './Daniel/Daniel Fisher - Patient List with insurance info.csv';// File path
            $handle = fopen(@$file, "r");
            //$handle = $this->utf8_fopen_read("billinglog_missings.xlsx"); 
            if($handle) {
                $i = 0;
               // while(($csvDatas = fgetcsv($handle, 1000, ",")) !== false) {
                while(($csvDatas = fgetcsv($handle, 3000, ",")) !== false) {
                    //echo "<pre>"; print_r($csvDatas); die;
                    $respArr = array_values($csvDatas); // read csv line convert into key value pair with out empty 
                    //echo "<pre>"; print_r($respArr);
                    if(!empty($respArr)) {
                        if($i == 0) {
                            $keyArr = array_values($respArr);
                        } else {
                            $pat++;        
                            //$resultArr[$pat] = $respArr;    

                            // patients
                            $resultArr[$pat]['medical_chart_no'] = isset($respArr[0]) ? $respArr[0] :'';    
                            $resultArr[$pat]['last_name'] = isset($respArr[1]) ? $respArr[1] :'';    
                            $resultArr[$pat]['middle_name'] = isset($respArr[3]) ? $respArr[3] : '';    
                            $resultArr[$pat]['first_name'] = isset($respArr[2]) ? $respArr[2] : '';    
                           // $resultArr[$pat]['title'] = $respArr[1];    
                            $resultArr[$pat]['address1'] = isset($respArr[5]) ? $respArr[5] : '';    
                            $resultArr[$pat]['address2'] = isset($respArr[6]) ? $respArr[6] : ''; 
                            $resultArr[$pat]['city'] = isset($respArr[7]) ? $respArr[7] : '';     
                            $resultArr[$pat]['state'] = isset($respArr[8]) ? $respArr[8] : '';     
                            $resultArr[$pat]['zip5'] = isset($respArr[9]) ? $respArr[9] : '';     
                            $resultArr[$pat]['zip4'] = isset($respArr[10]) ? $respArr[10] : '';     
                            $resultArr[$pat]['gender'] = isset($respArr[12]) ? $respArr[12] : '';   
                            $resultArr[$pat]['ssn'] = isset($respArr[13]) ? str_replace('-', '', $respArr[13]) : '';      
                            $resultArr[$pat]['dob'] = isset($respArr[11]) ? date('Y-m-d', strtotime($respArr[11])): '';
                            $resultArr[$pat]['phone'] = isset($respArr[14]) ? $respArr[14] : '';   
                            $resultArr[$pat]['work_phone'] = isset($respArr[15]) ? $respArr[15] : '';     
                            $resultArr[$pat]['work_phone_ext'] = isset($respArr[16]) ? $respArr[16] : ''; 
                            $resultArr[$pat]['mobile'] = isset($respArr[19]) ? $respArr[19] : ''; 
                            $resultArr[$pat]['email'] = isset($respArr[21]) ? $respArr[21] : '';    

                            //patient_insurance - Primary
                            $primary_details = [];
                            $primary_details['insurance_name'] = isset($respArr[44]) ? $respArr[44] : ''; 
                            $primary_details['policy_id'] = isset($respArr[55]) ? $respArr[55] : ''; 
                            $primary_details['group_name'] = isset($respArr[56]) ? $respArr[56] : ''; 
                            $primary_details['last_name'] = isset($respArr[57]) ? $respArr[57] : ''; 
                            $primary_details['first_name'] = isset($respArr[58]) ? $respArr[58] : ''; 
                            $primary_details['middle_name'] = isset($respArr[59]) ? $respArr[59] : ''; 
                            $primary_details['insured_ssn'] = isset($respArr[68]) ? str_replace('-', '', $respArr[68]) : ''; 
                            $primary_details['insured_dob'] = isset($respArr[67]) ? date('Y-m-d', strtotime($respArr[67])) : ''; 
                            $primary_details['insured_address1'] = isset($respArr[61]) ? $respArr[61] : ''; 
                            $primary_details['insured_address2'] = isset($respArr[62]) ? $respArr[62] : ''; 
                            $primary_details['insured_city'] = isset($respArr[63]) ? $respArr[63] : ''; 
                            $primary_details['insured_state'] = isset($respArr[64]) ? $respArr[64] : ''; 
                            $primary_details['insured_zip5'] = isset($respArr[65]) ? $respArr[65] : ''; 
                            $primary_details['insured_zip4'] = isset($respArr[66]) ? $respArr[66] : '';                             
                            $primary_details['insured_phone'] = isset($respArr[69]) ? $respArr[69] : ''; 
                            
                            $primary_details['ins_address1'] = isset($respArr[45]) ? $respArr[45] : ''; 
                            $primary_details['ins_address2'] = isset($respArr[46]) ? $respArr[46] : ''; 
                            $primary_details['ins_city'] = isset($respArr[47]) ? $respArr[47] : ''; 
                            $primary_details['ins_state'] = isset($respArr[48]) ? $respArr[48] : ''; 
                            $primary_details['ins_zip5'] = isset($respArr[49]) ? $respArr[49] : ''; 
                            $primary_details['ins_zip4'] = isset($respArr[50]) ? $respArr[50] : ''; 
                            $primary_details['ins_phone'] = isset($respArr[51]) ? $respArr[51] : ''; 
                            $primary_details['ins_phoneext'] = isset($respArr[52]) ? $respArr[52] : ''; 
                            $primary_details['ins_fax'] = isset($respArr[53]) ? $respArr[53] : ''; 
                            $primary_details['ins_email'] = isset($respArr[54]) ? $respArr[54] : '';  
                            $primary_details['insured_mobile'] = isset($respArr[74]) ? $respArr[74] : ''; 

                            $resultArr[$pat]['primary_details'] = $primary_details;

                            
                            //patient_insurance - Secondary
                            $secondary_details = [];
                            $secondary_details['insurance_name'] = isset($respArr[78]) ? $respArr[78] : ''; 
                            $secondary_details['policy_id'] = isset($respArr[89]) ? $respArr[89] : ''; 
                            $secondary_details['group_name'] = isset($respArr[90]) ? $respArr[90] : ''; 
                            $secondary_details['last_name'] = isset($respArr[91]) ? $respArr[91] : ''; 
                            $secondary_details['first_name'] = isset($respArr[92]) ? $respArr[92] : ''; 
                            $secondary_details['middle_name'] = isset($respArr[93]) ? $respArr[93] : ''; 
                            $secondary_details['insured_ssn'] = isset($respArr[102]) ? $respArr[102] : ''; 
                            $secondary_details['insured_dob'] = isset($respArr[101]) ? date('Y-m-d', strtotime($respArr[101])) : '';                             
                            $secondary_details['insured_address1'] = isset($respArr[95]) ? $respArr[95] : ''; 
                            $secondary_details['insured_address2'] = isset($respArr[96]) ? $respArr[96] : ''; 
                            $secondary_details['insured_city'] = isset($respArr[97]) ? $respArr[97] : ''; 
                            $secondary_details['insured_state'] = isset($respArr[98]) ? $respArr[98] : ''; 
                            $secondary_details['insured_zip5'] = isset($respArr[99]) ? $respArr[99] : ''; 
                            $secondary_details['insured_zip4'] = isset($respArr[100]) ? $respArr[100] : ''; 
                            $secondary_details['insured_phone'] = isset($respArr[103]) ? str_replace('-', '', $respArr[103]) : ''; 
                            
                            $secondary_details['ins_address1'] = isset($respArr[79]) ? $respArr[79] : ''; 
                            $secondary_details['ins_address2'] = isset($respArr[80]) ? $respArr[80] : ''; 
                            $secondary_details['ins_city'] = isset($respArr[81]) ? $respArr[81] : ''; 
                            $secondary_details['ins_state'] = isset($respArr[82]) ? $respArr[82] : ''; 
                            $secondary_details['ins_zip5'] = isset($respArr[83]) ? $respArr[83] : ''; 
                            $secondary_details['ins_zip4'] = isset($respArr[84]) ? $respArr[84] : ''; 
                            $secondary_details['ins_phone'] = isset($respArr[85]) ? $respArr[85] : ''; 
                            $secondary_details['ins_phoneext'] = isset($respArr[86]) ? $respArr[86] : ''; 
                            $secondary_details['ins_fax'] = isset($respArr[87]) ? $respArr[87] : ''; 
                            $secondary_details['ins_email'] = isset($respArr[88]) ? $respArr[88] : '';  
                            $secondary_details['insured_mobile'] = isset($respArr[108]) ? $respArr[108] : ''; 
                            
                            $resultArr[$pat]['secondary_details'] = $secondary_details;

                            //patient_insurance - Ter
                            $tertiary_details = [];
                            $tertiary_details['insurance_name'] = isset($respArr[112]) ? $respArr[112] : ''; 
                             
                            $tertiary_details['policy_id'] = isset($respArr[123]) ? $respArr[123] : ''; 
                            $tertiary_details['group_name'] = isset($respArr[124]) ? $respArr[124] : ''; 
                            $tertiary_details['last_name'] = isset($respArr[125]) ? $respArr[125] : ''; 
                            $tertiary_details['first_name'] = isset($respArr[126]) ? $respArr[126] : ''; 
                            $tertiary_details['middle_name'] = isset($respArr[127]) ? $respArr[127] : ''; 
                            $tertiary_details['insured_address1'] = isset($respArr[129]) ? $respArr[129] : ''; 
                            $tertiary_details['insured_address2'] = isset($respArr[130]) ? $respArr[130] : ''; 
                            $tertiary_details['insured_city'] = isset($respArr[131]) ? $respArr[131] : ''; 
                            $tertiary_details['insured_state'] = isset($respArr[132]) ? $respArr[132] : ''; 
                            $tertiary_details['insured_zip5'] = isset($respArr[133]) ? $respArr[133] : ''; 
                            $tertiary_details['insured_zip4'] = isset($respArr[134]) ? $respArr[134] : ''; 
                            $tertiary_details['insured_dob'] = isset($respArr[135]) ? date('Y-m-d', strtotime($respArr[135])) : ''; 
                            $tertiary_details['insured_ssn'] = isset($respArr[136]) ? str_replace('-', '', $respArr[136]) : '';
                            $tertiary_details['insured_phone'] = isset($respArr[137]) ? $respArr[137] : ''; 

                            $tertiary_details['insured_mobile'] = isset($respArr[142]) ? $respArr[142] : ''; 
                            $tertiary_details['ins_city'] = isset($respArr[115]) ? $respArr[115] : ''; 
                            $tertiary_details['ins_state'] = isset($respArr[116]) ? $respArr[116] : ''; 
                            $tertiary_details['ins_zip5'] = isset($respArr[117]) ? $respArr[117] : ''; 
                            $tertiary_details['ins_zip4'] = isset($respArr[118]) ? $respArr[118] : ''; 
                            $tertiary_details['ins_phone'] = isset($respArr[119]) ? $respArr[119] : ''; 
                            $tertiary_details['ins_phoneext'] = isset($respArr[120]) ? $respArr[120] : ''; 
                            $tertiary_details['ins_fax'] = isset($respArr[121]) ? $respArr[121] : ''; 
                            $tertiary_details['ins_email'] = isset($respArr[122]) ? $respArr[122] : '';  
                            $tertiary_details['ins_address1'] = isset($respArr[113]) ? $respArr[113] : ''; 
                            $tertiary_details['ins_address2'] = isset($respArr[114]) ? $respArr[114] : '';

                            $resultArr[$pat]['tertiary_details'] = $tertiary_details;
                        }
                    }                    
                    $i++;                    
                }
            } else {
                echo "Invalid File";
            }    
            
            ini_set('auto_detect_line_endings',FALSE);            
           // echo "<pre>"; print_r($uniqArr);
        } catch(Exception $e) {
            die("Error occured while import ". $e->getMessage() );
        }         

        echo "<br>################<br>";

        //$keyArr = $resultArr[0];
        //$keyArr = array_shift($resultArr);

        echo "<pre>";  print_r($keyArr); 
        echo "<pre>";  print_r($resultArr); 
        echo "CNT: ".count($resultArr)."## ".$pat;

        $final_patient_array = $resultArr;
        
        $err = $this->patient_import_lorddr($final_patient_array);
                

        dd("stop");

        return $resultArr;

        //Log::error(print_r($this->patient_name_Arr, TRUE));
        Log::useFiles(storage_path().'/logs/patientErrorLog.log');
        Log::info('Patient Error Log');
        Log::info('File Name:'.$file_name);
        Log::info($this->patient_name_Arr);
        Log::useFiles(storage_path().'/logs/patientSuccessLog.log');
        Log::info('Patient Success Log');
        Log::info('File Name:'.$file_name);
        Log::info($final_patient_array);
        //dd(\DB::getDatabaseName());
        //$this->patient_import($final_patient_array);
        $this->patient_update($this->valueArr);
        echo "Success";
        die;
    }

    // Ignore first entry
    // 11 th Patient DOB,13 => SSN 
    // 11 - DOB format      //  (isset($value)) ? date('Y-m-d', strtotime($value)) : '0000-00-00';
    // 13 SSN format
    // 14 Phone validation

    public function patient_import_lorddr($dataArr) {
        
        DB::beginTransaction();
        $failed_patient = [];
        try {
            $inc = 0;
            foreach ($dataArr as $key => $patient_data) {
                // Check patient SSN if not exist add it, otherwise write into existing patient log 

                $patient_counts = Patient::where('ssn', $patient_data['ssn'])->count();
                if ($patient_data['ssn'] != '' && $patient_counts != 0) {
                    $failed_patient[$inc]['patient_name'] = $patient_data['last_name'] . ", " . $patient_data['first_name'];
                    $failed_patient[$inc]['ssn'] = $patient_data['ssn'];                    
                    $failed_patient[$inc]['error_desc'] = 'Patient Already Exits';
                } else {
                    // Add new patient
                    $request['medical_chart_no'] = $patient_data['medical_chart_no'];
                    $request['last_name'] = ucwords(strtolower($patient_data['last_name']));
                    $request['middle_name'] = $patient_data['middle_name'];
                    $first_letter = strtoupper(substr($request['last_name'], 0, 1));
                    $bill_cycle_arr = ['A-G', 'H-M', 'N-S', 'T-Z'];
                    for ($i = 0; $i < count($bill_cycle_arr); $i++) {
                        $str_arr = explode('-', $bill_cycle_arr[$i]);
                        if ($first_letter >= $str_arr[0] && $first_letter <= $str_arr[1]) {
                            $request['bill_cycle'] = $str_arr[0] . " - " . $str_arr[1];
                        }
                    }
                    $request['first_name'] = ucwords(strtolower(@$patient_data['first_name']));
                    if (!empty($patient_data['dob']))
                        $request['dob'] = @$patient_data['dob'];

                    if (!empty($patient_data['address1']) && !empty($patient_data['city']) && !empty($patient_data['state']) && !empty($patient_data['zip5'])) {
                        $request['address1'] = @$patient_data['address1'];
                        $request['city'] = @$patient_data['city'];
                        $request['state'] = @$patient_data['state'];
                        $request['zip5'] = @$patient_data['zip5'];
                        $request['zip4'] = @$patient_data['zip4'];
                    }
                    if (!empty($patient_data['ssn']))
                        $request['ssn'] = @$patient_data['ssn'];
                    $request['gender'] = @$patient_data['gender'];
                    $request['phone'] = @$patient_data['phone'];
                    $request['work_phone'] = @$patient_data['work_phone'];
                    $request['work_phone_ext'] = @$patient_data['work_phone_ext'];
                    $request['work_phone_ext'] = @$patient_data['work_phone_ext'];
                    $request['mobile'] = @$patient_data['mobile'];
                    $request['email'] = @$patient_data['email'];

                    $request['percentage'] = '60';
                    $request['created_by'] = Auth::user()->id;
                    $request['demo_percentage'] = '60';
                    $request['status'] = 'Active';
                    $request['email_notification'] = 'No';
                    $request['phone_reminder'] = 'No';
                    $request['statements'] = 'Yes';
                    $request['preferred_communication'] = '';
                    $request['language_id'] = '5'; 

                    $result = Patient::create($request);
                    $patID  = $result->id;
                    $patient = Patient::where('id', $patID)->first();
                    
                    // Add Patient Primary Insurance Details
                    if(isset($patient_data['primary_details']['insurance_name']) && trim($patient_data['primary_details']['insurance_name']) != '') {

                        $insurance_id = Insurance::where('insurance_name', $patient_data['primary_details']['insurance_name'])->value('id');
                        if (empty($insurance_id)) { 
                            $failed_patient[$inc]['patient_name'] = $patient_data['last_name'] . ", " . $patient_data['first_name'];
                            $failed_patient[$inc]['insurance_name'] = $patient_data['primary_details']['insurance_name'];                    
                            $failed_patient[$inc]['error_desc'] = 'Patient Primary Insurance Invalid';    
                        } else {
                            
                            $this->addPatientInsuranceEntry($patient_data, $patID, $insurance_id, 'Primary');                            
                            $patient->is_self_pay = 'No';
                            if ($patient->ins_percentage == 0) {
                                $patient->ins_percentage = 40;
                                $patient->percentage = 100;                                
                            }
                        }
                    }
                    
                    // Add Patient Secondary Insurance Details
                    if(isset($patient_data['secondary_details']['insurance_name']) && trim($patient_data['secondary_details']['insurance_name']) != '') {

                        $insurance_id = Insurance::where('insurance_name', $patient_data['secondary_details']['insurance_name'])->value('id');
                        if (empty($insurance_id)) { 
                            $failed_patient[$inc]['patient_name'] = $patient_data['last_name'] . ", " . $patient_data['first_name'];
                            $failed_patient[$inc]['insurance_name'] = $patient_data['secondary_details']['insurance_name'];                    
                            $failed_patient[$inc]['error_desc'] = 'Patient Secondary Insurance Invalid';    
                        } else {
                            $this->addPatientInsuranceEntry($patient_data, $patID, $insurance_id, 'Secondary');  
                            $patient->is_self_pay = 'No';
                        }
                    }
                    
                     // Add Patient Secondary Insurance Details
                    if(isset($patient_data['tertiary_details']['insurance_name']) && trim($patient_data['tertiary_details']['insurance_name']) != '') {

                        $insurance_id = Insurance::where('insurance_name', $patient_data['tertiary_details']['insurance_name'])->value('id');
                        if (empty($insurance_id)) { 
                            $failed_patient[$inc]['patient_name'] = $patient_data['last_name'] . ", " . $patient_data['first_name'];
                            $failed_patient[$inc]['insurance_name'] = $patient_data['tertiary_details']['insurance_name'];                    
                            $failed_patient[$inc]['error_desc'] = 'Patient Tertiary Insurance Invalid';    
                        } else {
                            $this->addPatientInsuranceEntry($patient_data, $patID, $insurance_id, 'Tertiary');  
                            $patient->is_self_pay = 'No';
                        }
                    }  

                    $patient->account_no = $this->create_patient_accno($result->id);
                    $patient->save();
                   
                    if (!empty($patient_data['address1']) && !empty($patient_data['city']) && !empty($patient_data['state']) && !empty($patient_data['zip5'])) {
                        $address_flag = array();
                        $address_flag['type'] = 'patients';
                        $address_flag['type_id'] = $patID;
                        $address_flag['type_category'] = 'personal_info_address';
                        $address_flag['address2'] = $patient_data['address1'];
                        $address_flag['city'] = $patient_data['city'];
                        $address_flag['state'] = $patient_data['state'];
                        $address_flag['zip5'] = $patient_data['zip5'];
                        $address_flag['zip4'] = $patient_data['zip4'];
                        $address_flag['is_address_match'] = '';
                        $address_flag['error_message'] = '';
                        echo "Create Address Flag entry called"; echo "<pre>"; print_r($address_flag);
                        AddressFlag::checkAndInsertAddressFlag($address_flag);
                    }
                }
                $inc++;
            }            
            \Log::info("Failed Patient Insurance"); \Log::info($failed_patient);
            DB::commit();
            return $failed_patient;
        } catch (\Exception $e) {
            echo 'Error message: ' . $e->getMessage();
            \Log::info($e);
            DB::rollback();
        }
    }

    
    public function addPatientInsuranceEntry($data, $patID, $insurance_id, $category){
        try{
            $detailsArr = ['insured_phone', 'last_name', 'first_name', 'middle_name', 'insured_ssn', 'insured_dob', 'insured_address1', 'insured_address2', 'insured_city', 'insured_state', 'insured_zip5','insured_zip4','insured_phone', 'group_name', 'policy_id'];

            $details = [];
            $details['patient_id'] = $patID;
            $details['insurance_id'] = $insurance_id;
            $details['category'] = $category;                            
            $details['relationship'] = ($data['ssn'] == @$data['insured_ssn']) ? 'Self' : 'Others';

            if($category == 'Primary'){
                $insData = $data['primary_details'];
            } elseif ($category == 'Secondary') {
                $insData = $data['secondary_details'];
            } elseif ($category == 'Tertiary') {
                $insData = $data['tertiary_details'];
            }

            foreach($detailsArr As $det) {
                $details[$det] = isset($insData[$det]) ? $insData[$det] : ''; 
            }

            if($details['relationship'] == 'Self') {
                $details['last_name'] = $secondary_details['first_name'] = $secondary_details['first_name'] = '';
            }
            \Log::info("Create Patient Insurance called"); \Log::info($details);
            $pri_insurance = PatientInsurance::create($details);
        } catch(Exception $e) {
            \Log::info("Exception occured on create patient insurance ".$e->getMessage() );
        }
    }

    // Lord Practice Patient Import End
    
    
    /* Uploaded  Patient list  */
    
    public function getUploadedPatientApiAjax(){
        $request = Request::all();
        $start  = isset($request['start']) ? $request['start'] : 0;
        $len    = (isset($request['length'])) ? $request['length'] : 50;
        $practice_timezone = Helpers::getPracticeTimeZone();
        
        $uploadPatient = Uploadpatient::with(['user' => function($q) use($request){
            if(!empty(json_decode($request['dataArr']['data']['user'])))
                $q->whereIn('id', json_decode($request['dataArr']['data']['user']));            
        }])->where('id', '!=', 0);
        
        if(!empty(json_decode($request['dataArr']['data']['file_name']))){
            $uploadPatient->where('org_filename', 'like', '%' . json_decode($request['dataArr']['data']['file_name']) . '%');
        }
        
        if(!empty(json_decode($request['dataArr']['data']['total_patient']))){
            $uploadPatient->where('total_patients', json_decode($request['dataArr']['data']['total_patient']));
        }

        if(!empty(json_decode($request['dataArr']['data']['uploadedStatus']))){
            // Uploaded Patient: "All" Status filter condition is not working
            // Rev- 1 - Ref: MR-2994 - Ravi - 10-10-2019
            $statusArr = json_decode($request['dataArr']['data']['uploadedStatus']);
            if (!in_array('All', $statusArr)){
                $uploadPatient->whereIn('status', $statusArr);
            }
        }
        
        if(!empty(json_decode(@$request['dataArr']['data']['created_at']))){
            $date = explode('-',json_decode($request['dataArr']['data']['created_at']));
            $from = date("Y-m-d", strtotime($date[0]));         
            $to = date("Y-m-d", strtotime($date[1]));
            // Created date filter based on practice timezone issue
            // Revision 1 - Ref: MR-2588 01 Aug 2019: Ravi
    
            // created at condition for UTC practice timezone
            $uploadPatient->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$to."'");
            //->where(DB::raw('DATE(created_at)'),'>=',$from)->where(DB::raw('DATE(created_at)'),'<=',$to);
        }
        
        $pagination_count = $uploadPatient->count(DB::raw('DISTINCT(patient_upload.id)'));
        $uploadPatient->groupBy('id');
        $uploadPatient->skip($start)->take($len);
        $uploadInfo = $uploadPatient->orderBy('id','desc')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('uploadInfo','pagination_count')));
        
    }
    
    /* Uploaded  Patient list  */


    // RVU Patient Import Start
    public function getPrcoessUploadedPatientData($filename, $doc_id) {
        ini_set('max_execution_time', 0);        
        
        $file_path = storage_path().'/uploadPatient/';

        $dataArr = $keyArr = [];        
        $file = $file_name = $file_path.$filename;
        $resultArr = $pat_det = $other_det = $icd_lists = $cpt_lists = $patIds = $claimIds = []; 
        try {
            
            $pat = 0;
            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen(@$file, "r");            
            if($handle) {
                $i = 0; $prePtID = 0; $totalPatients = 0;
               // while(($csvDatas = fgetcsv($handle, 1000, ",")) !== false) {
                while(($csvDatas = fgetcsv($handle, 5000, ",")) !== false) {
                    
                    $respArr = array_values($csvDatas); // read csv line convert into key value pair with out empty 
                    // echo "<pre>"; print_r($respArr);                     
                    if(!empty($respArr)) {
                        if($i == 0) {
                            $keyArr = array_values($respArr);
                            $this->uploadData['key_details'] = $keyArr;
                        } else {                            
                            $pat++;        
                            $pat_id = @$respArr[0]; 
                            $patDetails = trim(strtolower(@$respArr[5])).trim(strtolower(@$respArr[6])).trim(str_replace('/','',@$respArr[8]));
                            array_push($patIds, $patDetails);
                            //$resultArr[$pat] = $respArr;   
                            $max_rvu_id = @$respArr[2];
                            array_push($claimIds, $max_rvu_id);
                            foreach ($respArr as $key => $rvalue) {                                                                
                                if($key <= 9) {                                        
                                    $pat_det[str_replace(" ", "_", $keyArr[$key])] = isset($respArr[$key]) ? $respArr[$key] :'';    
                                } else {
                                    $keyStr = str_replace(" ", "_", $keyArr[$key]);                                     
                                    if($key == 11 || $key == 12)
                                        $pat_det[$keyStr] = isset($respArr[$key]) ? $respArr[$key] :'';    

                                    $dataArr[$keyStr] = isset($respArr[$key]) ? $respArr[$key] :'';     
                                    for($t=1; $t<=20; $t++){
                                        if(!isset($dataArr['Diagnosis_code_'.$t]))
                                            $dataArr['Diagnosis_code_'.$t] = '';     
                                    }

                                    if($keyStr == 'Procedure_(CPT)_code' && $dataArr[$keyStr] != ''){
                                        $cpt_lists[] = $dataArr[$keyStr];
                                    }

                                    if (strpos($keyStr, "Diagnosis_code_") !== false  && $dataArr[$keyStr] != ''){
                                        $icd_lists[] = $dataArr[$keyStr];
                                    }
                                }
                            }

                            $resultArr[$pat_id]['pat_det'] = $pat_det;
                            $resultArr[$pat_id]['other_det'][$max_rvu_id][] = $dataArr;
                            $resultArr[$pat_id]['xls_details'][] = $dataArr;
                            for($st=COUNT($respArr); $st<COUNT($keyArr); $st++){
                                $respArr[] = '';    
                            }
                            $this->uploadData['org_details'][$pat_id] =  $respArr;

                            $prePtID = $pat_id;
                            $totalPatients+=1;
                        }
                    }
                    $i++;    
                }
                Uploadpatient::where('id',$doc_id)->update(['total_patients' => COUNT(array_filter(array_unique($patIds))), 'total_charges' => COUNT(array_filter(array_unique($claimIds))) ]);
            } else {
                echo "Invalid File";
            }    
            // echo "<pre>"; print_r($keyArr);
            // echo "<pre>"; print_r($this->uploadData);  dd("stop");
            // echo "<pre>"; print_r($resultArr);  dd("stop");
            ini_set('auto_detect_line_endings',FALSE);
           // echo "<pre>"; print_r($uniqArr);
        } catch(Exception $e) {
            die("Error occured while import ". $e->getMessage() );
        }
        
        //$keyArr = $resultArr[0];
        //$keyArr = array_shift($resultArr);
        //echo "<pre>";  print_r($keyArr); 
        //echo "<pre>";  print_r($resultArr); 
        //echo "<pre>";  print_r($cpt_lists); 
        //echo "CNT: ".count($resultArr)."## ".$pat;
        $final_patient_array = $resultArr;

        $this->uploadData['ren_providers'] = Provider::where('provider_types_id', '1')
                                            ->select(DB::raw("CONCAT(first_name,' ',last_name) AS provider_name"),'id')
                                            //->select('id', CONCAT(last_name,' ',first_name) AS provider_name)
                                            ->pluck('provider_name', 'id')->all();
        $this->uploadData['bill_providers'] = Provider::where('provider_types_id', '5')
                                                //->select('id', 'provider_name')
                                                ->select(DB::raw("CONCAT(first_name,' ',last_name) AS provider_name"),'id')
                                                ->pluck('provider_name', 'id')->all();
        $this->uploadData['ref_Providers'] = Provider::where('provider_types_id', '2')
                                            //->select('id', 'provider_name')
                                            ->select(DB::raw("CONCAT(first_name,' ',last_name) AS provider_name"),'id')
                                            ->pluck('provider_name', 'id')->all();
        $this->uploadData['cpt_list'] = Cpt::join('favouritecpts', 'cpts.id', '=', 'favouritecpts.cpt_id')->whereIn('cpt_hcpcs',$cpt_lists)
                                        ->orderby('cpt_hcpcs', 'asc')->pluck('cpt_id','cpt_hcpcs')->all();
        $this->uploadData['icd_list'] = Icd::whereIn('icd_code', $icd_lists)->orderby('icd_code','asc')->pluck('id', 'icd_code')->all();
        $this->uploadData['modifier_list'] = Modifier::where('status', 'Active')->where('deleted_at', NULL)->pluck('code','id')->all();
        $this->uploadData['facility_list'] = Facility::where('status', 'Active')->orderBy('facility_name', 'asc')->pluck('facility_name', 'id')->all(); 

        $this->patient_import_rvu($final_patient_array, $doc_id);
        echo "Success";
        return true;
    }

    public function cleanString($string) {
       $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
       return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function patient_import_rvu($dataArr, $doc_id) {
        $respData = []; 
        
        set_time_limit(0);

        DB::beginTransaction();
        $failed_patient = [];
        try {
            $inc = 0;
            $viCharges = new ChargeV1ApiController();
            $patientIds = $sucClaimIds = [];

            /* Hold option created for patient upload start */
            $hold_option = \App\Models\Holdoption::where('option', 'maxRVU Upload'); 
            $hold_optionCount = $hold_option->count();
            $hold_optionId = '';
            if ($hold_optionCount > 0) {
                $record = $hold_option->first();
                $hold_optionId = $record->id;
            } else {
                $newOption = array(
                    'option' => 'maxRVU Upload',
                    'created_by' => Auth::user()->id
                );
                $newOptionRecord = \App\Models\Holdoption::create($newOption);
                $hold_optionId = $newOptionRecord->id;
            }
            $errMSg = $providerErrMSg = $facErrMsg = $icdErrMsg = $cptErrMsg = '';
            /* Hold option created for patient upload end */            
            foreach ($dataArr as $key => $details) { 
                //\Log::info("Started Patient #".$inc);
                $comments = '';

                /***
                Comments for Charge Import
                1) Patient First Name missing
                2) Patient Last Name missing
                3) Patient DOB missing
                4) Rendering Provider Invalid
                5) Billing Provider Invalid
                6) Referring Provider Invalid
                7) CPT is not in the system
                8) Modifier is not valid
                9) ICD is not valid
                10) ICD exceeds 12 limit
                11) Facility name is not matching with existing facility name
                */
                $patient_data = $details['pat_det'];

                if($patient_data['Patient_First_Name'] == '') {
                    $comments .= 'Patient First Name missing';
                }

                if($patient_data['Patient_Last_Name'] == '') {
                    $comments .= 'Patient Last Name missing';
                }

                if($patient_data['Patient_Birthday'] == '') {
                    $comments .= ' Patient DOB missing';
                }                
                // Date format issue handled
                // Rev.1 - Ravi - 01-10-2019
                $patient_data['dob'] = '';
                if(isset($patient_data['Patient_Birthday']) && $patient_data['Patient_Birthday'] <> "") {
                    $dateArr = explode("/", $patient_data['Patient_Birthday']);   // 12/10/1944 => m/d/y 
                    $patient_data['dob'] = @$dateArr[2].'-'.@$dateArr[0].'-'.@$dateArr[1];
                    //date("Y-m-d", strtotime(str_replace('/', '-', @$patient_data['Patient_Birthday'])));
                }

                $patID  = 0;
                $patient_data['Patient_First_Name'] = $this->cleanString($patient_data['Patient_First_Name']);
                $patient_data['Patient_Last_Name'] = $this->cleanString($patient_data['Patient_Last_Name']);
                $patient_rec = Patient::where('first_name', $patient_data['Patient_First_Name'])
                                    ->where('last_name', $patient_data['Patient_Last_Name'])
                                    ->where('dob', $patient_data['dob'])
                                    ->first();

                $respData[$inc] = $details['xls_details']; 
                if(trim($patient_data['Patient_First_Name']) != '' && trim($patient_data['Patient_Last_Name'] != '')) {
                    if (count((array)$patient_rec) != 0) {
                        $patID  = $patient_rec['id'];
                        $comments .= 'Patient Already Exist. Patient #'.$patient_rec['account_no'];                        
                    } else {
                        // Add new patient
                        $request = [];
                        $request['last_name'] = $patient_data['Patient_Last_Name'];
                        $request['middle_name'] = $request['preferred_communication'] = '';
                        $first_letter = strtoupper(substr($request['last_name'], 0, 1));
                        $bill_cycle_arr = ['A-G', 'H-M', 'N-S', 'T-Z'];
                        for ($i = 0; $i < count($bill_cycle_arr); $i++) {
                            $str_arr = explode('-', $bill_cycle_arr[$i]);
                            if ($first_letter >= $str_arr[0] && $first_letter <= $str_arr[1]) {
                                $request['bill_cycle'] = $str_arr[0] . " - " . $str_arr[1];
                            }
                        }
                        $request['first_name'] = @$patient_data['Patient_First_Name'];
                        if (!empty($patient_data['dob']))
                            $request['dob'] = @$patient_data['dob'];

                        $request['gender'] = @$patient_data['Patient_Gender'];
                        $request['percentage'] = $request['demo_percentage'] = '0';
                        $request['created_by'] = Auth::user()->id;                    
                        $request['status'] = 'Active';
                        $request['email_notification'] = $request['phone_reminder'] = 'No';
                        $request['statements'] = 'Yes';                        
                        $request['medical_chart_no'] = @$patient_data['Medical_Record'];
                        $request['language_id'] = '5'; 

                        $result = Patient::create($request);
                        $patID  = $result->id;
                        $patient = Patient::where('id', $patID)->first();
                        $patient->account_no = $patAccNo = $this->create_patient_accno($result->id);
                        $patient->save();

                        $comments .= 'Patient Record Created. Patient #'.$patAccNo;
                        
                        // Add Patient Notes  Entry 
                        $notes_arr = [];
                        $notes_arr['title'] = "Patient created from RVU";
                        $notes_arr['content'] = "Patient created from RVU upload. Max RVU #".@$patient_data['maxRVU_ID'];
                        $notes_arr['notes_type'] = "Patient";
                        $notes_arr['patient_notes_type'] = "patient_notes";
                        $notes_arr['user_id'] = $patID;
                        $notes_arr['created_by'] = Auth::user()->id;
                        $notes_result = PatientNote::create($notes_arr);
                    }

                    $claim_note_id = 0;
                    // Add Patient Claim Notes Entry, if  provided any primary diagnosis
                    if (isset($patient_data['Primary_Diagnosis']) && trim($patient_data['Primary_Diagnosis'] != '')) {
                        $notes_arr = [];
                        $notes_arr['title'] = "Claim uploaded from RVU";
                        $notes_arr['content'] = "Primary Diagnosis #".@$patient_data['Primary_Diagnosis'];
                        $notes_arr['notes_type'] = "Patient";
                        $notes_arr['patient_notes_type'] = "claim_notes";
                        $notes_arr['user_id'] = $patID;
                        $notes_arr['created_by'] = Auth::user()->id;
                        $claim_notes_result = PatientNote::create($notes_arr);
                        $claim_note_id = $claim_notes_result->id;
                    }
                    
                    if(!in_array($patID, $patientIds))
                        array_push($patientIds, $patID);
                    // Check and add charge entry
                    $charge_arr = [];
                    $patient_charge_arr = $details['other_det'];
                    foreach($patient_charge_arr as $chKey=>$chValue){
                        $claimRefID = $chKey; 
                        $patient_charge_data = $chValue;
                        //\Log::info($patient_charge_data);
                        if(!empty($patient_charge_data) && $patID != 0){
                            $claimCnt = ClaimInfoV1::Where('claim_reference', $claimRefID)->count();
                            if($claimCnt > 0) {
                                $comments .= ',  Claim Already Added';
                            } else {
                                $chargeRequest = [];
                                $chargeRequest['appointment_id'] = $chargeRequest['claim_detail_id'] = $chargeRequest['claim_other_detail_id'] = '';
                                $chargeRequest['ambulance_billing_id'] = $chargeRequest['providertypeid'] = '';
                                $chargeRequest['patient_id'] = Helpers::getEncodeAndDecodeOfId($patID, 'encode');
                                $chargeRequest['facility_clai_no'] = '';                             
                                $chargeRequest['is_hold'] = 1;
                                $chargeRequest['hold_reason_id'] = $hold_optionId;
                                $chargeRequest['insurance_id'] = 0;
                                $chargeRequest['self'] = 1;
                                $chargeRequest['pos_id'] = 0;
                                $chargeRequest['auth_no'] = 0;                          
                                $chargeRequest['discharge_date'] = '';   
                                $chargeRequest['charge_add_type'] = 'billing';                                
                                $chargeRequest['note'] = @$patient_charge_data[0]['Notes'];  

                                $renProvider = $refProvider = $billProvider = 0;                                 
                                //$rp = preg_replace('/ /', ', ', $patient_charge_data[0]['Creating_provider_name'], 1);
                                $rp = preg_replace('/ /', ' ', $patient_charge_data[0]['Creating_provider_name'], 1);
                                //$renProvider = array_search($rp, $this->uploadData['ren_providers']);
                                // Ignore case sensitive
                                $renProvider = array_search(strtolower(trim($rp)), array_map('strtolower', $this->uploadData['ren_providers']));
                                if(!$renProvider){
                                    $comments .= ' ,  Rendering Provider Invalid';                                    
                                    $providerErrMSg = 'Rendering Provider Invalid';
                                    $renProvider = 0;
                                }

                                /*
                                $bp = preg_replace('/ /', ' ', $patient_charge_data[0]['Owning_Provider_Name'], 1);                                  
                                $billProvider = array_search(strtolower(trim($bp)), array_map('strtolower', $this->uploadData['bill_providers']));
                                if(!$billProvider){
                                    $comments .= ' ,  Billing Provider Invalid';
                                    $providerErrMSg = 'Billing Provider Invalid';
                                    $billProvider = 0;
                                }
                                */
                                
                                $fac_id = array_search(strtolower(trim($patient_data['Hospital_Details'])), array_map('strtolower', $this->uploadData['facility_list']) );
                                if(!$fac_id){
                                    $comments .= ' ,  Facility name is not matching with existing facility name';
                                    $fac_id = 0;
                                    $facErrMsg = 'Facility Invalid';
                                }   
                                $chargeRequest['facility_id'] = $fac_id; 
                                                    
                                $i = 0;                            
                                $dos_from = $dos_to = $cpt = $cpt_amt = $cpt_allowed = $modifier1 = $modifier2 = $modifier3 = $modifier4 = [];   
                                $unit = $charge = $cpt_icd_map = $cpt_icd_map_key = $line_items = $icd_Arr = [];
                                $total_charge = 0;
                                // Conditional flag added for create charge.
                                $allow_create = 1;
                                foreach($patient_charge_data as $cKey => $cValue) {
                                    if(COUNT($cpt) <30){
                                        $chargeRequest['doi'] = ''; 
                                        $chargeRequest['admit_date'] = date("Y-m-d", strtotime(@$cValue['Admission_Date'])); 
                                        $dos_from[] = date("Y-m-d", strtotime(@$patient_data['Session_Date']));
                                        $dos_to[] = date("Y-m-d", strtotime(@$patient_data['Session_Date']));
                                        
                                        $cpt_code = isset($this->uploadData['cpt_list'][$cValue['Procedure_(CPT)_code']]) ? $cValue['Procedure_(CPT)_code'] : 0;
                                        if(!$cpt_code) {
                                            $allow_create = 0; // Dont allow to create change
                                            $cpt_code = $modifier1[] = $modifier2[] = $modifier3[] =$modifier4[] = '';
                                            $comments .= ' ,  CPT: '.$cValue['Procedure_(CPT)_code'].' is not in the system';
                                            $cptErrMsg = 'CPT Invalid';
                                            $this->uploadData['error_cpt'][] = $cValue['Procedure_(CPT)_code'];
                                            $cpt_amt[] = $cpt_allowed[] = '';
                                            $cpt_icd_map[] = '';
                                            $cpt_icd_map_key[] = '';  
                                            $cpt[] = $charge[] = '';
                                        } else {                                    
                                            $cpt[] = $cpt_code;    
                                            $doi_yr = date("Y", strtotime(@$cValue['Admission_Date'])); 
                                            $cpt_amts = $this->checkAndGetCptBilledAmt($doi_yr, $this->uploadData['cpt_list'][$cValue['Procedure_(CPT)_code']]);
                                            $total_charge += @$cpt_amts['billed'];
                                            $charge[] = @$cpt_amts['billed'];
                                            $cpt_amt[] = @$cpt_amts['billed'];
                                            $cpt_allowed[] = @$cpt_amts['allowed'];

                                            $modifierArr = explode(",", @$cValue['Modifier']);
                                            if(isset($modifierArr[0]) && $modifierArr[0] != '' && in_array(trim($modifierArr[0]), $this->uploadData['modifier_list']) )
                                                $modifier1[] = @$modifierArr[0]; 
                                            else 
                                                $modifier1[] = '';

                                            if(isset($modifierArr[1]) && $modifierArr[1] != '' && in_array(trim($modifierArr[1]), $this->uploadData['modifier_list'])  )
                                                $modifier2[] =  @$modifierArr[1];
                                            else 
                                                $modifier2[] = '';

                                            if(isset($modifierArr[2]) && $modifierArr[2] != '' &&  in_array(trim($modifierArr[2]), $this->uploadData['modifier_list'])  )
                                                $modifier3[] = @$modifierArr[2]; 
                                            else 
                                                $modifier3[] = '';   

                                            if(isset($modifierArr[3]) && $modifierArr[3] != '' && in_array(trim($modifierArr[3]), $this->uploadData['modifier_list'])  )
                                                $modifier4[] = @$modifierArr[3]; 
                                            else 
                                                $modifier4[] = '';            
                                        }
                                        $icd_cnt_err = 0;
                                        for($c=1; $c<=20;$c++){
                                            if(isset($cValue['Diagnosis_code_'.$c]) && trim($cValue['Diagnosis_code_'.$c]) != '' ) {
                                                $icdCode = isset($this->uploadData['icd_list'][$cValue['Diagnosis_code_'.$c]]) ? $cValue['Diagnosis_code_'.$c] : 0;
                                                if(!$icdCode) {
                                                    $allow_create = 0;
                                                    $this->uploadData['error_icd'][] = $cValue['Diagnosis_code_'.$c];
                                                    $comments .= ' ,  ICD '.$cValue['Diagnosis_code_'.$c].' is not valid';
                                                    $icdErrMsg = 'ICD Invalid';
                                                } else {
                                                    if(COUNT($icd_Arr) < 12) {
                                                        if(!in_array($icdCode, $icd_Arr)) {
                                                            $icd_Arr[] = $cValue['Diagnosis_code_'.$c]; 
                                                            $chargeRequest['icd'.(sizeof($icd_Arr))] = $cValue['Diagnosis_code_'.$c];
                                                            $cpt_icd_map[] = $cValue['Diagnosis_code_'.$c];
                                                        }
                                                    } else {
                                                        $icd_cnt_err = 1;                                                
                                                    }
                                                }
                                            }
                                        } 
                                        
                                        $tmpRow = $mapKeyArr = [];
                                        for($icdT=0; $icdT<12; $icdT++){
                                            if( isset($cValue['Diagnosis_code_'.($icdT+1)]) && !in_array(@$cValue['Diagnosis_code_'.($icdT+1)], $tmpRow) && $cValue['Diagnosis_code_'.($icdT+1)] != ''){
                                                array_push($tmpRow, @$cValue['Diagnosis_code_'.($icdT+1)]);
                                                array_push($mapKeyArr, (array_search(@$cValue['Diagnosis_code_'.($icdT+1)], $cpt_icd_map)+1));
                                                $chargeRequest['icd'.($icdT+1).'_'.$cKey] = (array_search(@$cValue['Diagnosis_code_'.($icdT+1)], $cpt_icd_map)+1);
                                            } else {
                                                $chargeRequest['icd'.($icdT+1).'_'.$cKey] = '';
                                            }
                                        }
                                        $cpt_icd_map_key[] = implode(",", $mapKeyArr);
                                        $unit[] = 1;
                                        $line_items[] = ''; 
                                    }                                      
                                }
                                if($icd_cnt_err)
                                    $comments .= ' ,  ICD exceeds 12 limit';

                                $icd_map_diff = COUNT($cpt) - COUNT($cpt_icd_map);
                                for($temp = 0; $temp<$icd_map_diff; $temp++){
                                    array_push($cpt_icd_map, '');
                                    array_push($cpt_icd_map_key, '');
                                }

                                $chargeRequest['rendering_provider_id'] = $renProvider;
                                $chargeRequest['refering_provider_id'] = $refProvider;
                                $chargeRequest['billing_provider_id'] = $billProvider;

                                $chargeRequest['dos_from'] = $dos_from;
                                $chargeRequest['dos_to'] = $dos_to;
                                $chargeRequest['cpt'] = $cpt;                            
                                $chargeRequest['modifier1'] = $modifier1;
                                $chargeRequest['modifier2'] = $modifier2;
                                $chargeRequest['modifier3'] = $modifier3;
                                $chargeRequest['modifier4'] = $modifier4;
                                $chargeRequest['unit'] = $unit;
                                $chargeRequest['charge'] = $charge;
                                $chargeRequest['cpt_icd_map'] = $cpt_icd_map;                    
                                $chargeRequest['cpt_icd_map_key'] = $cpt_icd_map_key;

                                $chargeRequest['copay'] = '';
                                $chargeRequest['copay_amt'] = '';
                                $chargeRequest['copay_detail'] = @$claimRefID;
                                $chargeRequest['cpt_amt'] = $cpt_amt;
                                $chargeRequest['cpt_allowed'] = $cpt_allowed;
                                $chargeRequest['total_charge'] = $total_charge;   

                                $chargeRequest['copay_applied'] = $chargeRequest['box_24_AToG'] = $chargeRequest['copay_Transcation_ID'] = $line_items;
                                
                                //echo "<br>Create Charge called "; echo "<pre>"; print_r($chargeRequest);
                                if($allow_create != 0) {
                                    //\Log::info("Charge Create Request:::"); \Log::info($chargeRequest); 
                                    $responseData = $viCharges->createCharge($chargeRequest);
                                    if(isset($responseData)) {
                                        $api_response_data = $responseData->getData();    
                                        if ($api_response_data->status == 'success') {
                                            $claimId = $api_response_data->data;

											//Claim note claim id not updted issue changes
                                            if($claim_note_id !=0) {
                                                PatientNote::where('id', $claim_note_id)->update(['claim_id' => $claimId]);
                                            }

                                            if(!in_array($claimId, $sucClaimIds))
                                                array_push($sucClaimIds, $claimId);

                                            $newTransaction = ClaimCPTTXDESCV1::select(DB::raw('sum(pat_bal) as patient_balance'))
                                                                ->where('claim_id', $claimId)
                                                                ->first();                                                                        
                                            $patient_balance = $newTransaction['patient_balance'];
                                            ClaimTXDESCV1::where('claim_id', $claimId)->update(['pat_bal' => $patient_balance]);
                                            PMTClaimFINV1::where('claim_id', $claimId)->update(['patient_due' => $patient_balance]);

                                            $comments .= " ,  success: Claim Created # $claimId ";
                                        } else {
                                            $comments .= " ,  Error: ".$api_response_data->message;
                                        }
                                    } else {
                                        $comments .= " ,  Error: Please Contact Admin";
                                    }
                                } else {
                                    $comments .= " ,  Error: Charge Not Imported";
                                }
                            }
                        }
                    }
                    //\Log::info("Completed Patient #".COUNT($patientIds));      
                }         
                
                // Update Completed Patients and Failed Charges
                Uploadpatient::where('id',$doc_id)->update([
                        'completed_patients' => COUNT($patientIds), 
                        'failed_charges' => DB::raw('total_charges - ' . COUNT($sucClaimIds))
                    ]);
                $inc++;                    
                if(isset($this->uploadData['org_details'][$key]))
                    $this->uploadData['org_details'][$key]['comments'] = $comments;
                    
            } 
            /*   
            if($providerErrMSg != '')
                $errMSg .=" ".$providerErrMSg;

            if($facErrMsg != '')     
                $errMSg .=($errMSg != '') ? ", ".$facErrMsg : $facErrMsg;
            */
            if($icdErrMsg != '')     
                $errMSg .= ($errMSg != '') ? ", ".$icdErrMsg : $icdErrMsg;

            if($cptErrMsg != '')     
                $errMSg .=($errMSg != '') ? ", ".$cptErrMsg : $cptErrMsg;
            // Update Error Message if Found
            Uploadpatient::where('id',$doc_id)->update([
                        'error_msg' => $errMSg
                    ]);
            DB::commit();  
            $respData = $this->uploadData['org_details'];
            $columnheading  = $this->uploadData['key_details'];
            array_push($columnheading,"Comments");
            $excel          = App::make('excel');
            $path = storage_path().'/uploadPatient/';
            $fName = 'Uploader_'.time();
                        
            if(isset($this->uploadData['error_icd'])) {
                \Log::info("Error ICD :"); \Log::info(array_unique(@$this->uploadData['error_icd'])); 
            }
            if(isset($this->uploadData['error_cpt'])) {
                \Log::info("Error CPT :"); \Log::info(array_unique(@$this->uploadData['error_cpt'])); 
            }
            // Upload patient response write CSV
            $heading_array[] = $columnheading;
            $datas = isset($respData) && is_array($respData) ? $respData : []; //json_decode(json_encode($respData), true); \Log::info("Array");
            $collect_array = array_merge($heading_array,$datas);

            libxml_use_internal_errors(true);            
            Excel::store(new ViewExport($collect_array), 'uploadPatient/'.$fName.'.csv', 'storedsk');
            /*
            Excel::create($fName, function($excel) use($respData, $columnheading) {                
                $excel->sheet('Sheet1', function($sheet) use($respData, $columnheading) {
                    $collect_array = '';
                    $heading_array[] = $columnheading;
                    $datas = isset($respData) && is_array($respData) ? $respData : []; //json_decode(json_encode($respData), true); \Log::info("Array");
                    $collect_array = array_merge($heading_array,$datas);
                    $sheet->fromArray($collect_array, null, 'A1', false, false);
                });
            })->store('csv', $path);
            */
            //\Log::info($path.$fName);

            Uploadpatient::where('id',$doc_id)->update(['status' => 'Completed', 'resp_filename' => $fName.".csv"]);
            return true;
            
        } catch (\Exception $e) {
            echo 'Error message: ' . $e->getMessage();  \Log::info($e);
            DB::rollback();
            Uploadpatient::where('id',$doc_id)->update(['status' => 'Failed','completed_patients' => 0]);
        }
    }


    public function checkAndGetCptBilledAmt($year, $cpt_id) {
        $cpt_amts['billed'] = $cpt_amts['allowed'] = 0;
        $cptInfo = MultiFeeschedule::where('year',$year)->where('insurance_id',0)->where('status','Active')
                    ->where('cpt_id', $cpt_id)->first();
        if(!empty($cptInfo)) {
            $cpt_amts['billed'] = @$cptInfo->billed_amount;
            $cpt_amts['allowed'] = @$cptInfo->allowed_amount;
        } else {
            $cptDetails = Cpt::where('id', $cpt_id)->first();    
            $cpt_amts['billed'] = @$cptDetails->billed_amount;
            $cpt_amts['allowed'] = @$cptDetails->allowed_amount;
        }
        return $cpt_amts;
    }


    public function checkAndGetICD($icd_code) {

        if(!isset($this->uploadData['rvu_icd_arr'])) {
            if(Cpt::where('cpt_hcpcs',$cpt_code)->count() > 0){
                $this->uploadData['rvu_icd_arr'][$cpt_code] = 1;                
            }            
            $this->uploadData['rvu_icd_arr'][$cpt_code] = 0;
        }   

        if($this->uploadData['rvu_icd_arr'][$cpt_code] == 1) {
            return $cpt_code;
        }
        return '';
    }

   

    public function bulkPatientInsertRVU($request) {

        try{
            DB::beginTransaction();

           // 1- get the last id of your table ($lastIdBeforeInsertion)
            $result = Patient::create($request);
            $patID  = $result->id;
            $patient = Patient::where('id', $patID)->first();
            $patient->account_no = $this->create_patient_accno($result->id);
            $patient->save();

            // 2- insert your data
            Model::insert($array);

            // 3- Getting the last inserted ids
            $insertedIds = [];
            for($i=1; $i<=theCountOfTheArray; $i++) {
                array_push($insertedIds, $lastIdBeforeInsertion+$i);
            }        
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
        }
    }

    public function processUploadedSheet($docId) {
        $errorMSg= '';
        $doc_id = Helpers::getEncodeAndDecodeOfId($docId, 'decode');
        $uploadDoc = Uploadpatient::where('id',$doc_id)->first();
        if(!empty($uploadDoc)) {
            if($uploadDoc['status'] == 'Pending') {
                $docFile = storage_path()."/uploadPatient/".$uploadDoc['file_name'];
                $file_name = $uploadDoc['file_name'];
                if ($file_name != '' && File::exists($docFile)) {
                    // Start the process
                    Uploadpatient::where('id',$doc_id)->update(['status' => 'In Progress' ]);

                    $resp = $this->getPrcoessUploadedPatientData($uploadDoc['file_name'], $doc_id);
                    // Update the process as completed 
                    //Uploadpatient::where('id',$doc_id)->update(['status' => 'Completed' ]);
                } else {
                    $errorMSg= "File Not Found";
                }
            } else {
                $errorMSg = "Already Processed";
            }
            
        } else {
            $errorMSg= "Invalid File ID";            
        }

        if($errorMSg != '') {
            return Response::json(array('status' => 'failure', 'message' => $errorMSg, 'data' => ''));   
        } 
        return Response::json(array('status' => 'success', 'message' => "Imported Patients successfully", 'data' => $doc_id));
    }

    // RVU Patient Import End

     public function getEXLDataApiICD(){
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        
        $file_path = storage_path().'/uploadPatient/';
        try {
            $file_path = storage_path().'/uploadPatient/';
            $dataArr = $keyArr = [];        
            $file = $file_name = $file_path.'icd_book.csv';
            ini_set('auto_detect_line_endings',TRUE);
            $handle = fopen(@$file, "r");            
            if($handle) {
                while(($csvDatas = fgetcsv($handle, 5000, ",")) !== false) {                    
                    $respArr = array_values($csvDatas);
                    //echo "<pre>"; print_r($respArr);
                    $request['id'] = $respArr[0]; 
                    $request['icd_type'] = $respArr[1]; 
                    $request['order'] = $respArr[2]; 
                    $request['icd_code'] = $respArr[3]; 
                    $request['icdid'] = $respArr[4]; 
                    $request['header'] = $respArr[5]; 
                    $request['short_description'] = $respArr[6]; 
                    $request['medium_description'] = $respArr[7]; 
                    $request['long_description'] = $respArr[8]; 
                    $request['created_by'] = $respArr['19']; 
                    $data[] = $request;
                }
                $table_name = 'icd_10';
                DB::statement("TRUNCATE TABLE $table_name");

                $icd_arr = array_chunk($data, 20);

                foreach($icd_arr as $det) {
                    \Log::info($det);
                    $result = Icd::insert($det);    
                    //echo "<pre>"; print_r($result);
                }                
                dd("Stop");
            }

        } catch(Exception $e) {
            \Log::info("Error Occured ".$e->getMessage() );
        }
    }

    public function showProfilePicture($type) {
        return view('layouts.avatar-popup', ['type' => $type]);
    }

}