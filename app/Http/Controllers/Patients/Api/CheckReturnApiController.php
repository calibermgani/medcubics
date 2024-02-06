<?php namespace App\Http\Controllers\Patients\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Patients\CheckReturn as CheckReturn;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;

use Response;
use Validator;
use Lang;
use Auth;
use Illuminate\Http\Request;

class CheckReturnApiController extends Controller {

	public function getIndexApi($patient_id, $export="")
	{
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		$returncheck = CheckReturn::where('patient_id',$patient_id)->get();
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'CheckReturn',
				'heading'		=>	'',
				'fields' 		=>	array(
					'check_no'			=>	'Check No',
					'check_date'		=>	'Check Date',
					'financial_charges'	=>	'Financial Charges',
					)
			);
                       
			$callexport = new CommonExportApiController();
		return $callexport->generatemultipleExports($exportparam, $returncheck, $export); 
                
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('returncheck')));
		//
	}

	public function getStoreApi($patient_id,$request='')
	{
		if($request == '')
			$request = Request::all();
			
			$messages = CheckReturn::messages();
			$rule = CheckReturn::$rules+array('check_no' => 'required|unique:check_returns,check_no,NULL,id,deleted_at,NULL');
		// Check the option for unique
			
		$validator = Validator::make($request,$rule,$messages);
		// Check validation.
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$request['check_date'] = date('Y-m-d',strtotime($request['check_date']));
			$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
			$returncheck = CheckReturn::create($request);
			$user = Auth::user ()->id;
			$returncheck->created_by = $user;
			$returncheck->created_at = date('Y-m-d h:i:s');
			$returncheck->updated_at = '0000-00-00 00:00:00';
			$returncheck->patient_id = $patient_id ;
			$id = Helpers::getEncodeAndDecodeOfId($returncheck->id,'encode');
			$returncheck->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));
						
		}
	}
	public function getShowApi($patient,$id)
	{
		$patient = Helpers::getEncodeAndDecodeOfId($patient,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(CheckReturn::where('id',$id)->count()>0 && is_numeric($id))  			// Check invalid id
		{
			$returncheck = CheckReturn::with('creator','modifier')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('returncheck')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
		
	public function getEditApi($patient_id,$id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
		if(CheckReturn::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			$returncheck = CheckReturn::find($id);
			$returncheck->check_date = Helpers::dateFormat($returncheck->check_date,'dob');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('returncheck')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	public function getUpdateApi($patient_id, $request='', $id)
	{
		if($request == '')
			$request = Request::all();
		
		// Check the option for unique
			
		//$validator = Validator::make($request,$rule,$messages);
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$messages = CheckReturn::messages();
		$rule = CheckReturn::$rules+array('check_no' => 'required|unique:check_returns,check_no,'.$id.',id,deleted_at,NULL');
		if(CheckReturn::where('id',$id)->count()>0 && is_numeric($id))		// Check invalid id
		{
			// Check the option for unique
			$validator = Validator::make($request, $rule, $messages);
			// Check validation.
			if ($validator->fails())
			{	
				$errors = $validator->errors(); 
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{
				$request['check_date'] = date('Y-m-d',strtotime($request['check_date']));
				$patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
				$returncheck = CheckReturn::findOrFail($id);
				$returncheck->update($request);
				$user = Auth::user ()->id;
				$returncheck->updated_by = $user;
				$returncheck->updated_at = date('Y-m-d h:i:s');
				$returncheck->patient_id = $patient_id ;
				$returncheck->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		}
		else
		{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to update hold option ***/

	/*** Start to delete hold option ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(CheckReturn::where('id',$id)->count()>0 && is_numeric($id))
		{
			CheckReturn::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		}
		else
		{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to delete hold option ***/
	
	function __destruct() 
	{
    }

}
