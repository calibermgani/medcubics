<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use Auth;
use View;
use Config;
use App\Http\Controllers\Profile\Api\TaskApiController as TaskApiController;

class TaskController extends TaskApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 
	 public function __construct() { 
	  
		View::share ( 'heading', 'Tasks' );    
	    View::share ( 'selected_tab', 'task' ); 
		$api_response	    = $this->GetMessageApi();
		$api_response_data 	= $api_response->getData();
		View::share( 'users_list', $api_response_data->users_list);
		View::share( 'inbox_message', $api_response_data->inbox_message);
		View::share( 'message_count', $api_response_data->message_count);
		View::share( 'inbox_unread_count', $api_response_data->inbox_unread_count);
		View::share( 'label_list', $api_response_data->label_list);
		View::share( 'today_notes', $api_response_data->today_notes);
		View::share( 'heading_icon', Config::get('cssconfigs.patient.file_text'));
    }  
	 
	public function index()
	{
		$api_response = $this->getindexApi();
		$api_response_data = $api_response->getData();	
		$blogs1 = $api_response_data->data->blogs;
		$today_notes = $api_response_data->data->today_notes;
		$total_record     = count($blogs1);
        if ($api_response_data->status == 'success') {
			$data=$api_response_data->data;
			$events=$data->events;
			$users=$data->users_table;
			$blogs=$data->blogs;
			$PrivateMessageDetails = $api_response_data->data->message_inbox_list_arr;
			$login_user = $api_response_data->data->login_user;
			return view('profile/task/task', compact('events','blogs','PrivateMessageDetails','users','login_user','total_record','today_notes'));
        } else {
			return view('profile/profile');
		}		
	}
	
}