<?php

namespace App\Http\Controllers\Reports\Practicesettings;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Redirect;
use View;
use Auth;
use Config;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Practice;
use App\Http\Helpers\Helpers as Helpers;
use Excel;
use PDF;
use App\Models\ReportExport as ReportExportTask;
use App\Exports\BladeExport;

class CptlistController extends Api\CptlistApiController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'practice-report');
        View::share('heading_icon', 'fa-line-chart');
    }

    public function cptlist() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "practice-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getCptlistApi();
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/practicesettings/cptlist/list', compact('groupby', 'selected_tab', 'search_fields','heading_icon','heading','searchUserData'));
    }

    public function filter_cpt_result() {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getFilterResultApiSP(); // Stored Procedure
        } else {
            $api_response = $this->getFilterResultApi(); // DB Data Table
        }
        $api_response_data = $api_response->getData();
        $cpts = $api_response_data->data->cpts;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_response_data->data->search_by;
        $cpt_summary = json_decode(json_encode($api_response_data->data->cpt_summary));
       
        $summary_det = array('units' => 0, 'charges' => 0, 'adj' => 0, 'pmt' => 0, 'bal' => 0);        
        foreach ($cpt_summary as $item) {
            $cpt_code = $item->cpt_code;
            $summary_det['units'] += @$item->unit;
            $summary_det['charges'] += @$item->total_charge;
            $summary_det['adj'] += (@$item->ins_adj+@$item->pat_adj);
            $summary_det['pmt'] += (@$item->total_paid);
            $summary_det['bal'] += (@$item->total_ar_due);//(@$item->patient_bal+@$item->insurance_bal);
        }                
        
        $patient = isset($api_response_data->data->patient) ? $api_response_data->data->patient : [];
        $cptDesc = isset($api_response_data->data->cptDesc) ? $api_response_data->data->cptDesc : [];
        return view('reports/practicesettings/cptlist/report_list', compact('start_date', 'end_date', 'pagination','search_by', 'cpts', 'patient', 'summary_det', 'cptDesc'));
    }

    /*     * * Blade - Export CPT list ** */

    public function cptListExport($export = '', $data = '') {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 0)  {
            $api_response = $this->getFilterResultApiSP($export,$data); // Stored Procedure
        } else {
            $api_response = $this->getFilterResultApi($export,$data); // DB Data Table
        }
        $api_response_data = $api_response->getData();
        $cpts = $api_response_data->data->cpts;
        $cpts_count = count($cpts)+10;
        $cpts_count_wallet = "C".$cpts_count;
        $cpts_count2 = $cpts_count+2;
        $cpts_count3 = $cpts_count+5;
        $cpts_count_summary = "C".$cpts_count2.":"."C".$cpts_count3;
        $start_date = isset($api_response_data->data->start_date) ? $api_response_data->data->start_date : '';
        $end_date = isset($api_response_data->data->end_date) ? $api_response_data->data->end_date : '';
        $search_by = isset($api_response_data->data->search_by) ? $api_response_data->data->search_by : '';
        $patient = $api_response_data->data->patient;
        $cpt_summary = json_decode(json_encode($api_response_data->data->cpt_summary));
       
        //$units = $value = array_sum(array_column($cpt_summary,'unit'));
        $summary_det = array('units' => 0, 'charges' => 0, 'adj' => 0, 'pmt' => 0, 'bal' => 0);        
        foreach ($cpt_summary as $item) {
            $summary_det['units'] += $item->unit;
            $summary_det['charges'] += $item->total_charge;
            $summary_det['adj'] += (@$item->ins_adj+@$item->pat_adj);
            $summary_det['pmt'] += (@$item->total_paid);
            $summary_det['bal'] += (@$item->total_ar_due);//(@$item->patient_bal+@$item->insurance_bal);
        }
        $patient = isset($api_response_data->data->patient) ? $api_response_data->data->patient : [];
        $cptDesc = isset($api_response_data->data->cptDesc) ? $api_response_data->data->cptDesc : [];
        
        $date = date('m-d-Y');        
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';
        
        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $report_name = 'CPT/HCPCS Summary';
            $view_path = 'reports/practicesettings/cptlist/report_listexport_pdf';
            $data = ['cpts' => $cpts, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'patient' => $patient, 'cpt_summary' => $cpt_summary, 'summary_det' => $summary_det, 'cptDesc' => $cptDesc, 'search_by' => $search_by];
            return $data;
        }        

        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/practicesettings/cptlist/report_listexport';
            $data['cpts_count_wallet'] = $cpts_count_wallet;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['cpts_count_summary'] = $cpts_count_summary;
            $data['cpts'] = $cpts;
            $data['patient'] = $patient;
            $data['summary_det'] = $summary_det;
            $data['cptDesc'] = $cptDesc;
            $data['search_by'] = $search_by;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        } 
        if(isset($data['export_id']))
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
    }
}