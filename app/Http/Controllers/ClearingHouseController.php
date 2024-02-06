<?php namespace App\Http\Controllers;

use Request;
use View;
use Redirect;

class ClearingHouseController extends Api\ClearingHouseApiController {
	public function __construct()
    {
        View::share('heading', 'Practice');
		View::share('selected_tab','edi');
		View::share('heading_icon','fa-medkit');
    }	
	/*** Index page Listing the EDI Start***/
	public function index()
	{		
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();		
		$clearing_house		= 	$api_response_data->data->clearing_house;
		return view('practice/clearinghouse/index',compact('clearing_house'));
	}
	/*** Index page Listing the EDI End***/
	
	/*** Create page  EDI start ***/
	public function create()
	{
		 return view('practice/clearinghouse/create');
	}
	/*** Create page  EDI end ***/
	
	/*** Store page  EDI start ***/
	public function store()
	{
		$api_response = $this->getStoreApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('edi/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Store page  EDI end ***/
	
	/*** Show page  EDI start ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$clearing_house			= 	$api_response_data->data->clearing_house;
			return view('practice/clearinghouse/show',['clearing_house' => $clearing_house]);
		}
		else
		{
			return redirect('edi')->with('message',$api_response_data->message);
		}
	}
	/*** Show page  EDI end ***/
	
	/*** Edit page  EDI start ***/	
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$clearing_house = $api_response_data->data->clearing_house;
			return view('practice/clearinghouse/edit',compact('clearing_house'));
		}
		else
		{
			return redirect('edi')->with('message',$api_response_data->message);
		}
	}
	/*** Edit page  EDI end ***/
	
	/*** Update page  EDI start ***/
	public function update($id)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('edi/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Update page  EDI end ***/
	
	/*** Destroy page  EDI start ***/		
	public function destroy($id)
	{
		$api_response 		= 	$this->getDeleteApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('edi')->with('success',$api_response_data->message);
		}
		else
		{
			return redirect()->back()->with('error',$api_response_data->message);
		}
	}
	/*** Destroy page  EDI end ***/
}
