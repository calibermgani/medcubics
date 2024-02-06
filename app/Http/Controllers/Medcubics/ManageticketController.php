<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Controllers\Controller;
use View;
use Request;
use Redirect;
use Config;

class ManageticketController extends Api\ManageticketApiController 
{
	public function __construct() { 
		View::share ( 'heading', 'Tickets' );  
		View::share ( 'selected_tab', 'admin/manageticket' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.faq'));
	}  
	/**** Ticket List page start ***/
	public function index()
	{
		$api_response 		= $this->getIndexApi();
		$api_response_data 	= $api_response->getData();
		$ticket 			= $api_response_data->data->ticket;
		return view('admin/manageticket/manageticket',  compact('ticket'));
	}
	/**** Ticket List page end ***/
	
	/*** My Ticket List Page start ***/
	public function managemyticket()
	{
		$api_response 		= $this->manageMyticketApi();
		$api_response_data 	= $api_response->getData();
		$ticket 			= $api_response_data->data->ticket;
		return view('admin/manageticket/myticket',  compact('ticket'));
	}
	/*** My Ticket List Page end ***/
	
	/**** Ticket show page start ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success') {
			$get_ticket = $api_response_data->data->ticket;
                        $get_ticket_id = $api_response_data->data->ticket_id;
			$get_ticket_detail = $api_response_data->data->ticket_detail;
			$get_assigneelist = $api_response_data->data->get_assigneelist;
			
			return view('admin/manageticket/show',  compact('get_ticket','get_ticket_id','get_ticket_detail','get_assigneelist'));	
		} else {
			return Redirect::to('admin/manageticket')->with('error',$api_response_data->message);
		}
	}
	/**** Ticket show page end ***/
	
	/**** Ticket edit page start ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')	{
			$ticket 	= $api_response_data->data->ticket;
                        $ticket_id 	= $api_response_data->data->ticket_id;
			//$last_msg 	= $api_response_data->data->last_msg;
			return view('admin/manageticket/form', compact('ticket','ticket_id','last_msg'));
		} else {
			return Redirect::to('admin/manageticket')->with('error',$api_response_data->message);
		}
	}
	/**** Ticket edit page end ***/
	
	/**** Ticket Update page start ***/
	public function store($id,Request $request)
	{
		$api_response = $this->getUpdateApi($id,Request::all());
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'success') {
			$get_ticket 		= $api_response_data->data->ticket;
			$get_ticket_detail 	= $api_response_data->data->ticket_detail;
			return view('admin/manageticket/conversation',  compact('get_ticket','get_ticket_detail'));	
		} else {
			return 0;
		}        
	}
	/**** Ticket update page end ***/
	
	/**** Ticket delete page start *** /
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/manageticket')->with('success',$api_response_data->message);
	}
	/**** Ticket delete page end ***/
	
	/*** Get assign ticket start ***/
	public function assignticket($ticketid,$userid)
	{
		$api_response = $this->assignTicketApi($ticketid,$userid);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'success') {
			echo $message = $api_response_data->data->message;
		}
	}
	/*** Get assign ticket end ***/
	
	/*** Get medcubics user list start ***/
	public function getUserList($ticket_id,$user_id='')
	{
		$api_response = $this->getUserListApi($user_id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'success')	{
			$userlist = $api_response_data->data->userlist;			
			return view('admin/manageticket/userlist',  compact('userlist','ticket_id'));	
		}
	}
	/*** Get medcubics user list end ***/
	
}