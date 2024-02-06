<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use Request;
use Redirect;
use Auth;
use View;
use Config;

class ApiListController extends Api\ApiListApiController
{
	public function __construct() 
	{ 
       View::share ( 'heading', 'API List' );  
	   View::share ( 'selected_tab', 'apilist' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.admin.role'));
    }  

	/*** Start to List the API ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$apilist = $api_response_data->data->apilist;
		return view('admin/apilist/apilist',  compact('apilist'));
	}
	/*** End to List the API ***/
	
	/*** Start to Create the API List ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		return view('admin/apilist/create');
	}
	/*** End to Create the API List	 ***/

	/*** Start to store the API List ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
	
		if($api_response_data->status == 'success')
			{
				$response_apiid 	 = Helpers::getEncodeAndDecodeOfId($api_response_data->data,'encode');  
				return Redirect::to('admin/apilist/'.$response_apiid)->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::to('admin/apilist/create')->withInput()->withErrors($api_response_data->message);
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
			$apilist = $api_response_data->data->apilist;
			return view ( 'admin/apilist/show',compact('apilist'));
		}
		else
		{
			return Redirect::to('admin/apilist')->with('message',$api_response_data->message);
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
			$apilist = $api_response_data->data->apilist;
			return view('admin/apilist/edit',  compact('apilist'));
		}
		else
		{
			return Redirect::to('admin/apilist')->with('message',$api_response_data->message);
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
			return Redirect::to('admin/apilist/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/apilist/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to update the API List	 ***/
	
	/*** Start to delete the API List ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/apilist/')->with('success',$api_response_data->message);
	}
	/*** End to delete the API List	 ***/
}
