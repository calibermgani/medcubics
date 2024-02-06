<?php namespace App\Http\Controllers\Medcubics;

use View;
use Auth;
use Request;
use Redirect;
use Config;
//use App\Http\Controllers\Medcubics\Api\InsuranceApiController as InsuranceApiController;

class InsuranceController extends Api\InsuranceApiController
{
	public function __construct()
	{
		View::share( 'heading', 'Insurance' );
		View::share( 'selected_tab', 'insurance' );
		View::share( 'heading_icon', Config::get('cssconfigs.common.insurance'));
	}
	
	/*** Start to Listing the Insurance	 ***/
	public function index()
	{		
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$insurances 		= $api_response_data->data->insurances;
		return view ( 'admin/insurance/insurance', compact ( 'insurances' ) );
	}
	/*** End to Listing the Insurance	 ***/
	
	/*** Start to Create the Insurance	 ***/
	public function create()
	{
		$api_response 		= 	$this->getCreateApi();
		$api_response_data 	= 	$api_response->getData();
		$insurancetypes 	= 	$api_response_data->data->insurancetypes;
		$insuranceclasses	= 	$api_response_data->data->insuranceclasses;
		$insurancetype_id 	= $api_response_data->data->insurancetype_id;
		$insuranceclass_id 	= $api_response_data->data->insuranceclass_id;
		$address_flags 		= (array)$api_response_data->data->addressFlag;
		$address_flag['general'] = (array)$address_flags['general'];
		$address_flag['appeal'] = (array)$address_flags['appeal'];
		$claimformats 			= (array)$api_response_data->data->claimformats;
		$cmstypes 				= $api_response_data->data->inscmstypes;		

		return view ( 'admin/insurance/create', compact ('claimformats','insurancetypes', 'insuranceclasses', 'claimtype_id','claimformat_id','insurancetype_id','insuranceclass_id','address_flag', 'cmstypes' ) );
	}
	/*** End to Create the Insurance	 ***/
	
	/*** Start to Store the Insurance	 ***/
	public function store(Request $request)
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/insurance/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Store the Insurance	 ***/
	
	/*** Start to Edit the Insurance	 ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status=='success')
		{
			$insurance 			= 	$api_response_data->data->insurance;
			$insurancetypes 	= 	$api_response_data->data->insurancetypes;
			$insuranceclasses	= 	$api_response_data->data->insuranceclasses;
			$claimformats		= 	$api_response_data->data->claimformats;
			$claimtype_id = $api_response_data->data->claimtype_id;
			$claimformat_id = $api_response_data->data->claimformat_id;
			$insurancetype_id = $api_response_data->data->insurancetype_id;
			$insuranceclass_id = $api_response_data->data->insuranceclass_id;
			$address_flags = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['general'];
			$address_flag['appeal'] = (array)$address_flags['appeal'];
			$cmstypes 				= $api_response_data->data->inscmstypes;
			return view ( 'admin/insurance/edit', compact ('insurance','insurancetypes','insuranceclasses','claimformats','claimtype_id','claimformat_id','insurancetype_id','insuranceclass_id','address_flag', 'cmstypes' ) );
		}
		else
		{
			return redirect('admin/insurance')->with('message',$api_response_data->message);
		}
	}
	/*** End to Edit the Insurance	 ***/
	
	/*** Start to Update the Insurance	 ***/
	public function update($id, Request $request)
	{ 
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') {
			return Redirect::to('admin/insurance')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/insurance/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Update the Insurance	 ***/
	
	/*** Start to Destory the Insurance	 ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/insurance')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/insurance')->with('error', $api_response_data->message);
		}
	}
	/*** End to Destory the Insurance	 ***/
	
	/*** Start to Show the Insurance	 ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		
		if($api_response_data->status == 'success')
		{
			$insurance		 	 	 = 	$api_response_data->data->insurance;
			$address_flags 			 = (array)$api_response_data->data->addressFlag;
			$address_flag['general'] = (array)$address_flags['general'];
			$address_flag['appeal']  = (array)$address_flags['appeal'];
			$claimformats 		= json_decode(json_encode($api_response_data->data->claimformats), True); 
			return view ( 'admin/insurance/show',compact('claimformats','insurance','address_flag'));
		}
		else
		{
			return redirect('admin/insurance')->with('message',$api_response_data->message);
		}
	}
	/*** End to Show the Insurance	 ***/
	
	/*** Start to New Select the Insurance	 ***/
	public function addnewselect()
	{
		$tablename = Request::input('tablename');
		$fieldname = Request::input('fieldname');
		$addedvalue = Request::input('addedvalue');		
		return $this->addnewApi($addedvalue);			
	}
	/*** End to New Select the Insurance	 ***/
	
	public function avatarinsurance($id)
	{
		$api_response 		= $this->avatarapipicture($id);
		$api_response_data 	= $api_response->getData();
		return Redirect::to('admin/insurance/'.$id.'/edit')->with($api_response_data->message);
	}
}