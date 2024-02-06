<?php
namespace App\Http\Controllers\Reports;
use App\Http\Controllers\Controller;
use Request;
use Redirect;
use DB;
use View;
use Config;
use Log;
use PDF;
use Excel;
use Auth;
use App\Http\Controllers\Medcubics\Api\DBConnectionController;
use Session;
use Carbon\Carbon;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Facility;
use App\Http\Controllers\Claims\ClaimControllerV1;
use App\Http\Helpers\Helpers;
use App\Models\ReportExport as ReportExportTask;
use DateTime;
use App\Models\Code;
use App\Models\Icd;
use App\Models\Pos;
use App\Models\Provider as Provider;
use App\Exports\BladeExport;

class PerformanceController extends Controller {

    public function __construct() {
        $request = Request::all();
        View::share('heading', 'Reports');
        View::share('selected_tab', 'reports');
        View::share('heading_icon', 'barchart');
    }

    public function reportList() {
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/performance/list', compact('selected_tab', 'heading', 'heading_icon'));
    }

    public function monthendperformanceLoad() {
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('monthendperformance');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/performance/monthEnd/reports', compact('selected_tab', 'heading', 'heading_icon', 'search_fields'));
    }

    public function monthendperformance($export='',$data='') {
        if (!empty($data))
            $request = $data;
        else
            $request = Request::all();

        //$FacilityWiseOutstanding = $this->getCommonFacilityAging($request);
        $insuranceClaimsByFacility = $this->insuranceClaimsByFacility($request);
        $facilityStatus = $this->facilityStatus($request);
        $days = $facilityStatus['days'];
        $resultDays = $facilityStatus['resultDays'];
        $searchBy = $facilityStatus['get_list_header'];
        $facilityStatus = $facilityStatus['facilityStatus'];
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('monthendperformance');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        if(empty($export)){
            return view('reports/performance/monthEnd/ajax', compact('insuranceClaimsByFacility', 'facilityStatus','days','resultDays','searchBy'));
        } else {
            return compact( 'insuranceClaimsByFacility', 'facilityStatus','days','resultDays','searchBy');
        }
    }

