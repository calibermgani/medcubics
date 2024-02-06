<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;

class CheatsheetController extends Api\CheatsheetApiController {

	public function __construct() { 
      
       View::share ( 'heading', 'Cheat Sheet' );  
		View::share ( 'selected_tab', 'cheatsheet' );   
    }  

	public function index()
	{
		
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$cheatsheet = $api_response_data->data->cheatsheet;
		return view('practice/cheatsheet/cheatsheet',  compact('cheatsheet'));
	}

	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$cheatsheet = $api_response_data->data->cheatsheet;

		$facilities = $api_response_data->data->facilities;
		$facility_id = $api_response_data->data->facility_id;

		$providers = $api_response_data->data->providers;
		$provider_id = $api_response_data->data->provider_id;

		$resources = $api_response_data->data->resources;
		$resource_id = $api_response_data->data->resource_id;

		return view('practice/cheatsheet/create',  compact('cheatsheet','facilities','facility_id','providers','provider_id','resources','resource_id'));
	}
	
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		$id = $api_response_data->data->id;

		if($api_response_data->status == 'success')
			{
				return Redirect::to('cheatsheet/'.$id)->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::to('cheatsheet/create')->withInput()->withErrors($api_response_data->message);
			}        
	}
	
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		$cheatsheet = $api_response_data->data->cheatsheet;
		
		return view ( 'practice/cheatsheet/show',compact('cheatsheet'));
	}
	
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		$cheatsheet = $api_response_data->data->cheatsheet;

		$facilities = $api_response_data->data->facilities;
		$facility_id = $api_response_data->data->facility_id;

		$providers = $api_response_data->data->providers;
		$provider_id = $api_response_data->data->provider_id;

		$resources = $api_response_data->data->resources;
		$resource_id = $api_response_data->data->resource_id;

		return view('practice/cheatsheet/edit',  compact('cheatsheet','facilities','facility_id','providers','provider_id','resources','resource_id'));
	}

	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
			{
				return Redirect::to('cheatsheet/'.$id)->with('success',$api_response_data->message);
			}
		else
			{
				return Redirect::to('cheatsheet/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
			}        
	}

	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('cheatsheet')->with('success',$api_response_data->message);
	}
 	
}
