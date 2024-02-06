<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use Request;
use Redirect;
use Auth;
use View;
use Config;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class ApiConfigController extends Api\ApiConfigApiController
{
	public function __construct() 
	{ 
       View::share ( 'heading', 'API Config' );  
	   View::share ( 'selected_tab', 'apiconfig' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.admin.role'));
       $dbconnection = new DBConnectionController();	
  		View::share ( 'checkpermission', $dbconnection );
    }  

	/*** Start to List the API ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$apiconfig = $api_response_data->data->apiconfig;
		return view('admin/apiconfig/apiconfig',  compact('apiconfig'));
	}
	/*** End to List the API ***/
	
	/*** Start to Create the API List ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		return view('admin/apiconfig/create');
	}
	/*** End to Create the API List	 ***/

	/*** Start to store the API List ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
			{
				// $response_apiid 	 = Helpers::getEncodeAndDecodeOfId($api_response_data->data,'encode');  
				return Redirect::to('admin/apiconfig/')->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::to('admin/apiconfig/create')->withInput()->withErrors($api_response_data->message);
			}  
		
	}
	/*** End to store the API List	 ***/

	/*** Start to show the API List ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$apiconfig = $api_response_data->data->apiconfig;
			return view ('admin/apiconfig/show',compact('apiconfig'));
		}
		else
		{
			return Redirect::to('admin/apiconfig')->with('message',$api_response_data->message);
		}
	}
	/*** End to show the API List	 ***/
	
	/*** Start to edit the API List ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$apiconfig = $api_response_data->data->apiconfig;
			return view('admin/apiconfig/edit',  compact('apiconfig'));
		}
		else
		{
			return Redirect::to('admin/apiconfig')->with('message',$api_response_data->message);
		}
	}
	/*** End to edit the API List	 ***/
	
	/*** Start to update the API List ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$id 		 = Helpers::getEncodeAndDecodeOfId($id,'encode'); 
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/apiconfig/')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/apiconfig/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to update the API List	 ***/
	
	/*** Start to delete the API List ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/apiconfig/')->with('success',$api_response_data->message);
	}
	/*** End to delete the API List	 ***/
}
