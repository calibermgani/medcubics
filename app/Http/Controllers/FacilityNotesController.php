<?php namespace App\Http\Controllers;

use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use View;
use Session;
use Config;
use App\Models\Facility;
use App\Models\Note;

class FacilityNotesController extends Api\NotesApiController 
{
	public $note_type = 'facility';

	public function __construct() 
	{      
       View::share( 'heading', 'Practice' );  
	   View::share( 'selected_tab', 'facility' );
	   View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }  

	/*** Start to Display Listing of the notes	 ***/
	public function index($id)
	{
		$api_response 		= $this->getIndexApi($this->note_type,$id);
		$api_response_data 	= $api_response->getData();	
		if($api_response_data->status == 'success')
		{
			$notes 				= $api_response_data->data->notes;
			$facility 			= $api_response_data->data->type_details;
			return view('practice/facility/notes/notes',  compact('notes','facility'));
		}
		else
		{
			return redirect("facility")->with('error',$api_response_data->message);
		} 
	}
	/*** End to Display Listing of the notes	 ***/

	/*** Start to Create the notes	 ***/
	public function create($id)
	{
		$api_response 		= $this->getCreateApi($this->note_type,$id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$facility 			= $api_response_data->data->type_details;
			if(Request::ajax()){
				return view('practice/facility/notes/create_ajax',compact('facility'));
			}
			return view('practice/facility/notes/create', compact('facility'));
		}
		else
		{
			return redirect("facility")->with('error',"No facility details found");
		} 
	}
	/*** End to Create the notes	 ***/

	/*** Start to Store the notes	 ***/
	public function store($id, Request $request)
	{
		$api_response 		= $this->getStoreApi($this->note_type, $request::all(),$id);
		$api_response_data 	= $api_response->getData();
		if(Request::ajax()){
			return json_encode($api_response_data);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facility/'.$id.'/notes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('facility/'.$id.'/notes')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** End to Store the notes	 ***/
	
	/*** Start to Edit the notes	 ***/
	public function edit($facility_id,$id)
	{
		$api_response 		= $this->getEditApi($this->note_type,$facility_id,$id);
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'failure_facility') 
		{
			return redirect("facility")->with('error',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			$facility 			= $api_response_data->data->type_details;
			$notes 				= $api_response_data->data->notes;
			if(Request::ajax()){
				return view('practice/facility/notes/edit_ajax',compact('notes','facility'));
			}
			return view('practice/facility/notes/edit', compact('notes','facility'));
		}
		else
		{
			return redirect('facility/'.$facility_id.'/notes')->with('error',"No notes details found");
		} 
	}
	/*** End to Edit the notes	 ***/

	/*** Start to Update the notes	 ***/
	public function update($facility_id,$id, Request $request)
	{
		$api_response 			= $this->getUpdateApi($this->note_type, $request::all(),$facility_id, $id);
		$api_response_data 		= $api_response->getData();
		if(Request::ajax()){
			return json_encode($api_response_data);
		}
		if($api_response_data->status == 'failure_facility') 
			return redirect("facility")->with('error',$api_response_data->message);
		
		if($api_response_data->status == 'failure') 
			return redirect('facility/'.$facility_id.'/notes')->with('error',$api_response_data->message);
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facility/'.$facility_id.'/notes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('facility/'.$facility_id.'/notes/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** End to Update the notes	 ***/

	/*** Start to Delete the notes	 ***/
	public function deleteNotes($facility_id,$id)
	{
		$api_response 		= $this->getDeleteApi($this->note_type, $id,$facility_id);
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'failure_facility') 
			return redirect("facility")->with('error',$api_response_data->message);
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('facility/'.$facility_id.'/notes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('facility/'.$facility_id.'/notes')->with('error',$api_response_data->message);
		} 	
	}
	/*** End to Delete the notes	 ***/
}
