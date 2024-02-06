<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class UserActivityController extends Api\UserActivityApiController 
{
	public function __construct()
	{      
        View::share( 'heading', 'Customers' );  
        View::share( 'selected_tab', 'admin/useractivity' );
        View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
    }  

	/*** Start to Listing the User Activity ***/
	public function index()
	{	
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
		$user = $api_response_data->data->user;
		$practice = $api_response_data->data->practice;
		$module = $api_response_data->data->module;
		$user_id = '';
		$practice_id ='';
		return view('admin/useractivity/useractivitylist',  compact('user_id','user','practice_id','practice','module'));
	}
	/*** End to Listing the User Activity ***/

 
	/*** Start to Search the User Activity ***/
    public function store()
	{
		$data = Request::all();
		$api_response = $this->getUserRecordApi($data);
		//dd($api_response);
		$api_response_data = $api_response->getData();
		$useractivity = $api_response_data->data->useractivity;
		//$get_practice = $api_response_data->data->practice;
		//$user = $api_response_data->data->user;
		$user_id = $api_response_data->data->get_userid;
		return view('admin/useractivity/useractivitymodulelist',  compact('user_id','useractivity','user','get_practice')); 
	}
	/*** End to Search the User Activity ***/
}