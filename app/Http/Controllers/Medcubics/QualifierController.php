<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class QualifierController extends Api\QualifierApiController 
{
	public function __construct() 
	{      
		View::share ( 'heading', 'Customers' );  
		View::share ( 'selected_tab', 'admin/qualifiers');
		View::share ( 'heading_icon', Config::get('cssconfigs.admin.users'));
    }  
	
	/*** Listing the qualifiers start ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$qualifiers = $api_response_data->data->qualifiers;
		return view('admin/qualifier/qualifier',  compact('qualifiers'));
	}
	/*** Listing the qualifiers end ***/
	
	/*** Create qualifier details start ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$qualifiers = $api_response_data->data->qualifiers;
		return view('admin/qualifier/create',  compact('qualifiers'));
	}
	/*** Create qualifier details end ***/
	
	/*** Store the qualifier details start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$insertid = $api_response_data->data;
			return Redirect::to('admin/qualifiers/'.$insertid)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/qualifiers/create')->withInput()->withErrors($api_response_data->message);
		}      
	}
	/***Store the qualifier details end  ***/
	
	/*** View the qualifier details start ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$qualifiers = $api_response_data->data->qualifiers;
			return view('admin/qualifier/show',  compact('qualifiers','heading'));	
		}
		else
		{
			return redirect('admin/qualifiers')->with('message',$api_response_data->message);
		}   
	}
	/*** View the qualifier details end ***/
	
	/*** Edit the qualifier detail start ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$qualifiers = $api_response_data->data->qualifiers;
			return view('admin/qualifier/edit', compact('qualifiers'));
		}
		else
		{
			return redirect('admin/qualifiers')->with('message',$api_response_data->message);
		}   
	}
	/*** Edit qualifier details end ***/

	/*** Update the qualifier details start ***/
	public function update($id, Request $request)
	{
		$api_response = $this->getUpdateApi($id, $request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/qualifiers/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return redirect('admin/qualifiers/'.$id.'/edit')->with('message',$api_response_data->message);
		}       
	}
	/*** Update the qualifier details end ***/
	
	/*** Delete the qualifier details start***/
	public function destroy($id)
	{
		$api_response = $this->getDestroyApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/qualifiers')->with('success',$api_response_data->message);
	}
	/*** Delete the qualifier details end ***/
}
