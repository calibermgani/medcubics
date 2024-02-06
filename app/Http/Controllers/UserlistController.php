<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Request;
use Config;

class UserlistController extends Api\UserlistApiController {

	public function __construct() { 
		View::share ( 'heading', 'Users' );  
		View::share ( 'selected_tab', 'users' );
		View::share( 'heading_icon',  Config::get('cssconfigs.common.user'));
	}  
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$user_practices = $api_response_data->data->user_practices;
		return view('practice/user/users',  compact('user_practices'));
	}

	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		$user_practice = $api_response_data->data->user_practices;
		return view('practice/user/show',  compact('user_practice'));
	}

}

