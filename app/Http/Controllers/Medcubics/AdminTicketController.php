<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use DB;
use Config;
use Route;

class AdminTicketController extends Api\AdminTicketApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'ManageTicket' );  
		View::share ( 'selected_tab', 'admin/manageticket' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.ticket'));
    }  
	
	/*** Create new ticket form start ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$ticket_id = '';
			$message 	= '';
			$userlist	= $api_response_data->data->userlist;
			$assigneduserlist	= $api_response_data->data->assigneduserlist;
			return view('admin/newticket/create',compact('userlist','assigneduserlist','ticket_id','message'));	
		}
	}
	/*** Create new ticket form end ***/
	
	/*** Store ticket details start ***/
	public function store(Request $request)
	{
		$api_response = $this->postTicketApi($request::all());
		$api_response_data = $api_response->getData();
		$insertid = $api_response_data->data;
		if($api_response_data->status == 'success')
		{
			//return Redirect::to('ticket')->with('success', $api_response_data->message);
			$ticket_id = $api_response_data->data;
			$message = $api_response_data->message;
			return view('admin/newticket/create',compact('ticket_id','message'));
		}
		else
		{
			return Redirect::to('admin/createnewticket')->withInput()->withErrors($api_response_data->message);
		}    
	}
	/*** Store ticket details end ***/
}
