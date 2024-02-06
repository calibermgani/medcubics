<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Config;
use Illuminate\Http\Request;
use App\Http\Helpers\Helpers as Helpers;
use Redirect;

class SetUsersforPracticeController extends Api\SetUsersforPracticeApiController {
	public function __construct() {
        View::share ( 'heading', 'Customer' );  
		View::share ( 'selected_tab', 'admin/customerpractices' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
    } 
	
	public function index($customer_id,$practice_id,$customer_user_id)
	{	
		$api_response = $this->getIndexApi($customer_id,$practice_id,$customer_user_id);
		$api_response_data = $api_response->getData();
		$practice = $api_response_data->data->practices;			
		$customerusers = $api_response_data->data->customerusers;
		$practice_id = $api_response_data->data->practice_id;
		return view('admin/customer/setusersforpractice/setpracticeforusers', compact('customerusers','customer_id','customer_user_id','practice','practice_id'));
	}

	function myfunction($num)
	{
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
	
	public function edit($customer_id,$practice_id,$user_id)
	{
		$api_response = $this->getEditApi($customer_id,$practice_id,$user_id);
		$api_response_data = $api_response->getData();		
		$practice_permissions = $api_response_data->data->practice_permissions;
		$practices = $api_response_data->data->practices;
		$roles = $api_response_data->data->roles;	
		$roles 	 = array_flip(json_decode(json_encode($roles), True)); 
		$roles	 = array_flip(array_map(array($this,'myfunction'),$roles));
		
		$role_id = $api_response_data->data->role_id;
		$role_id = Helpers::getEncodeAndDecodeOfId($role_id,'encode');	
		$menus 	 = $api_response_data->data->menus;			
        $customerusers = $api_response_data->data->customerusers;
		$getActivePracticeAPI = $api_response_data->data->getActivePracticeAPI;
		
		$apilist = $api_response_data->data->apilist;
		$practice = $api_response_data->data->practice;
		$practice_id = $api_response_data->data->practice_id;
		$Setapiforusers = $api_response_data->data->Setapiforusers;
		$api_name 		= json_decode(json_encode($api_response_data->data->api_name), True);
		$apilist_subcat = $api_response_data->data->apilist_subcat;
		$modules = $api_response_data->data->module;	
		
		$returnHTML =  view('admin/customer/setusersforpractice/setpracticeforuserspopup', compact('apilist','practice','Setapiforusers','customerusers','customer_id','user_id','practice_id','role_id','pagepermissions','practice_permissions','practices','roles', 'menus','getActivePracticeAPI','apilist_subcat','api_name', 'modules'))->render();
		  return response()->json( array('success' => true, 'html'=>$returnHTML) );
	}

	
	public function update($customer_id,$practice_id,$user_id,Request $request)
	{
		$api_response = $this->getUpdateApi($customer_id,$practice_id,$user_id);
		$api_response_data = $api_response->getData();	

		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/customer/'.$customer_id.'/customerusers/'.$practice_id.'/setusersforpractice/'.$user_id.'/user')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/customer/'.$customer_id.'/customerusers/setpracticeforusers/'.$practice_id.'/user/'.$user_id.'/edit')->withInput()->withErrors($api_response_data->message);
		}  
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($customer_id,$user_id, $practice_id)
	{
		$api_response = $this->getDestroyApi($customer_id,$user_id, $practice_id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/customer/'.$customer_id.'/customerusers/'.$practice_id.'/setusersforpractice/'.$user_id.'/user')->with('success',$api_response_data->message);
		
	}

}
