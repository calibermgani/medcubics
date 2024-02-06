<?php

namespace App\Http\Controllers\Reports;

use View;
use Config;
use PDF;
use Excel;
use Request;
use Session;
use Response;
use Redirect;
use Url;
use Auth;
use App\Models\ReportExport as ReportExportTask;
use App\Models\Medcubics\Users as Users;
use DB;
use App\Exports\BladeExport;

class ReportController extends Api\ReportApiController {

    public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'reports');
        View::share('heading_icon', 'barchart');
        /* if(Request::segment(3) != 'list'){            
          echo $this->under_const();
          exit;
          } */
    }

    public function index() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        return view('reports/reports/reports');
    }

    /*     * * Adjustment report module start ** */

    public function adjustmentreport() {
        $currentURL = Request::url();
        $url = explode("/", $currentURL);
        $reportName = $url[count($url) - 1];
        $api_response = $this->getAdjustmentApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $adj_reason_ins = $api_response_data->data->adj_reason_ins;
        $adj_reason_patient = $api_response_data->data->adj_reason_patient;
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        //$selected_tab = "financial-report";
        $selected_tab = "collection-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $report_data = Session::get('report_data');
        return view('reports/financials/adjustment/adjustment', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'adj_reason_ins', 'adj_reason_patient', 'report_data', 'search_fields', 'searchUserData'));
    }

    public function adjustmentSearch() {
        set_time_limit(0);
        print(str_repeat(" ", 300) . "\n");
        flush(); // Flush all output to make sure
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  { 
            $api_response = $this->getAdjustmentsearchApiSP();//stored procedure export only
        } else {
            $api_response = $this->getAdjustmentsearchApi();
        }
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $adjustment = $api_response_data->data->adjustment;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $sdate = $api_response_data->data->startdate;
        $tdate = $api_response_data->data->todte;
        $instype = $api_response_data->data->instype;
        $adjustment_id = $api_response_data->data->adjustment_id;
        $tot_adjs = $api_response_data->data->tot_adjs;
        $export_array = isset($api_response_data->data->export_array) ? $api_response_data->data->export_array : [];
     
        return view('reports/financials/adjustment/lineitemreport', compact('adjustment', 'pagination', 'header', 'sdate', 'tdate', 'instype', 'adjustment_id', 'Adj_reason','Adj_reason_flds','export_array','tot_adjs'));
    }

    public function setReportSessionData() {
        $request = Request::all();
        $from = 'Adjustment_Analysis'; //$request['from'];
        unset($request['_token']);
        Session::set($from, $request);
        return Response::json(array('status' => 'success'));
    }

    /*     * * Blade - Export adjustment analysis ** */
    public function adjustmentSearchexport($export = '',$data = '') {
        set_time_limit(0);
        print(str_repeat(" ", 300) . "\n");
        flush(); // Flush all output to make sure
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  { 
            $api_response = $this->getAdjustmentsearchApiSP($export, $data);//stored procedure export only
        } else {
            $api_response = $this->getAdjustmentsearchApi($export, $data);
        }
        $api_response_data = $api_response->getData();
        @$sdate = $api_response_data->data->startdate;
        @$tdate = $api_response_data->data->todte;
        $instype = $api_response_data->data->instype;  
        $adjustment = $api_response_data->data->adjustment;     
        $search_by = $api_response_data->data->header;     
        $tot_adjs = $api_response_data->data->tot_adjs;   
        $cnt = (array)$tot_adjs;
        $pat = (isset($cnt['Patient']))?count(array_filter($cnt['Patient'])):0;
        $ins = (isset($cnt['Insurance']))?count(array_filter($cnt['Insurance'])):0;
        $adjRowCount1 = $pat+$ins+9;
        $adjRowCount2 = $pat+$ins+11;
        $adjRowCountVar = "C".$adjRowCount1.":"."C".$adjRowCount2;
        $date = date('m-d-Y');
        $name = @$data['export_id'].'X0X'.'Adjustment_Analysis_Reports_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        //Stream export
        $request = request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/adjustment/lineitemexport_pdf';
            $report_name = "Adjustment Analysis Report";
            $data = ['adjustment' => $adjustment, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'sdate' => $sdate, 'tdate' => $tdate, 'instype' => $instype, 'export' => $export, 'search_by' => $search_by, 'tot_adjs' => $tot_adjs];
            return $data;
        }
        
        if ($export == 'xlsx') {
            $export_type = @$data['type'];
            $data = [];
            $adjustments = $adjustment;
            $data['adjustment'] = $adjustments;
            $data['sdate'] = $sdate;
            $data['tdate'] = $tdate;
            $data['instype'] = $instype;
            $data['export'] = $export;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['tot_adjs'] = $tot_adjs;
            $data['file_path'] = 'reports/financials/adjustment/lineitemexport';

            if($export_type == "js-raw") {
                if(Request::ajax()) {
                    $j = 0;
                    $value = [];
                    foreach(@$adjustments as $k => $adjustment) {
                        $cnt = 0;
                        $patient_name =     @$adjustment->title.' '. \App\Http\Helpers\Helpers::getNameformat(@$adjustment->last_name,@$adjustment->first_name,@$adjustment->middle_name);
                        if(!empty($adjustment->cpt)) {
                            foreach($adjustment->cpt as $keys =>$cpt) {
                                if(!empty($cpt->adj_reason)) {
                                    $i=0;
                                    foreach(array_flatten(json_decode(json_encode($cpt->adj_reason),true)) as $key=>$adj) {
                                        $cpt->payer = array_flatten(json_decode(json_encode($cpt->payer),true));
                                        $cpt->adj_date = array_flatten(json_decode(json_encode($cpt->adj_date),true));
                                        $cpt->adj_amt = array_flatten(json_decode(json_encode($cpt->adj_amt),true));
                                        $cpt->reference = array_flatten(json_decode(json_encode($cpt->reference),true));
                                        $l = 0;
                                        $value[$j][$i][$l++] = $adjustment->claim_number;
                                        $value[$j][$i][$l++] = $patient_name;
                                        $value[$j][$i][$l++] = $adjustment->account_no;
                                        $value[$j][$i][$l++] = ($adjustment->self_pay =='Yes') ? "Patient" : \App\Models\Insurance::where('id', @$adjustment->insurance_id)->value("insurance_name");
                                        $value[$j][$i][$l++] = $adjustment->billing_provider_name;
                                        $value[$j][$i][$l++] = $adjustment->rendering_provider_name;
                                        $value[$j][$i][$l++] = str_limit($adjustment->facility_name);
                                        $value[$j][$i][$l++] = $cpt->payer[$key];
                                        $value[$j][$i][$l++] = $cpt->adj_date[$key];
                                        $value[$j][$i][$l++] = \App\Http\Helpers\Helpers::dateFormat($cpt->dos_from,'dob');
                                        $value[$j][$i][$l++] = @$cpt->cpt_code;
                                        $value[$j][$i][$l++] = $adj;
                                        $value[$j][$i][$l++] = (int)$cpt->adj_amt[$key];
                                        if($i == 0) {
                                            $value[$j][$i][$l++] = array_sum(array_flatten(json_decode(json_encode($adjustment->tot_adj), true)[$k][$keys]));
                                        }
                                        $value[$j][$i][$l++] = @$cpt->reference[$key];
                                        $value[$j][$i][$l++] = \App\Http\Helpers\Helpers::user_names($adjustment->created_by);
                                        // dd($value);
                                        if($i == 0) {
                                            $value[$j][$i][$l++] = count(array_flatten(json_decode(json_encode($cpt->adj_reason),true)));
                                        }                                                      
        
                                        $i++;
                                    }
                                }
                            }
                        }
                        $j++;
                    }
        
                    $sum_tot_adjs_ins = array_sum(array_flatten((array)@$tot_adjs->Insurance));
                    $sum_tot_adjs_pat = array_sum(array_flatten((array)@$tot_adjs->Patient));
                    $sum_tot_adjs = $sum_tot_adjs_ins + $sum_tot_adjs_pat;
                    
                    return Response::json(array('value' => $value, 'instype' => $instype, 'sum_tot_adjs_ins' => $sum_tot_adjs_ins, 'sum_tot_adjs_pat' => $sum_tot_adjs_pat, 'sum_tot_adjs' => $sum_tot_adjs));
                } else {
                    return $data;
                }
            } else {
                return $data;
            }
            $type = '.xls';
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($adjustment, $sdate, $tdate, $instype, $export, $createdBy, $practice_id, $search_by, $tot_adjs) {
                $excel->sheet('Excel', function($sheet) use ($adjustment, $sdate, $tdate, $instype, $export, $createdBy, $practice_id, $search_by, $tot_adjs) {
                    $sheet->loadView('reports/financials/adjustment/lineitemexport')->with("adjustment", $adjustment)->with("sdate", $sdate)->with("tdate", $tdate)->with("instype", $instype)->with("export", $export)->with('createdBy', $createdBy)->with('practice_id', $practice_id)->with('search_by', $search_by)->with('tot_adjs', $tot_adjs);
                });
            })->export('csv');
            $type = '.csv';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }

    /*     * * Adjustment report module end ** */
 /*     *** PROCEDURE REPORT LIST Start *** */
    public function procedurelist() {
        $currentURL = Request::url();
        $url = explode("/", $currentURL);
        $reportName = $url[count($url) - 1];
        $api_response = $this->getprocedurelistApi();
        $api_response_data = $api_response->getData();      
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        //$selected_tab = "financial-report";
        $selected_tab = "collection-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $report_data = Session::get('report_data');
        return view('reports/financials/procedure/procedure', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData'));
    }

    public function proceduresearch() {         
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getproceduresearchApiSP(); // Stored procedure
        } else {
            $api_response = $this->getproceduresearchApi(); // DB
        }
        $api_response_data = $api_response->getData();
      
        $cptreport_list = $api_response_data->data->cptreport_list;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];    
        return view('reports/financials/procedure/normalreport', compact('cptreport_list','search_by', 'pagination','pagination_prt'));
    }

    public function proceduresearchExport($export = '',$data = '') {
         
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getproceduresearchApiSP($export, $data); // Stored procedure
        } else {
            $api_response = $this->getproceduresearchApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $cptreport_list = $api_response_data->data->cptreport_list; 
        $search_by = $api_response_data->data->search_by;
        /*$cptreport_list_count = count((array)$cptreport_list)+6;
        $cptreport_list_count_total = "K".$cptreport_list_count;*/
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        // $name = 'Procedure_Collection_Report_Insurance_Only_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : ''; 

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/procedure/lineitemexport_pdf';
            $report_name = "Procedure Collection Report - Insurance Only";
            $data = ['cptreport_list' => $cptreport_list, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
            return $data;
        }   

        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/procedure/lineitemexport';
            //$data['cptreport_list_count_total'] = $cptreport_list_count_total;
            $data['cptreport_list'] = $cptreport_list;
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
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }
    /*     *** PROCEDURE REPORT LIST End *** */

    /*     * * Aging Analysis Report start ** */

    public function billingreport() {

        $selected_tab = "billing-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/billing/list', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'adjustment_reason'));
    }

    public function edisreport() {

        $selected_tab = "edi-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/edi/list', compact('selected_tab', 'heading', 'heading_icon'));
    }

    public function patientsreport() {

        $selected_tab = "patients-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/patients/list', compact('selected_tab', 'heading', 'heading_icon'));
    }

    public function managementreport() {

        $selected_tab = "management-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/management/list', compact('selected_tab', 'heading', 'heading_icon'));
    }

    public function practicereport() {

        $selected_tab = "practice-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/practicesettings/list', compact('selected_tab', 'heading', 'heading_icon'));
    }
    
    public function arreport() {

        $selected_tab = "ar-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/ar/list', compact('selected_tab', 'heading', 'heading_icon'));
    }

    public function aginganalysislist() {
        $api_response = $this->getAgingReportApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $selected_tab = "ar-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        return view('reports/financials/aginganalysis/aginganalysis', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'adjustment_reason', 'search_fields','searchUserData'));
    }

    public function aginganalysissearch() {
        $api_response = $this->getAgingReportSearchApi();
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $headers = $api_response_data->data->headers;
        $aging_report_list = $api_response_data->data->aging_report_list;
        $title = $api_response_data->data->title;

        return view('reports/financials/aginganalysis/normalreport', compact('aging_report_list', 'title', 'header', 'headers'));
    }

    /*     * * Blade - Export Aging Summary ** */

    public function getAgingReportSearchExport($export = '',$data='') {
        $api_response = $this->getAgingReportSearchApi($export,$data);
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $headers = $api_response_data->data->headers;
        $aging_report_list = $api_response_data->data->aging_report_list;
        $title = $api_response_data->data->title;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Aging_Summary_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/aginganalysis/agingreportexport_pdf';
            $report_name = "Aging Summary";
            $data = ['aging_report_list' => $aging_report_list, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name,'title' => $title, 'header' => $header, 'headers' => $headers];
            return $data;
        }
        
        if ($export == 'xlsx' || $export == 'csv') {            
            $filePath = 'reports/financials/aginganalysis/agingreportexport';
            $data['search_by'] = $headers;
            $data['header'] = $header;
            $data['aging_report_list'] = $aging_report_list;
            $data['title'] = $title;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            $type = '.xls';
        }
        /*if(isset($data['export_id']))
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);*/
    }
    /*     * * Aging Analysis Report View end ** */

    /*     * * Appointment report module start ** */
    public function schedulingreport() {

        $selected_tab = "appointment-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/scheduling/list', compact('selected_tab', 'heading', 'heading_icon'));
    }

    public function financialsreport() {

        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/financials/list', compact('selected_tab', 'heading', 'heading_icon'));
    }
    
    public function collectionsreport() {

        $selected_tab = "collection-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/collections/list', compact('selected_tab', 'heading', 'heading_icon'));
    }

    public function appointmentreport() {
        $api_response = $this->getAppointmentReportApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $adjustment_reason = $api_response_data->data->adjustment_reason;
        $selected_tab = "appointment-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";

        return view('reports/scheduling/appointment/appointment', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'adjustment_reason'));
    }

    public function appointmentSearch() {
        $api_response = $this->getAppointmentSearchApi();
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $appointment = $api_response_data->data->appointment_list;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        return view('reports/scheduling/appointment/normalreport', compact('appointment', 'header', 'column', 'pagination'));
    }

    /*     * *Blade - Export appointment analysis ** */

    public function appointmentSearchExport($export = '') {
        $api_response = $this->getAppointmentSearchApi($export);
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $appointment = $api_response_data->data->appointment_list;
        $date = date('m-d-Y_hia');
        $name = 'Appointment_Analysis_Reports_' . $date;

        if ($export == 'pdf') {
            $html = view('reports/scheduling/appointment/reportexport', compact('appointment', 'header', 'column', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            Excel::create($name, function($excel) use ($header, $column, $appointment, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($header, $column, $appointment, $export) {
                    $sheet->loadView('reports/scheduling/appointment/reportexport')->with("header", $header)->with("column", $column)->with("appointment", $appointment)->with("export", $export);
                });
            })->export('xls');
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($header, $column, $appointment, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($header, $column, $appointment, $export) {
                    $sheet->loadView('reports/scheduling/appointment/reportexport')->with("header", $header)->with("column", $column)->with("export", $export);
                });
            })->export('csv');
        }
    }
    /*     * * Appointment report module end ** */


    /*     * * Charges report module start ** */
    public function chargelist() {
        $api_response = $this->getChargesApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/financials/charges/chargereports', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields','searchUserData'));
    }
    

    // Author: Baskar
    // Charges & Payments summary report page
    public function chargesPaymentslist() {
        $api_response = $this->getChargesPaymentsApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/financials/chargesPayments/chargereports', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields','searchUserData'));
    }

    public function chargesearch() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getChargesearchApiSP(); // Stored procedure
        } else {
            $api_response = $this->getChargesearchApi(); // DB
        }
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $page = $api_response_data->data->page;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $claims = $api_response_data->data->claims;
        $include_cpt_option = $api_response_data->data->include_cpt_option;
        $status_option = $api_response_data->data->status_opt;
        $ftdate = $api_response_data->data->tdate;
        $charge_date_opt = $api_response_data->data->chrg_date_opt;
        $tot_summary = $api_response_data->data->tot_summary;

        $sinpage_charge_amount = $api_response_data->data->sinpage_charge_amount;
        $sinpage_claim_arr = $api_response_data->data->sinpage_claim_arr;
        $sinpage_total_cpt = $api_response_data->data->sinpage_total_cpt;
        $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
        return view('reports/financials/charges/' . $page, compact('claims', 'header', 'column', 'pagination', 'include_cpt_option', 'sinpage_charge_amount', 'sinpage_claim_arr', 'sinpage_total_cpt', 'status_option', 'ftdate', 'charge_date_opt', 'tot_summary','user_names'));
    }

    /*     * * Blade - Export Charge Reports ** */

    public function chargesearchexport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getChargesearchApiSP($export, $data); // Stored procedure
            if ($export == 'pdf') {
                $page = "lineitemexport_pdf_SP";
            }else{
                $page = "lineitemexport_SP";
            }
        } else {
            $api_response = $this->getChargesearchApiSP($export, $data); // DB
            if ($export == 'pdf') {
                $page = "lineitemexport_pdf";
            }else{
                $page = "lineitemexport_SP";
            }
        }
        
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $claims = $api_response_data->data->claims;
        $include_cpt_option = $api_response_data->data->include_cpt_option;
        $status_option = $api_response_data->data->status_opt;
        $ftdate = $api_response_data->data->tdate;
        $charge_date_opt = $api_response_data->data->chrg_date_opt;
        $sinpage_charge_amount = $api_response_data->data->sinpage_charge_amount;
        $sinpage_claim_arr = $api_response_data->data->sinpage_claim_arr;
        $sinpage_total_cpt = $api_response_data->data->sinpage_total_cpt;
        $tot_summary = $api_response_data->data->tot_summary;
        $date = date('m-d-Y');
        // $name = @$data['export_id'].'X0X'.'Charge_Analysis_Detailed_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        // $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
        $user_full_names =  Users::where('status', 'Active')->pluck('name', 'id')->all();
        $totalRec = 0;//dd($claims);
