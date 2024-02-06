<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class ResourcesController extends Api\ResourcesApiController {

	public function __construct() { 
      
       View::share ( 'heading', 'Resources' );  
	   View::share ( 'selected_tab', 'resources' ); 
       View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.resources'));

    }  

	public function index()
	{
		
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$resources = $api_response_data->data->resources;
		return view('practice/resources/resources',  compact('resources'));
	}

	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$resources = $api_response_data->data->resources;

		$facilities = $api_response_data->data->facilities;
		$facility_id = $api_response_data->data->facility_id;

		$providers = $api_response_data->data->providers;
		$provider_id = $api_response_data->data->provider_id;
		return view('practice/resources/create',  compact('resources','facilities','facility_id','providers','provider_id'));
	}
	
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		$id = $api_response_data->data->id;

		if($api_response_data->status == 'success')
			{
				return Redirect::to('resources/'.$id)->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::to('resources/create')->withInput()->withErrors($api_response_data->message);
			}        
	}
	
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$resources = $api_response_data->data->resources;
		return view ( 'practice/resources/show',compact('resources'));
	}
		else{
			return Redirect::to('resources');
		}
	}
	
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$resources = $api_response_data->data->resources;

		$facilities = $api_response_data->data->facilities;
		$facility_id = $api_response_data->data->facility_id;

		$providers = $api_response_data->data->providers;
		$provider_id = $api_response_data->data->provider_id;
		return view('practice/resources/edit',  compact('resources','facilities','facility_id','providers','provider_id'));
		}
		else{
			return Redirect::to('resources');
		}
	}

	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
			{
				return Redirect::to('resources/'.$id)->with('success',$api_response_data->message);
			}
		else
			{
				return Redirect::to('resources/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
			}        
	}

	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('resources')->with('success',$api_response_data->message);
	}
 	
}
