<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Controllers\Medcubics\Api\PosApiController as PosApiController;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use Redirect;
use Config;


class PosController extends PosApiController 
{
    public function __construct()
    {
        View::share( 'heading'       , 'Customers' );
		View::share( 'selected_tab'  , 'admin/placeofservice' );
		View::share( 'heading_icon'  , Config::get('cssconfigs.admin.users'));
    }
    
	/*** Start to Listing the Place of Service	 ***/
    public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();		
		$pos				= 	$api_response_data->data->pos;
		return view ( 'admin/pos/pos', compact ( 'pos') );
	}
	/*** End to Listing the Place of Service	 ***/

	/*** Start to Create the Place of Service	 ***/
	public function create()
	{
        $api_response = $this->getCreateApi();
        return view ( 'admin/pos/create');
	}
	/*** End to Create the Place of Service	 ***/

	/*** Start to Store the Place of Service	 ***/
	public function store()
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/placeofservice/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Store the Place of Service	 ***/

	/*** Start to Show the Place of Service	 ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();	
	
		if($api_response_data->status == 'success')
		{
			$pos				= 	$api_response_data->data->pos;
			return view ( 'admin/pos/show', ['pos' => $pos] );
		}
		else
		{
			return redirect('admin/placeofservice')->with('message',$api_response_data->message);
		}
	}
	/*** End to Show the Place of Service	 ***/

	/*** Start to Edit the Place of Service  ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$pos				= 	$api_response_data->data->pos;
			return view ( 'admin/pos/edit', compact ('pos') );
		}
		else
		{
			return redirect('admin/placeofservice')->with('message',$api_response_data->message);
		}
	}
	/*** End to Edit the Place of Service	 ***/

	/*** Start to Update the Place of Service	 ***/
	public function update($id)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/placeofservice/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Update the Place of Service	 ***/

	/*** Start to Destory the Place of Service	 ***/
	public function destroy($id)
	{
		$api_response 		= 	$this->getDeleteApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/placeofservice')->with ( 'success', $api_response_data->message );
		}
		else
		{
			return redirect()->back()->with ( 'error', $api_response_data->message );
		}
	}
	/*** End to Destory the Place of Service ***/
}