//        foreach($claims as $claim) {
//            foreach($claim->cpttransactiondetails as $claim_cpt){
//                $totalRec += (!empty($claim_cpt)) ? count((array)$claim_cpt) : 0;
//            }
//        }
        $claimCptRowCount = $totalRec;
        $claimCptRowCount1 = $claimCptRowCount+11;
        $claimCptRowCount2 = $claimCptRowCount+15;
        $claimCptRowCountVar = "C".$claimCptRowCount1.":"."C".$claimCptRowCount2;
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == "pdf") {
            $view_path = 'reports/financials/charges/lineitemexport_pdf';
            $report_name = "Charge Analysis Detailed";
            $data = ['claims' => $claims, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'include_cpt_option' => $include_cpt_option, 'column' => $column, 'status_option' => $status_option, 'header' => $header,'ftdate' => $ftdate, 'charge_date_opt' => $charge_date_opt, 'sinpage_charge_amount' => $sinpage_charge_amount, 'sinpage_claim_arr' => $sinpage_claim_arr, 'sinpage_total_cpt' => $sinpage_total_cpt, 'tot_summary' => $tot_summary, 'user_names' => $user_names, 'claimCptRowCount' => $claimCptRowCount, 'claimCptRowCount1' => $claimCptRowCount1, 'claimCptRowCount2' => $claimCptRowCount2, 'claimCptRowCountVar' => $claimCptRowCountVar];
            return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/charges/' . $page;
            $data['claims'] = $claims;
            $data['search_by'] = $header;
            $data['column'] = $column;
            $data['include_cpt_option'] = $include_cpt_option;
            $data['sinpage_charge_amount'] = $sinpage_charge_amount;
            $data['sinpage_claim_arr'] = $sinpage_claim_arr;
            $data['sinpage_total_cpt'] = $sinpage_total_cpt;
            $data['status_option'] = $status_option;
            $data['ftdate'] = $ftdate;
            $data['charge_date_opt'] = $charge_date_opt;
            $data['tot_summary'] = $tot_summary;
            // $data['user_names'] = $user_names;
            $data['user_full_names'] = $user_full_names;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['page'] = $page;
            $data['export'] = $export;
            $data['file_path'] = $filePath;

            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {
                return $data;
            }
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }

    /*     * * Charges report module end ** */

    // ------------------------------- Charges & Payments summary reports Start -------------------------------
    // Author: Baskar
    public function chargepaymentsearch($export = '',$data = '') {
        if(isset($data) && !empty($data))
            $request = $data;
        else
            $request = Request::All();
        $practice_timezone = \App\Http\Helpers\Helpers::getPracticeTimeZone();
        if(!isset($request['insurance_type']))
            $request['insurance_type'] = 'all';
        // Charges Summary Start
            $api_response = $this->getChargeResult($request);
            $charge_summary = $api_response['claims']->selectRaw('billing_provider_id,sum(total_charge) as total_charge')->where('billing_provider_id','<>',0)
                            ->groupby('billing_provider_id')->orderBy('id','asc')->get();
            $header = $api_response['header'];
            $column = $api_response['column'];
            // Charges Separate into provider wise
            if(!empty($charge_summary) && count($charge_summary)>0)
                foreach($charge_summary as $charge){
                    $provider_name = str_replace(',','', @$charge->billing_provider->provider_name);
                    $provider_name = str_replace(' ','_',($provider_name));
                    $charges[$provider_name] = $charge->total_charge;
                }
            else
                $charges = [];
        // Payments Summary Start
            if(!isset($request['insurance_charge']))
                $request['insurance_charge'] = 'all';
            $api_response_insurance = $this->getPaymentResult($request);
            $api_response_data=$api_response_insurance['payments']->orderBy('created_at','desc')->get();
            $payment = [];
            // Payments Separate into provider wise for insurance only
            if($api_response_data)
                foreach($api_response_data as $pmt){
                    $provider_name = str_replace(',','',$pmt['claim']['billing_provider']['provider_name']);
                    $provider_name = str_replace(' ','_',($provider_name));
                    $payment[$provider_name]['billing_provider_id'] = $pmt['claim']['billing_provider_id'];
                    if($pmt['pmt_method']=="Insurance" && $pmt['pmt_type']=="Payment")
                        $payment[$provider_name][$pmt['pmt_method']][] = $pmt['total_paid'];
                }
            //$request['insurance_charge'] = 'all';
            $payerType = $request['insurance_charge'];
            //$api_response_patient = $this->getPaymentResult($request);
            $exp = isset($request['select_transaction_date']) ? explode("-",$request['select_transaction_date']) : "";
            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
                if(isset($request['select_transaction_date']) && $exp != "" && isset($exp[0])) {
                    $start_date = ($exp != "") ? $exp[0] : "";
                    //$start_date = \App\Http\Helpers\Helpers::utcTimezoneStartDate($start_date);  
                    $start_date = date("Y-m-d",strtotime($start_date));
                } else {
                    $start_date = "";
                }
            }
            
            
            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
                if(isset($request['select_transaction_date']) && !empty($request['select_transaction_date']) && $exp != "" && isset($exp[1])) {
                    $end_date = ($exp != "") ? $exp[1] : "";
                    //$end_date = \App\Http\Helpers\Helpers::utcTimezoneEndDate($end_date);
                    $end_date = date("Y-m-d",strtotime($end_date));
                } else {
                    $end_date = "";
                }
            }

            if(isset($request['billing_provider_id'])) {
                $api_response_data = \DB::table('pmt_claim_tx_v1')                                  
                                    ->selectRaw('sum(pmt_claim_tx_v1.total_paid) as pmt_amt, providers.provider_name,pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method')
                                    ->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                                    ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                                    ->leftJoin('providers','providers.id','=','claim_info_v1.billing_provider_id')
                                    ->wherenull('claim_info_v1.deleted_at')
                                    ->wherenull('pmt_claim_tx_v1.deleted_at')
                                    ->where('pmt_claim_tx_v1.pmt_method','Patient')
                                    ->wherenull('providers.deleted_at');
                
                /*              
                $api_response_data=\DB::table('pmt_info_v1')
                                    ->selectRaw('pmt_info_v1.amt_used as pmt_amt,providers.provider_name,pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method')
                                    ->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.payment_id','=','pmt_info_v1.id')
                                    ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                                    ->leftJoin('providers','providers.id','=','claim_info_v1.billing_provider_id')
                                    ->where('pmt_info_v1.pmt_method','Patient');
                */
                if(isset($request['export'])){
                    $api_response_data->whereIn("billing_provider_id", explode(',', $request["billing_provider_id"]));
                } else {
                    $api_response_data->whereIn("billing_provider_id", $request["billing_provider_id"]);
                }
                //$api_response_data->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance']);
            } else {
                /*
                $api_response_data=\DB::table('pmt_info_v1')
                                    ->selectRaw('pmt_info_v1.amt_used as pmt_amt,providers.provider_name,pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method')
                                    ->leftJoin('pmt_claim_tx_v1','pmt_claim_tx_v1.payment_id','=','pmt_info_v1.id')
                                    ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                                    ->leftJoin('providers','providers.id','=','claim_info_v1.billing_provider_id')
                                    ->where('pmt_info_v1.pmt_method','Patient')
                                    ->whereIn('pmt_info_v1.pmt_type', ['Payment','Credit Balance']);
                */
                $api_response_data = \DB::table('pmt_claim_tx_v1')
                                    ->selectRaw('sum(pmt_claim_tx_v1.total_paid) as pmt_amt, providers.provider_name,pmt_info_v1.id,claim_info_v1.billing_provider_id, pmt_info_v1.pmt_method')
                                    ->leftJoin('pmt_info_v1','pmt_info_v1.id','=','pmt_claim_tx_v1.payment_id')
                                    ->leftJoin('claim_info_v1','claim_info_v1.id','=','pmt_claim_tx_v1.claim_id')
                                    ->leftJoin('providers','providers.id','=','claim_info_v1.billing_provider_id')
                                    ->wherenull('claim_info_v1.deleted_at')
                                    ->wherenull('pmt_claim_tx_v1.deleted_at')
                                    ->where('pmt_claim_tx_v1.pmt_method','Patient')
                                    ->wherenull('providers.deleted_at');
            }
            
            if(isset($request['include_refund']) ) {
                if($request['include_refund'] == 'Yes') {
                    $header['Include Refund'] = 'Yes';
                    $api_response_data->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance', 'Refund']);
                } else {
                    $header['Include Refund'] = 'No';
                    $api_response_data->whereIn('pmt_claim_tx_v1.pmt_type', ['Payment','Credit Balance']);
                }
            }
            // Filter by Rendering provider
            if (isset($request['rendering_provider_id'])) {
                if ($request["rendering_provider_id"] != "") {
                    if(isset($request['export'])){
                        $api_response_data->whereIn("rendering_provider_id", explode(',', $request["rendering_provider_id"]));
                    } else {
                        $api_response_data->whereIn("rendering_provider_id", $request["rendering_provider_id"]);
                    }
                }
            }
            
            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date')) {
                if(isset($request['select_transaction_date']) && $start_date != "" && $end_date != "")
                    $api_response_data->whereRaw("DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(pmt_claim_tx_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'");
            }
                    
            $api_response_data = $api_response_data
                                    //->whereNull('pmt_info_v1.void_check')
                                    ->whereNull('pmt_info_v1.deleted_at')
                                    //->groupby('pmt_info_v1.id')
                                    ->groupBy("claim_info_v1.billing_provider_id")                                    
                                    ->get();
            
            
            // Payments Separate into provider wise for patient only
            //dd($api_response_data);
            if($api_response_data)
                foreach($api_response_data as $pmt){
                    if(isset($pmt->provider_name)){
                        $provider_name = str_replace(',','',$pmt->provider_name);
                        $provider_name = str_replace(' ','_',$provider_name);
                    }
                    if(isset($pmt->provider_name)){
                        $payment[$provider_name]['billing_provider_id'] = $pmt->billing_provider_id;
                        $payment[$provider_name][$pmt->pmt_method][] = $pmt->pmt_amt;
                    } else {
                        $payment['wallet'][] = $pmt->pmt_amt;
                    }
                }
            // Payments wallet only
            $wallet=\DB::table('pmt_info_v1')
                        ->where('pmt_method','Patient')
                        ->whereIn('pmt_type', ['Payment','Credit Balance'])
                        ->selectRaw('(sum(pmt_amt))-(sum(amt_used)) as pmt_amt')
                        ->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at');
            /*
            /*          
            if(isset($request['choose_date']) && !empty($request['choose_date']) && 
                ($request['choose_date']=='all' || $request['choose_date']=='transaction_date'))
            $wallet->whereRaw("DATE(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(pmt_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'");
            */
            
            $wallet = $wallet->get();
            if($wallet[0]->pmt_amt!=null)
                $payment['wallet'][] = $wallet[0]->pmt_amt;
            // Payment calculation into provider wise for patient and insurance
            if($payment)
            foreach ($payment as $key=>$item) {
                if(isset($item['Patient']))
                    $payments[$key]['Patient'] = isset($item['Patient'])?array_sum($item['Patient']):0;
                else    
                    $payments[$key]['Patient'] = isset($item[''])?array_sum($item['']):0;
                $payments[$key]['Insurance'] = isset($item['Insurance'])?array_sum($item['Insurance']):0;
                $payments[$key]['billing_provider_id'] = isset($item['billing_provider_id'])?$item['billing_provider_id']:'';
                if($key=='wallet')
                    $payments[$key] = array_sum($item);
            }
            else
                $payments = [];
            // Adjustment Summary Start
            if(!isset($request['insurance_charge'])){
                $request['insurance_type'] = 'all';
                $request['insurance_charge'] = 'all';
            }
            if(isset($request['select_transaction_date']))
                $request['created_at'] = $request['select_transaction_date'];
            
            $api_response = $this->getAdjustmentResult($request);
            $api_response_data=$api_response['adjustment']->groupBy('pmt_claim_cpt_tx_v1.id')->orderBy('created_at','desc')->get();
            $ins_adj = $pat_adj = 0;
            // Adjustment Separate into provider wise
            if(isset($api_response_data))
                foreach($api_response_data as $adj){
                    $provider_name = str_replace(',','',$adj->billing_provider_name);
                    $provider_name = str_replace(' ','_',($provider_name));
                    if($adj->pmt_method=="Insurance")
                        $adjustments[$provider_name][$adj->pmt_method][] = $adj->withheld+$adj->writeoff;
                    else
                        $adjustments[$provider_name][$adj->pmt_method][] = $adj->withheld+$adj->writeoff;
                }

            // Adjustment calculation into provider wise
            if(isset($adjustments))
            foreach ($adjustments as $key=>$item) {
                $pmt_adj[$key]['Patient'] = isset($item['Patient'])?array_sum($item['Patient']):0;
                $pmt_adj[$key]['Insurance'] = isset($item['Insurance'])?array_sum($item['Insurance']):0;
            }
            else
                $pmt_adj = [];
        if(!isset($request['billing_provider_id']))
            $header['Billing Provider'] ='All';
        
        $createdBy = isset($data['created_user']) ? $data['created_user']: Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $billingprov = \DB::table('providers')->leftJoin('claim_info_v1','claim_info_v1.billing_provider_id','=','providers.id')->selectRaw('providers.id,concat(providers.short_name) as short_name,concat(providers.provider_name) as provider_name')->where('providers.provider_types_id','=', 5)->groupby('providers.id');
        if(isset($request['billing_provider_id'])){
            if(is_array($request['billing_provider_id'])){
                $billingprov->whereIn('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            }
            else {
                if(isset($request['export']))
                    $billingprov->whereIn('claim_info_v1.billing_provider_id',explode(',',$request['billing_provider_id']));
                else
                    $billingprov->where('claim_info_v1.billing_provider_id',$request['billing_provider_id']);
            }
        }
        
        $billingprov = $billingprov->get();
        $billingprov_count = (!empty($billingprov)) ? count((array)$billingprov)+9 : 9;
        $billingprov_count_b = "B".$billingprov_count;
        // Export PDF for Charges and Payments
        $request = request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/chargesPayments/export_pdf';
            $report_name = "Charges & Payments Summary";
            $data = ['billingprov' => $billingprov, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'charges' => $charges, 'pmt_adj' => $pmt_adj, 'payments' => $payments, 'header' => $header];
            return $data;
        } 
        if($export!=''){
            $date = date('m-d-Y');
            $name = @$data['export_id'].'X0X'.'Charges_Payments_Summary_' . $date;

            if ($export == 'pdf') {
                return Response::json(array('data'=>compact('header', 'column', 'billingprov', 'charges','payments','pmt_adj')));
                // $html = view('reports/financials/chargesPayments/export_pdf', compact('header', 'column', 'billingprov', 'charges','payments','pmt_adj','export','createdBy','practice_id'));
                // $type = '.pdf';
                // $path = storage_path('app/Report/exports/');
                // PDF::load($html, 'A4', 'landscape')->filename($path.$name . ".pdf")->output();

            } elseif ($export == 'xlsx' || $export == 'csv') {
                $filePath = 'reports/financials/chargesPayments/export';
                $data['billingprov_count_b'] = $billingprov_count_b;
                $data['header'] = $header;
                $data['column'] = $column;
                $data['billingprov'] = $billingprov;
                $data['charges'] = $charges;
                $data['payments'] = $payments;
                $data['pmt_adj'] = $pmt_adj;
                $data['createdBy'] = $createdBy;
                $data['practice_id'] = $practice_id;
                $data['export'] = $export;
                $data['payerType'] = $payerType;
                $data['file_path'] = $filePath;
                return $data;
                // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
                $type = '.xls';
            }
            /*if(isset($data['export_id']))
                ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);*/
        } else{            
            return view('reports/financials/chargesPayments/report', compact('header', 'column', 'billingprov', 'charges','payments','pmt_adj','payerType'));
        }
    }
    // ------------------------------- Charges & Payments summary reports End -------------------------------
        

    /*     * * Outstanding AR Claims report module start ** */

    public function claimlist() {
        $api_response = $this->getClaimsApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $insurance_type = $api_response_data->data->insurance_type;
        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";

        return view('reports/financials/outstanding/outstanding', compact('insurance', 'insurance_type', 'selected_tab', 'heading', 'heading_icon', 'facilities'));
    }

    public function claimsearch() {
        $api_response = $this->getClaimsearchApi();
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $page = $api_response_data->data->page;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $claims = $api_response_data->data->claims;
        return view('reports/financials/outstanding/' . $page, compact('claims', 'header', 'column', 'pagination'));
    }

    /*     * * Blade - outstanding AR pdf  ** */

    public function claimSearchExport($export = '') {
        $api_response = $this->getClaimsearchApi($export);
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $page = $api_response_data->data->page;
        //$pagination = $api_response_data->data->pagination;
        // $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $claims = $api_response_data->data->claims;
        $date = date('m-d-Y_hia');
        $name = 'OutstandingAR_Reports_' . $date;

        if ($export == 'pdf') {
            $html = view('reports/financials/outstanding/listitemreportexport', compact('claims', 'header', 'column', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            Excel::create($name, function($excel) use ($header, $column, $claims, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($header, $column, $claims, $export) {
                    $sheet->loadView('reports/financials/outstanding/listitemreportexport')->with("header", $header)->with("column", $column)->with("claims", $claims)->with("export", $export);
                });
            })->export('xls');
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($header, $column, $claims, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($header, $column, $claims, $export) {
                    $sheet->loadView('reports/financials/outstanding/listitemreportexport')->with("header", $header)->with("column", $column)->with("claims", $claims)->with("export", $export);
                });
            })->export('csv');
        }
    }

    /*     * * Outstanding AR Claims report module end ** */

    /*     * * Payments report module start ** */

    public function paymentlist() {
        $api_response = $this->getPaymentsApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        //$selected_tab = "financial-report";
        $selected_tab = "collection-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        return view('reports/financials/payments/payments', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields','searchUserData'));
    }

    public function paymentsearch() {
        $api_response = $this->getPaymentSearchApi();
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $total = $api_response_data->data->total;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $payments = $api_response_data->data->payments;
        $page = $api_response_data->data->page;
        $dataArr = $api_response_data->data->dataArr;

        $patient_wallet_balance = $api_response_data->data->patient_wallet_balance;
        return view('reports/financials/payments/' . $page . 'report', compact('payments', 'header', 'column', 'total', 'pagination', 'patient_wallet_balance', 'dataArr')); 
    }

    /*     * * Blade - Export Payments report ** */
    public function paymentsearchexport($export = '', $data = '') {
        
        $api_response = $this->getPaymentSearchApi($export, $data);
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $h_payer = $header->Payer;
        $payments = $api_response_data->data->payments;
        $p_payments_count1 = count((array)$payments)+10;
        $p_payments_count2 = count((array)$payments)+14;
        $p_payments_count_var = "C".$p_payments_count1.":"."C".$p_payments_count2;
        $i_payments_count1 = count((array)$payments)+10;
        $i_payments_count2 = count((array)$payments)+19;
        $i_payments_count_var = "C".$i_payments_count1.":"."C".$i_payments_count2;
        $totalRec = 0;
        /* // count issue
        foreach($payments as $payment) {
            if (isset($payment->cpt_fin) && !empty($payment->cpt_fin)) {
                foreach ($payment->cpt_fin as $payment_cpt) {
                    $totalRec += count($payment_cpt);
                }
            }
        }
        */
        $paymentCptRowCount = $totalRec;
        $paymentCptRowCount1 = $paymentCptRowCount+10;
        $paymentCptRowCount2 = $paymentCptRowCount+18;
        $paymentCptRowCountVar = "C".$paymentCptRowCount1.":"."C".$paymentCptRowCount2;
        $page = $api_response_data->data->page;
        $dataArr = $api_response_data->data->dataArr;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        //$name = @$data['export_id'].'X0X'.'Payment_Analysis_Detailed_Report_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $patient_wallet_balance = $api_response_data->data->patient_wallet_balance;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        
        $request = request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/payments/' . $page . 'export_pdf';
            $report_name = "Payment Analysis Detailed Report";
            $data = ['payments' => $payments, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'header' => $header, 'column' => $column, 'patient_wallet_balance' => $patient_wallet_balance, 'export' => $export, 'dataArr' => $dataArr, 'page' => $page];
            return $data;
        }
        
        if ($export == 'xlsx' || $export == 'csv') {
            ini_set('precision', 20);            
            $filePath = 'reports/financials/payments/' . $page . 'export';
            $data['i_payments_count_var'] = $i_payments_count_var;
            $data['h_payer'] = $h_payer;
            $data['p_payments_count_var'] = $p_payments_count_var;
            $data['header'] = $header;
            $data['column'] = $column;
            $data['payments'] = $payments;
            $data['patient_wallet_balance'] = $patient_wallet_balance;
            $data['dataArr'] = $dataArr;
            $data['page'] = $page;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        }
        /*if(isset($data['export_id']))
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);*/
    }

    /*     * * Payments report module end ** */


    /*     * * Refund report module start ** */

    public function refundlist() {
        //$selected_tab = "financial-report";
        $selected_tab = "collection-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $api_response = $this->getRefundsApi();
        $api_response_data = $api_response->getData();

        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        return view('reports/financials/refunds/refunds', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields', 'searchUserData'));
    }

    public function refundsearch() {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getRefundsearchApiSP(); // Store procedure
        } else {
            $api_response = $this->getRefundsearchApi(); // DB
        }
        $api_response_data = $api_response->getData();
        $header = $api_response_data->data->header;
        $column = $api_response_data->data->column;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $total_refund = $api_response_data->data->refund_value;
        $refund_type = $api_response_data->data->refund_type;
        $get_refund_datas = $api_response_data->data->get_refund_data;
        $refund_result = $api_response_data->data->refund_result;
        $unposted = $api_response_data->data->unposted;
        $wallet = $api_response_data->data->wallet;
        $search_by = $api_response_data->data->search_by;
        $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
        return view('reports/financials/refunds/lineitemreport', compact('refund_result', 'refund_type', 'total_refund', 'header', 'column', 'pagination', 'get_refund_datas', 'unposted', 'wallet', 'search_by','user_names'));
    }

    /*     * * Blade - Export refund report ** */

    public function refundsearchexport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getRefundsearchApiSP($export, $data); // Store procedure
        } else {
            $api_response = $this->getRefundsearchApi($export, $data); // DB
        }        
        $api_response_data = $api_response->getData();
        $search_by = $api_response_data->data->search_by;
        $column = $api_response_data->data->column;
        $total_refund = $api_response_data->data->refund_value;
        $end_date = $api_response_data->data->end_date;
        $start_date = $api_response_data->data->start_date;
        $refund_type = $api_response_data->data->refund_type;
        $get_refund_datas = $api_response_data->data->get_refund_data;
        $unposted = $api_response_data->data->unposted;
        $wallet = $api_response_data->data->wallet;
        $refund_result = $api_response_data->data->refund_result;
        $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Refund_Analysis_Detailed_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

         $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/financials/refunds/lineitemexport_pdf';
            $report_name = "Refund Analysis - Detailed";
            $data = ['total_refund' => $total_refund, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'refund_type' => $refund_type, 'get_refund_datas' => $get_refund_datas, 'unposted' => $unposted, 'wallet' => $wallet, 'refund_result' => $refund_result, 'user_names' => $user_names, 'search_by' => $search_by];
           return $data;
        }   
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/financials/refunds/lineitemexport';
            // $data['header'] = $header;
            $data['column'] = $column;
            $data['total_refund'] = $total_refund;
            $data['refund_type'] = $refund_type;
            $data['refund_result'] = $refund_result;
            $data['get_refund_datas'] = $get_refund_datas;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['wallet'] = $wallet;
            $data['unposted'] = $unposted;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['user_names'] = $user_names;
            $data['export'] = $export;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            return $data;
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update([ 'status'=>'Completed']);
        }*/
    }

    public function insuranceList($type_id) {
        $api_response = $this->getInsuranceListApi($type_id);
        $api_response_data = $api_response->getData();
        $insurance_list = $api_response_data->data->insurance_list;
        $insurance_list = json_encode($insurance_list);
        print_r($insurance_list);
        exit;
    }
    /*     * * Refund report module end ** */

    /*     * * Finanical report module start ** */
    public function yearendReport() {
        $api_response = $this->getYearendApi();
        $api_response_data = $api_response->getData();
        $array_year = $api_response_data->data->array_year;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";

        return view('reports/financials/yearend/yearend', compact('claims', 'array_year', 'selected_tab', 'heading', 'heading_icon', 'search_fields', 'searchUserData'));
    }

    public function financialSearch() {
        $api_response = $this->getFinancialSearchApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $search_by = $api_response_data->data->search_by;
        return view('reports/financials/yearend/normalreport', compact('claims', 'search_by'));
    }
    /*     * * Blade - export yearend report ** */

    public function financialSearchExport($export = '', $data = '') {
        $api_response = $this->getFinancialSearchApi($export, $data);
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $search_by = $api_response_data->data->search_by;
        $claims_count = 20;
        $claims_count_co = "C".$claims_count.":"."O".$claims_count;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Year_End_Financials_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if ($export == 'pdf') {
            $view_path = 'reports/financials/yearend/listitemreportexport_pdf';
            $report_name = "Year End Financials";
            $data = ['claims' => $claims, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'claims_count' => $claims_count, 'claims_count_co' => $claims_count_co,'search_by' => $search_by];
            return $data;
        }   
        if ($export == 'xlsx' || $export == 'csv') {            
            $filePath = 'reports/financials/yearend/listitemreportexport';
            $data['claims_count_co'] = $claims_count_co;
            $data['claims'] = $claims;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            $data['search_by'] = $search_by;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {    
                return $data;
            }
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }

    /*     * * Finanical report module start end ** */


    /*     * * Custom Analysis Search Function Start here ** */

    public function customReport() {
        $api_response = $this->getFinancialApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $array_year = $api_response_data->data->array_year;
        $selected_tab = "custom_report";
        $heading = "Custom Reports";
        $heading_icon = "fa-file-word-o";

        return view('reports/customs/customs', compact('claims', 'array_year', 'selected_tab', 'heading', 'heading_icon'));
    }

    public function customSearch() {
        $api_response = $this->getFinancialSearchApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        return view('reports/financial/normalreport', compact('claims'));
    }

    /*     * * Custom Analysis Search Function End here ** */

    /*     * * Custom Analysis Search Function Start here ** */

    public function ediReport() {
        $api_response = $this->getFinancialApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $array_year = $api_response_data->data->array_year;
        $selected_tab = "edi_report";
        $heading = "Custom Reports";
        $heading_icon = "fa-file-word-o";
        return view('reports/edi/edi', compact('claims', 'selected_tab', 'heading', 'heading_icon'));
    }

    public function ediSearch() {
        $api_response = $this->getFinancialSearchApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        return view('reports/edi/normalreport', compact('claims'));
    }

    /*     * * Custom Analysis Search Function End here ** */


    /*     * * Miscellenous Analysis Search Function Start here ** */

    public function miscellenousReport() {
        $api_response = $this->getFinancialApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $array_year = $api_response_data->data->array_year;
        $selected_tab = "miscellenous_report";
        $heading = "Miscellenous Reports";
        $heading_icon = "fa-file-word-o";
        return view('reports/miscellenous/miscellenous', compact('claims', 'selected_tab', 'heading', 'heading_icon'));
    }

    public function miscellenousSearch() {
        $api_response = $this->getFinancialSearchApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        return view('reports/miscellenous/normalreport', compact('claims'));
    }

    /*     * * Miscellenous Analysis Search Function End here ** */


    /*     * * Patient Analysis Search Function Start here ** */

    public function patientReport() {
        $api_response = $this->getFinancialApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $array_year = $api_response_data->data->array_year;
        $selected_tab = "patient_report";
        $heading = "Patient Reports";
        $heading_icon = "fa-file-word-o";
        return view('reports/patient/patient', compact('claims', 'selected_tab', 'heading', 'heading_icon'));
    }

    public function patientSearch() {
        $api_response = $this->getFinancialSearchApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        return view('reports/patient/normalreport', compact('claims'));
    }

    /*     * * Patient Analysis Search Function End here ** */


    /*     * * Patient Analysis Search Function Start here ** */

    public function userReport() {
        $api_response = $this->getFinancialApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        $array_year = $api_response_data->data->array_year;
        $selected_tab = "user_report";
        $heading = "User Reports";
        $heading_icon = "fa-file-word-o";
        return view('reports/user/user', compact('claims', 'selected_tab', 'heading', 'heading_icon'));
    }

    public function userSearch() {
        $api_response = $this->getFinancialSearchApi();
        $api_response_data = $api_response->getData();
        $claims = $api_response_data->data->claims;
        return view('reports/user/normalreport', compact('claims'));
    }

    /*     * * Patient Analysis Search Function End here ** */

    /**
     * Create a new payer analysis controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard  $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
     * @return void
     */
    public function payeranalysis() {

        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');
        $api_response = $this->getPayeryearApi();
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);
        return view('reports/financials/payeranalysis/list', compact('groupby', 'selected_tab', 'heading', 'heading_icon'));
    }

    /**
     * Create a new payer analysis controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard  $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
     * @return void
     */
    public function payerfilter() {

        $api_response = $this->getPayerFilterApi();
        $api_response_data = $api_response->getData();
        $claim_details = $api_response_data->data->claim_details;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        return view('reports/financials/payeranalysis/report_list', compact('claim_details', 'start_date', 'end_date', 'pagination'));
    }

    /*     * * Blade - Export payer analysis ** */

    public function payerfilterexport($export = '') {

        $api_response = $this->getPayerfilterexportApi();
        $api_response_data = $api_response->getData();
        $claim_details = $api_response_data->data->claim_details;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $date = date('m-d-Y_hia');
        $name = 'PayerAnalysis_Report_' . $date;

        if ($export == 'pdf') {
            $html = view('reports/financials/payeranalysis/report_listexport', compact('claim_details', 'start_date', 'end_date', 'export'));            
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            Excel::create($name, function($excel) use ($claim_details, $start_date, $end_date, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($claim_details, $start_date, $end_date, $export) {
                    $sheet->loadView('reports/financials/payeranalysis/report_listexport')->with("claim_details", $claim_details)->with("start_date", $start_date)->with("end_date", $end_date)->with("export", $export);
                });
            })->export('xls');
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($claim_details, $start_date, $end_date, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($claim_details, $start_date, $end_date, $export) {
                    $sheet->loadView('reports/financials/payeranalysis/report_listexport')->with("claim_details", $claim_details)->with("start_date", $start_date)->with("end_date", $end_date)->with("export", $export);
                });
            })->export('csv');
        }
    }

    /**
     * Create a new payer analysis controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard  $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
     * @return void
     */
    public function provider_reimbursement() {
        $groupby = array();
        $selected_tab = "financial-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');
        $api_response = $this->getProviderreimbursementApi();
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        $rendenring_provider_list = $api_response_data->data->rendenring_provider_list;
        $billing_provider_list = $api_response_data->data->billing_provider_list;

        foreach ($cliam_date_details as $year_list) {
            if ($year_list->year != 0)
                $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);
        $rendering_list['all'] = "All";
        foreach ($rendenring_provider_list as $list) {
            $rendering_list[$list->id] = $list->provider_name;
        }
        $billing_list['all'] = "All";
        foreach ($billing_provider_list as $list) {
            $billing_list[$list->id] = $list->provider_name;
        }
        return view('reports/financials/provider_reimbursement/list', compact('groupby', 'rendering_list', 'billing_list', 'selected_tab'));
    }

    /**
     * Create a new payer analysis controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard  $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
     * @return void
     */
    public function provider_reimbursement_filter() {
        $api_response = $this->getProviderFilterApi();
        $api_response_data = $api_response->getData();
        $report_details = $api_response_data->data->data;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $rendering_provider = $api_response_data->data->rendering_provider;
        $billing_provider = $api_response_data->data->billing_provider;
        $data_value = $api_response_data->data->data_value;
        $provider_info = $api_response_data->data->provider_info;
        return view('reports/financials/provider_reimbursement/newreportlist', compact('report_details', 'start_date', 'end_date', 'rendering_provider', 'billing_provider', 'providers', 'data_value', 'provider_info'));
    }

    /*     * * Blade - Export provider analysis ** */
    public function getProviderFilterExport($export = '') {
        $api_response = $this->getProviderExportApi();
        $api_response_data = $api_response->getData();
        $report_details = $api_response_data->data->data;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $rendering_provider = $api_response_data->data->rendering_provider;
        $billing_provider = $api_response_data->data->billing_provider;
        $date = date('m-d-Y_hia');
        $name = 'Provider_Reimbursement_Report_' . $date;

        if ($export == 'pdf') {
            $html = view('reports/financials/provider_reimbursement/report_listexport', compact('report_details', 'start_date', 'end_date', 'rendering_provider', 'billing_provider', 'export'));            
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            Excel::create($name, function($excel) use ($report_details, $start_date, $end_date, $rendering_provider, $billing_provider, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($report_details, $start_date, $end_date, $rendering_provider, $billing_provider, $export) {
                    $sheet->loadView('reports/financials/provider_reimbursement/report_listexport')->with("report_details", $report_details)->with("start_date", $start_date)->with("end_date", $end_date)->with("rendering_provider", $rendering_provider)->with("billing_provider", $billing_provider)->with("export", $export);
                });
            })->export('xls');
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($report_details, $start_date, $end_date, $rendering_provider, $billing_provider, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($report_details, $start_date, $end_date, $rendering_provider, $billing_provider, $export) {
                    $sheet->loadView('reports/financials/provider_reimbursement/report_listexport')->with("report_details", $report_details)->with("start_date", $start_date)->with("end_date", $end_date)->with("rendering_provider", $rendering_provider)->with("billing_provider", $billing_provider)->with("export", $export);
                });
            })->export('csv');
        }
    }

    /**
     *  Patient Reports Starts here
     *  Reports for Patient Address List starts here 
     *  @return groupby,selected_tab, heading, heading_icon. 
     */
    public function getPatientAddressList() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "patients-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getPatientAddressListCreatedApi();
        $api_response_data = $api_response->getData();
        $patient_date_details = $api_response_data->data->patient_created_date;
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        foreach ($patient_date_details as $year_list) {
            if ($year_list->year != 0)
                $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/patients/patientaddresslist/pateintaddresslist', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'searchUserData', 'search_fields'));
    }

    /**
     * Result of Patient Address List
     * @Return start_date,end_date,patient_address_list_filter.
     */
    public function getPatientAddressListFilter() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientAddressListSPFilterApi(); // Store procedure
        } else {
            $api_response = $this->getPatientAddressListFilterApi(); // DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $patient_address_list_filter = $api_reponse_dat->data->filter_result;
        $pagination = $api_reponse_dat->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_reponse_dat->data->search_by;
        return view('reports/patients/patientaddresslist/reportaddresslist', compact('start_date', 'end_date', 'patient_address_list_filter', 'pagination','search_by'));
    }

    /*     * * Blade - Export Patient Address List ** */

    public function patientAddressListExport($export = '', $data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientAddressListSPFilterApi($export,$data); // Store procedure
        } else {
            $api_response = $this->getPatientAddressListFilterApi($export,$data); // DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $patient_address_list_filter = $api_reponse_dat->data->filter_result;
        $search_by = $api_reponse_dat->data->search_by;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Address Listing_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
          $report_name = 'Address Listing';
          $view_path = 'reports/patients/patientaddresslist/reportaddresslistexport_pdf';
          $data = ['patient_address_list_filter' => $patient_address_list_filter, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
          return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/patients/patientaddresslist/reportaddresslistexport';
            $data['patient_address_list_filter'] = $patient_address_list_filter;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }

    /**
     *  Patient Address List Ends Here        
     */

    /**
     *  Patient Demographics List start here  
     *  @return groupby,selected_tab, heading, heading_icon. 
     */
    public function getPatientDemographics() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "patients-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getPatientDemographicsListApi();
        $api_response_data = $api_response->getData();
        $patient_details = $api_response_data->data->patient_create_date;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        foreach ($patient_details as $year_list) {
            if ($year_list->year != 0)
                $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/patients/patientdemographics/patientdemographics', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'search_fields', 'searchUserData'));
    }

    /**
     * Result of Patient Demographics List
     * @Return start_date,end_date,patient_demographics_filter.
     */
    public function getPatientDemographicsFilter() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientDemographicsSPFilterApi(); // Store procedure
        } else {
            $api_response = $this->getPatientDemographicsFilterApi(); // DB table searching
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $patient_demographics_filter = $api_reponse_dat->data->filter_result;
        $search_by = $api_reponse_dat->data->search_by;
        $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
        $pagination = $api_reponse_dat->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        return view('reports/patients/patientdemographics/reportdemographics', compact('start_date', 'end_date', 'patient_demographics_filter', 'pagination', 'search_by','user_names'));
    }

    /*     * * Blade - Export patientdemographics_reports ** */

    public function patientDemographicsExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getPatientDemographicsSPFilterApi($export, $data); // Store procedure
        } else {
            $api_response = $this->getPatientDemographicsFilterApi($export, $data); // DB table
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = isset($api_reponse_dat->data->start_date) ? $api_reponse_dat->data->start_date : "";
        $end_date = isset($api_reponse_dat->data->end_date) ? $api_reponse_dat->data->end_date : "";
        $patient_demographics_filter = $api_reponse_dat->data->filter_result;
        $search_by = $api_reponse_dat->data->search_by;
        $user_names =  Users::where('status', 'Active')->pluck('name', 'id')->all();
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Demographic_Sheet_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        
        $request = Request::all();
        if (isset($request['export']) && $request['export'] == 'pdf') {
          $report_name = 'Demographic Sheet';
          $view_path = 'reports/patients/patientdemographics/reportdemographicsexport_pdf';
          $data = ['patient_demographics_filter' => $patient_demographics_filter, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
          return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/patients/patientdemographics/reportdemographicsexport';
            $data['patient_demographics_filter'] = $patient_demographics_filter;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['user_names'] = $user_names;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            $data['export'] = $export;
            if(Request::ajax()) {
                return Response::json(array('value' => $data));
            } else {    
                return $data;
            }
            $type = '.xls';
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }

    /**
     *  Patient Demographics List Ends here      
     */

    /**
     *  Patient Patient Icd Worksheet List start here  
     *  @return groupby,selected_tab, heading, heading_icon. 
     */
    public function getPatientIcdWorksheet() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "patients-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');
        $api_response = $this->getPatientIcdWorksheetListApi();
        $api_response_data = $api_response->getData();
        $patient_created_date = $api_response_data->data->patient_create_date;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        foreach ($patient_created_date as $year_list) {
            if ($year_list->year != 0)
                $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/patients/patienticdworksheet/patienticdworksheet', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'search_fields','searchUserData'));
    }

    /**
     * Result of Patient Icd Worksheet List
     * @Return start_date,end_date,icd_result.
     */
    public function getPatientIcdWorksheetReport() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientIcdWorksheetFilterApiSP(); // store procedure
        } else {
            $api_response = $this->getPatientIcdWorksheetFilterApi(); // DB
        }
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;

        $end_date = $api_response_data->data->end_date;
        $icd_result = $api_response_data->data->payment_details;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_response_data->data->search_by;
        return view('reports/patients/patienticdworksheet/reportworksheet', compact('start_date', 'end_date', 'icd_result', 'pagination','search_by'));
    }

    /*     * * Blade - Export Patient Icd Worksheet List ** */

    public function patientIcdWorksheetExport($export = '',$data='') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientIcdWorksheetFilterApiSP($export, $data); // store procedure
        } else {
            $api_response = $this->getPatientIcdWorksheetFilterApi($export, $data); //DB
        }
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $icd_result = $api_response_data->data->payment_details;
        $search_by = $api_response_data->data->search_by;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $date = date('m-d-Y');
        $request = Request::all();
        // $name = $data['export_id'].'X0X'.'ICD_Worksheet_' . $date;

        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/patients/patienticdworksheet/reportworksheetexport_pdf';
            $report_name = "ICD Worksheet";
            $data = ['icd_result' => $icd_result, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
            return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {            
            $filePath = 'reports/patients/patienticdworksheet/reportworksheetexport';
            $data['icd_result'] = $icd_result;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            $data['export'] = $export;
            return $data;
        }
        /*if(isset($data['export_id']))
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);*/
    }
    /**
     * Patient ICD Worksheet List Ends here  
     */

    /**
     * patient AR report starts here  
     */
    public function getPatientArReportList() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "patients-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');
        $api_response = $this->getPatientArReportListApi();
        $api_response_data = $api_response->getData();
        $patient_created_date = $api_response_data->data->patient_create_date;

        foreach ($patient_created_date as $year_list) {
            if ($year_list->year != 0)
                $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/patients/patientarreport/patientarlist', compact('groupby', 'selected_tab', 'heading', 'heading_icon'));
    }

    public function getPatientArReport() {

        $api_response = $this->getPatientArReportApi();
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $ageingday = $api_response_data->data->agingdays;
        $ar_filter_result = $api_response_data->data->arresult;
        $patient_data = $api_response_data->data->patient_data;
        //dd($patient_data);
        return view('reports/patients/patientarreport/patientarreport', compact('ar_filter_result', 'ageingday', 'start_date', 'end_date', 'patient_data'));
    }

    /*     * * Blade - export patient ar report ** */

    public function patientArExport($export = '') {
        $api_response = $this->getPatientArReportApi();
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $ageingday = $api_response_data->data->agingdays;
        $ar_filter_result = $api_response_data->data->arresult;
        $patient_data = $api_response_data->data->patient_data;
        $date = date('m-d-Y_hia');
        $name = 'PatientAR_Report_' . $date;

        if ($export == 'pdf') {
            $html = view('reports/patients/patientarreport/patientarexport', compact('ar_filter_result', 'ageingday', 'start_date', 'end_date', 'patient_data', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            Excel::create($name, function($excel) use ($ageingday, $start_date, $end_date, $ar_filter_result, $patient_data, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($ageingday, $start_date, $end_date, $ar_filter_result, $patient_data, $export) {
                    $sheet->loadView('reports/patients/patientarreport/patientarexport')->with("ar_filter_result", $ar_filter_result)->with("ageingday", $ageingday)->with("patient_data", $patient_data)->with("start_date", $start_date)->with("end_date", $end_date)->with("export", $export);
                });
            })->export('xls');
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($ageingday, $start_date, $end_date, $ar_filter_result, $patient_data, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($ageingday, $start_date, $end_date, $ar_filter_result, $patient_data, $export) {
                    $sheet->loadView('reports/patients/patientarreport/patientarexport')->with("ar_filter_result", $ar_filter_result)->with("ageingday", $ageingday)->with("patient_data", $patient_data)->with("start_date", $start_date)->with("end_date", $end_date)->with("export", $export);
                });
            })->export('csv');
        }
    }

    /**
     * patient AR report ends here  
     */
    /**
     * Patient Reports Ends here.
     */

    /**
     *  Pratice Setting Reports starts here
     *  Employer List start here  
     *  @Return groupby,selected_tab, heading, heading_icon. 
     */
    public function getEmployerList() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "practice-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');
        $api_response = $this->getEmployerListApi();
        $api_response_data = $api_response->getData();
        $employer_created_date = $api_response_data->data->employer_create_date;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $employer_created_date = $api_response_data->data->employer_create_date;

        foreach ($employer_created_date as $year_list) {
            if ($year_list->year != 0)
                $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/practicesettings/employerlist/employerlist', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'search_fields', 'searchUserData'));
    }

    /**
     * Result of Employer List
     * @Return start_date,end_date,employer_filter_result.
     */
    public function getEmployerListReport() {
        $api_response = $this->getEmployerListReportApi();// DB
        //$api_response = $this->getEmployerListReportApiSP(); // Store Procedure
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $employer_filter_result = $api_reponse_dat->data->filter_result;
        $pagination = $api_reponse_dat->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_reponse_dat->data->search_by;
        return view('reports/practicesettings/employerlist/employerlistreport', compact('start_date', 'end_date', 'employer_filter_result', 'pagination','search_by'));
    }

    /*     * * Blade - Export employerlist ** */

    public function employerListExport($export = '', $data = '') {
        $api_response = $this->getEmployerListReportApi($export,$data);// DB
        //$api_response = $this->getEmployerListReportApiSP($export,$data); // Store Procedure
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $employer_filter_result = $api_reponse_dat->data->filter_result;
        $date = date('m-d-Y');
        $name = $data['export_id'].'X0X'.'Employer_Summary_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : '';
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        if ($export == 'pdf') {
            $html = view('reports/practicesettings/employerlist/employerlistexport_pdf', compact('start_date', 'end_date', 'employer_filter_result', 'export', 'createdBy', 'practice_id'));
            $type = '.pdf';
            $path = storage_path('app/Report/exports/');
            return PDF::loadHTML($html, 'A4', 'landscape')->download($path.$name . ".pdf");
        } elseif ($export == 'xlsx') {
            Excel::create($name, function($excel) use ($start_date, $end_date, $employer_filter_result, $export, $createdBy, $practice_id) {
                $excel->sheet('New Sheet', function($sheet) use ($start_date, $end_date, $employer_filter_result, $export, $createdBy, $practice_id) {
                    $sheet->loadView('reports/practicesettings/employerlist/employerlistexport')->with("start_date", $start_date)->with("end_date", $end_date)->with("employer_filter_result", $employer_filter_result)->with("export", $export)->with("createdBy", $createdBy)->with('practice_id', $practice_id);
                });
            })->store('xls', storage_path("app/Report/exports"));           
            $type = '.xls';
        } elseif ($export == 'csv') {
            Excel::create($name, function($excel) use ($start_date, $end_date, $employer_filter_result, $export, $createdBy, $practice_id) {
                $excel->sheet('Excel', function($sheet) use ($start_date, $end_date, $employer_filter_result, $export, $createdBy, $practice_id) {
                    $sheet->loadView('reports/practicesettings/employerlist/employerlistexport')->with("start_date", $start_date)->with("end_date", $end_date)->with("employer_filter_result", $employer_filter_result)->with("export", $export)->with("createdBy", $createdBy)->with('practice_id', $practice_id);
                });
            })->export('csv');
            $type = '.csv';
        }
        /*if(isset($data['export_id']))
            ReportExportTask::where('id',$data['export_id'])->update([ 'status'=>'Completed']);*/
    }

    /**
     *  Employer List Ends here
     *  Practice Setting Ends here
     */
    /*     * * patient Wallet history report starts here  */

    /**
     * Result of Patient Wallethistory List
     * @Return start_date,end_date,patient_wallethistory_filter.
     */
    public function getPatientWalletHistory() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "patients-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getPatientWalletHistoryListApi();
        $api_response_data = $api_response->getData();
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        return view('reports/patients/patientwallethistory/patientwallethistory', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'search_fields', 'searchUserData'));
    }

    public function getPatientWalletHistoryFilter() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientWalletHistoryFilterApiSP(); //Store procedure
        } else {
            $api_response = $this->getPatientWalletHistoryFilterApi(); // DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $patient_wallethistory_filter = $api_reponse_dat->data->filter_result;
        $search_by = $api_reponse_dat->data->search_by;
        $pagination = $api_reponse_dat->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        return view('reports/patients/patientwallethistory/reportwallethistory', compact('start_date', 'end_date', 'patient_wallethistory_filter', 'pagination', 'search_by'));
    }

    public function patientWalletHistoryExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientWalletHistoryFilterApiSP($export, $data); //Store procedure
        } else {
            $api_response = $this->getPatientWalletHistoryFilterApi($export, $data);// DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $patient_wallethistory_filter = $api_reponse_dat->data->filter_result;
        $search_by = $api_reponse_dat->data->search_by;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Wallet_History_Detailed_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $request = Request::all();  
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
          $report_name = 'Wallet History - Detailed';
          $view_path = 'reports/patients/patientwallethistory/reportwallethistoryexport_pdf';
          $data = ['patient_wallethistory_filter' => $patient_wallethistory_filter, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
          return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/patients/patientwallethistory/reportwallethistoryexport';
            $data['patient_wallethistory_filter'] = $patient_wallethistory_filter;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }
    /*     * * patient Wallet history report ends here  */


    /*     * * patient Statement  starts here  */
    public function getPatientStatementHistory() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "patients-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getPatientStatementHistoryListApi();
        $api_response_data = $api_response->getData();
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        return view('reports/patients/patientstatementhistory/patientstatement', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'search_fields', 'searchUserData'));
    }

    public function getPatientStatementHistoryFilter() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientStatementHistoryFilterApiSP(); //call store procedure
        } else {
            $api_response = $this->getPatientStatementHistoryFilterApi(); // DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $patient_statementhistory_filter = $api_reponse_dat->data->filter_result;

        $pagination = $api_reponse_dat->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_reponse_dat->data->search_by;
        return view('reports/patients/patientstatementhistory/reportpatientstatement', compact('start_date', 'end_date', 'patient_statementhistory_filter', 'pagination','search_by'));
    }

    public function patientStatementHistoryExport($export = '', $data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientStatementHistoryFilterApiSP($export, $data); //call store procedure
        } else {
            $api_response = $this->getPatientStatementHistoryFilterApi($export, $data); // DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = $api_reponse_dat->data->start_date;
        $end_date = $api_reponse_dat->data->end_date;
        $patient_statementhistory_filter = $api_reponse_dat->data->filter_result;
        $search_by = $api_reponse_dat->data->search_by;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Statement_History_Detailed_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/patients/patientstatementhistory/reportpatientstatementexport_pdf';
            $report_name = "Statement History - Detailed";
            $data = ['api_reponse_dat' => $api_reponse_dat, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by,'patient_statementhistory_filter' => $patient_statementhistory_filter];
            // dd($data);
            return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {            
            $filePath = 'reports/patients/patientstatementhistory/reportpatientstatementexport';
            $data['patient_statementhistory_filter'] = $patient_statementhistory_filter;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            $data['export'] = $export;
            return $data;
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }
    
    public function generated_reports() {

        $selected_tab = "generated-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $practice_timezone = \App\Http\Helpers\Helpers::getPracticeTimeZone();
        // dd(ReportExportTask::get());
        $data = ReportExportTask::select("*",DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('createdUser')->where('practice_id',Session::get('practice_dbid'))
            ->where('created_by',Auth::user()->id);

        $data->Where(function ($qry) use ($data) {
            $qry->where('status','Completed')->orWhere('status','Pending');
        });     
            
        $data->where('export_type','reports')
            ->where("created_at", ">",DB::raw('DATE_SUB(NOW(), INTERVAL 1 WEEK)'))
            ->whereNull('deleted_at')
            ->orderBy('id','DESC');
            
        $data = $data->get()->toArray();
        return view('reports/generated_reports/list', compact('selected_tab', 'heading', 'heading_icon','data'));
    }

    public function generated_reports_view() {
        $selected_tab = "generated-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $data = ReportExportTask::with('createdUser')
                ->where('practice_id',Session::get('practice_dbid'))->where('created_by',Auth::user()->id);
        $data->Where(function ($qry) use ($data) {
            $qry->where('status','Completed')->orWhere('status','Pending');
        });     
            
        $data = $data->where('export_type','reports')
            ->where("created_at", ">",DB::raw('DATE_SUB(NOW(), INTERVAL 1 WEEK)'))
            ->whereNull('deleted_at')
            ->orderBy('id','DESC')->get()->toArray();
        return Response::view('reports/generated_reports/generatedReports', compact('selected_tab', 'heading', 'heading_icon','data'));
    }
    /*     * * patient statment list report ends here  */

    /*     * * patient Statement status starts here  */

    public function getPatientStatementStatus() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "patients-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getPatientStatementStatusListApi();
        $api_response_data = $api_response->getData();
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        return view('reports/patients/patientstatementstatus/patientstatementstatus', compact('groupby', 'selected_tab', 'heading', 'heading_icon', 'search_fields', 'searchUserData'));
    }

    public function getPatientStatementStatusFilter() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientStatementStatusFilterApiSP(); //store procedure
        } else {
            $api_response = $this->getPatientStatementStatusFilterApi(); // DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = isset($api_reponse_dat->data->start_date) ? $api_reponse_dat->data->start_date : '';
        $end_date = isset($api_reponse_dat->data->end_date) ? $api_reponse_dat->data->end_date : '';
        $patient_statementstatus_filter = $api_reponse_dat->data->filter_result;

        $pagination = $api_reponse_dat->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_reponse_dat->data->search_by;
        return view('reports/patients/patientstatementstatus/reportpatientstatementstatus', compact('start_date', 'end_date', 'patient_statementstatus_filter', 'pagination','search_by'));
    }

    public function patientStatementStatusExport($export = '', $data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getPatientStatementStatusFilterApiSP($export, $data); //store procedure
        } else {
            $api_response = $this->getPatientStatementStatusFilterApi($export, $data); // DB
        }
        $api_reponse_dat = $api_response->getData();
        $start_date = isset($api_reponse_dat->data->start_date) ? $api_reponse_dat->data->start_date : '';
        $end_date = isset($api_reponse_dat->data->end_date) ? $api_reponse_dat->data->end_date : '';
        $patient_statementstatus_filter = $api_reponse_dat->data->filter_result;
        $search_by = $api_reponse_dat->data->search_by;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Statement_Status_Detailed_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/patients/patientstatementstatus/reportpatientstatementstatusexport_pdf';
            $report_name = "Statement Status - Detailed";
            $data = ['patient_statementstatus_filter' => $patient_statementstatus_filter, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'search_by' => $search_by];
            // dd($data);
            return $data;
        }
        if ($export == 'xlsx' || $export == 'csv') {            
            $filePath = 'reports/patients/patientstatementstatus/reportpatientstatementstatusexport';
            $data['patient_statementstatus_filter'] = $patient_statementstatus_filter;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        }
        /*if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }*/
    }    

    /*     * * patient statment status list report ends here  */

    /* public function under_const(){
      return view('reports/customs/customs');
      } */
    
    public function demoreport() {
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/demo_reports/list', compact('selected_tab', 'heading', 'heading_icon'));
    }
    
    public function emptylist() {
        $api_response = $this->getChargesApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/demo_reports/empty/chargereports', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields'));
    }
    
    public function outstandingardemo() {
        $api_response = $this->getChargesApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/demo_reports/empty/outstandingar', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields'));
    }

    public function pendingclaimdemo() {
        $api_response = $this->getChargesApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/demo_reports/empty/pendingclaimdemo', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields'));
    }
    
    public function monthendperformance() {
        $api_response = $this->getChargesApi();
        $api_response_data = $api_response->getData();
        $insurance = $api_response_data->data->insurance;
        $facilities = $api_response_data->data->facilities;
        $search_fields = $api_response_data->data->search_fields;
        $selected_tab = "demo-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        return view('reports/demo_reports/empty/monthendperformance', compact('insurance', 'selected_tab', 'heading', 'heading_icon', 'facilities', 'search_fields'));
    }
}