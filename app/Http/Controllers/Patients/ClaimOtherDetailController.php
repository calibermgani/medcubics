<?php namespace App\Http\Controllers\Patients;
use App\Http\Controllers\Controller;
use Request;
use view;
use Redirect;

class ClaimOtherDetailController extends Api\ClaimOtherDetailApiController {

	public function create($patient_id)
	{
		return view('patients/claimotherdetail/create', compact('patient_id'));
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
        $claimotherdetail = $api_response_data->data->claimotherdetail;
		return view('patients/claimotherdetail/edit', compact('claimotherdetail'));
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
