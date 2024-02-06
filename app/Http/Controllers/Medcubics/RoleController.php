<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use Route;
use URL;
use Config;

class RoleController extends Api\RoleApiController 
{
	public function __construct() 
	{      
        View::share ( 'heading', 'Roles' );  
        View::share ( 'selected_tab', 'admin/medcubicsrole' );
        View::share( 'heading_icon', Config::get('cssconfigs.admin.role'));
    }  

	/*** Start to Medcubics Role Listing  ***/
	public function index()
	{	
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$roles = $api_response_data->data->roles;
		return view('admin/role/rolelist',  compact('roles'));
	}
	/*** End to Medcubics Role Listing  ***/
	
	/*** Start to Create the Role	 ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$roles = $api_response_data->data->roles;
		$get_prev = explode('/',URL::previous());
		return view('admin/role/create',  compact('roles','get_prev'));
	}
	/*** End to Create the Role	 ***/

	/*** Start to Store the Role ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		$insertid = $api_response_data->data;
        if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/role/'.$insertid)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/role/create')->withInput()->withErrors($api_response_data->message);
		}      
	}
	/*** End to Store the Role	 ***/

	/*** Start to Show the Role	 ***/
	public function show($id)
	{		
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$roles = $api_response_data->data->roles;
			return view ( 'admin/role/show', ['role' => $roles] );
		}
		else
		{
			return redirect('admin/medcubicsrole')->with('message',$api_response_data->message);
		}
	}
	/*** End to Show the Role	 ***/
	

	/*** Start to Edit the Role	 ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		$get_prev = array();
		if($api_response_data->status == 'success')
		{
			$roles = $api_response_data->data->roles;
			return view('admin/role/edit', compact('roles','get_prev'));
		}
		else
		{
			return redirect('admin/medcubicsrole')->with('message',$api_response_data->message);
		}
	}
	/*** End to Edit the Role	 ***/

	/*** Start to Update the Role	 ***/
	public function update($id, Request $request)
	{
		$redirect_request = $request::all();
		$api_response = $this->getUpdateApi($id, $request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			if($redirect_request['role_type']=='Practice')
			{
				return Redirect::to('admin/practicerole/'.$id)->with('success',$api_response_data->message);
			}	
			else
			{
				return Redirect::to('admin/medcubicsrole/'.$id)->with('success',$api_response_data->message);
			}
		}
		else
		{
			return Redirect::to('admin/role/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}       
	}
	/*** End to Update the Role	 ***/

	/*** Start to Destory the Role	 ***/
	public function destroy($id)
	{
		$api_response = $this->getDestroyApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/medcubicsrole')->with('success',$api_response_data->message);
	}
	/*** End to Destory the Role	***/
	
	/*** Start to Practice Role Listing  ***/
	public function practice_permission()
	{	
		$api_response = $this->getPracticePermissionApi();
		$api_response_data = $api_response->getData();
		$roles = $api_response_data->data->roles;
		return view('admin/role/rolelpracticelist',  compact('roles'));
	}
	/*** End to Practice Role Listing  ***/
}
