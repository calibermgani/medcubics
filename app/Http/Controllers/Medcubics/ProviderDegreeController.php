<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class ProviderDegreeController extends Api\ProviderDegreeApiController 
{

	public function __construct()
	{      
		View::share('heading', 'Customers');  
		View::share('selected_tab', 'admin/providerdegree');
		View::share('heading_icon', Config::get('cssconfigs.admin.users'));
	}

	/* Display a listing of the resource */
	public function index()
	{
		$api_response 		= $this->getIndexApi();
		$api_response_data 	= $api_response->getData();
		$degrees 			= $api_response_data->data->degrees;
		return view('admin/providerdegree/providerdegree',compact('degrees'));
	}

	/* Show the form for creating a new resource */
	public function create()
	{
		$api_response 		= $this->getCreateApi();
		$api_response_data 	= $api_response->getData();
		$degrees 			= $api_response_data->data->degrees;
		return view('admin/providerdegree/create',compact('degrees'));
	}

	/* Store a newly created resource in storage */
	public function store(Request $request)
	{
		$api_response 		= $this->getStoreApi($request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$insertid = $api_response_data->data;
			return Redirect::to('admin/providerdegree/'.$insertid)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/providerdegree/create')->withInput()->withErrors($api_response_data->message);
		}      
	}

	/* Display the specified resource */
	public function show($id)
	{
		$api_response 		= $this->getShowApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$degrees = $api_response_data->data->degrees;
			return view('admin/providerdegree/show',compact('degrees','heading'));	
		}
		else
		{
			return redirect('admin/providerdegree')->with('message',$api_response_data->message);
		}   
	}

	/* Show the form for editing the specified resource */
	public function edit($id)
	{
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$degrees = $api_response_data->data->degrees;
			return view('admin/providerdegree/edit',compact('degrees'));	
		}
		else
		{
			return redirect('admin/providerdegree')->with('message',$api_response_data->message);
		}   
	}

	/* Update the specified resource in storage */
	public function update($id, Request $request)
	{
		$api_response 		= $this->getUpdateApi($id, $request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/providerdegree/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/providerdegree/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}       
	}

	/* Remove the specified resource from storage */
	public function destroy($id)
	{
		$api_response 		= $this->getDestroyApi($id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/providerdegree')->with('success',$api_response_data->message);
	}

}
