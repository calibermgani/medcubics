<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Redirect;
use Config;

class EmailTemplateController extends Api\EmailTemplateApiController
 {
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );    
		View::share ( 'selected_tab', 'emailtemplate' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	/*** start to list the page ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$emailtemplate		= 	$api_response_data->data->emailtemplate;
		$email_template_count	= 	$api_response_data->data->email_template_count;
		return view ( 'practice/emailtemplate/emailtemplate', compact ( 'emailtemplate', 'email_template_count'));
	}
	/*** end to list the page ***/
	
	/*** start to update the email template content ***/
	public function update()
	{
		$api_response 		= 	$this->getUpdateApi();
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('emailtemplate')->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to update the email template content ***/	
}
