<?php namespace App\Http\Controllers;

use Request;
use Input;
use Redirect;
use View;
use Config;

class StaticPageController extends Api\StaticPageApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Practice' );  
		View::share ( 'selected_tab', 'staticpage' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	/*** Start to listing the Helps  ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$staticpages = $api_response_data->data->staticpages;
		return view('practice/staticpage/staticpage',  compact('staticpages'));
	}
	/*** End to listing the Helps  ***/

	/*** Start to Create the Helps	 ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$staticpages = $api_response_data->data->staticpages;
		return view('practice/staticpage/create',  compact('staticpages'));
	}
	/*** End to Create the Helps	 ***/
	
	/*** Start to Store the Helps	 ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		$id = $api_response_data->data;
		if($api_response_data->status == 'success')
		{
			return Redirect::to('staticpage/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('staticpage/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** End to Store the Helps	 ***/
	
	/*** Start to Show the Helps ***/
	public function show($id)
	{
		$api_response = $this->getShowApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$staticpages = $api_response_data->data->staticpages;
			return view('practice/staticpage/show',  compact('staticpages'));	
		}
		else
		{
			return Redirect::to('staticpage')->with('error', $api_response_data->message);
		}
	}
	/*** End to Show the Helps	 ***/
	
	/*** Start to Edit the Helps	 ***/
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$staticpages = $api_response_data->data->staticpages;
			return view('practice/staticpage/edit',  compact('staticpages'));
		}
		else
		{
			return Redirect::to('staticpage')->with('error', $api_response_data->message);
		}
	}
	/*** End to Edit the Helps ***/

	/*** Start to Update the Helps	 ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
				 return Redirect::to('staticpage')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('staticpage/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('staticpage/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** End to Update the Helps	 ***/

	/*** Start to Destory Helps ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('staticpage')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('staticpage')->with('error', $api_response_data->message);
		}
	}
	/*** End to Destory Helps	 ***/
	
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
	public function getStaticpageContent($type)
	{
		$api_response = $this->getHelpContentApi($type);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$staticpage = $api_response_data->data->staticpage;
			return ucwords($staticpage->title).'~~'.html_entity_decode($staticpage->content);
		}
		else
		{
			return $api_response_data->message;
		}        
	}
}
