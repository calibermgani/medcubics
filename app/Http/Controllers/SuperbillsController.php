<?php namespace App\Http\Controllers;

use View;
use Redirect;
use Request;
use Config;

class SuperbillsController extends Api\SuperbillsApiController
{

	public function __construct() 
	{
		View::share ( 'heading', 'Superbills' );
		View::share ( 'selected_tab', 'superbills' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.superbills'));
	}
	
	/* Display a listing of the resource */
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();		
		$superbill 			= 	$api_response_data->data->superbill_template;
		return view('practice/superbill/superbill', compact('superbill'));
	}
	
	/* Show the form for creating a new resource */
	public function create()
	{
		$api_response 		= $this->getCreateApi();
		$api_response_data 	= $api_response->getData();
		$providers 			= $api_response_data->data->providers;
		return view('practice/superbill/create',  compact('providers'));
	}
	
	
	
	/* Display the specified resource */
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$superbill_array	= 	$api_response_data->data->superbill_arr;
			
			return view('practice/superbill/show',  compact('superbill_array'));
		}
		else
		{
			return Redirect::to('superbills')->with('error', $api_response_data->message);
		}
	}
	
	/* Show the form for editing the specified resource */
	public function edit($id)
	{
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$superbill_array 				= $api_response_data->data->superbill_arr;
			$providers 					= $api_response_data->data->providers;
			return view('practice/superbill/edit', compact('superbill_array','providers'));
		}
		else
		{
			return Redirect::to('superbills')->with('error', $api_response_data->message);
		}
	}
	
	
	
	/* Remove the specified resource from storage */
	public function destroy($id)
	{
		$api_response 		= $this->getDeleteApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('superbills')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('superbills')->with('error', $api_response_data->message);
		}
	}
	
	/*** Templates create starts here [ajax] ***/
	public function getTemplatelist(Request $request)
	{
		$request = Request::all();
		if($request != '')
		{
			return view('practice/superbill/template', compact('request'));
		}
		else
		{
			return Redirect::to('superbills')->with('error', $api_response_data->message);
		}
	}
	/*** Templates create ends here ***/
	
	/*** Search CPT Codes starts here [ajax] ***/
	public function getTemplatesearch(Request $request)
	{
		$api_response 		= $this->getTemplatesearchApi(Request::all());
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$cpt_list 	= $api_response_data->data;
			return view('practice/superbill/search_part', compact('cpt_list'));
		}
		else
		{
			$cpt_list='';
			return view ('practice/superbill/search_part', compact('cpt_list'));
		}
	}
	/*** Search CPT Codes ends here ***/
	
	/*** Template show function starts here [ajax] ***/
	public function getTemplateshow(Request $request)
	{
		$api_response 		= $this->getTemplateshowApi(Request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'update')
		{
			$result 	= $api_response_data->data->request;
			$header_style 	= $api_response_data->data->header_style;
			return view('practice/superbill/get_template', compact('result','header_style'));
		}
		else
		{
			$result 	= $api_response_data->data->request;
			return view('practice/superbill/get_template', compact('result'));
		}
	}
	/*** Template show function ends here ***/
	
	/*** Template store function starts here [ajax] ***/
	public function getTemplatestore(Request $request)
	{
		$api_response 		= $this->getTemplatestoreApi(Request::all());
		$api_response_data 	= $api_response->getData();
		$data = json_encode($api_response_data);
		print_r($data);exit;
	}
	/*** Template store function ends here ***/
}
