<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use View;
use Config;
use Request;

class UserActivityController extends Api\UserActivityApiController
 {
      public function __construct()
	{      
        View::share( 'heading', 'Users' );  
        View::share( 'selected_tab', 'practice/useractivity' );
        View::share( 'heading_icon', Config::get('cssconfigs.common.user'));
    }  

	public function index($request='')
	{
		$api_response = $this->getIndexApi($request);
		$api_response_data = $api_response->getData();
		$useractivity = $api_response_data->data->user_activity_list;
		$user = $api_response_data->data->user;
		$user_id = $api_response_data->data->user_id;
		if(Request::ajax())
		{
			return view('practice/useractivity/useractivitylisttable',  compact('useractivity')); 
		}
		else
		{
			return view('practice/useractivity/useractivitylist',  compact('user_id','useractivity','user'));
		}
			
	}
}
