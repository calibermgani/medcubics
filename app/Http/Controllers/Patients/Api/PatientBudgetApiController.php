<?php namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\PatientBudget;
use App\Models\Patients\Patient;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Input;
use File;
use Auth;
use Response;
use Request;
use Validator;
use Schema;
use DB;
use App;
use Config;
use View;
use Lang;
use Mail;
use App\Http\Helpers\Helpers as Helpers;

class PatientBudgetApiController extends Controller 
{
	/*** Start to create patient budget form ***/
	public function getCreateApi($id)
	{
		$dbconnection = new DBConnectionController();
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        $get_patientbalance = $dbconnection->get_PatientBalance($id);
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('get_patientbalance')));
	}
	/*** End to create patient budget form ***/
	
	/*** Start to store patient budget details ***/
	public function getStoreApi($id)
	{
		if($id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		
		$request = request::all();
		
		$validator = Validator::make($request, PatientBudget::$rules, PatientBudget::$messages);
		
		
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$request['patient_id'] =  $patient_id;
			$request['statement_start_date'] = date('Y-m-d',strtotime($request['statement_start_date'])); 
			$request['budget_balance'] = $request['budget_total']; 
			$PatientBudget = PatientBudget::create($request);
			$user = Auth::user ()->id;
			$PatientBudget->created_by = $user;
			$PatientBudget->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>''));					
		}
	}
	/*** End to store patient budget details ***/
	
	/*** Start to show patient budget details ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Patient::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			if(PatientBudget::where('patient_id',$id)->count())
			{
				$PatientBudget 			= 	PatientBudget::where('patient_id',$id)->first();
				$PatientBudget->statement_start_date = Helpers::dateFormat($PatientBudget->statement_start_date);
				$PatientBudget->budget_period = Helpers::dateFormat($PatientBudget->budget_period);
				
				$dbconnection = new DBConnectionController();
				$get_patientbalance = $dbconnection->get_PatientBalance($id);
				 
				if($PatientBudget->last_statement_sent_date != '0000-00-00') 
					$PatientBudget->last_statement_sent_date = Helpers::dateFormat($PatientBudget->last_statement_sent_date);

				 $patientbudget_id = Helpers::getEncodeAndDecodeOfId($PatientBudget->id,'encode');		
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('PatientBudget','patientbudget_id','get_patientbalance')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>'','data'=>null));
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	/*** End to show patient budget details ***/
	
	/*** Start to edit patient budget details ***/
	public function getEditApi($patient_id,$budget_id)
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id)) 
		{
			$dbconnection = new DBConnectionController();
			$get_patientbalance = $dbconnection->get_PatientBalance($patient_id);
			$PatientBudget 			= 	PatientBudget::where('patient_id',$patient_id)->first();
			$patientbudget_id = 	Helpers::getEncodeAndDecodeOfId($PatientBudget->id,'encode');	
			$PatientBudget->budget_period = date('m/d/Y',strtotime($PatientBudget->budget_period));
			$PatientBudget->statement_start_date = date('m/d/Y',strtotime($PatientBudget->statement_start_date));

			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('get_patientbalance','PatientBudget','patientbudget_id')));
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	/*** End to edit patient budget details ***/
	
	/*** Start to update patient budget details ***/
	public function getUpdateApi($id,$budget_id)
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$budget_id = Helpers::getEncodeAndDecodeOfId($budget_id,'decode'); 
		
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id) && PatientBudget::where('id', $budget_id)->count()>0) 
		{
			$request = request::all();
			$validator = Validator::make($request, PatientBudget::$rules, PatientBudget::$messages);
			
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$request['patient_id'] =  $patient_id;
				$request['statement_start_date'] = date('Y-m-d',strtotime($request['statement_start_date'])); 
				$PatientBudget 	= PatientBudget::findOrFail($budget_id);
				$PatientBudget->update($request);
				
				$user = Auth::user ()->id;
				$PatientBudget->created_by = $user;
				$PatientBudget->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' =>Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	/*** End to update patient budget details ***/
	
	/*** Start to delete patient budget details ***/
	public function deletebudgetApi($id,$budget_id)
	{	
		$patient_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$budget_id = Helpers::getEncodeAndDecodeOfId($budget_id,'decode'); 
		if(Patient::where('id', $patient_id)->count()>0 && is_numeric($patient_id) && PatientBudget::where('id', $budget_id)->count()>0) 
		{
			PatientBudget::where('id',$budget_id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}	
	}
	/*** End to delete patient budget details ***/
	
	
	function __destruct() 
	{
    }
}