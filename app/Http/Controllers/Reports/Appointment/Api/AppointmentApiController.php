<?php

namespace App\Http\Controllers\Reports\Appointment\Api;

use App;
use Request;
use Config;
use Response;
use Input;
use DB;
use Session;
use Carbon\Carbon;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Models\Insurance as Insurance;
use App\Models\Insurancetype as Insurancetype;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Controller;
use App\Models\AdjustmentReason as AdjustmentReason;
use App\Models\Scheduler\PatientAppointment as PatientAppointment;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Practice as Practice;
use App\Models\Patients\Patient as Patient;
use App\Models\Employer as Employer;
use App\Models\Icd;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\PatientStatementTrack as PatientStatementTrack;
use App\Models\STMTHoldReason as STMTHoldReason;
use App\Models\STMTCategory as STMTCategory;
use Log;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Medcubics\Users as Users;
use App\Models\ReasonForVisit as ReasonForVisit;

class AppointmentApiController extends Controller {

    public function getIndexApi() {
        return Response::json(array('status' => 'success', 'message' => null, 'data' => ''));
    }

    ############ APPOINTMENT ANALYSIS START ############
    ##### APPOINTMENT REPORT Function START #####
    public function appointmentanalysisApi() {
        /* @$facility = Facility::orderBy('id','ASC')->selectRaw('CONCAT(short_name,"-",facility_name) as facility_data, id')->pluck("facility_data", "id")->all(); */     

        $ClaimController  = new ClaimControllerV1();  
        $search_fields_data = $ClaimController->generateSearchPageLoad('appoinment_analysis');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];

     
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('searchUserData','search_fields')));
    }

    public function analysissearchApi($export = '', $data = '') {

        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        
            $search_by = array();
            $appt_analysis = PatientAppointment::with('patient','reasonforvisit','facility','facility.speciality_details','facility.pos_details','facility.facility_address','provider','provider.provider_type_details','provider.provider_types','provider.degrees','created_user')
                ->where('patient_appointments.patient_id', '<>', 0)
                ->select("patient_appointments.*", 
                  \DB::raw("(SELECT scheduled_on as next_appt FROM patient_appointments as pa
                          WHERE pa.patient_id = patient_appointments.patient_id
                          AND pa.scheduled_on > patient_appointments.scheduled_on
                          order by scheduled_on ASC 
                          limit 1
                        ) as next_appt"))                
                ;                

        if(!empty($request['created_at'])){
            $date = explode('-',$request['created_at']);          
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }           
            $end_date = date("Y-m-d", strtotime($date[1]));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Appointment Date']= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }else{
                $search_by['Appointment Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            }
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);                       
               $appt_analysis->where('scheduled_on', '>=', $start_date)->where('scheduled_on', '<=', $end_date);    
        }
        if(!empty($request['status'])) {
            $status = explode(',',$request['status']);
            if(is_array($request['status']))
              if(in_array("All", $request['status'])){                    
                    $appt_analysis->whereIn('status', ['Complete','Scheduled','No Show','Canceled','Rescheduled','Encounter']);
                }else{
                    $appt_analysis->whereIn('status', explode(',',$request['status']));
                }
            else
                if($request['status'] == 'All'){
                    $appt_analysis->whereIn('status', ['Complete','Scheduled','No Show','Canceled','Rescheduled','Encounter']);
                }else{
                    $appt_analysis->whereIn('status', explode(',',$request['status']));
                }
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Appointment Status'] = $request['status'];
            }else{
                $search_by['Appointment Status'][] = $request['status'];
            }
         }
         if(!empty($request['rendering_provider_id'])){
            $renders_id = explode(',',$request['rendering_provider_id']);
             $appt_analysis->whereHas('provider', function($q) use($renders_id) {      
                  $q->whereIn('id',$renders_id); 
                });
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name)); 
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['Rendering Provider'] = $search_render;
            }else{
                $search_by['Rendering Provider'][] = $search_render;  
            }
        }

       if(!empty($request['facility_id'])){
        $facility_id = explode(',',$request['facility_id']);
             $appt_analysis->whereHas('facility', function($q) use($facility_id) {      
                  $q->whereIn('id',$facility_id); 
                });

            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['facility_id']))->get()->toArray();
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by["Facility Name"] =  @array_flatten($facility)[0];
            }else{
                $search_by["Facility Name"][] =  @array_flatten($facility)[0];
            }
        }
        if(!empty($request['reason_for_visits'])){
        $reasonforvisit = explode(',',$request['reason_for_visits']);
             $appt_analysis->whereHas('reasonforvisit', function($q) use($reasonforvisit) {      
                  $q->whereIn('id',$reasonforvisit); 
                });

            $reason = ReasonForVisit::selectRaw("GROUP_CONCAT(reason SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['reason_for_visits']))->get()->toArray();
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by["Reason for Visit"] =  @array_flatten($reason)[0];
            }else{
                $search_by["Reason for Visit"][] =  @array_flatten($reason)[0];
            }
        }
        if(!empty($request['reason_for_visits'])){
        $reasonforvisit = explode(',',$request['reason_for_visits']);
            $appt_analysis->whereHas('reasonforvisit', function($q) use($reasonforvisit) {      
                  $q->whereIn('id',$reasonforvisit); 
                });

            $reason = ReasonForVisit::selectRaw("GROUP_CONCAT(reason SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['reason_for_visits']))->get()->toArray();
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by["Reason for Visit"] =  @array_flatten($reason)[0];
            }else{
                $search_by["Reason for Visit"][] =  @array_flatten($reason)[0];
            }
        }
        if(isset($request['eligible'])) {
            $eligible = $request['eligible'];
         
                if($request['eligible'] != 'All'){            
                     $appt_analysis->whereHas('patient', function($q) use($eligible){      
                       $q->where('eligibility_verification', $eligible);
                    }); 
                }
                if($request['eligible'] == 'Active'){
                    $eligible = 'Eligible';
                }else if($request['eligible'] == 'Inactive'){
                    $eligible = 'Ineligible';
                }else if($request['eligible'] == 'None'){
                    $eligible = 'Unverified';
                }else{
                    $eligible = 'All';
                }
                if (isset($request['exports']) && $request['exports'] == 'pdf') {
                    $search_by['Eligibility'] = $eligible;
                }else{
                    $search_by['Eligibility'][] = $eligible;
                }
         }
        if (isset($request['user']) && $request['user'] != '') {      
            $appt_analysis->whereIn('created_by', explode(',',$request['user']));     
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all(); 
            $User_name = implode(", ", array_unique($User_name));
            if (isset($request['exports']) && $request['exports'] == 'pdf') {
                $search_by['User'] = $User_name;
            }else{
                $search_by['User'][] = $User_name;
            }
        } 
        $appt_analysis->whereNull('deleted_at');
         // \Log::info($appt_analysis->toSql());
        if (isset($request['export']) && $request['export'] == 'pdf'){
            $appt_result = $appt_analysis->get()->toArray();
        } elseif (isset($request['export']) && $request['export'] == 'xlsx'){
            $appt_result = $appt_analysis->get()->toArray();
        }else{
            $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
            $appt_analysis = $appt_analysis->paginate($paginate_count);
            // Get export result
            $ref_array = $appt_analysis->toArray();

            $pagination_prt = $appt_analysis->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination">
                                        <li class="disabled"><span>&laquo;</span></li>
                                        <li class="active"><span>1</span></li>
                                        <li><a class="disabled" rel="next">&raquo;</a>
                                        </li>
                                    </ul>';
            $pagination = array('total' => $ref_array['total'], 'per_page' => $ref_array['per_page'], 'current_page' => $ref_array['current_page'], 'last_page' => $ref_array['last_page'], 'from' => $ref_array['from'], 'to' => $ref_array['to'], 'pagination_prt' => $pagination_prt);
            $appt_analysis = json_decode($appt_analysis->toJson());
            $appt_result = $appt_analysis->data;                    
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('appt_result','pagination','pagination_prt','search_by')));
    }
    
    //Stored procedure for Appointment Analysis Report - Anjukaselvan
    public function analysissearchApiSP($export = '', $data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $search_by = array();
        $practice_timezone = Helpers::getPracticeTimeZone();
        $start_date = $end_date = $status = $rendering_provider_id = $facility_id = $reason_for_visits = $eligibility = $user_ids = '';
        
        if(!empty($request['created_at'])){
            $date = explode('-',$request['created_at']);
            $start_date = date("Y-m-d", strtotime($date[0]));
            if($start_date == '1970-01-01'){
                $start_date = '0000-00-00';
            }
            $end_date = date("Y-m-d", strtotime($date[1]));
            $search_by['Appointment Date'][]= date("m/d/y",strtotime($start_date)).' to '.date("m/d/y",strtotime($end_date));
            $start_date = App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);
            $end_date = App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
        }
        if(!empty($request['status'])) {
            $status = $request['status'];
            $search_by['Appointment Status'][] = $request['status'];
        }
        if(!empty($request['rendering_provider_id'])){
            $rendering_provider_id = $request['rendering_provider_id'];
            $renders_id = explode(',',$request['rendering_provider_id']);
            foreach ($renders_id as $id) {
                $value_name[] = App\Models\Provider::getProviderFullName($id);
            }
            $search_render = implode(", ", array_unique($value_name)); 
            $search_by['Rendering Provider'][] = $search_render;  
        }

        if(!empty($request['facility_id'])){
            $facility_id = $request['facility_id'];
            $facility = Facility::selectRaw("GROUP_CONCAT(facility_name SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['facility_id']))->get()->toArray();
            $search_by["Facility Name"][] =  @array_flatten($facility)[0];        
        }
        if(!empty($request['reason_for_visits'])){
            $reason_for_visits = $request['reason_for_visits'];
            $reason = ReasonForVisit::selectRaw("GROUP_CONCAT(reason SEPARATOR ', ') as facility_name")->whereIn('id', explode(',',$request['reason_for_visits']))->get()->toArray();
            $search_by["Reason for Visit"][] =  @array_flatten($reason)[0];
        }
        if(isset($request['eligible'])) {
            $eligibility = $request['eligible'];
            if($request['eligible'] == 'Active'){
                $eligible = 'Eligible';
            }else if($request['eligible'] == 'Inactive'){
                $eligible = 'Ineligible';
            }else if($request['eligible'] == 'None'){
                $eligible = 'Unverified';
            }else{
                $eligible = 'All';
            }
            $search_by['Eligibility'][] = $eligible;            
        }
        if (isset($request['user']) && $request['user'] != '') {
            $user_ids = $request['user'];
            $User_name =  Users::whereIn('id', explode(',', $request["user"]))->where('status', 'Active')->pluck('short_name', 'id')->all();
            $User_name = implode(", ", array_unique($User_name));
            $search_by['User'][] = $User_name;
        }
        $paginate_count = Config::get('siteconfigs.reports.paginate_list_per_page');
        $offset = 0;
        $page = 0;

        if (isset($request['page'])) {
            $page = $request['page'];
            $offset = ($page - 1) * $paginate_count;
            $from = $offset + 1;
            $to = $offset + $paginate_count;
        } else {
            $from = 1;
            $to = $paginate_count;
        }
        if ($export == "") {
            $recCount = 1;
            //echo("CR Start".$start_date." ## ".$end_date."## ".$status."##".$rendering_provider_id."##".$facility_id."##".$reason_for_visits."##".$eligibility."##".$user_ids."##".$offset."##".$paginate_count."##".$recCount);
            $appt_count = DB::select('call appointmentAnalysis("' . $start_date . '", "' . $end_date . '", "' . $status . '", "' . $rendering_provider_id . '","' . $facility_id . '", "' . $reason_for_visits . '", "' . $eligibility . '", "' . $user_ids . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $count = $appt_count[0]->appointment_count;
            $last_page = 0;
            if ($count != 0)
                $last_page = ceil($count / $paginate_count);
            if (isset($request['page'])) {
                $page = $request['page'];
                $offset = ($page - 1) * $paginate_count;
                $from = $offset + 1;
                $to = $offset + $paginate_count;
                if ($page == $last_page) {
                    $to = $offset + $count % $paginate_count;
                }
            } else {
                if($paginate_count > $count){
                    $from = 1;
                    $to = $count % $paginate_count;
                }
            }              
            $recCount = 0;
            // Total appt_analysis 
            $appt_analysis = DB::select('call appointmentAnalysis("' . $start_date . '", "' . $end_date . '", "' . $status . '", "' . $rendering_provider_id . '","' . $facility_id . '", "' . $reason_for_visits . '", "' . $eligibility . '", "' . $user_ids . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
            $paging = [];
            $i = 0;
            while ($count > $i) {
                $paging[] = '1';
                $i++;
            }
            $report_array = $this->paginate($appt_analysis)->toArray();
            $pagination_prt = $this->paginate($paging)->render();
            if ($pagination_prt == '')
                $pagination_prt = '<ul class="pagination"><li class="disabled"><span>&laquo;</span></li> <li class="active"><span>1</span></li><li><a class="disabled" rel="next">&raquo;</a></li></ul>';
            $pagination = array('total' => $count, 'per_page' => $paginate_count, 'current_page' => $page, 'last_page' => $last_page, 'from' => $from, 'to' => $to, 'pagination_prt' => $pagination_prt);            
        } else {
            $recCount = 0;
            $paginate_count = 0;
            $offset = 0;
            // Total appt_analysis 
            $appt_analysis = DB::select('call appointmentAnalysis("' . $start_date . '", "' . $end_date . '", "' . $status . '", "' . $rendering_provider_id . '","' . $facility_id . '", "' . $reason_for_visits . '", "' . $eligibility . '", "' . $user_ids . '",  "' . $practice_timezone . '", "' . $offset . '", "' . $paginate_count . '", "' . $recCount . '")');
        }
            $appt_result = $appt_analysis;
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('appt_result','pagination','pagination_prt','search_by')));
    }

     ##### APPOINTMENT ANALYSIS REPORT Function END #####
    public function paginate($items, $perPage = 25) {
        $items = collect($items);
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

        //Create our paginator and pass it to the view
        return new LengthAwarePaginator($currentPageItems, count($items), $perPage);
    }
    
}