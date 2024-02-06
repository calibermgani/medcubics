<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Config;
use Redirect;

use Illuminate\Http\Request;

class AdminPasswordController extends Api\AdminPasswordApiController {
	public function __construct() 
	{      
        View::share ( 'heading', 'Admin' );  
        View::share ( 'selected_tab', 'admin/adminpassword' );
        View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    } 
	public function index()
	{	
		return view('admin/adminpassword/changepassword');
	}
	public function updatepassword()
	{
		$api_response = $this->postchangepasswordApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
		###Success page#
			return Redirect::to('admin/userpassword')->with('success', $api_response_data->message);
		}
		else
		{
			##Error page redirect##
			return Redirect::to('admin/userpassword')->withInput()->with('error',$api_response_data->message);	
		}
	}
}
