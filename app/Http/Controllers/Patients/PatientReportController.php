<?php namespace App\Http\Controllers\Patients;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View;
use Request;
use Redirect;
use PDF;
use App;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patients\PatientEligibility;
use Response;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;

class PatientReportController extends Api\PatientReportApiController {
	
	 public function __construct() {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'reports');
        View::share('heading_icon', 'fa-user');
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($patient_id)
	{		
	
		$api_response = $this->getindexApi($patient_id);		
        $api_response_data = $api_response->getData();
	    if($api_response_data->status == 'failure') {
			return Redirect::to('patients')->with('error', $api_response_data->message);
		}		
		$eligibility = $api_response_data->data->eligibility;
		$benefit_verification = $api_response_data->data->benefit_verification;
		$patient_insurances = $api_response_data->data->patient_insurances;
		return view('patients/reports/reports', compact('patient_id','eligibility','benefit_verification','patient_insurances'));
	}
        
        
        public function listing($patient_id)
	{		
            View::share('selected_tab', 'reports1');
	
		$api_response = $this->getindexApi($patient_id);		
        $api_response_data = $api_response->getData();
	    if($api_response_data->status == 'failure') {
			return Redirect::to('patients')->with('error', $api_response_data->message);
		}		
		$eligibility = $api_response_data->data->eligibility;
		$benefit_verification = $api_response_data->data->benefit_verification;
		$patient_insurances = $api_response_data->data->patient_insurances;
		return view('patients/reports/reports1', compact('patient_id','eligibility','benefit_verification','patient_insurances'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($patient_id)
	{
		$api_response = $this->getCreateApi($patient_id);
        $api_response_data = $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			 return Redirect::to('patients')->with('error', $api_response_data->message);
		}
		$templates = $api_response_data->data->templates;
		$templates 	 = array_flip(json_decode(json_encode($templates), True));  
		$templates	 = array_flip(array_map(array($this,'getTemplateEncode'),$templates));
		$insurance = $api_response_data->data->insurance_details;
		return view('patients/eligibility/create', compact('insurance','templates','patient_id'));
	}
	
	function getTemplateEncode($num)
	{
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}	

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id,Request $request)
	{
		$request = $request::all();		
		$api_response = $this->getStoreApi($id,Request::all());        
        $api_response_data = $api_response->getData();	
		
		if($api_response_data->status == 'failure') 
		{
			 return Redirect::to('patients')->with('error', $api_response_data->message);
		}
        if ($api_response_data->status == 'success') 
		{
			$eligiblityid 		= $api_response_data->data;
            return Redirect::to('patients/'.$id.'/eligibility/'.$eligiblityid)->with('success', $api_response_data->message);
        } 
		else 
		{
            return Redirect::to('patients/'.$id.'/eligibility/create')->withInput()->withErrors($api_response_data->message);
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($patient_id,$eligibility_id)
	{
		$api_response = $this->getshowApi($patient_id,$eligibility_id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
				 return Redirect::to('patients')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'error') {
				 return Redirect::to('patients/'.$patient_id.'/eligibility')->with('error', $api_response_data->message);
		}
		$eligibility = $api_response_data->data->eligibility;
		
		
		return view('patients/eligibility/show', compact('eligibility','patient_id'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($patient_id,$elibility_id)
	{		
		$api_response = $this->geteditApi($patient_id,$elibility_id);    
        $api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
				 return Redirect::to('patients')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'error') {
				 return Redirect::to('patients/'.$patient_id.'/eligibility')->with('message', $api_response_data->message);
		}
		$insurance 		= $api_response_data->data->insurance_details;
		$eligibility 	= $api_response_data->data->eligibility;
		$templates 		= $api_response_data->data->templates;
		$templates 	 	= array_flip(json_decode(json_encode($templates), True));  
		$templates	 	= array_flip(array_map(array($this,'getTemplateEncode'),$templates));
		return view('patients/eligibility/edit', compact('insurance','eligibility', 'templates', 'patient_id'));
        		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($patientid,$eligiblityid, Request $request)
	{
		$api_response = $this->getupdateApi($patientid,$eligiblityid);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
				 return Redirect::to('patients')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'failure_nodetail') 
		{
				 return Redirect::to('patients/'.$patientid.'/eligibility')->with('message', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patientid.'/eligibility/'.$eligiblityid)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$patientid.'/eligibility/'.$eligiblityid.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($patient_id, $eligibility_id)
	{
		$api_response = $this->getDeleteApi($patient_id, $eligibility_id);
		$api_response_data 	= 	$api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('patients')->with('error', $api_response_data->message);
		}
		if($api_response_data->status == 'failure_nodetail') 
		{
				 return Redirect::to('patients/'.$patient_id.'/eligibility')->with('message', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/eligibility')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$patient_id.'/eligibility')->with('error', $api_response_data->message);
		}
	}
	public function showpdf($type,$id)
	{
		$value = PatientEligibility::find($id);
		$pdf = App::make('dompdf.wrapper');		
	    $pdf->loadHTML($value['content']);
		//$this->getDownload($value['file_path'], $value['filename']);
		return $pdf->stream();	
	}
	public function getDownload($path, $file_name){
        //PDF file is stored under project/public/download/info.pdf
        $file= public_path(). $path;
        $headers = array(
              'Content-Type: application/pdf',
            );
        return Response::download($file, $file_name, $headers);
   }
   
   public function GetAuthTokenPverifyPatient(){
		$api_response = $this->GetAuthTokenAPIPverifyPatient();
		$result = json_decode($api_response,True);
		echo "<pre>";print_r($result['access_token']);die;
		
   }
   public function eligibilitytemplate($patient_id)
	{		
		$api_response = $this->getEligibilityTemplateApi($patient_id);		
        $api_response_data = $api_response->getData();
	    if($api_response_data->status == 'failure') {
			return Redirect::to('patients')->with('error', $api_response_data->message);
		}		
		$eligibility = $api_response_data->data->eligibility;
		$benefit_verification = $api_response_data->data->benefit_verification;
		$patient_insurances = $api_response_data->data->patient_insurances;
		return view('patients/eligibility/eligibilitytemplate', compact('patient_id','eligibility','benefit_verification','patient_insurances'));
	}
}
