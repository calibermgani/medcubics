<?php

namespace App\Http\Controllers\Reports\Financials;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Auth;
use Response;
use Redirect;
use Config;
use Session;
use Excel;
use Carbon\Carbon;
use PDF;
use Storage;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Practice;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\User as Users;
use Request;
use App\Models\ReportExport as ReportExportTask;
use App\Exports\BladeExport;

if (!defined("DS"))
    DEFINE('DS', DIRECTORY_SEPARATOR); 

class FinancialController extends Api\FinancialApiController {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'financial-report');
        View::share('heading_icon', 'fa-line-chart');
    }

    public function enddaytotal() {

        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getEnddaytotalApi();
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $selected_tab = "financial-report";
        $practice_id = Helpers::getEncodeAndDecodeOfId(Session::get('practice_dbid'),'encode');
        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/financials/enddaytotal/list', compact('practice_id','groupby', 'search_fields', 'searchUserData', 'heading', 'heading_icon', 'selected_tab'));
    }

    public function filter_result() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getFilterResultApiSP(); // Store procedure
        } else {
            $api_response = $this->getFilterResultApi(); // DB
        }        
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $name = Auth::user()->name;
        $filter_result = $api_response_data->data->filter_result;
        $result = $api_response_data->data->result;
        $fin_list = $api_response_data->data->fin_list;
        $daterange = $api_response_data->data->daterange;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_response_data->data->search_by;
        return view('reports/financials/enddaytotal/report_list', compact('start_date', 'end_date', 'name', 'filter_result', 'pagination', 'result', 'fin_list', 'daterange', 'search_by'));
    }

    /*     * * Blade - endday total export ** */

    public function endDayExport($export = '', $data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getFilterResultApiSP($export, $data); // Store procedure
        } else {
            $api_response = $this->getFilterResultApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $result = $api_response_data->data->export_array;
        $search_by = json_decode(json_encode($api_response_data->data->search_by), true);
        $date = date('m-d-Y');
        // $name = 'End_of_the_Day_Totals_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
          $report_name = 'End of the Day Totals';
          $view_path = 'reports/financials/enddaytotal/enddayreportexport_pdf';
          $data = ['result' => $result, 'search_by' => $search_by, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name];
          return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/enddaytotal/enddayreportexport';
            $data['response'] = $result;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['practice_id'] = $practice_id;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array("value" => $data));
            } else {
                return $data;
            }
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['report_file_name'=>$name.$type, 'status'=>'Completed']);
        }*/
    }

    public function getUnbilledreportsfilter() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "financial-report";

        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        /* Getting search field and saved search data Start */

        $ClaimController = new ClaimControllerV1();
        $search_fields_data = $ClaimController->generateSearchPageLoad('unbillied_report_listing');
        $searchUserData = $search_fields_data['searchUserData'];
        $search_fields = $search_fields_data['search_fields'];

        /* Getting search field and saved search data End */

        $api_response = $this->getUnbilledClaimCreatedApi();
        $api_response_data = $api_response->getData();
        $claims_date_details = $api_response_data->data->claims_created_date;
        foreach ($claims_date_details as $year_list) {
            if ($year_list->year != 0)
                $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);
        return view('reports/financials/unbilled/unbilledclaimsreport', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'searchUserData', 'search_fields'));
    }

    public function getUnbilledreports() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getUnbilledClaimApiSP(); // Stored procedure
        } else {
            $api_response = $this->getUnbilledClaimApi();// DB
        }
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $unbilled_claim_details = $api_response_data->data->unbilled_claim_details;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $total_charges = $api_response_data->data->total_charges;
        $search_by = $api_response_data->data->search_by;
        return view('reports/financials/unbilled/report_list', compact('unbilled_claim_details', 'start_date', 'end_date', 'pagination', 'total_charges', 'search_by'));
    }

    /*     * * Blade - export unbilled reports ** */

    public function unbilledexport($export = '',$data = '') {
        // if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getUnbilledClaimApiSP($export,$data); // Stored procedure
        // } else {
        //     $api_response = $this->getUnbilledClaimApi($export,$data);// DB
        // }
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $unbilled_claim_details = $api_response_data->data->unbilled_claim_details;
        $total_charges = $api_response_data->data->total_charges;
        $search_by = $api_response_data->data->search_by;
        $date = date('m-d-Y');
        $timestamp = time();
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        // $name = 'Unbilled_Claims_Analysis_'.$date;
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $report_name = 'Unbilled Claims Analysis';
            $view_path = 'reports/financials/unbilled/report_listexport_pdf';
            $data = ['unbilled_claim_details' => $unbilled_claim_details, 'total_charges' => $total_charges, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
            return $data;
        }
        
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/unbilled/report_listexport';
            $data['unbilled_claim_details'] = $unbilled_claim_details;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['total_charges'] = $total_charges;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array("value" => $data));
            } else {
                return $data;
            }
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';        
        }
    }
        /*if(isset($data['export_id']))
          ReportExportTask::where('id',$data['export_id'])->update(['report_file_name'=>$name.$type, 'status'=>'Completed']);
        }*/

    /* public function getUnbilledexport($export=''){
      $api_response = $this->getUnbilledClaimApi();
      $api_response_data = $api_response->getData();
      $unbilled_claim_details = $api_response_data->data->unbilled_claim_details;
      $total_charge_count = 0;
      $grand_total = 0;

      foreach($unbilled_claim_details as $list){
      $data['claim_number'] = $list->claim_number;
      $data['first_name'] = $list->patient->first_name;
      $data['account_no'] = $list->patient->account_no;
      $data['insurance_name'] = $list->insurance_details->insurance_name;
      $data['provider_name'] = $list->rendering_provider->provider_name;
      $data['billing_provider_name'] = $list->billing_provider->provider_name;
      $data['date_of_service'] = date('m/d/Y',strtotime($list->date_of_service));
      $data['created_at'] = date('m/d/Y',strtotime($list->created_at));
      $data['total_charge'] = $list->total_charge;
      $grand_total = $grand_total + $list->total_charge;
      $get_list[$total_charge_count] = $data;
      $total_charge_count++;
      }
      //echo "<pre>";print_r($get_list);die;
      $get_result = $get_list;
      $total = '100';
      $get_result[$total_charge_count] = ['claim_number'=>'','first_name'=>'','account_no'=>'','insurance_name'=>'','provider_name'=>'','billing_provider_name'=>'','date_of_service'=>'','created_at'=>'','total_charge'=>''];

      $total_charge_count = $total_charge_count + 1;

      $get_result[$total_charge_count] = ['claim_number'=>'','first_name'=>'','account_no'=>'','insurance_name'=>'','provider_name'=>'','billing_provider_name'=>'','date_of_service'=>'','created_at'=>'','total_charge'=>'Grand Total : '.Helpers::priceFormat($grand_total,'no')];

      $result["value"] = json_decode(json_encode($get_result));
      $result["exportparam"] = array(
      'filename'  =>  'Unbilled Claim Reports',
      'heading' =>  '',
      'fields'  =>  array(
      'claim_number'  =>  'Claim#',
      'first_name'  =>  'Patient Name',
      'account_no'  =>  'Acc#',
      'insurance_name'=>  'Responsibility',
      'provider_name' =>  'Rendering Provider',
      'billing_provider_name' =>  'Billing Provider',
      'date_of_service'=>  'DOS',
      'created_at'  =>  'Transaction Date',
      'total_charge'  =>  'Fee',
      ));

      $callexport = new CommonExportApiController();
      return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);

      } */

    public function getEnddayexport($export = '') {

        $api_response = $this->getExportResultApi();
        $api_response_data = $api_response->getData();
        $export_result = $api_response_data->data->export_result;
        $heading_name = Practice::getPracticeName();
        $total_charge_count = 0;
        $total_adj = 0;
        $patient_total = 0;
        $insurance_total = 0;
        foreach ($export_result as $list) {
            $data['created_at'] = date("m/d/Y", strtotime($list->created_at));
            $data['total_charge'] = $list->claim->total_charge;
            $data['payment_type'] = $list->payment_type;
            $data['total_adjusted'] = $list->total_adjusted;
            $data['patient_paid_amt'] = $list->patient_paid_amt;
            $data['insurance_paid_amt'] = $list->insurance_paid_amt;
            $get_list[$total_charge_count] = $data;
            $total_charge_count++;
            $total_adj = $total_adj + $list->total_adjusted;
            $patient_total = $patient_total + $list->patient_paid_amt;
            $insurance_total = $insurance_total + $list->insurance_paid_amt;
        }


        $get_result = $get_list;

        $get_result[$total_charge_count] = ['created_at' => '', 'total_charge' => '', 'payment_type' => '', 'total_adjusted' => '', 'patient_paid_amt' => '', 'insurance_paid_amt' => ''];

        $total_charge_count = $total_charge_count + 1;

        $get_result[$total_charge_count] = ['created_at' => '', 'total_charge' => '', 'payment_type' => '', 'total_adjusted' => '', 'patient_paid_amt' => '', 'insurance_paid_amt' => ''];

        $total_charge_count = $total_charge_count + 1;
        $pratices_name = "Practice " . $heading_name . ' Total :';
        $get_result[$total_charge_count] = ['created_at' => '', 'total_charge' => '', 'payment_type' => $pratices_name, 'total_adjusted' => Helpers::priceFormat($total_adj, 'no'), 'patient_paid_amt' => Helpers::priceFormat($patient_total, 'no'), 'insurance_paid_amt' => Helpers::priceFormat($insurance_total, 'no')];

        $total_charge_count = $total_charge_count + 1;

        $get_result[$total_charge_count] = ['created_at' => '', 'total_charge' => '', 'payment_type' => 'Grand Total : ', 'total_adjusted' => Helpers::priceFormat($total_adj, 'no'), 'patient_paid_amt' => Helpers::priceFormat($patient_total, 'no'), 'insurance_paid_amt' => Helpers::priceFormat($insurance_total, 'no')];

        $total_charge_count = $total_charge_count + 1;

        $get_result[$total_charge_count] = ['created_at' => '', 'total_charge' => '', 'payment_type' => '', 'total_adjusted' => '', 'patient_paid_amt' => '', 'insurance_paid_amt' => ''];

        $result["value"] = json_decode(json_encode($get_result));
        $result["exportparam"] = array(
            'filename' => 'End of the Day Total Reports',
            'heading' => '',
            'fields' => array(
                'created_at' => 'Date',
                'total_charge' => 'Charges($)',
                'payment_type' => 'Payments',
                'total_adjusted' => 'Adjustments($)',
                'patient_paid_amt' => 'Patient Payments($)',
                'insurance_paid_amt' => 'Insurance Payments($)',
        ));

        $callexport = new CommonExportApiController();
        return $callexport->generatemultipleExports($result["exportparam"], $result["value"], $export);
    }

    ### Aging Report module start ###
    /**
     * Display a listing of the dropdown.
     *
     * @return Response
     */

    public function aginganalysislist() {
        $api_response = $this->getAgingReportApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        //$selected_tab = "financial-report";
        $selected_tab = "ar-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/financials/aginganalysisdetails/aginganalysis', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'adjustment_reason', 'searchUserData', 'search_fields'));
    }

    /**
     * Search base on aging.
     *
     * @return Response
     */
    public function aginganalysissearch() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getAgingReportSearchApiSP(); // Stored procedure
        } else {
            $api_response = $this->getAgingReportSearchApi(); // DB
        }
        
        $api_response_data = $api_response->getData();
        $aging_report_list = $api_response_data->data->aging_report_list;
        $show_flag = $api_response_data->data->show_flag;
        //$pagination_prt = $api_response_data->data->pagination_prt;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $group_by = $api_response_data->data->group_by;
        $search_by = $api_response_data->data->search_by;
        $search_lable = $api_response_data->data->search_lable;
        $summaries = $api_response_data->data->summaries;


        return view('reports/financials/aginganalysisdetails/normalreport', compact('aging_report_list', 'end_date', 'start_date', 'show_flag', 'pagination', 'pagination_prt', 'group_by', 'search_by','search_lable', 'summaries'));
    }

    /*     * * Blade - export aging analysis ** */

    public function agingDetailsReportExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getAgingReportSearchApiSP($export, $data); // Stored procedure
        } else {
            $api_response = $this->getAgingReportSearchApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $aging_report_list = $api_response_data->data->aging_report_list;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $show_flag = $api_response_data->data->show_flag;
        $group_by = $api_response_data->data->group_by;
        $search_lable = $api_response_data->data->search_lable;
        $search_by = $api_response_data->data->search_by;
        $summaries = $api_response_data->data->summaries;
        $date = date('m-d-Y');
        $name = 'Aging_Analysis_Detailed_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/aginganalysisdetails/agingdetailsexport_pdf';
            $report_name = "Aging Analysis Detailed";
            $data = ['aging_report_list' => $aging_report_list, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name,'start_date' => $start_date, 'end_date' => $end_date,'export' => $export, 'show_flag' => $show_flag,'group_by' => $group_by, 'search_lable' => $search_lable,'search_by' => $search_by, 'summaries' => $summaries];
            return $data;
        }
        
        if ($export == 'xlsx' || $export == 'csv') {
            $data = [];
            $data['aging_report_list'] = $aging_report_list;
            $data['group_by'] = $group_by;
            $data['show_flag'] = $show_flag;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_lable'] = $search_lable;
            $data['search_by'] = $search_by;
            $data['summaries'] = $summaries;
            $data['file_path'] = 'reports/financials/aginganalysisdetails/agingdetailsexport';
            $data['export'] = $export;

            if(Request::ajax()) {
                return Response::json(array("value" => $data));
            } else {
                return $data;
            }
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['report_file_name'=>$name.$type, 'status'=>'Completed']);
        }*/
    }

    ### Agingn report module start ###

  public function generateReportExport(){
    $request = Request::all();
    $request['created_by'] = Auth::user()->id;
        $practice_id =Session::get('practice_dbid');
    $request['parameter'] = $request['parameter'];
    $counts = ReportExportTask::selectRaw('count(report_name) as counts')->where('report_name',$request['report_name'])->groupby('report_name')->where('practice_id',$practice_id)->pluck('counts')->first();
    $request['parameter'] = $request['parameter'];
    $request['report_count'] = (isset($counts) && !empty($counts))?($counts+1):1;
    $request['practice_id'] = Session::get('practice_dbid');
    ReportExportTask::create((array)$request);
  }

  public function notify_generateReportExport(){
    $user_type = Auth::user()->user_type;
    if($user_type != 'Medcubics') {
      $reportDetails = Helpers::getReportNotification();
      $reportNotification = $reportDetails['ExportInfo'];
      $pendingExportCount = $reportDetails['pendingExportCount'];
      return view('layouts/generate_export_notification', compact('reportDetails','reportNotification','pendingExportCount'));
    }
  }

  public function showGenerateReport(){
    $pratice_id = Session::get('practice_dbid');
    $completedReport = ReportExportTask::where('status','Completed')->where('practice_id',$pratice_id)->get()->toArray();
    return view('reports/show', compact('completedReport'));
  }

  public function exportDownload($id){
    $completedReport = ReportExportTask::where('id',$id)->get()->first();
    return Helpers::downloadExportFile($completedReport['report_file_name']);
  }

  public function getParameter($id){
    $parameterInfo = array();
    $exportInfo = ReportExportTask::where('id',$id)->get()->first();
    $parameterData = explode('&', $exportInfo->parameter);
    if(isset($exportInfo->parameter) && !empty($exportInfo->parameter)){
      foreach($parameterData as $data){
        $tempData = rtrim($data, '~');
        $tempData = str_replace('~',' | ',$tempData);
        $tempData = str_replace('_',' ',$tempData);
        $tempData = str_replace('[]',' ',$tempData);
        $tempData = explode('=',$tempData);
        if(strtolower($tempData[0]) == 'select transaction date') {
              $tempData[0] = 'Transaction Date';
        } elseif(strtolower($tempData[0]) == 'select date of service') {
            $tempData[0] = 'DOS';
        }
        $tempData[0] = ($tempData[0] == 'responsibility') ? 'Insurance': $tempData[0];

        if($exportInfo->report_name == 'Denial Trend Analysis') {
            if(strtolower($tempData[0]) == 'exclude zero ar') {
                $tempData[0] = '$0 Line Item';
                //$tempData[1] = $tempData[1]; //($tempData[1] == 'Include') ? 'Contains $0 Line Item' : 'Remove $0 Line Item';
            } elseif(strtolower($tempData[0]) == 'created at') {
                $tempData[0] = 'Denied Date';
            }
        }
        $parameterInfo[str_replace(' id', '', $tempData[0])] = @$tempData[1];
      }
    }
    return Response::view('reports/generated_reports/parameterPopup',compact('parameterInfo'));
  }

  public function exportDelete($id){
    $report = ReportExportTask::where('id', $id);
    $reportInfo = $report->get()->first();

    $gcs_file = $reportInfo->practice_id.DS.'reports'.DS.$reportInfo->created_by.DS.
                            date('my', strtotime($reportInfo->created_at)).DS.$reportInfo->report_file_name;          

    if(isset($reportInfo->report_file_name)) {
        if(Storage::disk('local')->exists($reportInfo->report_file_name))
            Storage::disk('local')->delete($reportInfo->report_file_name);
        if(Storage::disk('gcs')->exists($gcs_file)) { \Log::info("Exists in GCS".$gcs_file);
            Storage::disk('gcs')->delete($gcs_file);
        }
    }
    $report->update(['deleted_at'=>Carbon::now()]);
  }

    ### Work RUV Report module start ###
    /**
     * Display a listing of the dropdown.
     *
     * @return Response
     */
    public function workrvulist() {
        $api_response = $this->getWorkrvuReportApi();
        $api_response_data = $api_response->getData();
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        //$selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $selected_tab = "financial-report";
        return view('reports/financials/workrvu/workrvu', compact('searchUserData', 'search_fields','selected_tab','heading','heading_icon'));
    }

    public function workrvureport() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getWorkrvulistApiSP(); // Stored procedure
        } else {
            $api_response = $this->getWorkrvulistApi(); // DB table searching
        }
        $api_response_data = $api_response->getData();
        $workrvu_list = $api_response_data->data->workrvu_list;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        // $pagination_prt = $api_response_data->data->pagination_prt;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];

        return view('reports/financials/workrvu/normalreport', compact('workrvu_list','search_by','pagination','pagination_prt'));
    }

    public function workrvusearchExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getWorkrvulistApiSP($export, $data);// Stored procedure
        } else {
            $api_response = $this->getWorkrvulistApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $workrvu_list = $api_response_data->data->workrvu_list;
        // $workrvu_list_data = $api_response_data->data->workrvu_list_data;
        $search_by = $api_response_data->data->search_by;
        $date = date('m-d-Y');
        // $name = 'Work_RVU_Report_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/workrvu/lineitemexport_pdf';
            $report_name = "Work RVU Report";
            $data = ['workrvu_list' => $workrvu_list, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
            return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/workrvu/lineitemexport';
            $data['workrvu_list'] = $workrvu_list;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['export'] = $export;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['report_file_name'=>$name.$type, 'status'=>'Completed']);
        }*/
    }


    /* AR workbench report start */

    public function workbenchList() {
        $api_response = $this->getWorkbenchReportApi();
        $api_response_data = $api_response->getData();
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        //$selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $selected_tab = "ar-report";

        return view('reports/financials/workbench/workbench', compact('searchUserData', 'search_fields','selected_tab','heading','heading_icon'));
    }

    public function workbenchReport() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getWorkbenchListApiSP(); // Stored Procedure
        } else {
            $api_response = $this->getWorkbenchListApi(); // DB
        }        
        $api_response_data = $api_response->getData();
        $workbench_list = $api_response_data->data->workbench_list;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        //$pagination_prt = $api_response_data->data->pagination_prt;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];

        return view('reports/financials/workbench/normalreport', compact('workbench_list','search_by','pagination','pagination_prt'));
    }

    public function workbenchSearchExport($export = '',$data = '') {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getWorkbenchListApiSP($export, $data); // Stored Procedure
        } else {
            $api_response = $this->getWorkbenchListApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $workbench_list = $api_response_data->data->workbench_list;
        $search_by = $api_response_data->data->search_by;
        $date = date('m-d-Y');
        $name = 'AR_Workbench_Report_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        
        //Stream export pdf
        $request = request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/workbench/lineitemexport_pdf';
            $report_name = "AR Workbench Report";
            $data = ['workbench_list' => $workbench_list, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'export' => $export, 'search_by' => $search_by];
            return $data;
        }
                
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/workbench/lineitemexport';
            $data['workbench_list'] = $workbench_list;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }

        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['report_file_name'=>$name.$type, 'status'=>'Completed']);
        }*/
    }

    /* AR workbench report end */
     ### Charge Category report  Start  Thilagavathy ###
    public function chargecategory() {
        $api_response = $this->getchargecategoryReportApi();
        $api_response_data = $api_response->getData();
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        //$selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $selected_tab = "financial-report";
        return view('reports/financials/chargecategory/chargecategory', compact('searchUserData', 'search_fields','selected_tab','heading','heading_icon'));
    }
    public function chargecategoryreport() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getchargecategoryresultApiSP();// Stored procedure
        } else {
            $api_response = $this->getchargecategoryresultApi(); // DB table searching
        }
        $api_response_data = $api_response->getData();
        $charges_list = $api_response_data->data->charges_list;
        $charges_lists = $api_response_data->data->charges_lists;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        // $pagination_prt = $api_response_data->data->pagination_prt;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $temp_array = $api_response_data->data->temp_array;
        $total_arr = $api_response_data->data->total_arr;
      return view('reports/financials/chargecategory/normalreport', compact('charges_list', 'search_by','pagination','pagination_prt','total_arr','charges_lists','temp_array'));
    }

    public function chargecategorysearchExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getchargecategoryresultApiSP($export, $data);// Stored procedure
        } else {
            $api_response = $this->getchargecategoryresultApi($export, $data); // DB table searching
        }
        $api_response_data = $api_response->getData();
        $charges_list = $api_response_data->data->export_array;
        $search_by = $api_response_data->data->search_by;
        $total_arr = $api_response_data->data->total_arr;
        $charges_list_count = count((array)$charges_list)+10;
        $total_count_f2 = "F".$charges_list_count;
        $total_count_g2 = "G".$charges_list_count;
        $date = date('m-d-Y');
        // $name = 'Charge_Category_Report_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/chargecategory/lineitemexport_pdf';
            $report_name = "Charge Category Report";
            $data = ['charges_list' => $charges_list, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'total_arr' => $total_arr, 'charges_list_count' => $charges_list_count, 'total_count_f2' => $total_count_f2, 'total_count_g2' => $total_count_g2, 'search_by' => $search_by];
            return $data;
        } 
        
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/chargecategory/lineitemexport';
            $data['total_count_f2'] = $total_count_f2;
            $data['total_count_g2'] = $total_count_g2;
            $data['charges_list'] = $charges_list;
            $data['total_arr'] = $total_arr;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['export'] = $export;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array("value" => $data));
            } else {
                return $data;
            }
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['report_file_name'=>$name.$type, 'status'=>'Completed']);
        }*/

    }
    ### Charge Category report  End  Thilagavathy ###

    /* Denial trend analysis report start */

    public function denialAnalysisList() {
        $api_response = $this->getDenialAnalysisReportApi();
        $api_response_data = $api_response->getData();
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $selected_tab = "ar-report";

        return view('reports/financials/denialanalysis/denialanalysis', compact('searchUserData', 'search_fields','selected_tab','heading','heading_icon'));
    }

    public function denialAnalysisReport() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getDenialAnalysisListApiSP(); // Stored Procedure
        } else {
            $api_response = $this->getDenialAnalysisListApi(); // DB
        }        
        $api_response_data = $api_response->getData();
        $denial_cpt_list = $api_response_data->data->denial_cpt_list;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        //$pagination_prt = $api_response_data->data->pagination_prt;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $workbench_status = $api_response_data->data->workbench_status;

        return view('reports/financials/denialanalysis/normalreport', compact('denial_cpt_list','search_by','pagination','pagination_prt','workbench_status'));
    }

    public function denialAnalysisSearchExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getDenialAnalysisListApiSP($export, $data); // Stored Procedure
        } else {
            $api_response = $this->getDenialAnalysisListApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $denial_cpt_list = $api_response_data->data->export_array;
        $workbench_status = $api_response_data->data->workbench_status;
        $search_by = $api_response_data->data->search_by;
        $date = date('m-d-Y');
        // $name = 'Denial_Trend_Analysis_Report_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/denialanalysis/lineitemexport_pdf';
            $report_name = "Denial Trend Analysis";
            $data = ['denial_cpt_list' => $denial_cpt_list, 'workbench_status'=> $workbench_status ,'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
            return $data;
        }
        
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/denialanalysis/lineitemexport';
            $data['denial_cpt_list'] = $denial_cpt_list;
            $data['workbench_status'] = $workbench_status;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }

            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['report_file_name'=>$name.$type, 'status'=>'Completed']);
        }*/
    }

    /* Denial trend analysis report end */

}