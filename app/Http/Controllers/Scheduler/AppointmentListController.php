<?php namespace App\Http\Controllers\Scheduler;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Scheduler\Api\AppointmentListApiController as AppointmentListApiController;
use View;
use Request;
use  Response;
use PDF;
use Excel;
use App\Exports\BladeExport;
use App\Models\Medcubics\Users as Users;

class AppointmentListController extends AppointmentListApiController {
	 public function __construct()
    {
		//Icon, selected tab
        View::share ( 'heading', 'Scheduler' );
        View::share ( 'selected_tab', 'Reports' );
        View::share( 'heading_icon', 'fa-calendar-o');
    }
	/**
	 * Display a listing of the resource.
	 *
	 *
	 * @return Response
	 */
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
        $api_response_data 	= 	$api_response->getData();
        $patient_app  	= 	$api_response_data->data->patient_app;
        $search_fields = $api_response_data->data->search_fields;       
        $searchUserData = $api_response_data->data->searchUserData; 

        return view ( 'scheduler/listing/appointment', compact ('patient_app','search_fields','searchUserData') );
	}

	/***
		AJAX Request Check box click event
	***/	
	public function getapointmentajax($type= '')
	{

		$value = ($type== "type")?null:"all";
		$api_response 		= 	$this->getIndexApi($value);
        $api_response_data 	= 	$api_response->getData();
        $patient_app  	= 	$api_response_data->data->patient_app;
        return view ( 'scheduler/listing/appointmentlist', compact ('patient_app') );
	}
	public function schedulerTableData($appCheck='') {  
    	
    	($appCheck == '') ? null : $appCheck;

		$api_response 	= $this->getIndexApi('',$appCheck);		
        $api_response_data = $api_response->getData();
		$patient_app 		= (array)$api_response_data->data->patient_app;

		$view_html 		= Response::view ( 'scheduler/listing/appointmentlist', compact ('patient_app') );
		$content_html 	= htmlspecialchars_decode($view_html->getContent());
		$content 		= array_filter(explode("</tr>",trim($content_html)));
		$request = Request::all();	
		if(!empty($request['draw']))
		$data['draw'] = $request['draw'];
		$data['data'] = $content;	
		$data['datas'] = ['id'=>2 ];	
		$data['recordsTotal'] = $api_response_data->data->count;
		$data['recordsFiltered'] = $api_response_data->data->count;		
		return Response::json($data);
    }
    public function schedulerTableDataExport($export = '', $type = '', $appCheck = '') {

        ($type != 'all' ) ? null : $type;
        ($appCheck == '') ? null : $appCheck;
        $api_response = $this->getIndexApi($export, $type, $appCheck);
        $api_response_data = $api_response->getData();
        $patient_app = (array) $api_response_data->data->patient_app;
        $search_by = $api_response_data->data->get_list_header;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Appointmentslist_' . $date;

        if ($export == 'pdf') {
            $html = view('scheduler/listing/appointmentlist_export_pdf', compact('patient_app', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'scheduler/listing/appointmentlist_export';
            $data['patient_app'] = $patient_app;
            $data['search_by'] = $search_by;
            $data['title'] = "Appointments List";
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            // return Excel::download(new BladeExport($data,$filePath), $name.'.xls');

        } elseif ($export == 'csv') {
            $filePath = 'scheduler/listing/appointmentlist_export';
            $data['patient_app'] = $patient_app;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

}
