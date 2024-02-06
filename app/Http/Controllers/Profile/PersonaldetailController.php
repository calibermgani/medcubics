<?php namespace App\Http\Controllers\Profile;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Request;
use Config;
use Redirect;

class PersonaldetailController extends Api\PersonaldetailApiController {
	 public function __construct() { 
	  
		View::share ( 'heading', 'Profile' );    
	    View::share ( 'selected_tab', 'personaldetails' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }  
	 
	public function personaldetail($id)
	{
		$api_response 		= $this->personaldetailApi($id);
		$api_response_data 	= $api_response->getData();
        $customers 					= $api_response_data->data->customers;
		$address_flags 				= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] 	= (array)$address_flags['general'];
		return view('profile/personaldetail/edit', compact('customers','address_flag','tabs'));
	}
        
    public function personaldetailview($id)
	{
		$api_response 		= $this->personaldetailApi($id);
		$api_response_data 	= $api_response->getData();
        $customers = $api_response_data->data->customers;
        $blogs = $api_response_data->data->blogs;
        $total_blogs     = count($blogs);
        $notes = $api_response_data->data->notes;
        $total_notes = count($notes);
        $messages = $api_response_data->data->messages;
        $total_messages = count($messages);
		$address_flags 				= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] 	= (array)$address_flags['general'];
		return view('profile/personaldetail/view', compact('customers','address_flag','tabs','total_blogs','total_notes','total_messages'));
	}
	
	public function updatedetail($id, Request $request)
	{
		$api_response 		= $this->UpdateApi($id, $request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success') {
			return Redirect::to('profile/personaldetailsview/'.$id)->with('success',$api_response_data->message);
		} else {
			return Redirect::to('profile/personaldetails/'.$id)->withInput()->withErrors($api_response_data->message);
		}  
	}
	
	public function avatarpersonal($id,$picture_name)
	{
		$api_response 		= $this->avatarapipicture($id,$picture_name);
		$api_response_data 	= $api_response->getData();
                return "success";
	}
}