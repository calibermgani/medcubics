<?php namespace App\Http\Controllers\Patients;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Redirect;
use View;


class CheckReturnController extends Api\CheckReturnApiController  {

	public function __construct() 
    {
        View::share('heading', 'Patient');
        View::share('selected_tab', 'patientpayment');
        View::share('heading_icon', 'fa-user');
    }
	
	public function index($patient_id)
	{
		$api_response = $this->getIndexApi($patient_id);
		$api_response_data = $api_response->getData();
		$returncheck = $api_response_data->data->returncheck;
		return view('patients/checkreturn/returncheck',compact('patient_id','returncheck'));
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($patient_id)
	{
		return view('patients/checkreturn/create',compact('patient_id'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($patient_id,Request $request)
	{
		$api_response = $this->getStoreApi($patient_id,$request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/returncheck/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
			{
				return Redirect::to('patients/'.$patient_id.'/returncheck/create')->withInput()->withErrors($api_response_data->message);
			} 
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($patient_id,$id)
	{
		$api_response = $this->getShowApi($patient_id,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$returncheck = $api_response_data->data->returncheck;
		$patient_id = $patient_id;
		return view ( 'patients/checkreturn/show',compact('returncheck','patient_id'));
	}
		else{
			return Redirect::to('returncheck')->with('error', $api_response_data->message);
		}
		
	}
		
	public function edit($patient_id,$id)
	{
		$api_response = $this->getEditApi($patient_id,$id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success'){
		$returncheck = $api_response_data->data->returncheck;
		return view('patients/checkreturn/edit',  compact('returncheck','patient_id'));
		}
		else{
			return Redirect::to('patients/'.$patient_id.'/returncheck/'.$id)->with('error', $api_response_data->message);
		}
	}	
	
	public function update($patient_id,$id,Request $request)
	{
		$api_response = $this->getUpdateApi($patient_id,Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') {
			return Redirect::to('patients/'.$patient_id.'/returncheck/'.$id.'/edit')->with('error', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/returncheck/'.$id)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$patient_id.'/returncheck/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}
	}

	/***	Remove the hold option from database ***/
	public function destroy($patient_id,$id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/returncheck')->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$patient_id.'/returncheck')->with('error', $api_response_data->message);
		}
	}
}
