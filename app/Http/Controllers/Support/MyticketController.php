<?php namespace App\Http\Controllers\Support;

use Request;
use Redirect;
use Auth;
use View;
use DB;
use Route;
use Config;

class MyticketController extends Api\MyticketApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Ticket' );  
		View::share ( 'selected_tab', 'Ticket' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.ticket'));
    }  
	
	public function index()
	{
		$api_response 		= $this->getMyTicketApi();
		$api_response_data 	= $api_response->getData();
		$ticket 			= $api_response_data->data->ticket;
		if(Request::ajax())
        {           
            return view('support/myticket/myticket_list',compact('ticket'));
        }
		else
		{
			return view('support/myticket/myticket',  compact('ticket'));
		}	
	}
	
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$get_ticket = $api_response_data->data->ticket;
			$get_ticket_detail = $api_response_data->data->ticket_detail;
			$getlastticket = $api_response_data->data->getlastticket;
			return view('support/myticket/show',  compact('get_ticket','get_ticket_detail','getlastticket'));	
		}
		else
		{
			return Redirect::to('support/myticket/myticket')->with('error',$api_response_data->message);
		}
	}
	
	public function removeReply($ticketid,$replyid)
	{
		echo $this->removeReplyApi($ticketid,$replyid);
	}
}