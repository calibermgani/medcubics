<?php namespace App\Http\Controllers;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use View;
use Session;
use Config;
class PracticeNotesController extends Api\NotesApiController 
{
	public $note_type = 'practice';
	
	public function __construct() 
	{      
		View::share('heading','Practice');
		View::share('selected_tab','practice');  
		View::share('heading_icon',Config::get('cssconfigs.Practicesmaster.practice'));
    }  
	
	/********************** Start Display a listing of the notes ***********************************/
	public function index()
	{
		$api_response 		= $this->getIndexApi($this->note_type);
		$api_response_data 	= $api_response->getData();
		$notes 				= $api_response_data->data->notes;
		$practice 			= $api_response_data->data->type_details;
		return view('practice/practice/notes/notes',compact('notes','practice'));
	}
	/********************** End Display a listing of the notes ***********************************/

	/********************** Start Display note created page ***********************************/
	public function create()
	{
		$api_response 		= $this->getCreateApi($this->note_type);
		$api_response_data 	= $api_response->getData();
		$practice 			= $api_response_data->data->type_details;
		if(Request::ajax()){
			return view('practice/practice/notes/create_ajax',compact('practice'));
		}
		return view('practice/practice/notes/create',compact('practice'));
	}
	/********************** End Display note created page ***********************************/

	/********************** Start note added process ***********************************/
	public function store(Request $request)
	{
		$api_response 		= $this->getStoreApi($this->note_type, $request::all());
		$api_response_data 	= $api_response->getData();
		if(Request::ajax()){
			return json_encode($api_response_data);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('notes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('notes')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/********************** End note added process ***********************************/
	
	/********************** Start Display note edit page ***********************************/
	public function edit($id)
	{
 		$api_response 		= $this->getEditApi($this->note_type,0,$id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$practice 	= $api_response_data->data->type_details;
			$notes 		= $api_response_data->data->notes;
			if(Request::ajax()){
				return view('practice/practice/notes/edit_ajax',compact('notes','practice'));
			}
			return view('practice/practice/notes/edit', compact('notes','practice'));
		}
		else
		{
			return Redirect::to('notes')->with('message', $api_response_data->message);
		}
	}
	/********************** End Display note edit page ***********************************/

	/********************** Start note update process ***********************************/
	public function update($id, Request $request)
	{
		$api_response 		= $this->getUpdateApi($this->note_type,$request::all(),0,$id);
		$api_response_data 	= $api_response->getData();
		if(Request::ajax()){
			return json_encode($api_response_data);
		}
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('notes')->with('message', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('notes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('notes/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/********************** End note update process ***********************************/

	/********************** Start note deleted process ***********************************/
	public function deleteNotes($id)
	{
		$api_response 		= $this->getDeleteApi($this->note_type, $id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('notes')->with('message', $api_response_data->message);
		}
		
		return Redirect::to('notes')->with('success',$api_response_data->message);
	}
	/********************** End note deleted process ***********************************/

}
