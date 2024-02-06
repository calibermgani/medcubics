<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Speciality as Speciality;
use App\Http\Helpers\Helpers as Helpers;
use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use Session;
use View;
use Config;

class UserApiSettingsController extends Api\UserApiSettingsApiController
{
	
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'userapisettings' );  
		View::share ( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** Listing the code end ***/
	
	function myfunction($num)
	{
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
	
	public function index()
	{
		$api_response 	= $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$practiceApiList = $api_response_data->data->practiceApiList;
		$apilist 		 = $api_response_data->data->apilist;
		$apilist_arr 	 = json_decode(json_encode($apilist), True);
		$userlist 	 	 = $api_response_data->data->userlist;
		$maincat_api  = $api_response_data->data->maincat_api;
		
		$userlist_arr 	 = array_flip(json_decode(json_encode($userlist), True));  
		$userlist_arr	 = array_flip(array_map(array($this,'myfunction'),$userlist_arr));
		
		return view('practice/userapisettings/view',  compact('practiceApiList','apilist_arr','userlist_arr','maincat_api'));
	}
	/*** Listing the code end ***/
	
	/*** Store the code start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('userapisettings')->with('success', $api_response_data->message);
		}
	}
	/*** Store the code end ***/
}