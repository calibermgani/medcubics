<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Redirect;
use Auth;
use View;
use Config;

class UserLoginHistoryController extends Api\UserLoginHistoryApiController
{
	public function __construct() { 
      
       View::share ( 'heading', 'Practice' );  
	   View::share ( 'selected_tab', 'userLoginHistory' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));

    }  
	/*** Reason for for visit Lising ***/
	public function index($type)
	{	
		$api_response = $this->getIndexApi($type);
		$api_response_data = $api_response->getData();
		$userLoginInfo = $api_response_data->data->userLoginInfo;
		return view('practice/userloginhistory/userloginhistory',compact('userLoginInfo'));
	}
	
	public function userStatusChange(){
		$api_response = $this->userStatusChangeApi();
		$api_response_data = $api_response->getData();
		return $userLoginInfo = $api_response_data->status;
	}
	public function userIpSecurityCodeRest(){
		$api_response = $this->userIpSecurityCodeRestApi();
		$api_response_data = $api_response->getData();
		return $userLoginInfo = $api_response_data->status;
	}
	
}
