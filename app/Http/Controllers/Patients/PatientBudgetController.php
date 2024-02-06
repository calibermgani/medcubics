<?php namespace App\Http\Controllers\Patients;
use Auth;
use View;
use Input;
use Session;
use Request;
use Response;
use Redirect;
use Validator;

class PatientBudgetController extends Api\PatientBudgetApiController 
{
	public function __construct()
	{
		View::share('heading', 'Patient');
		View::share('selected_tab', 'patientstatements');
		View::share('heading_icon', 'fa-user');
	}
	
	/*** Start to display patient budget view or add form ***/
	public function index($patient_id)
	{
		$api_response 		= 	$this->getShowApi($patient_id);
		$api_response_data 	= 	$api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			$patientBudget = $api_response_data->data->PatientBudget;
			$patientBudget_id = $api_response_data->data->patientbudget_id;
			$get_patientbalance = $api_response_data->data->get_patientbalance;
			return view('patients/patients/budgetplan/show',  compact('patient_id','patientBudget','patientBudget_id','get_patientbalance'));	
		}
		else
		{
			$api_response 			= $this->getCreateApi($patient_id);
			$api_response_data 		= $api_response->getData();
			$patient_balance		= $api_response_data->data->get_patientbalance;
			return view ( 'patients/patients/budgetplan/create', compact ( 'patient_id','patient_balance') );	
		}	
	}
	/*** End to display patient budget view or add form ***/
	
	/*** Start to store patient budget ***/
	public function store($patient_id = '')
	{
		$api_response 		= $this->getStoreApi($patient_id);
		$api_response_data 	= $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/budgetplan')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$patient_id.'/budgetplan')->withInput()->withErrors($api_response_data->message);
		}   
	}
	/*** End to store patient budget ***/
	
	/*** Start to edit patient budget ***/
	public function edit($patient_id,$budget_id)
	{
		$api_response 			= $this->getEditApi($patient_id,$budget_id);
		$api_response_data 		= $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}
		
		$patient_balance		= $api_response_data->data->get_patientbalance;
		$patient_budget			= $api_response_data->data->PatientBudget;
		$patientbudget_id			= $api_response_data->data->patientbudget_id;
		return view ( 'patients/patients/budgetplan/edit', compact ( 'patient_id','patient_balance','patient_budget','patientbudget_id') );	
	} 
	/*** End to edit patient budget ***/
	
	/*** Start to update patient budget ***/
	public function update($patient_id,$budget_id)
	{
		$api_response 		= $this->getUpdateApi($patient_id,$budget_id);
		$api_response_data 	= $api_response->getData();
		
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}
		
		if($api_response_data->status == 'success')
		{
			return Redirect::to('patients/'.$patient_id.'/budgetplan')->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('patients/'.$patient_id.'/budgetplan')->withInput()->withErrors($api_response_data->message);
		}   
	}
	/*** End to update patient budget ***/
	
	/*** Start to delete patient budget ***/
	public function deletebudget($patient_id,$budget_id)
	{
		$api_response = $this->deletebudgetApi($patient_id,$budget_id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'failure') 
		{
			return Redirect::to('patients')->with('message', $api_response_data->message);
		}
		
		return Redirect::to('patients/'.$patient_id.'/budgetplan')->with('success',$api_response_data->message);
	}
	/*** End to delete patient budget ***/
}
