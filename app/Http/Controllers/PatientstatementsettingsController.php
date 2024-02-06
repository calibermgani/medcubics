<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class PatientstatementsettingsController extends Api\PatientstatementsettingsApiController 
{
	public function __construct()
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'patientstatementsettings' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    } 
	/*** lists page Starts ***/
	public function index()
	{		
		$api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
		$facility =$api_response_data->data->facility;
		$provider =$api_response_data->data->provider; 
		$category =$api_response_data->data->category; 
		$psettings =$api_response_data->data->psettings;
		$address_flags 		= (array)$api_response_data->data->addressFlag;
        $address_flag['general'] = (array)$address_flags['general'];
		return view('practice/patientstatementsettings/edit',  compact('facility','provider','psettings','address_flag', 'category'));
	}
	/*** Lists Function Ends ***/
	
	// Get address details.
	public function getaddress()
	{
		$api_response = $this->getaddressApi();
		$api_response_data = $api_response->getData();
		$practice 	   = $api_response_data->data->practice;
		$facility 	   = $api_response_data->data->facility;
		$provider 	   = $api_response_data->data->provider;
		return view('practice/patientstatementsettings/getaddress',  compact('practice','facility','provider'));
	}
	
	
	/*** Store Function Starts ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi(Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patientstatementsettings')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('patientstatementsettings')->withInput()->withErrors($api_response_data->message);
		}       
	}
	/*** Store Function Ends ***/
}
