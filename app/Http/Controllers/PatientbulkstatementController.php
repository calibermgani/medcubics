<?php

namespace App\Http\Controllers;

use Request;
use Response;
use Redirect;
use Auth;
use View;
use Config;
use App\Models\PatientStatementSettings as PatientStatementSettings;

class PatientbulkstatementController extends Api\PatientbulkstatementApiController {

    public function __construct() {
        View::share('heading', 'Practice');
        View::share('selected_tab', 'patientbulkstatement');
        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }

    /*     * * lists page Starts ** */

    public function indexTableData() {

        $api_response = $this->getBulkStatementPatListApi();
        $api_response_data = $api_response->getData();
        $psettings = $api_response_data->data->psettings;
        $patients_arr = $api_response_data->data->patients_arr;
        $patient_balance = []; //json_decode(json_encode($api_response_data->data->patient_balance), True);
        $get_currentweek = $this->weekOfMonth(date('Y-m-d'));

        $view_html = Response::view('practice/patientstatementsettings/statement_list', compact('get_currentweek', 'psettings', 'patients_arr', 'patient_balance', 'insurance_list', 'patLastPmtArr'));

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
    
    public function getPatientBulkStatementExport($export = '') {
        $api_response = $this->getBulkStatementPatListApi($export);
        $api_response_data = $api_response->getData();
        $patients_arr = $api_response_data->data->patients_arr;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Patients_BulkStatement_list_' . $date;
        
        if ($export == 'xlsx') {
            $filePath = 'practice/patientstatementsettings/bulkstatement_export';
            $data['patients_arr'] = $patients_arr;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
        }
    }

    public function index() {
        //$api_response = $this->getIndexApi();
        //$api_response_data = $api_response->getData();
        $psettings = [];  // $api_response_data->data->psettings;
        $patients_arr = []; // $api_response_data->data->patients_arr;
        $patient_balance = []; //$api_response_data->data->patient_balance;
        $patient_balance = []; //json_decode(json_encode($patient_balance), True);
        $insurance_list = []; //(array) $api_response_data->data->insurances;
        $get_currentweek = $this->weekOfMonth(date('Y-m-d'));
        $psettings = PatientStatementSettings::first();
        return view('practice/patientstatementsettings/bulkstatement', compact('get_currentweek', 'psettings', 'patients_arr', 'patient_balance', 'insurance_list'));
    }

    /*     * * Lists Function Ends ** */

    /*     * * Store Function Starts ** */

    public function store() {
        $api_response = $this->getStoreApi();
        if (empty($api_response))
            return Redirect::to('bulkstatement')->with('error', '');

        $api_response_data = $api_response->getData();

        if ($api_response_data->status == 'failure') {
            return Redirect::to('bulkstatement')->with('error', $api_response_data->message);
        }

        if ($api_response_data->status == 'success') {
            return Redirect::to('bulkstatement')->with('success', $api_response_data->message);
        }
    }

}
