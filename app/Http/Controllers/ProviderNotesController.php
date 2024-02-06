<?php namespace App\Http\Controllers;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use View;
use Session;
use Config;

class ProviderNotesController extends Api\NotesApiController 
{
	public $note_type = 'provider';

	public function __construct() 
	{      
		View::share('heading','Practice');
		View::share('selected_tab','provider');
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($id)
	{
		$api_response 		= $this->getIndexApi($this->note_type,$id);
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$notes 		= $api_response_data->data->notes;
			$provider 	= $api_response_data->data->type_details;
			return view('practice/provider/notes/notes',  compact('notes','provider'));
		}
		else
		{
			return redirect('/provider/')->with('message',$api_response_data->message);    
		} 
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($id)
	{
		$api_response 		= $this->getCreateApi($this->note_type,$id);
		$api_response_data 	= $api_response->getData();
        
		if($api_response_data->status == 'success')
		{
			
			$provider = $api_response_data->data->type_details;
			if(Request::ajax()){
				return view('practice/provider/notes/create_ajax',compact('provider'));
			}
			return view('practice/provider/notes/create', compact('provider'));
		}
		else
		{
			return redirect('/provider')->with('error',$api_response_data->message);   
		}
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id, Request $request)
	{
		$api_response 		= $this->getStoreApi($this->note_type, $request::all(),$id);
		$api_response_data 	= $api_response->getData();
		if(Request::ajax()){
			return json_encode($api_response_data);
		}
		if($api_response_data->status == 'failure') {
			return Redirect::to('provider')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$id.'/notes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('provider/'.$id.'/notes/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($provider_id,$id)
	{
 		$api_response 		= $this->getEditApi($this->note_type,$provider_id,$id);
		$api_response_data 	= $api_response->getData();
		
        if($api_response_data->status=='failure_provider')
		{
			return redirect('/provider')->with('message',$api_response_data->message);    
		}
		
		if($api_response_data->status == 'success')
		{
			$provider 	= $api_response_data->data->type_details;
			$notes 		= $api_response_data->data->notes;
			if(Request::ajax()){
				return view('practice/provider/notes/edit_ajax',compact('notes','provider'));
			}
			return view('practice/provider/notes/edit', compact('notes','provider'));
		}
		else
		{
			return Redirect::to('provider/'.$provider_id.'/notes')->with('error',$api_response_data->message);  
		} 
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($provider_id,$id, Request $request)
	{
		$api_response 		= $this->getUpdateApi($this->note_type, $request::all(),$provider_id, $id);
		$api_response_data 	= $api_response->getData();
		if(Request::ajax()){
			return json_encode($api_response_data);
		}
		if($api_response_data->status=='failure_provider')
		{
			return redirect('/provider')->with('message',$api_response_data->message);    
		}
		
		if($api_response_data->status=='failure')
		{
			return redirect('provider/'.$provider_id.'/notes')->with('message',$api_response_data->message);    
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$provider_id.'/notes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('provider/'.$provider_id.'/notes/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function deleteNotes($provider_id,$id)
	{
		$api_response = $this->getDeleteApi($this->note_type, $id,$provider_id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status=='failure_provider')
		{
			return redirect('/provider')->with('message',$api_response_data->message);    
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('provider/'.$provider_id.'/notes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('provider/'.$provider_id.'/notes')->with('error',$api_response_data->message);  
		}
	}

}
