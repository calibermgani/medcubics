<?php 
namespace App\Http\Controllers\Patients;

use Request;
use Redirect;
use View;
use Config;
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;

class PatientNotesController extends Api\NotesApiController 
{
	public function __construct() 
	{      
		View::share ( 'heading', 'Patient' );
		View::share ( 'selected_tab', 'patientnote' );  
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }  

	/*** lists page Starts ***/
	public function index($id)
	{
		$api_response = $this->getIndexApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return redirect('patients')->with('message',$api_response_data->message);
		}
		else
		{
			$patients = $api_response_data->data->type_details;
			$notes = $api_response_data->data->notes;
            $claims_id = $api_response_data->data->claims_id;                        
			return view('patients/patients/notes/notes', compact('notes','patients','claims_id'));
		}
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function create($id){
		$api_response = $this->getCreateApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return redirect('patients')->with('message',$api_response_data->message);
		}
        else
		{
			$patients = $api_response_data->data->type_details;
			$claims_id = $api_response_data->data->claims_id;
			$notes_exist = $api_response_data->data->notes_exist;
			$view = 'patients/patients/notes/create';
			if(Request::ajax()){
				$view = 'patients/patients/notes/create_ajax';
			}			
			return view($view, compact('claims_id','patients', 'notes_exist'));
		}
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function store($id,Request $request)
	{
		$api_response = $this->getStoreApi($id,$request::all());
		$api_response_data = $api_response->getData();
		if(Request::ajax()){
				return json_encode($api_response_data);
			}
		if($api_response_data->status=='failure')
		{
			if(Request::ajax()){
				return "error";
			}
			return redirect('patients')->with('message',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			if(Request::ajax()){
				return "success";
			}
			return Redirect::to('patients/'.$id.'/notes')->with('success', $api_response_data->message);
		}
		else
		{			
			return Redirect::to('patients/'.$id.'/notes')->with('error', "Type already selected");
		}        
	}
	/*** Store Function Ends ***/
	
	/*** Edit page Starts ***/
	public function edit($id,$type_id)
	{
 		$api_response = $this->getEditApi($type_id,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return redirect('patients')->with('message',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			$patients = $api_response_data->data->type_details;
			$claims_id = $api_response_data->data->claims_id;
			$notes = $api_response_data->data->notes;
			$type_detailsID = $api_response_data->data->type_detailsID;
			$notesID = $api_response_data->data->notesID;
			$view = 'patients/patients/notes/edit';
			if(Request::ajax()){
				$view = 'patients/patients/notes/edit_ajax';
			}
			return view($view, compact('claims_id','notes','patients','patients_insurance','patient_tabs_details','patient_tabs_insurance_details','patient_tabs_insurance_count','notesID','type_detailsID'));
		}
		else
		{
			return Redirect::to('patients/'.$id.'/notes')->with('message',$api_response_data->message);
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function update($id,$type_id,Request $request)
	{
		$api_response = $this->getUpdateApi($type_id,$request::all(), $id);
		$api_response_data = $api_response->getData();
		if(Request::ajax()){
				return json_encode($api_response_data);
			}
		if($api_response_data->status=='failure')
		{
			return redirect('patients')->with('message',$api_response_data->message);
		}
		
		if($api_response_data->status=='failure_note')
		{
			return Redirect::to('patients/'.$id.'/notes')->with('message',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$id.'/notes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$id.'/notes')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function deleteNotes($patient_id,$type_id)
	{
		$api_response = $this->getDeleteApi($type_id,$patient_id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='success')
		{
			return Redirect::to('patients/'.$patient_id.'/notes')->with('success',$api_response_data->message);
		}
		else
		{
			return redirect('patients')->with('message',$api_response_data->message);
		}
		
	}
	/*** Delete Function Starts ***/
	
	
	public function statusNotes(){
		$api_response = $this->getChangeStatusApi();
		$api_response_data = $api_response->getData();
		$data['data'] = $api_response_data->data->status;
		$data['status'] =  $api_response_data->status;
		return $data;
	}
}
