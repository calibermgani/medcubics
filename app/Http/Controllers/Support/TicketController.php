<?php namespace App\Http\Controllers\Support;

use Request;
use Redirect;
use Auth;
use View;
use DB;
use Route;
use Config;
use Mail;

class TicketController extends Api\TicketApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Ticket' );  
		View::share ( 'selected_tab', 'Ticket' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.ticket'));
    }  
	
	public function index()
	{
		$this->getTicketApi();
		$ticket_id = '';
		$message 	= '';
		return view('support/ticket/create',compact('ticket_id','message'));
	}
	
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
			return view('support/ticket/create',compact('ticket_id','message'));
		}
		else
		{
			return Redirect::to('ticket')->withInput()->withErrors($api_response_data->message);
		}    
	}
	
	public function emailticket()
	{
		try	{
			Mail::send('emails.general', ['msg' => 'Hi this is new user'], function($message)
			{
				$message->to('medcubics@gmail.com', 'John Smith')->subject('Welcome!');
			}); 
		} catch (\Exception $e) {
			dd($e->getMessage());
		}		
		mail("medcubics@gmail.com","My subject",'Hi this is new user');		
		echo 'sent';
	}
	
}