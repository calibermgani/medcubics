<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use View;
use Config;

class RegistrationController extends Api\RegistrationApiController 
{
	public function __construct()
	{ 
		View::share ( 'heading', 'Registration' );  
		View::share ( 'selected_tab', 'registration' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.registration'));
    } 
	/*** lists page Starts ***/
	public function index()
	{		
		$api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
		if($api_response_data->status == "success")
		{
			$registration =$api_response_data->data->registration;
			return view('practice/registration/edit',  compact('registration'));
		} 
		else
		{
			return redirect()->back();
		}
	}
	/*** Lists Function Ends ***/
	
	/*** Store Function Starts ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi(Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('registration')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('registration')->withInput()->withErrors($api_response_data->message);
		}       
	}
	/*** Store Function Ends ***/
}
