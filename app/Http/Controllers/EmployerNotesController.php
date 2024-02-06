<?php namespace App\Http\Controllers;

use Request;
use Redirect;
use Auth;
use View;
use Config;

class EmployerNotesController extends Api\NotesApiController 
{
	public $note_type = 'employer';

	public function __construct() 
	{        
		View::share ( 'heading', 'Employer' );  
		View::share ( 'selected_tab', 'employer' ); 
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }  
	/*** lists page Starts ***/
	public function index($id)
	{
		$api_response = $this->getIndexApi($this->note_type,$id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			$notes = $api_response_data->data->notes;
			$employer = $api_response_data->data->type_details;
			return view('practice/employer/notes/notes',  compact('notes','employer'));
		}
		else
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function create($id)
	{
		$api_response = $this->getCreateApi($this->note_type,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$employer = $api_response_data->data->type_details;
			return view('practice/employer/notes/create', compact('employer'));
		}
		else
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function store($id, Request $request)
	{
		$api_response = $this->getStoreApi($this->note_type, $request::all(),$id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('employer/'.$id.'/notes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('employer/'.$id.'/notes/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Store Function Ends ***/
	
	/*** Edit page Starts ***/
	public function edit($employer_id,$id)
	{
 		$api_response = $this->getEditApi($this->note_type,$employer_id,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure_employer') 
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			$employer = $api_response_data->data->type_details;
			$notes = $api_response_data->data->notes;
			return view('practice/employer/notes/edit', compact('notes','employer'));
		}
		else
		{
			return Redirect::to('employer/'.$employer_id.'/notes')->with('error', $api_response_data->message);
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function update($employer_id,$id, Request $request)
	{
		$api_response = $this->getUpdateApi($this->note_type, $request::all(),$employer_id, $id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure_employer') 
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('employer/'.$employer_id.'/notes')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('employer/'.$employer_id.'/notes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('employer/'.$employer_id.'/notes/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function deleteNotes($employer_id,$id)
	{
		$api_response = $this->getDeleteApi($this->note_type, $id,$employer_id);
		$api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure_employer') 
		{
			return Redirect::to('employer')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('employer/'.$employer_id.'/notes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('employer/'.$employer_id.'/notes')->with('error',$api_response_data->message);
		}
	}
	/*** Delete Function Ends ***/
}
