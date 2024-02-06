<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Medcubics\Api\TaxanomyApiController as TaxanomyApiController;
use View;
use Redirect;
use Config;

class TaxanomyController extends TaxanomyApiController 
{
	public function __construct()
	{
		View::share ( 'heading', 'Customers' );
		View::share ( 'selected_tab', 'admin/taxanomy' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
	}
	/*** lists page Starts ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();		
		$taxanomies			= 	$api_response_data->data->taxanomies;
		return view ( 'admin/taxanomy/taxanomy', compact ( 'taxanomies') );
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/	
	public function create()
	{
        $api_response           = $this->getCreateApi();
        $api_response_data      = $api_response->getData();
        $specialities           = $api_response_data->data->specialities;
        $speciality_id          =   "";
        return view ( 'admin/taxanomy/create', compact ('specialities','speciality_id'));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function store()
	{
		$api_response 		= 	$this->getStoreApi();
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$insertid = $api_response_data->data;
			return Redirect::to('admin/taxanomy/'.$insertid)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Store Function Ends ***/
	
	/*** Show Function Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$taxanomy			= 	$api_response_data->data->taxanomy;
			return view ( 'admin/taxanomy/show', ['taxanomy' => $taxanomy] );
		}
		else
		{
			return redirect('admin/taxanomy')->with('message',$api_response_data->message);
		}
	}
	/*** Show Function Ends ***/
	
	/*** Edit page Starts ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$specialities       = 	$api_response_data->data->specialities;
			$taxanomy           =   $api_response_data->data->taxanomy;
			$speciality_id      =   $taxanomy->speciality_id;
			return view ( 'admin/taxanomy/edit', compact ('taxanomy','specialities','speciality_id') );
		}
		else
		{
			return redirect('admin/taxanomy')->with('message',$api_response_data->message);
		}
		
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function update($id)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/taxanomy/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function destroy($id)
	{
		$api_response 		= 	$this->getDeleteApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/taxanomy')->with ( 'success', $api_response_data->message );
		}
		else
		{
			return redirect()->back()->with ( 'error', $api_response_data->message );
		}
	}
	/*** Delete Function Ends ***/
}
