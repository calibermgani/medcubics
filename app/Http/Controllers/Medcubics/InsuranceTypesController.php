<?php namespace App\Http\Controllers\Medcubics;
use View;
use Redirect;
use Request;
use Config;
use App\Http\Controllers\Medcubics\Api\InsuranceTypesApiController as InsuranceTypesApiController;

class InsuranceTypesController extends InsuranceTypesApiController 
{
	public function __construct() 
	{
		View::share ( 'heading', 'Ins Types' );
		View::share ( 'selected_tab', 'admin/insurancetypes' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
	}
	
	/*** Start to listing the Insurance Types  ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$insurancetypes 	= 	$api_response_data->data->insurancetypes;
		$heading = 'Customers';
		return view ( 'admin/insurancetypes/insurancetypes', compact ( 'insurancetypes','heading') );
	}
	/*** End to listing the Insurance Types  ***/

	/*** Start to Create the Insurance Types ***/
	public function create()
	{
		$api_response 		= $this->getCreateApi();
		$api_response_data 	= $api_response->getData();
		$cmstypes = $api_response_data->data->inscmstypes;
		$heading = 'Customers';
		return view('admin/insurancetypes/create', compact('cmstypes','heading'));
	}
	/*** End to Create the Insurance Types	 ***/

	/*** Start to Store the Insurance Types	 ***/
	public function store(Request $request)
	{
		$api_response 		= $this->getStoreApi(Request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/insurancetypes/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/insurancetypes/create')->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Store the Insurance Types	 ***/
	
	/*** Start to Show the Insurance Types ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
                $heading = 'Customers';
		if($api_response_data->status=='error')
		{
			return redirect('/admin/insurancetypes')->with('message',$api_response_data->message);
		}
                
		$insurancetypes		= 	$api_response_data->data->insurancetypes;
		if($api_response_data->status == 'success')
		{
			return view ( 'admin/insurancetypes/show', ['insurancetypes' => $insurancetypes,'heading' => $heading] );
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Show the Insurance Types ***/
	
	/*** Start to Edit the Insurance Types	 ***/
	public function edit($id)
	{
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
                $heading = 'Customers';
		if($api_response_data->status=='error')
		{
			return redirect('/admin/insurancetypes')->with('message',$api_response_data->message);
		}
                
		$insurancetypes		= 	$api_response_data->data->insurancetypes;
		$cmstypes = $api_response_data->data->inscmstypes;
		return view('admin/insurancetypes/edit', compact('insurancetypes','cmstypes','heading'));
	}
	/*** End to Edit the Insurance Types ***/
	
	/*** Start to Update the Insurance Types	 ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id, $request);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/insurancetypes/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Update the Insurance Types	 ***/
	
	/*** Start to Destory Insurance Types ***/
	public function destroy($id)
	{
		$api_response 		= $this->getDeleteApi($id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/insurancetypes')->with('success',$api_response_data->message);
	}
	/*** End to Destory Insurance Types	 ***/
}
