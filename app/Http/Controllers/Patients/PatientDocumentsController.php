<?php namespace App\Http\Controllers\Patients;

use Redirect;
use View;
use Config;
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;
use App\Http\Helpers\Helpers as Helpers;

class PatientDocumentsController extends Api\PatientDocumentApiController 
{
	public $doc_type = 'patients';

	public function __construct() 
	{      
		View::share ( 'heading', 'Patient' );
		View::share ( 'selected_tab', 'documents' );  
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
    }  
	/*** lists page Starts ***/
	public function index($id)
	{
		$api_response = $this->getIndexApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return redirect('patients/')->with('message',$api_response_data->message);
		}      
		
		
		$pictures = $api_response_data->data->pictures;
		$claim_number = $api_response_data->data->claim_number;
		$patients = $api_response_data->data->type_details;
        $category_list		= 	$api_response_data->data->category_list;
        $total_document		= 	$api_response_data->data->total_document;
        $users		=	  $api_response_data->data->users;
		$categories		=	$api_response_data->data->categories; 		
		$insurances		=	$api_response_data->data->insurances;

		return view('patients/patients/Document/documents', compact('pictures','patients','category_list', 'claim_number','total_document','users', 'categories', 'insurances'));
	}
        
        
        
        
        
        public function documentsummary($id)
	{
            View::share ( 'heading', 'Patient' );
		View::share ( 'selected_tab', 'documents' );  
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.user'));
		$api_response = $this->getIndexApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return redirect('patients/')->with('message',$api_response_data->message);
		}      
		$pictures = $api_response_data->data->pictures;
		$claim_number = $api_response_data->data->claim_number;
		$patients = $api_response_data->data->type_details;
        $category_list		= 	$api_response_data->data->category_list;
		$priority = $api_response_data->data->priority;
		$user_list		= 	$api_response_data->data->user_list;
		$other_document		= 	$api_response_data->data->other_document;
        $prescription_document		= 	$api_response_data->data->prescription_document;
        $patient_corresp_document		= 	$api_response_data->data->patient_corresp_document;
        $payer_document		= 	$api_response_data->data->payer_document;
        $edi_document		= 	$api_response_data->data->edi_document;
        $procedure_document		= 	$api_response_data->data->procedure_document;
        $clinical_document		= 	$api_response_data->data->clinical_document;
        $authorization_document		= 	$api_response_data->data->authorization_document;
        $eligibility_document		= 	$api_response_data->data->eligibility_document;
        $patient_document		= 	$api_response_data->data->patient_document;
        $total_document_count		= 	$api_response_data->data->total_document_count;
        $assigned_document_count		= 	$api_response_data->data->assigned_document_count;
        $inprocess_document_count		= 	$api_response_data->data->inprocess_document_count;
        $pending_document_count		= 	$api_response_data->data->pending_document_count;
        $review_document_count		= 	$api_response_data->data->review_document_count;
        $completed_document_count		= 	$api_response_data->data->completed_document_count;
        $assigned_document		= 	$api_response_data->data->assigned_document;
		return view('patients/patients/Document/documentsummary', compact('pictures','patients','category_list', 'claim_number', 'priority','user_list','other_document','prescription_document','patient_corresp_document','payer_document','edi_document','procedure_document','clinical_document','authorization_document','eligibility_document','patient_document','total_document_count','assigned_document_count','inprocess_document_count','pending_document_count','review_document_count','completed_document_count','assigned_document'));
	}
        
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function create($id)
	{
		$api_response 		= 	$this->getCreateApi($id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status=='failure')
		{
			return redirect('patients/')->with('message',$api_response_data->message);
		} 
		$category_list		= 	$api_response_data->data->category_list;
		$patients = $api_response_data->data->patients;
        return view('patients/patients/Document/create',  compact('patients','category_list'));
	}
	/*** Create page Ends ***/
	
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to store the document in the database and aws.  
			Status		:: Start The Function Here
	***/
	public function addDocument($id) 
	{
		$api_response 		= 	$this->getAddDocumentApi($this->doc_type,$id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status=='failure')
		{
			return redirect('patients/')->with('message',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$id.'/documents')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
		} 
		
	}
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to store the document in the database and aws.  
			Status		:: End The Function Here
	***/
	
	
	
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to soft deleted the recored without assigned users record only deleted.  
			Status		:: Start The Function Here
	***/
	public function destroy($id,$patient_id)
	{
		
		$api_response 		= $this->getDestroyApi($this->doc_type,$id,$patient_id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return redirect('patients/')->with('message',$api_response_data->message);
		}
		if($api_response_data->status=='failure_document')
		{
			return redirect('patients/'.$patient_id.'/documentsummary')->with('message',$api_response_data->message);
		}
		return Redirect::to('patients/'.$patient_id.'/documentsummary')->with('success',$api_response_data->message);
	}
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to soft deleted the recored without assigned users record only deleted.  
			Status		:: End The Function Here
	***/
	
	
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to show the category based document lists and count.  
			Status		:: Start The Function Here
	***/
	
	public function show_document_listing($id){
		$api_response = $this->getDocumentListingApi($id);
		
	}
	
	/*** 	Author	 	:: Selvakumar V  
			Date 		:: 27-DEC-2017  
			Purpose	 	:: This function using to show the category based document lists and count.  
			Status		:: End The Function Here
	***/
	
	
	public function document_assigned_show($patient_id,$id)
	{	
		$api_response = $this->getAssignedApiList($patient_id,$id);
        $api_response_data = $api_response->getData();
        $assigned_document = $api_response_data->data->assigned_document;
        $practice = $api_response_data->data->practice;
        $patient_id = $api_response_data->data->patient_id;
        $claims_number = $api_response_data->data->claims_number;
		return view('patients/patients/Document/show',compact('patient_id','claims_number','assigned_document','practice','id'));	
	}
	
	public function document_assigned_store($doc_id){
		$api_response = $this->getStoreAssignedApiList($doc_id);
		return $api_response;
	}
	
	public function search_patient_document($patient_id){
		$api_response = $this->getFiltersearchDocument($patient_id);
		$api_response_data = $api_response->getData();
		$total_document = $api_response_data->data->total_document;
		return view('patients/patients/Document/document_ajax_list',compact('total_document'));

	}

	public function patient_document($patient_id){
		$api_response = $this->getPatientDocumentApi($patient_id);
		$api_response_data = $api_response->getData();
		$total_document = $api_response_data->data->total_document;
		return view('patients/patients/Document/document_ajax_list',compact('total_document'));

	}
	
	
}
