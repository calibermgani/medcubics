<?php namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Api\ClinicalNotesApiController as ClinicalNotesApiController;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Http\Response;
use View;
use Redirect;

class ClinicalNotesController extends Api\ClinicalNotesApiController {

    public function __construct() {
        View::share('heading', 'Clinical Notes');
        View::share('selected_tab', 'clinicalnotes');
        View::share('heading_icon', 'fa-sticky-note');
    }
	/*** Lising page start ***/
    public function index($patient_id) 
	{ 
		$api_response 		= 	$this->getIndexApi($patient_id);
		$api_response_data 	= 	$api_response->getData();	
		if($api_response_data->status == 'success')
		{
			$clinical_notes		= 	$api_response_data->data->clinical_notes;
			$tabpatientid		= 	$patient_id;
			return view('patients/clinicalnotes/index',compact('patient_id','tabpatientid','clinical_notes'));
		}
		else
		{
		   return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('message', $api_response_data->message);;		
		}
    }
   /*** Lising page End ***/

	/*** Create page Start ***/
	public function create($patient_id)
	{
		$api_response 		= $this->getcreateApi($patient_id);
		$api_response_data  = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$claims			= $api_response_data->data->claims;	
			$provider		= $api_response_data->data->provider;	
			$facility		= $api_response_data->data->facility;	
			$category		= $api_response_data->data->clinical_category;	
			return view('patients/clinicalnotes/create',compact('patient_id','category','claims','provider','facility'));
		}
		else
		{
		   return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('message', $api_response_data->message);;		
		}
		
	}
	/*** Create page End ***/
	
	/*** Store page Start ***/	
	public function store($patient_id)
	{
		$api_response		= $this->getStoreApi($patient_id);
		$api_response_data	= $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('message',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
		} 
	}
	/*** Store page End ***/
	
	/*** Show page Start ***/	
	public function show($patient_id,$id)
	{
		$api_response			= $this->getShowApi($patient_id,$id);
		$api_response_data 		= $api_response->getData();	
		if($api_response_data->status=='failure')
		{
			return Redirect::to('patients')->with('message',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			$picture	= 	$api_response_data->data->picture;
			$file 		= 	Helpers::amazon_server_get_file($picture->document_path,$picture->filename);
			return (new Response ( $file, 200 ))->header ( 'Content-Type', $picture->mime );
		}
		else
		{
		   return Redirect::to($patient_id.'/clinicalnotes');		
		}
	}
	/*** Show page End ***/	
	
	/*** Edit page Start ***/
	public function edit($patient_id,$clinical_id)
	{
		$api_response 		= $this->getEditApi($patient_id,$clinical_id);
		$api_response_data  = $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return Redirect::to('patients')->with('message',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			$claims			= $api_response_data->data->claims;	
			$provider		= $api_response_data->data->provider;	
			$facility		= $api_response_data->data->facility;	
			$category		= $api_response_data->data->clinical_category;	
			$clinical_detail	= $api_response_data->data->clinical_detail;	
			return view('patients/clinicalnotes/edit',compact('patient_id','provider','facility','category','claims','clinical_detail'));
		}
		else
		{
			return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('error',$api_response_data->message);
		} 
		
		
	}
	/*** Edit page End ***/	
	
	/*** Update page Start ***/		
	public function update($patient_id,$claim_id)
	{
		$api_response		= $this->getUpdateApi($patient_id,$claim_id);
		$api_response_data	= $api_response->getData();
		if($api_response_data->status=='failure')
		{
			 return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('message',$api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::back()->withInput()->withErrors($api_response_data->message);
		} 
	}
	/*** Update page End ***/
	
	/*** Delete page Start ***/		
	public function destroy($patient_id,$id)
	{
		$api_response 		= $this->getDestroyApi($patient_id,$id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status=='failure')
		{
			return Redirect::to('patients')->with('message',$api_response_data->message);
		}
		if($api_response_data->status == 'success')
		{
			return Redirect::to('/patients/'.$patient_id.'/clinicalnotes')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$patient_id.'/clinicalnotes')->with('error',$api_response_data->message);
		} 
	}
	/*** Delete page End ***/
	
	/*** Get Claim detail via ajax function starts ***/
	public function claimdetails($patient_id,$id)
	{
		$api_response		= $this->claimdetailsApi($patient_id,$id);
		$api_response_data	= $api_response->getData();
		$patients_claims	= $api_response_data->data;
		$message = json_encode($patients_claims);
		 print_r($message);

	}
	/*** Get Claim detail via ajax function ends ***/
	
}
