<?php namespace App\Http\Controllers;
use View;
use Redirect;
use Request;
use App\Models\Template;
use Auth;
use Config;
use Lang;

class TemplatesController extends Api\TemplatesApiController 
{
	public function __construct() 
	{
		View::share( 'heading', 'Practice' );
		View::share( 'selected_tab', 'templates' );
		View::share( 'heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
	}
	
	/*** Start to listing the Templates  ***/
	public function index()
	{
		$type = 'General';
		$export = '';
		$api_response 		= 	$this->getIndexApi($type,$export);
		$api_response_data 	= 	$api_response->getData();		
		$templates 			= 	$api_response_data->data->templates;
		return view ( 'practice/template/template', compact ('templates') );
	}
	/*** End to listing the Templates  ***/
	
	/*** Start to Create the Templates	 ***/
	public function create()
	{
		$api_response 		= $this->getCreateApi();
		$api_response_data 	= $api_response->getData();
		$templatestype 		= $api_response_data->data->templatestype;
		$template_type_id 	= $api_response_data->data->templates_type_id;
		$templatepairs 		= $api_response_data->data->templatepairs;
		return view('practice/template/create',  compact('templatestype','template_type_id','templatepairs'));
	}
	/*** End to Create the Templates	 ***/
	
	/*** Start to Store the Templates	 ***/
	public function store(Request $request)
	{
		$api_response 		= $this->getStoreApi(Request::all());
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('templates/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Store the Templates	 ***/
	
	/*** Start to Show the Templates	 ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$templates			= 	$api_response_data->data->templates;
			if($templates->templatetype->templatetypes !="App")
				return view ( 'practice/template/show', ['templates' => $templates] );
			else
				return Redirect::to('templates')->with('error', Lang::get("common.validation.empty_record_msg"));
		}
		else
		{
			return Redirect::to('templates')->with('error', $api_response_data->message);
		}
	}
	/*** End to Show the Templates	 ***/
	
	/*** Start to Edit the Templates	 ***/
	public function edit($id)
	{
		$api_response 		= $this->getEditApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$templates 			= $api_response_data->data->templates;
			$templatestype 		= $api_response_data->data->templatestype;
			$template_type_id 	= $api_response_data->data->templates_type_id;
			$templatepairs 		= $api_response_data->data->templatepairs;
			$patient_correspondence 		= $api_response_data->data->patient_correspondence;
			if($templates->templatetype->templatetypes !="App")
				return view('practice/template/edit', compact('templates','templatestype','template_type_id','templatepairs','patient_correspondence'));
			else
				return Redirect::to('templates')->with('error', Lang::get("common.validation.empty_record_msg"));
			
		}
		else
		{
			return Redirect::to('templates')->with('error', $api_response_data->message);
		}
	}
	/*** End to Edit the Templates ***/
	
	/*** Start to Update the Templates	 ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id, $request);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('templates')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('templates/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withInput()->withErrors($api_response_data->message);
		}
	}
	/*** End to Update the Templates	 ***/
	
	/*** Start to Destory the Templates	 ***/
	public function destroy($id)
	{
		$api_response 		= $this->getDeleteApi($id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('templates')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('templates')->with('error', $api_response_data->message);
		}
	}
	/*** End to Destory the Templates	 ***/
	
	public function addnewselect()
	{
		$tablename = Request::input('tablename');
		$fieldname = Request::input('fieldname');
		$addedvalue = Request::input('addedvalue');		
		return $this->addnewApi($addedvalue);			
	}
	
	
}