<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Practice as Practice;
use App\Contactdetail as Contactdetail;
use App\Http\Helpers\Helpers as Helpers;
use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use View;
use Lang;
use Session;
use Config;

class ContactdetailController extends Api\ContactdetailApiController 
{
	public function __construct() 
	{      
		View::share('heading','Practice');
		View::share('selected_tab','practice'); 
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/********************** Start Display a details of the contact ***********************************/
	public function index($id=1)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		
		//dd($address_flag['general']['zip5']);

		if($api_response_data->status == 'success')
		{
			$contact_detail =  $contactdetails = $api_response_data->data->contact_detail;
			$practice		 	= 	$api_response_data->data->practice;
			$address_flags 		= (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['billing_service'];
			$address_flag['billing_service'] = (array)$address_flags['billing_service'];
			if($contactdetails->updated_by <> 0)
				return view('practice/practice/contactdetail/show',compact('contact_detail','practice','address_flag'));
			else
				return view('practice/practice/contactdetail/add_contactdetail',compact('contactdetails','practice','address_flag'));
		}
		else
		{
			return view('practice/practice/contactdetail/add_contactdetail',compact('contactdetails','practice','address_flag'));
			/* $practice_dbid = Helpers::getEncodeAndDecodeOfId(Session::get('practice_dbid'),'encode');
			return redirect('practice/'.$practice_dbid)->withErrors($api_response_data->message); */
		}
	}
	/********************** End Display a details of the contact ***********************************/
	
	/********************** Start Display contact edit page ***********************************/
	public function edit($id)
	{
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$contactdetails 	= $api_response_data->data->contactdetail;
			$practice 			= $api_response_data->data->practice;
			$address_flags 		= (array)$api_response_data->data->addressFlag;
			$address_flag['billing_service'] = (array)$address_flags['billing_service'];
			return view('practice/practice/contactdetail/add_contactdetail',compact('contactdetails','practice','address_flag'));
		}
		else
		{
			 return Redirect::to('contactdetail')->with('message', $api_response_data->message);
		}
	}
	/********************** End Display contact edit page ***********************************/
	
	/********************** Start contact update process ***********************************/
	public function update($id, Request $request)
	{
		$api_response 		= $this->getUpdateApi($request::all(), $id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('contactdetail')->with('message', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('contactdetail')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('contactdetail/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/********************** End contact update process ***********************************/
	
	

}
