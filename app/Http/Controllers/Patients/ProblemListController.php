<?php

namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;
use View;
use Request;
use Response;
use Redirect;
use Config;
use Auth;
use App\Models\Patient;
use App\Http\Helpers\Helpers as Helpers;
use Route;
use PDF;
use Excel;
use App\Exports\BladeExport;

class ProblemListController extends Api\ProblemListApiController {

    public function __construct() {
        View::share('heading', 'Patient');
        $get_url =Route::getFacadeRoot()->current()->uri();
        $selected_tab = (strrpos($get_url, "myproblemlist") !== FALSE) ? "myproblemlist" : "problemlist";
        View::share('selected_tab', $selected_tab);

        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }

    public function index($patient_id) {
        $api_response = $this->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $practice = $api_response_data->data->practice;
            $last_addin_problemlist = $api_response_data->data->last_addin_problemlist;
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
            $search_fields = $api_response_data->data->search_fields;
            $searchUserData = $api_response_data->data->searchUserData;
            // Sidebar issue fixed.
            // Rev-1. Ravi - 17-09-2019
            View::share('heading', 'Patient');
            View::share('selected_tab', 'problemlist');
            View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
           
            return view('patients/problemlist/problemlist', compact('patient_id', 'claims_list', 'last_addin_problemlist','search_fields','searchUserData'));
        } else {
            return Redirect::to('/patients')->with('message', $api_response_data->message);
        }
    }

    public function indexTableData($patient_id = null) {        
        $api_response = $this->getListIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
        $view_html = Response::view('patients/problemlist/problemlistloopvalue', compact('last_addin_problemlist'));
        $content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['draw'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;
        return Response::json($data);
    }
    
    public function getProblemListExport($patient_id='', $export=''){
        $api_response = $this->getListIndexApi($patient_id,$export);
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patient_Workbench_List_' . $date;

        if ($export == 'pdf') {
            $html = view('patients/problemlist/problemlist_export_pdf', compact('last_addin_problemlist', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'patients/problemlist/problemlist_export';
            $data['last_addin_problemlist'] = $last_addin_problemlist;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'patients/problemlist/problemlist_export';
            $data['last_addin_problemlist'] = $last_addin_problemlist;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

    public function ajaxUpdate($patient_id) {
        $api_response = $this->getIndexApi($patient_id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $practice = $api_response_data->data->practice;
            $last_addin_problemlist = $api_response_data->data->last_addin_problemlist;
            return view('patients/problemlist/problemlistloop', compact('patient_id', 'claims_list', 'last_addin_problemlist'));
        } else {
            return Redirect::to('/patients')->with('message', $api_response_data->message);
        }
    }

    public function create($id) {
        $api_response = $this->getCreateApi($id);
        $api_response_data = $api_response->getData();
        $practice = $api_response_data->data->practice;
        $claims_number = $api_response_data->data->claims_number;
        $claim_number = $api_response_data->data->claim_number;
        return view('patients/problemlist/createproblem', compact('id', 'practice', 'claims_number', 'claim_number'));
    }

    public function problemcreatestore(Request $request, $patient_id, $claim_id) {
        $request = Request::all();
        $api_response = $this->getProblemNewStoreApi($request, $patient_id, $claim_id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == "success") {
            $index = $this->index($patient_id);
            $get_data = $index->getdata();
            $last_addin_problemlist = $get_data['last_addin_problemlist'];
            return view('patients/problemlist/problemlistloop', compact('patient_id', 'last_addin_problemlist'));
        } else {
            print_r($api_response_data->message);
            exit;
        }
    }

    public function problemstore(Request $request, $patient_id, $claim_id) {
        $request = Request::all();
        $api_response = $this->getProblemStoreApi($request, $patient_id, $claim_id);
        $api_response_data = $api_response->getData();
        $problemlist = $api_response_data->data->problemlist;
        return view('patients/problemlist/problemdetaillist', compact('problemlist'));
    }

    public function show($patient_id, $id) {
        $api_response = $this->getShowApiList($patient_id, $id);
        $api_response_data = $api_response->getData();
        $problemlist = $api_response_data->data->problemlist;
        $practice = $api_response_data->data->practice;
        $patient_id = $api_response_data->data->patient_id;
        $claims_number = $api_response_data->data->claims_number;
        return view('patients/problemlist/show', compact('patient_id', 'claims_number', 'problemlist', 'practice', 'id'));
    }

    public function filteroption($id) {
        $api_response = $this->getIndexApi($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
            $practice = $api_response_data->data->practice;
            @$last_addin_problemlist = $api_response_data->data->last_addin_problemlist;
            @$loop = $api_response_data->data->loop;
            $patient_id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
            return view('patients/problemlist/problemlistloop', compact('patient_id', 'loop', 'claims_list', 'last_addin_problemlist'));
        } else {
            print_r($api_response_data->message);
            exit;
        }
    }

    public function getProblemList() {

        $heading = "AR";
        $heading_icon = "fa-laptop";
        $current_route =Route::getFacadeRoot()->current()->uri();
        $type = (strrpos($current_route, "myproblemlist") !== FALSE) ? "current" : "";
        $api_response = $this->getProblemListApi($type);
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
		$search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
		View::share('heading', 'Patient');
        $get_url = Route::getFacadeRoot()->current()->uri();
        $selected_tab = (strrpos($get_url, "myproblemlist") !== FALSE) ? "myproblemlist" : "problemlist";
        View::share('selected_tab', $selected_tab);

        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
        return view('armanagement/problemlist/problem_list', compact('heading', 'heading_icon','search_fields','searchUserData'));
    }
	
	public function getProblemListAjax(){
        $api_response = $this->getProblemListFilterApi();
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
		$view_html = Response::view('patients/problemlist/problemlistloopvalue', compact('last_addin_problemlist'));
		$content_html = htmlspecialchars_decode($view_html->getContent());
        $content = array_filter(explode("</tr>", trim($content_html)));
        $request = Request::all();
        if (!empty($request['draw']))
            $data['draw'] = $request['draw'];
        $data['data'] = $content;
        $data['datas'] = ['id' => 2];
        $data['recordsTotal'] = $api_response_data->data->count;
        $data['recordsFiltered'] = $api_response_data->data->count;
        return Response::json($data);
	}
	
    public function getWorkbenchListExport($type = '',$export=''){
        $api_response = $this->getProblemListFilterApi($type,$export);
        $api_response_data = $api_response->getData();
        $last_addin_problemlist = $api_response_data->data;
        $heading = ($type == 'myproblemlist') ? 'Assigned' : 'Total' ;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = $heading . '_Workbench_' . $date;
        
        if ($export == 'pdf') {
            $html = view('armanagement/problemlist/problemlist_export_pdf', compact('last_addin_problemlist', 'export', 'heading'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'armanagement/problemlist/problemlist_export';
            $data['last_addin_problemlist'] = $last_addin_problemlist;
            $data['export'] = $export;
            $data['heading'] = $heading;
            $data['file_path'] = $filePath;
            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'armanagement/problemlist/problemlist_export';
            $data['last_addin_problemlist'] = $last_addin_problemlist;
            $data['export'] = $export;
            $data['heading'] = $heading;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

}
