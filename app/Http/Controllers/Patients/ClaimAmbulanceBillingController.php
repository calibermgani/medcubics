<?php namespace App\Http\Controllers\Patients;

use App\Http\Controllers\Controller;
use Request;
use View;

class ClaimAmbulanceBillingController extends Api\ClaimAmbulanceBillingApiController {

	public function create($patient_id)
	{
		return view('patients/ambulancebilling/create',compact('patient_id'));
	}
	public function store(Request $request)
	{
		$request = $request::all();	
		$api_response = $this->getStoreApi($request);
        $api_response_data = $api_response->getData();
        if ($api_response_data->status == 'success') 
        {            
           return json_encode($api_response_data);
        } 
        else 
        {
			return 'failiur';
        }
	}
	public function edit($id)
	{
		$api_response = $this->getEditApi($id);
        $api_response_data = $api_response->getData();
        $claimambulancedetail = $api_response_data->data->claimambulancedetail;
		return view('patients/ambulancebilling/edit', compact('claimambulancedetail'));
	}
	public function update($id, Request $request)
	{
		$request = $request::all();
		$api_response = $this->getUpdateApi($id, $request);
		$api_response_data 	= 	$api_response->getData();		
		if ($api_response_data->status == 'success') 
		{
           return $api_response_data->data;
        } 
        else 
        {
			return 'failiur';
        }
	}

}
