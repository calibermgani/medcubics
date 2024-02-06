<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use View;
use Request;
use Redirect;
use Config;
use App\Http\Controllers\Controller;

class FaqController extends Api\FaqApiController 
{
	public function __construct() { 
		View::share ( 'heading', 'Tickets' );  
		View::share ( 'selected_tab', 'admin/faq' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.faq'));
	}  
	/**** FAQ List page start ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$faq = $api_response_data->data->faq;
		return view('admin/faq/faq',  compact('faq'));
	}
	/**** FAQ List page end ***/
	
	/**** FAQ Create page start ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$faq = $api_response_data->data->faq;
		return view('admin/faq/create',  compact('faq'));
	}

	/**** FAQ create page end ***/
	
	/**** FAQ store page start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/faq/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/faq/create')->withInput()->withErrors($api_response_data->message);
		}    
	}
	/**** FAQ store page end ***/
	/**** FAQ show page start ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$faq = $api_response_data->data->faq;
			return view('admin/faq/show',  compact('faq'));	
		}
		else
		{
			return Redirect::to('admin/faq')->with('error','Invalid faq');
		}
	}
	/**** FAQ show page end ***/
	/**** FAQ edit page start ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$faq = $api_response_data->data->faq;
			return view('admin/faq/edit', compact('faq'));
		}
		else
		{
			return Redirect::to('admin/faq')->with('error','Invalid Faq');
		}
	}
	/**** FAQ edit page end ***/
	/**** FAQ Update page start ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi($id,Request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/faq/'.$api_response_data->data)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/faq/'.$api_response_data->data.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/**** FAQ update page end ***/
	/**** FAQ delete page start ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/faq')->with('success',$api_response_data->message);
	}
	/**** FAQ delete page end ***/
}