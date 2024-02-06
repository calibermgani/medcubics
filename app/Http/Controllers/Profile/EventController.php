<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use Auth;
use View;
use App\Http\Controllers\Profile\Api\EventApiController as EventApiController;

class EventController extends EventApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 
	public function __construct() { 
       View::share ( 'heading', 'Profile' );    
	    View::share ( 'selected_tab', 'profile' ); 
		View::share( 'heading_icon', 'user');
    }  
	 
	public function index()
	{
		$api_response = $this->getindexApi();  
		//dd($api_response);		
        $api_response_data = $api_response->getData();		
        if ($api_response_data->status == 'success') {
			$data=$api_response_data->data;
			$reminder=$data->reminder;
			return view('profile/calendar/show', compact('reminder'));
        } else {
			return view('profile/calendar/show');
		}
	}

	public function getEventCreate(Request $request,$id=""){
		
		$request = $request::all();
		$api_response = $this->getEventCreateApi($request,$id);        
        $api_response_data = $api_response->getData();	
        if ($api_response_data->status == 'success') {
			$status = $api_response_data->status;
			print_r($status);exit;
        }
	}

	public function getCalendarAdd(){
		$api_response = $this->getCalendarAddApi();        
         $api_response_data = $api_response->getData();	
        if ($api_response_data->status == 'success') {
			$select_list = $api_response_data->data->select_list;
			//dd($select_list);
			return view('profile/calendar/add-event',compact('select_list'));
		}
	}

	public function getCalendarshow(){
		$api_response = $this->getCalendarshowApi();        
         $api_response_data = $api_response->getData();	
		
        if ($api_response_data->status == 'success') {
			$reminder=$api_response_data->data;
			$today_events = $api_response_data->data->today_events;
			$reminder = $api_response_data->data->reminder;
			$total_date = $api_response_data->data->total_date;
			return view('profile/calendar/calendar-show', compact('reminder','total_date','today_events'));
        }
	}

	public function getCalendaredit($id){
		$api_response = $this->getCalendareditApi($id);        
        $api_response_data = $api_response->getData();	
        if ($api_response_data->status == 'success') {
			$reminder=$api_response_data->data;
			$reminder = $api_response_data->data->reminder;
			$select_list = $api_response_data->data->select_list;
			return view('profile/calendar/popup-edit', compact('reminder','select_list'));
        }
	}

	public function getCalendarshowTimestamp($timestamp){
		$api_response = $this->getCalendarshowTimestampApi($timestamp);        
         $api_response_data = $api_response->getData();	
		//dd( $api_response_data);
        if ($api_response_data->status == 'success') {
			$reminder=$api_response_data->data;
			
			return view('profile/calendar/date-show', compact('reminder'));
        }
	}

	public function getEventDelete($id)
	{
		$api_response = $this->getEventDeleteApi($id);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') {
			$data= $api_response_data->data;
			$start_date= $api_response_data->message;
			//dd($start_date);
			echo $data.",".$start_date;exit;
        } else {
            return redirect()->back()->with('error', $api_response_data->message);
        }
	}
}