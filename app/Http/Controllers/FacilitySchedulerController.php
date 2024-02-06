<?php namespace App\Http\Controllers;
use Auth;
use Request;
use Redirect;
use View;
use Config;
use App\Http\Controllers\Api\FacilitySchedulerApiController as FacilitySchedulerApiController;
use PDF;
use Excel;
use App\Exports\BladeExport;

class FacilitySchedulerController extends FacilitySchedulerApiController 
{
	public function __construct()
	{
        View::share('heading', 'Practice');
        View::share('selected_tab', 'scheduler');
        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	/********************** Start Display a listing of the Facility scheduler ***********************************/
	public function index()
	{		
		$api_response 		= $this->getIndexApi();
		$api_response_data 	= $api_response->getData();		
		$facilities 		= $api_response_data->data->facilities;
		
		if(Request::ajax())
		{
			return view ( 'practice/scheduler/facilitytablelist', compact ( 'facilities') );
		}
		else
		{
			$all_speciality 	= $api_response_data->data->all_speciality;
			$all_pos 			= $api_response_data->data->all_pos;
			//$all_short_nam 		= $api_response_data->data->all_short_nam;
			//$all_facl_nam		= $api_response_data->data->all_facl_nam;
			$scheduler_tab_type = "facility";
			return view ( 'practice/scheduler/facilitylist', compact ( 'facilities','scheduler_tab_type','all_speciality','all_pos') );
		}
	}
	/********************** End Display a listing of the Facility scheduler ***********************************/
    public function getFacilitySchedulerExport($export=''){
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $facility_scheduler = $api_response_data->data->facilities;
        $all_speciality = $api_response_data->data->all_speciality;
	$all_pos = $api_response_data->data->all_pos;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Facility_Scheduler_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/scheduler/facility_scheduler_export_pdf', compact('facility_scheduler', 'all_speciality', 'all_pos', 'export'));
            return PDF::loadHTML($html, 'A4')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/scheduler/facility_scheduler_export';
            $data['facility_scheduler'] = $facility_scheduler;
            $data['all_speciality'] = $all_speciality;
            $data['all_pos'] = $all_pos;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/scheduler/facility_scheduler_export';
            $data['facility_scheduler'] = $facility_scheduler;
            $data['all_speciality'] = $all_speciality;
            $data['all_pos'] = $all_pos;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	/********************** Start view facility scheduler list page ***********************************/
	public function viewFacilityScheduler($facility_id)
	{
		$api_response 		= $this->viewFacilitySchedulerApi($facility_id);
		$api_response_data 	= $api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$facilityschedulers = $api_response_data->data->facilityschedulers;
			$facility 			= $api_response_data->data->facility;
			return view('practice/scheduler/facility_scheduler',compact('facilityschedulers','facility'));
		}
		else
		{
			return Redirect::to('practicefacilityschedulerlist')->with('error', $api_response_data->message);
		}
	}
	/********************** End view facility scheduler list page ***********************************/
	
	public function facilityScheduledListExport($facility_id = '', $export='') {
        $api_response = $this->viewFacilitySchedulerApi($facility_id);
        $api_response_data = $api_response->getData();
        $facilityschedulers = $api_response_data->data->facilityschedulers;
        $facility = $api_response_data->data->facility;
        $date = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m-d-Y');
        $name = 'Facility_Scheduled_List_' . $date;

        if ($export == 'pdf') {
            $html = view('practice/scheduler/facility_scheduled_list_export_pdf', compact('facility', 'facilityschedulers', 'export'));
            return PDF::loadHTML($html)->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            $filePath = 'practice/scheduler/facility_scheduled_list_export';
            $data['facility'] = $facility;
            $data['facilityschedulers'] = $facilityschedulers;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            //return Excel::download(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            $filePath = 'practice/scheduler/facility_scheduled_list_export';
            $data['facility'] = $facility;
            $data['facilityschedulers'] = $facilityschedulers;
            $data['export'] = $export;
            return Excel::download(new BladeExport($data,$filePath), $name.'.csv');
        }
    }
	
	/********************** Start view facility scheduler details individual ***********************************/
	public function viewFacilitySchedulerDetailsById($facility_id, $id)
	{
		$api_response 							= $this->viewFacilitySchedulerDetailsByIdApi($facility_id, $id);
		$api_response_data 						= $api_response->getData();
		if ($api_response_data->status == 'success') 
		{	
			$facility_schedulers_dates_listing_arr 	= $api_response_data->data->facility_schedulers_dates_listing_arr;
			$facilityschedulers 					= $api_response_data->data->facilityschedulers;
			$facility 								= $api_response_data->data->facility;
			return view('practice/scheduler/facility_scheduler_details',compact('facility_schedulers_dates_listing_arr','facilityschedulers','facility'));
		} 
		else 
		{
            return Redirect::to('practicefacilityschedulerlist')->with('error', $api_response_data->message);
        }
	}
	/********************** End view facility scheduler details individual ***********************************/
		
	public function destroy($facility_id, $id)
	{
		$api_response 		= 	$this->getDeleteApi($facility_id, $id);
		$api_response_data 	= 	$api_response->getData();
		//dd($api_response_data->status);
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facilityscheduler/facility/'.$facility_id)->with('success', $api_response_data->message );
		}
		else
		{
			return Redirect::to('facilityscheduler/facility/'.$facility_id)->with('error', $api_response_data->message);
		}
	}	
}
