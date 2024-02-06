<?php namespace App\Http\Controllers\Medcubics;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Input;
use Redirect;
use View;
use Config;

class StaticPageController extends Api\StaticPageApiController 
{

	public function __construct() 
	{ 
		View::share ( 'heading', 'Customers' );  
		View::share ( 'selected_tab', 'admin/staticpage' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
    }  
	/*** Help option index listing page start here ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$staticpages = $api_response_data->data->staticpages;
		return view('admin/staticpage/staticpage',  compact('staticpages'));
	}	
	/*** Help option index listing page end here ***/
	
	/*** Help option create start here ***/	
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$staticpages = $api_response_data->data->staticpages;
		return view('admin/staticpage/create', compact('staticpages'));	
	}
	/*** Help option create end here ***/
	
	/*** Help option store start here ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		$id = $api_response_data->data;
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/staticpage/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/staticpage/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Help option store end here ***/
	
	/*** Help option show start here ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$staticpages = $api_response_data->data->staticpages;
			return view('admin/staticpage/show',  compact('staticpages'));	
		}
		else
		{
			return Redirect::to('admin/staticpage');
		}
	}
	/*** Show the Help optionsend here ***/
	
	/*** Edit Help option start here ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$staticpages = $api_response_data->data->staticpages;
			return view('admin/staticpage/edit',  compact('staticpages'));
		}
		else
		{
			return Redirect::to('admin/staticpage');
		}
	}
	/*** Edit help option end here ***/
	
	/*** Update help option start here ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/staticpage/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/staticpage/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** update help option status end here ***/
	
	/*** Delete help option start here***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/staticpage')->with('success',$api_response_data->message);
	}
	/*** Delete the Help option end here ***/
	
	public function getHelpContent($type)
	{
		$api_response = $this->getHelpContentApi($type);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$staticpage = $api_response_data->data->staticpage;
			return ucwords($staticpage->title).'~~'.$staticpage->content;
		}
		else
		{
			return $api_response_data->message;
		}        
	}
}