    //Outstanding AR - By facility
    public function getCommonFacilityAging($request) {
        DB::enableQueryLog();
        $aging = [];
        $aging_arr = ['Unbilled','0-30','31-60', '61-90', '91-120', '121-150', '>150'];
        $practice_timezone = Helpers::getPracticeTimeZone();
        foreach ($aging_arr as $key => $value) {
            $data_value = explode("-", $value);
            if((empty($data_value[1]))){
                $end_date = '';
            }else{
                $end_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(@$data_value[1]-1)));
            }
            if(strpos($value, ">") !== false){
                $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", 149))));
            } else{
                if(@$data_value[0]=='Unbilled'){
                    $start_date = 'Unbilled';
                }else{
                    if(@$data_value[0] == 0){
                        $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]))));
                    } else {
                        $start_date = date('Y-m-d', strtotime(Carbon::now($practice_timezone)->subDays(str_replace(">", "", @$data_value[0]-1))));
                    }
                }
            }
            //\Log::info($value.'#'.$start_date.'-'.$end_date);
            if($value=="Unbilled"){
                $query = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    ->join('patients', 'claim_info_v1.patient_id', '=', 'patients.id')
                    ->join('facilities', 'claim_info_v1.facility_id', '=', 'facilities.id')
                    ->select( 'facilities.facility_name',DB::raw('SUM(claim_info_v1.total_charge) - SUM(insurance_paid+patient_paid+withheld+patient_adj+insurance_adj) AS total_ar'), DB::raw('COUNT(claim_info_v1.id) as claim_count'), DB::raw('SUM(pmt_claim_fin_v1.patient_due+pmt_claim_fin_v1.insurance_due) as   patient_balance'))
                    ->whereNull('claim_info_v1.deleted_at')->groupBy('claim_info_v1.facility_id')->orderBy('claim_info_v1.facility_id')->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', '=', 0)->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');

                    if(isset($request['rendering_provider_id']) && !empty($request['rendering_provider_id'])){
                        if(isset($request['export'])) {
                            $rendering_provider_id = is_array($request['rendering_provider_id']) ?  $request['rendering_provider_id'] : explode(',', $request['rendering_provider_id']);
                        } else {
                            $rendering_provider_id = $request['rendering_provider_id'];
                        }
                        $query->whereIn('claim_info_v1.rendering_provider_id',$rendering_provider_id);
                    }

                $aging_data[$value] = $query->get()->toArray();
            }else{
                $query = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    ->join('patients', 'claim_info_v1.patient_id', '=', 'patients.id')
                    ->join('facilities', 'claim_info_v1.facility_id', '=', 'facilities.id')
                    ->select( 'facilities.facility_name','patients.bill_cycle',DB::raw('SUM(claim_info_v1.total_charge) - SUM(insurance_paid+patient_paid+withheld+patient_adj+insurance_adj) AS total_ar'), DB::raw('COUNT(pmt_claim_fin_v1.id) as claim_count'))->whereRaw('claim_info_v1.total_charge - (pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.patient_adj + pmt_claim_fin_v1.insurance_adj + pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.withheld) !=  0');
                        if(empty($end_date)) {
                             $query->whereDate("claim_info_v1.date_of_service", "<", $start_date );
                        }else {
                           $query->whereDate('claim_info_v1.date_of_service', '<=', $start_date)->whereDate('claim_info_v1.date_of_service', '>=', $end_date);
                        }
                        $query->where(function($qry){
                            $qry->where(function($query){
                                $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                            })->orWhere('claim_info_v1.insurance_id',0);
                        })->whereNull('claim_info_v1.deleted_at')->groupBy('claim_info_v1.facility_id')->orderBy('claim_info_v1.facility_id');

                        if(isset($request['rendering_provider_id']) && !empty($request['rendering_provider_id'])){
                            if(isset($request['export'])) {
                                $rendering_provider_id = is_array($request['rendering_provider_id']) ?  $request['rendering_provider_id'] : explode(',', $request['rendering_provider_id']);
                            } else {
                                $rendering_provider_id = $request['rendering_provider_id'];
                            }
                            $query->whereIn('claim_info_v1.rendering_provider_id',$rendering_provider_id);
                        }

                    $aging_data[$value] = $query->get()->toArray();
                }
        }
        if(!empty($aging_data)){
            foreach ($aging_data as $key => $value) {
                foreach ($value as $k => $v) {
                	$aging[$v['facility_name']][$key]['claim_count'] = $v['claim_count'];
                	$aging[$v['facility_name']][$key]['total_ar'] = $v['total_ar'];
                }
            }
        }
        return $aging;
    }

    //Insurance Claims - By facility
    public function insuranceClaimsByFacility($request){
        $get_list_header = [];
        $query = ClaimInfoV1::leftJoin('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    ->join('facilities', 'claim_info_v1.facility_id', '=', 'facilities.id')
                    ->leftJoin('insurances', 'insurances.id', '=', 'claim_info_v1.insurance_id')
                    ->select( DB::raw('COUNT(case when claim_info_v1.status = "Paid" then claim_info_v1.id end) as paid'),'facilities.facility_name as facility_name',DB::raw('insurances.short_name as insurance_name'),DB::raw('insurances.insurance_name as insurance_full_name'),DB::raw('COUNT(claim_info_v1.id) as claim_count'),DB::raw('SUM(claim_info_v1.total_charge-(pmt_claim_fin_v1.insurance_paid+pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)) AS total_ar'),'insurances.id as insurance_id','facilities.id as facility_id')->where('claim_info_v1.insurance_id',"!=", '0')
                    ->groupBy('claim_info_v1.facility_id','claim_info_v1.insurance_id')->orderBy('claim_info_v1.facility_id');
                    /*->where('claim_info_v1.insurance_id',"!=", '0')->where('claim_info_v1.claim_submit_count','!=',0)->whereNotIn('claim_info_v1.status',['Hold','Rejection'])*/
        if(isset($request['performance_date']) && !empty($request['performance_date'])){
            $exp = explode("-",$request['performance_date']);
            $start_date = Helpers::utcTimezoneStartDate($exp[0]);
            $end_date = Helpers::utcTimezoneEndDate($exp[1]);
            $query->where(DB::raw('(pmt_claim_fin_v1.created_at)'), '>=', $start_date)->where(DB::raw('(pmt_claim_fin_v1.created_at)'), '<=', $end_date);
            $get_list_header["Transaction Date"] = date("m/d/Y",strtotime(Helpers::dateFormat($exp[0],'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($exp[1],'datedb')));
        } else {
            $practice_id = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : @$request['practice_id'];
            $start_date = Helpers::timezone(date('Y-m-1 H:i:s'),'Y-m-d', $practice_id);
            $start_date = Helpers::utcTimezoneStartDate($start_date);
            $end_date = Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d', $practice_id);
            $end_date = Helpers::utcTimezoneStartDate($end_date);
            $query->where(DB::raw('(claim_info_v1.created_at)'), '>=', $start_date)->where(DB::raw('(claim_info_v1.created_at)'), '<=', $end_date);
            $get_list_header["Transaction Date"] = date("m/d/Y",strtotime(Helpers::dateFormat($start_date,'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($end_date,'datedb')));
        }

        if(isset($request['date_of_service']) && !empty($request['date_of_service'])){
            $exp = explode("-",$request['date_of_service']);
            $query->where(DB::raw('(claim_info_v1.date_of_service)'), '>=', Helpers::dateFormat($exp[0],'datedb'))->where(DB::raw('(claim_info_v1.date_of_service)'), '<=', Helpers::dateFormat($exp[1],'datedb'));
            $get_list_header["Date Of Service"] = date("m/d/Y",strtotime(Helpers::dateFormat($exp[0],'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($exp[1],'datedb')));
        }
        if(isset($request['rendering_provider_id']) && !empty($request['rendering_provider_id'])){
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name");
            if(isset($request['export'])) {
                $rendering_provider_id = is_array($request['rendering_provider_id']) ?  $request['rendering_provider_id'] : explode(',', $request['rendering_provider_id']);
            } else {
                $rendering_provider_id = $request['rendering_provider_id'];
            }

            if(!empty($rendering_provider_id))
                $query->whereIn('claim_info_v1.rendering_provider_id',$rendering_provider_id);
            $provider= $provider->whereIn('id', $rendering_provider_id)->get()->toArray();
            $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
        }
        $insuranceClaim = $query->get()->toArray();
        if(!empty($insuranceClaim))
        foreach ($insuranceClaim as $key => $value) {
            $insuranceClaims[$value['facility_name']][$key]['insurance_name'] = $value['insurance_name'];
            $insuranceClaims[$value['facility_name']][$key]['insurance_full_name'] = $value['insurance_full_name'];
            $insuranceClaims[$value['facility_name']][$key]['paid'] = $value['paid'];
            $insuranceClaims[$value['facility_name']][$key]['claim_count'] = $value['claim_count'];
            $insuranceClaims[$value['facility_name']][$key]['total_ar'] = $value['total_ar'];
        }
        else
            $insuranceClaims = [];
        return $insuranceClaims;
    }

    //  Location Status Summary
    public function facilityStatus($request){
        $get_list_header = [];
        $query = ClaimInfoV1::leftJoin('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    ->join('facilities', 'claim_info_v1.facility_id', '=', 'facilities.id')
                    ->selectRaw('facilities.facility_name, COUNT(claim_info_v1.id) as claim_count, SUM(pmt_claim_fin_v1.insurance_due-(pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.patient_adj)) AS total_ar, facilities.id as facility_id, SUM(claim_info_v1.total_charge) as total_charge, SUM(pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.insurance_paid) as payments, DATEDIFF(MAX(pmt_claim_fin_v1.created_at),MIN(pmt_claim_fin_v1.created_at)) as days, IF(sunday_forenoon="0;0" AND sunday_afternoon="720;720",1,0) as Sunday,IF(monday_forenoon="0;0" AND monday_afternoon="720;720",count(monday_afternoon),0) as Monday,IF(tuesday_forenoon="0;0" AND tuesday_afternoon="720;720",count(tuesday_afternoon),0) as Tuesday, IF(wednesday_forenoon="0;0" AND wednesday_afternoon="720;720",count(wednesday_afternoon),0) as Wednesday, IF(thursday_forenoon="0;0" AND thursday_afternoon="720;720",count(thursday_afternoon),0) as Thursday, IF(friday_forenoon="0;0" AND friday_afternoon="720;720",count(friday_afternoon),0) as Friday, IF(saturday_forenoon="0;0" AND saturday_afternoon="720;720",count(saturday_afternoon),0) as Saturday')
                    ->groupBy('claim_info_v1.facility_id')
                    ->orderBy('claim_info_v1.facility_id');
        if(isset($request['performance_date']) && !empty($request['performance_date'])){
            $exp = explode("-",$request['performance_date']);
            $start = Helpers::dateFormat($exp[0],'datedb');
            $end = Helpers::dateFormat($exp[1],'datedb');
            $start_date = Helpers::utcTimezoneStartDate($exp[0]);
            $end_date = Helpers::utcTimezoneEndDate($exp[1]);
            $date1 = strtotime($exp[0]);
            $date2 = strtotime($exp[1]);
            $diff = $date2 - $date1;
            $days = ($diff/60/60/24)+1;
            $query->where(DB::raw('(claim_info_v1.created_at)'), '>=', $start_date)->where(DB::raw('(claim_info_v1.created_at)'), '<=', $end_date);
            $get_list_header["Transaction Date"] = date("m/d/Y",strtotime(Helpers::dateFormat($exp[0],'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($exp[1],'datedb')));
        }else{
            $practice_id = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : @$request['practice_id'];
            $end_date = Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d', $practice_id);
            $end_date = Helpers::utcTimezoneStartDate($end_date);
            $start_date = Helpers::timezone(date('Y-m-01 H:i:s', strtotime($end_date)),'Y-m-d', $practice_id);
            $start_date = Helpers::utcTimezoneStartDate($start_date);
            $date1 = strtotime($start_date);
            $date2 = strtotime($end_date);
            $diff = $date2 - $date1;
            $days = ($diff/60/60/24)+1;
            $query->where(DB::raw('(claim_info_v1.created_at)'), '>=', $start_date)->where(DB::raw('(claim_info_v1.created_at)'), '<=', $end_date);
            $get_list_header["Transaction Date"] = date("m/d/Y",strtotime(Helpers::dateFormat($start_date,'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($end_date,'datedb')));
        }
        $date_of_service = '';
        if(isset($request['date_of_service']) && !empty($request['date_of_service'])){
            $exp = explode("-",$request['date_of_service']);
            $query->where(DB::raw('(claim_info_v1.date_of_service)'), '>=', Helpers::dateFormat($exp[0],'datedb'))->where(DB::raw('(claim_info_v1.date_of_service)'), '<=', Helpers::dateFormat($exp[1],'datedb'));
            $get_list_header["Date Of Service"] = date("m/d/Y",strtotime(Helpers::dateFormat($exp[0],'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($exp[1],'datedb')));
            $date_of_service = " and (claim_info_v1.date_of_service) >='".Helpers::dateFormat($exp[0],'datedb')."' and (claim_info_v1.date_of_service) <='".Helpers::dateFormat($exp[1],'datedb')."'";
        }
        $rendering= '';
        if(isset($request['rendering_provider_id']) && !empty($request['rendering_provider_id'])){
            if(isset($request['export'])) {
                $rendering_provider_id = is_array($request['rendering_provider_id']) ?  $request['rendering_provider_id'] : explode(',', $request['rendering_provider_id']);
            } else {
                $rendering_provider_id = $request['rendering_provider_id'];
            }

            if(!empty($rendering_provider_id)){
                $query->whereIn('claim_info_v1.rendering_provider_id',$rendering_provider_id);
                if(is_array($request['rendering_provider_id']))
                    $ren = implode(',', $request['rendering_provider_id']);
                else
                    $ren = $request['rendering_provider_id'];
                $rendering = ' and claim_info_v1.rendering_provider_id in ('.$ren.')';
            }
            $provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name");
            $provider= $provider->whereIn('id', $rendering_provider_id)->get()->toArray();
            $get_list_header["Rendering Provider"] =  @array_flatten($provider)[0];
        }
        $facilityStatus = $query->get()->toArray();
        $result = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        foreach($facilityStatus as $key=>$val){
            $result[$key]["facility_name"] = $val['facility_name'];
            $result[$key]["claim_count"] = $val['claim_count'];
            $result[$key]["total_ar"] = $val['total_ar'];
            $result[$key]["facility_id"] = $val['facility_id'];
            $result[$key]["days"] = $val['days'];
            $result[$key]["Sunday"] = $val['Sunday'];
            $result[$key]["Monday"] = $val['Monday'];
            $result[$key]["Tuesday"] = $val['Tuesday'];
            $result[$key]["Wednesday"] = $val['Wednesday'];
            $result[$key]["Thursday"] = $val['Thursday'];
            $result[$key]["Friday"] = $val['Friday'];
            $result[$key]["Saturday"] = $val['Saturday'];
            $charge = DB::select("select sum(claim_info_v1.total_charge) as charges  from  claim_info_v1 where  claim_info_v1.deleted_at is null and facility_id=".$val['facility_id']." and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start."'  and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end."'".$date_of_service.$rendering);
            if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
                $res = DB::select("select sum(pmt_claim_tx_v1.total_paid) as payments  from pmt_claim_tx_v1  join claim_info_v1 on claim_info_v1.id=pmt_claim_tx_v1.claim_id where pmt_type in ('Payment','Credit Balance', 'Refund') and claim_info_v1.deleted_at is null and facility_id=".$val['facility_id']." and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start."'  and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end."'".$date_of_service.$rendering);
            } else {
                $res = DB::select("select sum(pmt_claim_tx_v1.total_paid) as payments  from pmt_claim_tx_v1  join claim_info_v1 on claim_info_v1.id=pmt_claim_tx_v1.claim_id where pmt_type in ('Payment','Credit Balance') and claim_info_v1.deleted_at is null and facility_id=".$val['facility_id']." and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start."'  and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end."'".$date_of_service.$rendering);
            }
            $result[$key]["total_charge"] = $charge[0]->charges;
            $result[$key]["payments"] = $res[0]->payments;
        }
        $facilityStatus = $result;
        $resultDays = array('Monday' => 0,
                'Tuesday' => 0,
                'Wednesday' => 0,
                'Thursday' => 0,
                'Friday' => 0,
                'Saturday' => 0,
                'Sunday' => 0);
        $startDate = new DateTime($start_date);
        $endDate = new DateTime($end_date);
        // iterate over start to end date
        while($startDate <= $endDate ){
            // find the timestamp value of start date
            $timestamp = strtotime($startDate->format('d-m-Y'));
            // find out the day for timestamp and increase particular day
            $weekDay = date('l', $timestamp);
            $resultDays[$weekDay] = $resultDays[$weekDay] + 1;
            // increase startDate by 1
            $startDate->modify('+1 day');
        }
        return compact('facilityStatus','days','resultDays','get_list_header');
    }

    // -------------------------------------- Start - Export Month End Performance Summary Report ------------------------------------
    public function monthendperformanceExport($export = '',$data = '')
    {
        // Send request and get data
        $monthendperformance = $this->monthendperformance($export, $data);
        //$FacilityWiseOutstanding = $monthendperformance['FacilityWiseOutstanding'];
        $insuranceClaimsByFacility = $monthendperformance['insuranceClaimsByFacility'];
        $facilityStatus = $monthendperformance['facilityStatus'];
        $days = $monthendperformance['days'];
        $resultDays = $monthendperformance['resultDays'];
        $searchBy = $monthendperformance['searchBy'];
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $date = Helpers::timezone(date('m/d/y H:i:s'),'m-d-Y',$practice_id);
        //$name = $data['export_id'].'X0X'.'Month_End_Performance_Summary_Report_' . $date;

        $request = request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/performance/monthEnd/export_pdf';
            $report_name = "Month End Performance Summary Report";
            $data = ['createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'export' => $export, 'insuranceClaimsByFacility' => $insuranceClaimsByFacility, 'facilityStatus' => $facilityStatus, 'days' => $days, 'search_by' => $searchBy];
            return $data;
        }

        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/performance/monthEnd/export';
            //$data['FacilityWiseOutstanding'] = $FacilityWiseOutstanding;
            $data['insuranceClaimsByFacility'] = $insuranceClaimsByFacility;
            $data['facilityStatus'] = $facilityStatus;
            $data['days'] = $days;
            $data['resultDays'] = $resultDays;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['searchBy'] = $searchBy;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        }
        // Status change to report_export_task table
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }

    // Provider Summary by Location
    public function providerSummary($request ='', $export = '',$data = '') {
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $ClaimController  = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('provider_summary_by_location');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];
        if (!empty($data))
            $request = $data;
        else
            $request = Request::all();

        /*$query = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    ->join('claim_cpt_info_v1', 'claim_cpt_info_v1.claim_id', '=', 'claim_info_v1.id')
                    ->join('pmt_claim_tx_v1', 'pmt_claim_tx_v1.claim_id', '=', 'claim_info_v1.id')
                    ->join('cpts', 'claim_cpt_info_v1.cpt_code', '=', 'cpts.cpt_hcpcs')
                    ->join('facilities', 'claim_info_v1.facility_id', '=', 'facilities.id')
                    ->join('providers', 'claim_info_v1.rendering_provider_id', '=', 'providers.id')
                    ->join(DB::raw('(SELECT cpt_id, insurance_id, year, allowed_amount FROM multi_fee_schedule group by id) as multi_fee_schedule'),
                    function($join)
                    {
                       $join->on('cpts.id', '=', 'multi_fee_schedule.cpt_id');
                       $join->on('pmt_claim_tx_v1.claim_insurance_id', '=', 'multi_fee_schedule.insurance_id');
                       $join->on(DB::raw('year(claim_info_v1.date_of_service)'), '=', 'multi_fee_schedule.year');
                    })
                    ->selectRaw('claim_info_v1.id as id, claim_info_v1.facility_id as facility_id, claim_info_v1.rendering_provider_id as provider_id,facilities.facility_name, providers.provider_name, SUM(distinct claim_info_v1.total_charge) as total_charge, SUM(distinct insurance_paid+patient_paid) as payments, SUM(distinct withheld+patient_adj+insurance_adj) as adjustment, SUM(distinct claim_info_v1.total_charge) - SUM(distinct insurance_paid+patient_paid+withheld+patient_adj+insurance_adj) as total_ar, CASE WHEN pmt_claim_tx_v1.ins_category = "Primary" THEN sum(distinct  multi_fee_schedule.allowed_amount)-(pmt_claim_fin_v1.total_allowed) ELSE  ( multi_fee_schedule.allowed_amount) END as expected')->whereNull('pmt_claim_fin_v1.deleted_at')->groupBy('provider_id','facility_id','id')->orderBy('total_charge','desc');*/
        $start_date = '';
        $end_date = '';
        if(isset($request['provider_by_location_date'])){
            $exp = explode("-",$request['provider_by_location_date']);
            $start_date = Helpers::dateFormat($exp[0],'datedb');
            $end_date = Helpers::dateFormat($exp[1],'datedb');
            //$query->where(DB::raw('DATE(claim_info_v1.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(claim_info_v1.created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
            $get_list_header["Transaction Date"] = date("m/d/Y",strtotime(Helpers::dateFormat($exp[0],'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($exp[1],'datedb')));
        }else{
            $practice_id = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : @$request['practice_id'];
            $start_date = Helpers::dateFormat(date('Y-m-1 H:i:s'),'datedb');
            $end_date = Helpers::dateFormat(date('Y-m-d H:i:s'),'datedb');
            /*$start_date = Helpers::timezone(date('Y-m-1 H:i:s'),'Y-m-d', $practice_id);
            $start_date = Helpers::utcTimezoneStartDate($start_date);
            $end_date = Helpers::timezone(date('Y-m-d H:i:s'),'Y-m-d', $practice_id);
            $end_date = Helpers::utcTimezoneStartDate($end_date);*/
            //$query->where(DB::raw('DATE(claim_info_v1.created_at)'), '>=', date("Y-m-d", strtotime($start_date)))->where(DB::raw('DATE(claim_info_v1.created_at)'), '<=', date("Y-m-d", strtotime($end_date)));
            $get_list_header["Transaction Date"] = date("m/d/Y",strtotime(Helpers::dateFormat($start_date,'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($end_date,'datedb')));
        }
        $practice_timezone = Helpers::getPracticeTimeZone();
        //  SUM(distinct pmt_claim_cpt_fin_v1.insurance_paid + pmt_claim_cpt_fin_v1.patient_paid) as payments,
        $query = "(select claim_cpt_info_v1.id as id,
                    claim_info_v1.facility_id as facility_id,
                    claim_info_v1.rendering_provider_id as provider_id,
                    facilities.facility_name,
                    providers.provider_name,
                    claim_info_v1.insurance_id,
                    SUM(distinct claim_info_v1.total_charge) as total_charge,
                    sum(pmt_claim_tx_v1.total_paid) as payments,
                    SUM(distinct pmt_claim_cpt_fin_v1.with_held + pmt_claim_cpt_fin_v1.patient_adjusted + pmt_claim_cpt_fin_v1.insurance_adjusted) as adjustment,
                    SUM(distinct claim_cpt_info_v1.charge) - SUM(distinct pmt_claim_cpt_fin_v1.insurance_paid + pmt_claim_cpt_fin_v1.patient_paid +pmt_claim_cpt_fin_v1.with_held + pmt_claim_cpt_fin_v1.patient_adjusted + pmt_claim_cpt_fin_v1.insurance_adjusted) as total_ar,
                    CASE WHEN pmt_claim_tx_v1.ins_category = 'Primary' THEN multi_fee_schedule.allowed_amount-pmt_claim_cpt_tx_v1.allowed ELSE  multi_fee_schedule.allowed_amount END as expected,
                    multi_fee_schedule.allowed_amount
                from claim_info_v1
                    left join claim_cpt_info_v1 on claim_cpt_info_v1.claim_id = claim_info_v1.id
                    left join cpts on claim_cpt_info_v1.cpt_code = cpts.cpt_hcpcs
                    left join facilities on claim_info_v1.facility_id = facilities.id
                    left join providers on claim_info_v1.rendering_provider_id = providers.id
                    left join pmt_claim_tx_v1 on pmt_claim_tx_v1.claim_id = claim_info_v1.id
                    left join (SELECT cpt_id, insurance_id, year, allowed_amount FROM multi_fee_schedule group by id) as multi_fee_schedule on cpts.id = multi_fee_schedule.cpt_id and if(pmt_claim_tx_v1.claim_insurance_id is NULL or pmt_claim_tx_v1.claim_insurance_id='',if((SELECT count(id) FROM multi_fee_schedule where claim_info_v1.insurance_id=insurance_id)>1,claim_info_v1.insurance_id= multi_fee_schedule.insurance_id,0=multi_fee_schedule.insurance_id),pmt_claim_tx_v1.claim_insurance_id= multi_fee_schedule.insurance_id)  and year(claim_info_v1.date_of_service) = multi_fee_schedule.year
                    left join pmt_claim_cpt_tx_v1 on claim_cpt_info_v1.id = pmt_claim_cpt_tx_v1.claim_cpt_info_id
                    left join pmt_claim_cpt_fin_v1 on claim_cpt_info_v1.id = pmt_claim_cpt_fin_v1.claim_cpt_info_id
                where claim_info_v1.deleted_at is null
                    and claim_cpt_info_v1.deleted_at is null
                    and pmt_claim_cpt_fin_v1.deleted_at is null
                    and pmt_claim_tx_v1.deleted_at is null
                    and cpts.deleted_at is null
                    and pmt_claim_cpt_tx_v1.deleted_at is null
                    and facilities.deleted_at is null
                    and providers.deleted_at is null ";

                    if(isset($request['date_of_service']) && !empty($request['date_of_service'])){
                        $exp = explode("-",$request['date_of_service']);
                        $query .="and (claim_info_v1.date_of_service) >='".Helpers::dateFormat($exp[0],'datedb')."' and (claim_info_v1.date_of_service) <='".Helpers::dateFormat($exp[1],'datedb')."'";
                        $get_list_header["Date Of Service"] = date("m/d/Y",strtotime(Helpers::dateFormat($exp[0],'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($exp[1],'datedb')));
                    }
                    if(isset($request['rendering_provider_id']) && !empty($request['rendering_provider_id'])){

                        if(isset($request['export'])) {
                            $rendering_provider_id = is_array($request['rendering_provider_id'])?implode(',', $request['rendering_provider_id']):$request['rendering_provider_id'];
                        } else {
                            $rendering_provider_id = implode(',',$request['rendering_provider_id']);
                        }
                        if(!empty($rendering_provider_id))
                            $query .="and claim_info_v1.rendering_provider_id in (".$rendering_provider_id.")";

                        $rendering_provider = !is_array($request['rendering_provider_id']) ?explode(',', $request['rendering_provider_id']):$request['rendering_provider_id'];

                        $ren_provider = Provider::selectRaw("GROUP_CONCAT(provider_name SEPARATOR ', ') as provider_name");
                        $ren_provider= $ren_provider->whereIn('id', $rendering_provider)->get()->toArray();
                        $get_list_header["Rendering Provider"] =  @array_flatten($ren_provider)[0];
                    }
                    $query .="group by provider_id, facility_id, id order by claim_info_v1.total_charge desc)as a";

        $providers = [];

        $provider = DB::select("select id, facility_id, provider_id, facility_name, provider_name, sum(total_charge) as total_charge, sum(payments) as payments, sum(adjustment) as adjustment, sum(total_ar) as total_ar, sum(expected) as expected from $query group by provider_id, facility_id order by total_charge desc");

        if(!empty($provider)) {
            foreach($provider as $key => $value){
                $date_of_service = '';
                if(isset($request['date_of_service']) && !empty($request['date_of_service'])){
                        $exp = explode("-",$request['date_of_service']);
                        $date_of_service = " and (claim_info_v1.date_of_service) >='".Helpers::dateFormat($exp[0],'datedb')."' and (claim_info_v1.date_of_service) <='".Helpers::dateFormat($exp[1],'datedb')."'";
                    }
                $charge = DB::select("select sum(claim_info_v1.total_charge) as charges  from  claim_info_v1 where  claim_info_v1.deleted_at is null and rendering_provider_id=".$value->provider_id." and facility_id=".$value->facility_id." and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."'  and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'".$date_of_service);
                $providers[$value->provider_name.'_'.$value->provider_id][$value->facility_name]['facility_name'] = $value->facility_name;
                $providers[$value->provider_name.'_'.$value->provider_id][$value->facility_name]['total_charge'] = $charge[0]->charges;
                if(isset($request['include_refund']) && $request['include_refund'] == 'Yes' ) {
                    $res = DB::select("select sum(pmt_claim_tx_v1.total_paid) as payments  from pmt_claim_tx_v1  join claim_info_v1 on claim_info_v1.id=pmt_claim_tx_v1.claim_id where pmt_type in ('Payment','Credit Balance', 'Refund') and claim_info_v1.deleted_at is null and rendering_provider_id=".$value->provider_id." and facility_id=".$value->facility_id." and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."'  and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'".$date_of_service);
                } else {
                    $res = DB::select("select sum(pmt_claim_tx_v1.total_paid) as payments  from pmt_claim_tx_v1  join claim_info_v1 on claim_info_v1.id=pmt_claim_tx_v1.claim_id where pmt_type in ('Payment','Credit Balance') and claim_info_v1.deleted_at is null and rendering_provider_id=".$value->provider_id." and facility_id=".$value->facility_id." and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."'  and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'".$date_of_service);
                }

                $adj = DB::select("select SUM(pmt_claim_tx_v1.total_withheld +  pmt_claim_tx_v1.total_writeoff) as adjustment  from pmt_claim_tx_v1  join claim_info_v1 on claim_info_v1.id=pmt_claim_tx_v1.claim_id where  claim_info_v1.deleted_at is null and rendering_provider_id=".$value->provider_id." and facility_id=".$value->facility_id." and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."'  and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'".$date_of_service);
                $fin = DB::select("select SUM(claim_info_v1.total_charge)-sum(pmt_claim_fin_v1.insurance_paid+patient_paid+patient_adj+insurance_adj+withheld) as total_ar  from pmt_claim_fin_v1  join claim_info_v1 on claim_info_v1.id=pmt_claim_fin_v1.claim_id where  claim_info_v1.deleted_at is null and rendering_provider_id=".$value->provider_id." and facility_id=".$value->facility_id." and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."'  and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'".$date_of_service);
                $providers[$value->provider_name.'_'.$value->provider_id][$value->facility_name]['payments'] = $res[0]->payments;
                $providers[$value->provider_name.'_'.$value->provider_id][$value->facility_name]['adjustment'] = $adj[0]->adjustment;
                $providers[$value->provider_name.'_'.$value->provider_id][$value->facility_name]['total_ar'] = $fin[0]->total_ar;
                $providers[$value->provider_name.'_'.$value->provider_id][$value->facility_name]['expected'] = $value->expected;
            }
        } else {
            $providers = [];
        }
        // Start - Top two Provider Chart
        $i = 0;
        if(!empty($providers)) {
            foreach($providers as $key => $p){
                if($i<2){
                    foreach($p as $k=>$v){
                        $chart[$key]['facility_name'][]['label'] = $v['facility_name'];
                        $chart[$key]['total_charge'][]['value'] = $v['total_charge'];
                        $chart[$key]['payments'][]['value'] = $v['payments'];
                        $chart[$key]['adjustment'][]['value'] = $v['adjustment'];
                        $chart[$key]['total_ar'][]['value'] = $v['total_ar'];
                        $chart[$key]['expected'][]['value'] = $v['expected'];
                    }
                    $i++;
                }
            }
        }
        $chart_1 = $chart_2 = [];
        $provider_name_1 = $provider_name_2 = '';
        $searchBy = $get_list_header;
        /*if(!empty($chart))
            foreach ($chart as $chart_key => $chart_value) {
                $charts[] = $chart_value;
                $provider_name[] = $chart_key;
            }

            if(!empty($charts[0])){
                $chart_1 = $charts[0];
                $provider_name_1 = explode('_',$provider_name[0])[0];
            }else{
                $chart_1 = [];
                $provider_name_1 = [];
            }

            if(!empty($charts[1])){
                $chart_2 = $charts[1];
                $provider_name_2 = explode('_',$provider_name[1])[0];
            }else{
                $chart_2 = [];
                $provider_name_2 = [];
            }*/
        // End - Top two Provider Chart
        if(empty($export)){
            if(empty($request) || isset($request['search'])){
                return view('reports/performance/providerSummary/reports', compact('provider_name_1', 'provider_name_2', 'chart_1', 'chart_2', 'providers', 'selected_tab', 'heading', 'heading_icon', 'search_fields','searchBy'));
            }else{
                return view('reports/performance/providerSummary/ajax', compact('provider_name_1', 'provider_name_2', 'chart_1', 'chart_2', 'providers', 'selected_tab', 'heading', 'heading_icon', 'search_fields','searchBy'));
            }
        }else{
             return compact('providers', 'searchBy');
        }
    }

    // -------------------------------------- Start - Export Provider Summary by Location ------------------------------------
    public function providerSummaryExport($export = '',$data = '')
    {
        // Send request and get data
        $providers = $this->providerSummary($export, $data);
        $search_by = $providers['searchBy'];
        $providers = $providers['providers'];
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $date = Helpers::timezone(date('m/d/y H:i:s'),'m-d-Y',$practice_id);
        // $name = $data['export_id'].'X0X'.'Provider_Summary_by_Location_Report_' . $date;

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/performance/providerSummary/export_pdf';
            $report_name = "Provider Summary by Location";
            $data = ['providers' => $providers, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
            return $data;
        }

        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/performance/providerSummary/export';
            $data['providers'] = $providers;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['searchBy'] = $search_by;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        }
        // Status change to report_export_task table
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }
    // -------------------------------------- Start - Denials Summary ------------------------------------
    public function denialsSummary($export = '',$data = ''){
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        if (!empty($data))
            $request = $data;
        else
            $request = Request::all();
        $denials_billings = ClaimInfoV1::join('pmt_claim_cpt_tx_v1','pmt_claim_cpt_tx_v1.claim_id', '=', 'claim_info_v1.id')
                                ->leftJoin('claim_cpt_info_v1','pmt_claim_cpt_tx_v1.claim_cpt_info_id', '=', 'claim_cpt_info_v1.id')
                                ->leftJoin('cpts','claim_cpt_info_v1.cpt_code', '=', 'cpts.cpt_hcpcs')
                                ->leftJoin('multi_fee_schedule','cpts.id', '=', 'multi_fee_schedule.cpt_id')
                                ->selectRaw('claim_info_v1.id, claim_info_v1.status, pmt_claim_cpt_tx_v1.denial_code, count(claim_info_v1.id) as claims, sum(distinct claim_info_v1.total_charge) as value, sum(multi_fee_schedule.allowed_amount) as  fee_schedule')
                                ->where('reason_type','Billing')
                                ->whereIn('claim_info_v1.status',['Pending','Denied'])
                                ->groupBy('status','denial_code')
                                ->get()->toArray();
        if(!empty($denials_billings)) {
            foreach($denials_billings as $res){
                $exp = explode(',', $res['denial_code']);
                if(count($exp)>1){
                    foreach($exp as $code){
                        if(!empty($code)){
                            $denials_billing[$res['status']][$code]['status'] = $res['status'];
                            $denials_billing[$res['status']][$code]['claims'][] = $res['claims'];
                            $denials_billing[$res['status']][$code]['value'] = $res['value'];
                            $denials_billing[$res['status']][$code]['fee_schedule'] = $res['fee_schedule'];
                            $denials_billing[$res['status']][$code]['description'] = Code::where('transactioncode_id',$code)->value('description');
                        }
                    }
                } else{
                    $denials_billing[$res['status']][$res['denial_code']]['status'] = $res['status'];
                    $denials_billing[$res['status']][$res['denial_code']]['claims'][] = $res['claims'];
                    $denials_billing[$res['status']][$res['denial_code']]['value'] = $res['value'];
                    $denials_billing[$res['status']][$res['denial_code']]['fee_schedule'] = $res['fee_schedule'];
                    $denials_billing[$res['status']][$res['denial_code']]['description'] = Code::where('transactioncode_id',$res['denial_code'])->value('description');
                }
            }
        }
        $denials_codings = ClaimInfoV1::join('pmt_claim_cpt_tx_v1','pmt_claim_cpt_tx_v1.claim_id', '=', 'claim_info_v1.id')
                                ->selectRaw('claim_info_v1.status, pmt_claim_cpt_tx_v1.denial_code, count(claim_info_v1.id) as claims, sum(distinct claim_info_v1.total_charge) as value')
                                ->where('reason_type','Coding')
                                ->where('claim_info_v1.status','Denied')
                                ->groupBy('status','denial_code')
                                ->get()->toArray();
        if(!empty($denials_codings)) {
            foreach($denials_codings as $res){
                $exp = explode(',', $res['denial_code']);
                if(count($exp)>1){
                    foreach($exp as $code){
                        if(!empty($code)){
                            $denials_coding[$code]['status'] = $res['status'];
                            $denials_coding[$code]['claims'][] = $res['claims'];
                            $denials_coding[$code]['value'] = $res['value'];
                            $denials_coding[$code]['description'] = Code::where('transactioncode_id',$code)->value('description');
                        }
                    }
                } else{
                    $denials_coding[$res['denial_code']]['status'] = $res['status'];
                    $denials_coding[$res['denial_code']]['claims'][] = $res['claims'];
                    $denials_coding[$res['denial_code']]['value'] = $res['value'];
                    $denials_coding[$res['denial_code']]['description'] = Code::where('transactioncode_id',$res['denial_code'])->value('description');
                }
            }
        }

        if(empty($export)){
            return view('reports/performance/denials/reports', compact('selected_tab', 'heading', 'heading_icon', 'denials_billing', 'denials_coding'));
        } else {
            $result['denials_coding'] = $denials_coding;
            $result['denials_billing'] = $denials_billing;
            return $result;
        }
    }

    // -------------------------------------- Start -  Export Denials Summary ------------------------------------
    public function denialsSummaryExport($export = '',$data = '')
    {
        // Send request and get data
        $denials = $this->denialsSummary($export, $data);
        $createdBy = isset($data['created_user']) ? $data['created_user'] : '';
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $date = Helpers::timezone(date('m/d/y H:i:s'),'m-d-Y',$practice_id);
        $name = $data['export_id'].'X0X'.'Denial_and_Pending_Claims_Summary_' . $date;

        $request = request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/performance/denials/export_pdf';
            $report_name = "Denial and Pending Claims Summary";
            $data = ['denials' => $denials, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'export' => $export];
            return $data;
        }

        if ($export == 'xlsx') {
            Excel::create($name, function($excel) use ($denials, $export, $createdBy, $practice_id) {
                $excel->sheet('Excel', function($sheet) use ($denials, $export, $createdBy, $practice_id) {
                    $sheet->loadView('reports/performance/denials/export')->with("denials", $denials)->with("export", $export)->with('createdBy', $createdBy)->with('practice_id', $practice_id);
                });
            })->store('xls', storage_path("app/Report/exports"));
            $type = '.xls';
        // Load and save CSV Format
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($denials, $export, $createdBy, $practice_id) {
                $excel->sheet('Excel', function($sheet) use ($denials, $export, $createdBy, $practice_id) {
                    $sheet->loadView('reports/performance/denials/export')->with("denials", $denials)->with("export", $export)->with('createdBy', $createdBy)->with('practice_id', $practice_id);
                });
            })->export('csv');
            $type = '.csv';
        }
        // Status change to report_export_task table
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }

    // -------------------------------------- Start -  Export Weekly Billing Report ------------------------------------
    public function weeklyBillingReport()
    {
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/performance/billing/reports', compact('selected_tab', 'heading', 'heading_icon'));
    }

    // -------------------------------------- Start -  Export Weekly Billing Report Export ------------------------------------
    public function weeklyBillingReportExport()
    {
        ini_set('max_execution_time', 0);
        ob_implicit_flush(true);

        try {
            $request = Request::all();
            $practice_timezone = Helpers::getPracticeTimeZone();
            $exp = explode("-",$request['select_transaction_date']);
            $start_date = Helpers::dateFormat($exp[0],'datedb');
            $end_date = Helpers::dateFormat($exp[1],'datedb');

            $billing = ClaimCPTInfoV1::leftJoin('claim_info_v1','claim_info_v1.id','=','claim_cpt_info_v1.claim_id')
                        ->leftJoin('pmt_claim_cpt_tx_v1','pmt_claim_cpt_tx_v1.claim_cpt_info_id','=','claim_cpt_info_v1.id')
                        ->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.claim_id','=','claim_info_v1.id')
                        ->leftJoin('pmt_claim_cpt_fin_v1','pmt_claim_cpt_fin_v1.claim_cpt_info_id','=','claim_cpt_info_v1.id')
                        ->leftJoin('patients','patients.id','=','claim_cpt_info_v1.patient_id')
                        ->leftJoin('providers as rendering','rendering.id','=','claim_info_v1.rendering_provider_id')
                        ->leftJoin('providers as billing','billing.id','=','claim_info_v1.billing_provider_id')
                        //->leftJoin('pos','pos.id','=','claim_info_v1.pos_id')
                       // ->leftJoin('facilities','facilities.id','=','claim_info_v1.facility_id')
                        ->leftJoin('cpts','cpts.cpt_hcpcs','=','claim_cpt_info_v1.cpt_code')
                        ->leftJoin('insurances','insurances.id','=','claim_info_v1.insurance_id')
                        ->leftJoin('insurancetypes','insurances.insurancetype_id','=','insurancetypes.id')
                        ->leftJoin('patient_notes','patient_notes.claim_id','=','claim_cpt_info_v1.claim_id')
                        //->leftJoin('procedure_categories','procedure_categories.id','=','cpts.procedure_category')
                        ->selectRaw("DATE_FORMAT(claim_cpt_info_v1.dos_from,'%m/%d/%Y') as dos_from,
                        claim_cpt_info_v1.cpt_code,
                        claim_cpt_info_v1.modifier1,
                        claim_cpt_info_v1.modifier2,
                        claim_info_v1.icd_codes,
                        claim_cpt_info_v1.cpt_icd_map_key,
                        claim_cpt_info_v1.charge,
                        DATE_FORMAT(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')),'%m/%d/%Y') as posted_date,
                        DATE_FORMAT(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')),'%b') as posted_month,
                        DATE_FORMAT(DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')),'%Y') as posted_year,
                        claim_info_v1.claim_number,
                        claim_info_v1.id as claim_id,
                        claim_cpt_info_v1.id as cpt_id,
                        insurances.insurance_name,
                        insurancetypes.type_name,
                        pmt_claim_cpt_tx_v1.withheld,
                        pmt_claim_cpt_tx_v1.writeoff,

                        cpts.procedure_category,

                        (select sum(pmt_cpt.paid) from pmt_claim_tx_v1 pmt,pmt_claim_cpt_tx_v1 pmt_cpt where pmt.claim_id = claim_cpt_info_v1.claim_id and pmt_cpt.claim_id = claim_cpt_info_v1.claim_id and pmt_cpt.pmt_claim_tx_id = pmt.id and pmt_cpt.claim_cpt_info_id = claim_cpt_info_v1.id and pmt_type in('Payment','Credit Balance') and pmt_method in('Patient','Insurance') and pmt_cpt.deleted_at is null) as tot_amt,

                        (select sum(pmt_claim_cpt_tx_v1.withheld+pmt_claim_cpt_tx_v1.writeoff) from pmt_claim_cpt_tx_v1 where pmt_claim_cpt_tx_v1.claim_id = claim_cpt_info_v1.claim_id and pmt_claim_cpt_tx_v1.claim_cpt_info_id=claim_cpt_info_v1.id and pmt_claim_cpt_tx_v1.deleted_at is null) as tot_adj,
                        (claim_cpt_info_v1.charge-(pmt_claim_cpt_fin_v1.with_held+pmt_claim_cpt_fin_v1.insurance_adjusted+pmt_claim_cpt_fin_v1.patient_adjusted+pmt_claim_cpt_fin_v1.insurance_paid+pmt_claim_cpt_fin_v1.patient_paid)) as tot_ar,

                        (select sum(pmt_cpt.paid) from pmt_claim_tx_v1 pmt,pmt_claim_cpt_tx_v1 pmt_cpt where pmt.claim_id = claim_cpt_info_v1.claim_id and pmt_cpt.claim_id = claim_cpt_info_v1.claim_id and pmt_cpt.pmt_claim_tx_id = pmt.id and pmt_cpt.claim_cpt_info_id = claim_cpt_info_v1.id and pmt.pmt_type = 'Refund' and (pmt_method='Insurance' || pmt_method='Patient')) as tot_refund,

                        (select if((select responsibility from claim_cpt_tx_desc_v1 where claim_cpt_tx_desc_v1.claim_cpt_info_id = claim_cpt_info_v1.id ORDER by id desc limit 1) = (select responsibility from claim_cpt_tx_desc_v1 where claim_cpt_tx_desc_v1.claim_cpt_info_id = claim_cpt_info_v1.id and transaction_type='Denials' ORDER by id desc limit 1) or (select responsibility from claim_cpt_tx_desc_v1 where claim_cpt_tx_desc_v1.claim_cpt_info_id = claim_cpt_info_v1.id ORDER by id desc limit 1) = 0 and (select transaction_type from claim_cpt_tx_desc_v1 where claim_cpt_tx_desc_v1.claim_cpt_info_id = claim_cpt_info_v1.id ORDER by id desc limit 1) = 'Denials' ,value_1,'') as denial_code from claim_cpt_tx_desc_v1 where claim_cpt_tx_desc_v1.claim_cpt_info_id = claim_cpt_info_v1.id and transaction_type='Denials' ORDER by id desc limit 1) as denial_code,

                        pmt_claim_cpt_tx_v1.created_at as transaction_date,
                        patients.account_no,
                        rendering.provider_name as rendering_name,
                        billing.provider_name as billing_name,
                        claim_info_v1.rendering_provider_id,
                        claim_info_v1.billing_provider_id,

                        claim_info_v1.pos_id,
                        claim_info_v1.facility_id,

                        cpts.short_description,
                        cpts.long_description,

                        DATE_FORMAT(DATE(CONVERT_TZ(max(pmt_claim_cpt_tx_v1.created_at),'UTC','".$practice_timezone."')),'%m/%d/%Y') as transanction_date,
                        DATE_FORMAT(DATE(CONVERT_TZ(max(pmt_claim_cpt_tx_v1.created_at),'UTC','".$practice_timezone."')),'%b') as transanction_month,
                        DATE_FORMAT(DATE(CONVERT_TZ(max(pmt_claim_cpt_tx_v1.created_at),'UTC','".$practice_timezone."')),'%Y') as transanction_year,
                        DATE_FORMAT(DATE(CONVERT_TZ(claim_info_v1.submited_date,'UTC','".$practice_timezone."')),'%m/%d/%Y') as first_submitted_date,
                        DATE_FORMAT(DATE(CONVERT_TZ(claim_info_v1.last_submited_date,'UTC','".$practice_timezone."')),'%m/%d/%Y') as last_submitted_date,
                        rendering.npi,
                        claim_cpt_info_v1.patient_id,

                        DATE_FORMAT(DATE(CONVERT_TZ(MAX(patient_notes.created_at),'UTC','".$practice_timezone."')),'%m/%d/%Y') as note_date,

                        (select content from patient_notes where patient_notes.claim_id = claim_cpt_info_v1.claim_id  and patient_notes.status='active' and patient_notes.deleted_at is NULL and patient_notes_type = 'claim_notes' ORDER by id desc limit 1)  as content
                        ")

                        //  facilities.facility_name, facilities.facility_npi,pos.code, procedure_categories.procedure_category,
                        ->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '$start_date' AND  DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '$end_date'")
                        ->groupBy('claim_cpt_info_v1.id');

            //\Log::info($billing->toSql());
            $billing =$billing->orderBy('claim_info_v1.id')->get()->toArray();

            \Log::info("Query executed");
            if(!empty($billing)) {
                $facilities = Facility::selectRaw('facility_name, id, facility_npi')
                              ->where('status', 'Active')
                              ->get()->keyBy('id')->toArray();
                $pos =  Pos::selectRaw('id, code')->pluck('code', 'id')->all();
                $proCat = DB::table('procedure_categories')->selectRaw('id, procedure_category')
                            ->where('status', 'Active')
                            ->pluck('procedure_category', 'id')->all();

                $temp_icd = $tmp_ins = $pat_ins = [];
                $cpt_icd = $map_key = [];

                foreach($billing as $key => $value){
                    $facId = $value["facility_id"];
                    $posId = $value["pos_id"];
                    $billing[$key]['facility_name'] = isset($facilities[$facId]['facility_name']) ? $facilities[$facId]['facility_name'] : '';
                    $billing[$key]['facility_npi'] = isset($facilities[$facId]['facility_npi']) ? $facilities[$facId]['facility_npi'] : '';
                    $billing[$key]['code'] = isset($pos[$posId]) ? $pos[$posId] : '';
                    $billing[$key]['patient_notes_type'] = 'claim_notes';
                    $billing[$key]['tot_refund'] = ($value['tot_refund']!="")?$value['tot_refund']:0;
                    $billing[$key]['procedure_category'] = '';
                    $prCat = $value["procedure_category"];
                    if($prCat != '' )
                        $billing[$key]['procedure_category'] = isset($proCat[$prCat]) ? $proCat[$prCat] : '';

                    $billing[$key]['denial_code'] = $billing[$key]['remarks'] = $billing[$key]['denials'] = '';
                    $denial_code = $remarks = '';
                    if($value['denial_code']!=null){
                        $exp = [];
                        $array = array_filter(explode(',', $value['denial_code']));
                        foreach($array as $val){
                            if(starts_with($val, 'CO')) {
                                $exp[] = str_replace('CO','',$val);
                                $denial_code .= $val.',';
                            } elseif(starts_with($val, 'OA')) {
                                $exp[] = str_replace('OA','',$val);
                                $denial_code .= $val.',';
                            } elseif(starts_with($val, 'PI')) {
                                $exp[] = str_replace('PI','',$val);
                                $denial_code .= $val.',';
                            } elseif(starts_with($val, 'PR')) {
                                $exp[] = str_replace('PR','',$val);
                                $denial_code .= $val.',';
                            } else {
                                $remarks .= $val.',';
                            }
                        }
                        $billing[$key]['denials'] = DB::table('codes')->selectRaw("GROUP_CONCAT(description SEPARATOR ',') as description")->whereIn('transactioncode_id',$exp )->pluck('description')->first();
                    } else{
                        $billing[$key]['denials'] = '';
                    }
                    $billing[$key]['remarks'] = $remarks;
                    $billing[$key]['denial_code'] = $denial_code;

                    if(isset($pat_ins[@$value['patient_id']])) {
                        $billing[$key]['pat_insurances'] = $pat_ins[@$value['patient_id']];
                    } else {
                        $billing[$key]['pat_insurances'] = $pat_ins[@$value['patient_id']] = DB::table('patient_insurance')->whereIn('category',['Primary', 'Secondary', 'Tertiary'])->select('insurances.short_name')->leftJoin('insurances','insurances.id','=','patient_insurance.insurance_id')->where('patient_insurance.patient_id',$value['patient_id'])->get();
                    }

                    if($value['icd_codes']!='') {
                        $i=0;
                        $cpt_icd = explode(',', $value['icd_codes']);
                        $map_key = explode(',', $value['cpt_icd_map_key']);
                        if(!empty($cpt_icd)) {
                            foreach($cpt_icd as $ckey=>$cmap){
                                $tVal = @$cpt_icd[@$map_key[$ckey]-1];
                                $icdDet = [];
                                if($tVal != '') {
                                    if(isset($temp_icd[$tVal])) {
                                        $icdDet = $temp_icd[$tVal];
                                    } else {
                                        $icdDet = $temp_icd[$tVal] = Icd::getIcdCodeAndDesc($tVal);
                                    }
                                }
                                $billing[$key]['icdDet'][$i] = $icdDet;
                                if($i==1){
                                    break;
                                }
                                $i++;

                            }
                        }
                    }
                }
            }
            \Log::info("Query response prepared");
            //\Log::info($billing);
            $get_list_header["Transaction Date"] = date("m/d/Y",strtotime(Helpers::dateFormat($start_date,'datedb'))) . "  To " . date("m/d/Y",strtotime(Helpers::dateFormat($end_date,'datedb')));
            $filePath = 'reports/performance/billing/export';
            $createdBy = Auth::user()->id;
            $practice_id = session()->get('practice_dbid');
            $data['billing'] = $billing;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['file_path'] = $filePath;
            $data['header'] = $get_list_header;
            $data['sheet'] = 'Weekly Billing';
            return $data;
        } catch(Exception $e) {
            \Log::info("Exception occured".$e->getMessage());
            \Log::info($e);
            return [];
        }
        /*
        $date = Helpers::timezone(date('m/d/y H:i:s'),'m-d-Y',$practice_id);
        $name = 'Weekly_Billing_Report_' . $date;
        try{
            ini_set('precision', 20);
            Excel::create($name, function($excel) use ($billing, $createdBy, $practice_id) {
                $excel->sheet('Weekly Billing', function($sheet) use ($billing, $createdBy, $practice_id) {
                    $sheet->loadView('reports/performance/billing/export')->setColumnFormat(array('B'=>'# ?/?','U'=>'#,#0.00','V'=>'#,#0.00','W'=>'#,#0.00','X'=>'#,#0.00','Y'=>'#,#0.00'))->with("billing", $billing)->with('createdBy', $createdBy)->with('practice_id', $practice_id);
                });
            })->download('xls');
        } catch(Exception $e) {
            return redirect('reports/performance/billing');
        }
        */
    }
}