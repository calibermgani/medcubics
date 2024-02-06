<?php namespace App\Http\Controllers;

use App\Http\Requests;
use View;
use Request;
use Redirect;
use Config;
use App\Http\Controllers\Controller;

class ClinicalNotesCategoryController extends Api\ClinicalNotesCategoryApiController 
{
	public function __construct() { 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'clinicalcategories' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
	}  
	/**** clinicalnotescategory List page start ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$clinicalcategories = $api_response_data->data->clinicalcategories;
		return view('practice/clinicalnotescategory/clinicalcategories',  compact('clinicalcategories'));
	}
	/**** clinicalnotescategory List page end ***/
	
	/**** clinicalnotescategory Create page start ***/
	public function create()
	{
		return view('practice/clinicalnotescategory/create');
	}

	/**** clinicalnotescategory create page end ***/
	
	/**** clinicalnotescategory store page start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('clinicalnotescategory')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('clinicalnotescategory/create')->withInput()->withErrors($api_response_data->message);
		}    
	}
	/**** clinicalnotescategory store page end ***/
	
	/**** clinicalnotescategory edit page start ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$clinicalcategories = $api_response_data->data->clinicalcategories;
			return view('practice/clinicalnotescategory/edit', compact('clinicalcategories'));
		}
		else
		{
			return Redirect::to('clinicalnotescategory')->with('error','Invalid clinicalcategories');
		}
	}
	/**** clinicalnotescategory edit page end ***/
	/**** clinicalnotescategory Update page start ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi($id,Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('clinicalnotescategory')->with('success',$api_response_data->message);
		}
		else
		{
			return redirect()->back()->withInput()->withErrors($api_response_data->message);
		}        
	}
	/**** clinicalnotescategory update page end ***/
	/**** clinicalnotescategory delete page start ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('clinicalnotescategory')->with('success',$api_response_data->message);
	}
	/**** clinicalnotescategory delete page end ***/
}