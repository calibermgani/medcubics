<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use View;
use Request;
use Redirect;
use Config;
use App\Http\Controllers\Controller;

class LogController extends Api\LogApiController 
{
	public function __construct() { 
		View::share ( 'heading', 'Tickets' );  
		View::share ( 'selected_tab', 'admin/log' );
		View::share( 'heading_icon', "fa-question");
	} 
	
	/**** LOG List page start ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
        $log_data =  $api_response_data->data->log_data;
		return view('admin/log/log',  compact('log_data'));
	}
	/**** LOG List page end ***/
	
	public function view_log($file_name){ 
		$api_response = $this->getViewLogApi($file_name);
		$api_response_data = $api_response->getData();
        $file_content =  $api_response_data->data->file_content;
		return view('admin/log/show',  compact('file_content'));
	}
	
	
}