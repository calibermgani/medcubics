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

class ProviderlistController extends Api\ProviderlistApiController {

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

    public function providerlist() {

        $heading_icon = "fa-line-chart";
        $heading = "Reports";
        $selected_tab = "practice-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getProviderlistApi();
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        $search_fields = $api_response_data->data->search_fields;

        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/practicesettings/providerlist/list', compact('groupby', 'selected_tab', 'search_fields'));
    }

    public function filter_resultProvider() {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getProviderFilterResultApiSP(); // Stored Procedure
        } else {
            $api_response = $this->getProviderFilterResultApi(); // DB
        }
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practiceopt = $api_response_data->data->practiceopt;
        $header = $api_response_data->data->header;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $providers = $api_response_data->data->providers;
        $charges = $api_response_data->data->charges;
        $writeoff = $api_response_data->data->writeoff;
        $pat_adj = $api_response_data->data->pat_adj;
        $ins_adj = $api_response_data->data->ins_adj;
        $patient = $api_response_data->data->patient;
        $insurance = $api_response_data->data->insurance;
        $patient_bal = $api_response_data->data->patient_bal;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $units = $api_response_data->data->units;
        return view('reports/practicesettings/providerlist/provider_reportlist', compact('start_date', 'end_date', 'practiceopt', 'providers', 'pagination', 'header', 'charges', 'writeoff', 'pat_adj', 'ins_adj', 'patient', 'insurance', 'patient_bal', 'insurance_bal', 'units'));
    }

    /*     * * Blade - Export Provider list ** */

    public function providerListExport($export = '',$data = '') {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getProviderFilterResultApiSP($export, $data); // Stored Procedure
        } else {
            $api_response = $this->getProviderFilterResultApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practiceopt = $api_response_data->data->practiceopt;
        $header = $api_response_data->data->header;
        $providers = $api_response_data->data->providers;
        $providers_c = count($providers)+10;
        $providers_c_wallet = "B".$providers_c;
        $providers_c1 = $providers_c+2;
        $providers_c2 = $providers_c+5;
        $providers_c_summary = "B".$providers_c1.":"."B".$providers_c2;
        $charges = $api_response_data->data->charges;
        $writeoff = $api_response_data->data->writeoff;
        $pat_adj = $api_response_data->data->pat_adj;
        $ins_adj = $api_response_data->data->ins_adj;
        $patient = $api_response_data->data->patient;
        $insurance = $api_response_data->data->insurance;
        $patient_bal = $api_response_data->data->patient_bal;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $units = $api_response_data->data->units;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Provider_Summary_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/practicesettings/providerlist/provider_reportlistexport_pdf';
            $report_name = "Provider Summary";
            $data = ['providers' => $providers,
                    'createdBy' => $createdBy,
                    'practice_id' => $practice_id,
                    'view_path' => $view_path,
                    'report_name' => $report_name,
                    'charges' => $charges,
                    'patient' => $patient, 
                    'insurance' => $insurance,
                    'patient_bal' => $patient_bal,
                    'insurance_bal' => $insurance_bal,
                    'units' => $units, 
                    'header' => $header, 
                    'writeoff' => $writeoff,
                    'pat_adj' => $pat_adj,
                    'ins_adj' => $ins_adj];
            return $data;
        } 

        if ($export == 'xlsx' || $export == 'csv') {           
            $filePath = 'reports/practicesettings/providerlist/provider_reportlistexport';
            $data['providers_c_summary'] = $providers_c_summary;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['providers_c_wallet'] = $providers_c_wallet;
            $data['practiceopt'] = $practiceopt;
            $data['providers'] = $providers;
            $data['header'] = $header;
            $data['charges'] = $charges;
            $data['writeoff'] = $writeoff;
            $data['pat_adj'] = $pat_adj;
            $data['ins_adj'] = $ins_adj;
            $data['patient'] = $patient;
            $data['insurance'] = $insurance;
            $data['patient_bal'] = $patient_bal;
            $data['insurance_bal'] = $insurance_bal;
            $data['units'] = $units;
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

    //---------------------------------PROVIDER SUMMARY BY BASKAR---------------------
    public function providerSummary() {

        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "practice-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getProviderlistApi('summary');
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;

        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        return view('reports/practicesettings/providerlist/summary', compact('groupby', 'selected_tab', 'search_fields','heading_icon','heading','searchUserData'));
    }

    /* public function filter_group_resultProvider(){	
      //echo "jhgkk1";die;
      $api_response = $this->getGroupFilterResultApi();
      $api_response_data = $api_response->getData();

      $filter_group_list = $api_response_data->data->rendering_provider_claim;
      $billing_provider = $api_response_data->data->billing_provider;

      $provider_type_id = $api_response_data->data->provider_type_id;

      $start_date = $api_response_data->data->start_date;
      $end_date = $api_response_data->data->end_date;

      return view('reports/practicesettings/providerlist/report_list',compact('start_date','end_date','name','filter_group_list','billing_provider','provider_type_id'));
      } */
}
