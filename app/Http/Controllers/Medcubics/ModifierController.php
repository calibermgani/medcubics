<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use View;
use Config;

class ModifierController extends Api\ModifierApiController 
{
	public function __construct() 
	{
		View::share ( 'heading', 'Customers' );  
		View::share ( 'selected_tab', 'admin/modifiers' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
    }  
	
	/*** lists page Starts ***/
	public function index()
	{
		$api_response 		= 	$this->getIndexApi();
		$api_response_data 	= 	$api_response->getData();
		$modifiers 			= 	$api_response_data->data->modifiers;
		return view ( 'admin/modifier/modifierlevel1/modifier', compact ( 'modifiers') );
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$modifierstype = $api_response_data->data->modifierstype;
		$modifiers_type_id = $api_response_data->data->modifiers_type_id;
		return view('admin/modifier/modifierlevel1/create',  compact('modifierstype','modifiers_type_id'));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$modifiers = $api_response_data->data->modifiers_type_id;
			return Redirect::to('admin/modifierlevel'.$modifiers.'/'.$api_response_data->data->id)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/modifierlevel1/create')->withInput()->withErrors($api_response_data->message);
		} 
	}
	/*** Store Function Ends ***/
	
	/*** Show page Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();		
		if($api_response_data->status == 'success')
		{
			$modifiers		 	= 	$api_response_data->data->modifiers;
			return view ( 'admin/modifier/modifierlevel1/show', ['modifiers' => $modifiers] );
		}
		else
		{
			return Redirect::to('admin/modifierlevel1')->with('error', $api_response_data->message);
		}
	}
	/*** Show Function Ends ***/
	
	/*** Edit page Starts ***/
	public function edit($id)
	{		
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$modifiers = $api_response_data->data->modifiers;
			$modifierstype = $api_response_data->data->modifierstype;
			$modifiers_type_id = $api_response_data->data->modifiers_type_id;
			return view('admin/modifier/modifierlevel1/edit', compact('modifiers','modifierstype','modifiers_type_id'));
		}
		else
		{
			return Redirect::to('admin/modifierlevel1');
		}
		
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function update($id, Request $request)
	{
		$api_response 		= 	$this->getUpdateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'success')
		{
			$modifiers = $api_response_data->data->modifiers->modifiers_type_id;
			return Redirect::to('admin/modifierlevel'.$modifiers.'/'.$id)->with('success', $api_response_data->message);
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
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/modifierlevel1')->with('success',$api_response_data->message);
	}
	/*** Delete Function Ends ***/
}
