<?php namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use Auth;
use View;
use Config;
use App\Http\Controllers\Profile\Api\DashboardApiController as DashboardApiController;

class DashboardController extends DashboardApiController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	 
	public function __construct() { 
	  
		View::share ( 'heading', 'Notes' );    
	    View::share ( 'selected_tab', 'notes' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.notes'));
    }  
	/**
	 * This function using to display the all notes stored in database.
	 *
	 * 
	 * @return data to view page
	 */ 
	public function index()
	{
		$api_response = $this->getindexApi();
		$api_response_data = $api_response->getData();	
		$blogs1 = $api_response_data->data->blogs;
		$notes_list = $api_response_data->data->notes_list;
		$total_record     = count($blogs1);                
		$login_user = $api_response_data->data->login_user;
		$today_notes = $api_response_data->data->today_notes;
                $messages = $api_response_data->data->messages;
                $total_messages = count($messages);
        if ($api_response_data->status == 'success') {
			$data=$api_response_data->data;
			$events=$data->events;
			$users=$data->users_table;
			$blogs=$data->blogs;
			$PrivateMessageDetails = $api_response_data->data->message_inbox_list_arr;
			return view('profile/dashboard/dashboard', compact('events','blogs','PrivateMessageDetails','users','login_user','total_record','notes_list','today_notes','total_messages'));
        } else {
			return view('profile/profile');
		}
		
	}

	/**
	 * This function using to create and update notes in database.
	 *
	 * @param  use App\Models\Profile\PersonalNotes;
	 * @return data to view page
	 */
	public function store(Request $request){
		$api_response = $this->setnoteApi($request);
		$api_response_data = $api_response->getData();
		$notes_list = $api_response_data->data->notes_list;
		return view('profile/dashboard/dynamicnotes', compact('notes_list'));	
	}
	/**
	 * This function using to delete the already added notes in database.
	 *
	 * @param  use App\Models\Profile\PersonalNotes;
	 * @return data to view page
	 */
	public function destroy($id){
		$api_response = $this->setdeleteApi($id);
		$api_response_data = $api_response->getData();
		$notes_list = $api_response_data->data->notes_list;
		return view('profile/dashboard/dynamicnotes', compact('notes_list'));
	}
	/**
	 * This is using to retrieved the stored notes in database.
	 *
	 * @param  use App\Models\Profile\PersonalNotes;
	 * @return data to view page
	 */
	public function show($id){
		$api_response = $this->getnotesApi($id);
		$api_response_data = $api_response->getData();
		$notes_list = $api_response_data->data->notes_list;
		$resp['note_id'] = $notes_list[0]->id;
		if($notes_list[0]->date != '0000-00-00')
			$resp['date'] = date('m/d/Y',strtotime($notes_list[0]->date));
		else
			$resp['date'] = '';
		$resp['notes'] = $notes_list[0]->notes;
		echo json_encode($resp);
	}
	
}