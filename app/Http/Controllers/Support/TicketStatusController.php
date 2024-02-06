<?php namespace App\Http\Controllers\Support;

use Request;
use Redirect;
use Auth;
use View;
use DB;
use Config;

class TicketStatusController extends Api\TicketStatusApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Ticket' );  
		View::share ( 'selected_tab', 'Ticket' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.ticket'));
    }  
	
	public function index()
	{
		$this->getTicketStatusApi();
		return view('support/ticketstatus/create');
	}
	
	public function getTicketDetail($id,$page='')
	{
		echo $this->getTicketDetailApi($id,$page);
	}
	
	public function getReplyTicket(Request $request)
	{
		echo $api_response = $this->getReplyTicketApi($request::all());
	}
	
}