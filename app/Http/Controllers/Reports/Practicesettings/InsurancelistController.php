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
use App\Models\Patients\Claims as Claims;
use App\Http\Helpers\Helpers as Helpers;
use Excel;
use PDF;
use App\Models\ReportExport as ReportExportTask;
use App\Exports\BladeExport;

class InsurancelistController extends Api\InsurancelistApiController {

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

    public function insurancelist() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "practice-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getInsurancelistApi();
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);
        return view('reports/practicesettings/insurancelist/list', compact('groupby', 'selected_tab', 'searchUserData', 'search_fields','heading_icon','heading'));
    }

    public function filter_insurance_result() {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getFilterResultApiSP(); // Stored Procedure
        } else {
            $api_response = $this->getFilterResultApi(); // DB
        }
        $api_response_data = $api_response->getData();
        $payers = $api_response_data->data->payers;
        $charges = $api_response_data->data->charges;
        $adjustments = $api_response_data->data->adjustments;
        $insurance = $api_response_data->data->insurance;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $unit_details = $api_response_data->data->unit_details;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $tot_units =$api_response_data->data->tot_units;    
        $tot_charges =$api_response_data->data->tot_charges;    
        $total_adj =$api_response_data->data->total_adj;    
        $total_pmt =$api_response_data->data->total_pmt;    
        $insurance_total = $api_response_data->data->insurance_total;
        return view('reports/practicesettings/insurancelist/report_list', compact('start_date', 'end_date', 'payers', 'pagination', 'search_by', 'charges', 'adjustments', 'insurance', 'insurance_bal', 'unit_details', 'unit_details','tot_units','tot_charges','total_adj','total_pmt','insurance_total'));
    }

    /*     * * Blade - export insurancelist report ** */

    public function insuranceListExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getFilterResultApiSP($export,$data); // Stored Procedure
        } else {
            $api_response = $this->getFilterResultApi($export,$data); // DB
        }        
        $api_response_data = $api_response->getData();
        $payers = $api_response_data->data->payers;
        $charges = $api_response_data->data->charges;
        $adjustments = $api_response_data->data->adjustments;
        $insurance = $api_response_data->data->insurance;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $unit_details = $api_response_data->data->unit_details;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $search_by = $api_response_data->data->search_by;
        $tot_units =$api_response_data->data->tot_units;    
        $tot_charges =$api_response_data->data->tot_charges;    
        $total_adj =$api_response_data->data->total_adj;    
        $total_pmt =$api_response_data->data->total_pmt;    
        $insurance_total = $api_response_data->data->insurance_total;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Payer_Summary_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/practicesettings/insurancelist/report_listexport_pdf';
            $report_name = "Payer Summary";
            $data = ['payers' => $payers, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'tot_units' => $tot_units, 'tot_charges' => $tot_charges,  'charges' => $charges, 'adjustments' => $adjustments, 'total_adj' => $total_adj, 'insurance' => $insurance, 'total_pmt' => $total_pmt, 'insurance_bal' => $insurance_bal, 'unit_details' => $unit_details, 'insurance_total' => $insurance_total, 'search_by' => $search_by];
            return $data;
        } 

        if ($export == 'xlsx' || $export == 'csv') {            
            $filePath = 'reports/practicesettings/insurancelist/report_listexport';
            $data['payers'] = $payers;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['charges'] = $charges;
            $data['adjustments'] = $adjustments;
            $data['insurance'] = $insurance;
            $data['insurance_bal'] = $insurance_bal;
            $data['unit_details'] = $unit_details;
            $data['tot_units'] = $tot_units;
            $data['tot_charges'] = $tot_charges;
            $data['total_adj'] = $total_adj;
            $data['total_pmt'] = $total_pmt;
            $data['insurance_total'] = $insurance_total;
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