<?php namespace App\Http\Controllers\Medcubics;

use Request;
use View;
use Redirect;
use DB;
use App\Http\Controllers\Medcubics\Api\ClearingHouseApiController as ClearingHouseApiController;

class ClearingHouseController extends ClearingHouseApiController 
{    
    public function __construct()
    {
        View::share('heading', 'Customers');
		View::share('selected_tab','admin/edi');
		View::share('heading_icon','fa-users');
    }
    
    public function index()
	{		
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();		
		$clearing_house		= 	$api_response_data->data->clearing_house;
		
		return view('admin/clearinghouse/index',compact('clearing_house'));
	}

	public function create()
	{
        $api_response 		= $this->getCreateApi();
        $api_response_data 	= $api_response->getData();
		$practice_list		= 	$api_response_data->data->practice_list;
        return view('admin/clearinghouse/create',compact('practice_list'));
	}
	
	public function store()
	{
		$api_response = $this->getStoreApi();
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/edi/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}

	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$clearing_house			= 	$api_response_data->data->clearing_house;
			return view('admin/clearinghouse/show',['clearing_house' => $clearing_house]);
		}
		else
		{
			return redirect('admin/edi')->with('message',$api_response_data->message);
		}
	}

	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$clearing_house = $api_response_data->data->clearing_house;
			$practice_list			= 	$api_response_data->data->practice_list;
			return view('admin/clearinghouse/edit',compact('clearing_house','practice_list'));
		}
		else
		{
			return redirect('admin/edi')->with('message',$api_response_data->message);
		}
	}
	
	public function update($id)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/edi/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}

	public function destroy($id)
	{
		$api_response 		= 	$this->getDeleteApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/edi')->with('success',$api_response_data->message);
		}
		else
		{
			return redirect()->back()->with('error',$api_response_data->message);
		}
	}

}
