<?php

namespace App\Http\Controllers\Reports\Appointment;

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
use App\Exports\BladeExport;

class AppointmentController extends Api\AppointmentApiController {

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

 /*     * * Appointment report module end ** */
 /*     *** Appoinment REPORT LIST Start *** */
    public function appointmentanalysis() {
        $currentURL = Request::url();
        $url = explode("/", $currentURL);
        $reportName = $url[count($url) - 1];
        $api_response = $this->appointmentanalysisApi();
        $api_response_data = $api_response->getData();      
        $searchUserData = $api_response_data->data->searchUserData;
        $search_fields = $api_response_data->data->search_fields;
        //$selected_tab = "financial-report";
        $selected_tab = "appointment-report";
        $heading = "Reports";
        $heading_icon = "fa-bar-chart";
        $report_data = Session::get('report_data');
        return view('reports/scheduling/appointmentanalysis/appointment', compact('selected_tab', 'heading', 'heading_icon', 'report_data', 'search_fields', 'searchUserData'));
    }

    public function appointmentreport() {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->analysissearchApiSP();
        }else{
            $api_response = $this->analysissearchApi();
        }
        $api_response_data = $api_response->getData();      
        $appt_result = $api_response_data->data->appt_result;
        $search_by = $api_response_data->data->search_by;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $pagination_prt = $api_response_data->data->pagination_prt;
        $user_names =  Users::where('status', 'Active')->pluck('short_name', 'id')->all();
        
        return view('reports/scheduling/appointmentanalysis/normalreport', compact('appt_result','search_by', 'pagination','pagination_prt','user_names'));
    }

    public function appointmentanalysisExport($export = '',$data = '') {
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->analysissearchApiSP($export, $data);
        }else{
            $api_response = $this->analysissearchApi($export, $data);
        }
        $api_response_data = $api_response->getData();
        $appt_result = $api_response_data->data->appt_result;
        $search_by = $api_response_data->data->search_by;
        $user_names =  Users::where('status', 'Active')->pluck('name', 'id')->all();
        $date = date('m-d-Y');
        $name = 'Appointment_Analysis_Report_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';        
        
        $request = request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/scheduling/appointmentanalysis/lineitemexport_pdf';
            $report_name = "Appointment Analysis Report";
            $data = ['appt_result' => $appt_result, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'export' => $export, 'user_names' => $user_names, 'search_by' => $search_by];
            return $data;
        }
        
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/scheduling/appointmentanalysis/lineitemexport';
            $data['appt_result'] = $appt_result;
            $data['user_names'] = $user_names;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['search_by'] = $search_by;
            $data['file_path'] = $filePath;
            $data['export'] = $export;
            return $data;
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }
        if(isset($data['export_id'])){
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
        }
    }
}