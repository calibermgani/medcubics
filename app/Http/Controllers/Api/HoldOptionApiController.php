<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use View;
use App\Models\Holdoption as Holdoption;
use App\Http\Helpers\Helpers as Helpers;
use Requests;
use DB;
use Lang;

class HoldOptionApiController extends Controller {
  
	/*** Start to listing page ***/
	public function getIndexApi($export='')
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$holdoption = Holdoption::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->get();
		
		 if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'holdoption',
				'heading'		=>	'',
				'fields' 		=>	array(
					'option'				=>	'Hold Option',
					'status'				=>	'Status',
					'created_by'		=>	array('table'=>'creator','column' => 'short_name','label' => 'Created By'),
					'updated_by'		=>	array('table'=>'modifier','column' => 'short_name','label' => 'Updated By'),
					'updated_at'		=>	'Updated On',
					)
			);
                       
			$callexport = new CommonExportApiController();
		return $callexport->generatemultipleExports($exportparam, $holdoption, $export); 
                
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('holdoption')));
	}
	/*** End to listing page ***/
	
	/*** Start to create hold option ***/
	public function getCreateApi()
	{
		$holdoption = Holdoption::all();
		return view('practice/holdoption/create');
	}
	/*** End to create hold option ***/
	
	/*** Start to store hold option ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		
		// Check the option for unique
		$validator = Validator::make($request, Holdoption::$rules+array('option' => 'required|unique:holdoptions,option,NULL,id,deleted_at,NULL'), Holdoption::messages());
		
		// Check validation.
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$Holdoption = Holdoption::create($request);
			$user = Auth::user ()->id;
			$Holdoption->created_by = $user;
			$Holdoption->created_at = date('Y-m-d H:i:s');
			$id = Helpers::getEncodeAndDecodeOfId($Holdoption->id,'encode');
			$Holdoption->save ();

			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));		
		}
	}
	/*** End to store hold option ***/
	
	/*** Start to view hold option ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(Holdoption::where('id',$id)->count()>0 && is_numeric($id))  			// Check invalid id
		{
			$holdoption = Holdoption::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('holdoption')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to view hold option ***/
	
	/*** Start to edit hold option ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(Holdoption::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			$holdoption = Holdoption::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('holdoption')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to edit hold option ***/
	
	/*** Start to update hold option ***/
	public function getUpdateApi($type, $id, $request='')
	{
		if($request == '')
			$request = Request::all();
		
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Holdoption::where('id',$id)->count()>0 && is_numeric($id))		// Check invalid id
		{
			// Check the option for unique
			$validator = Validator::make($request, Holdoption::$rules+array('option' => 'required|unique:holdoptions,option,'.$id.',id,deleted_at,NULL'), Holdoption::messages());
			// Check validation.
			if ($validator->fails())
			{	
				$errors = $validator->errors(); 
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				// Dont allow to inactive while assigned to any claim condition
				if(isset($request['status']) && $request['status'] == 'Inactive') {
					$usedCnt = Holdoption::getClaimHoldReasonAppliedCount($id);
					if($usedCnt > 0) {						
						return Response::json(array('status'=>'failure', 'message'=>"Unable to deactivate due to Hold assigned to claim, Please unassign to proceed further", 'data'=>''));			
					}
				}		

				$holdoption = Holdoption::findOrFail($id);
				$holdoption->update(Request::all());
				$user = Auth::user ()->id;
				$holdoption->updated_by = $user;
				$holdoption->updated_at = date('Y-m-d H:i:s');
				$holdoption->save ();
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
		if(Holdoption::where('id',$id)->count()>0 && is_numeric($id)) {
			$usedCnt = Holdoption::getClaimHoldReasonAppliedCount($id);
			if($usedCnt > 0) {						
				return Response::json(array('status'=>'failure', 'message'=>"Unable to delete due to Claim Hold Reason assigned to claim, Please unassign to proceed further", 'data'=>''));			
			}
			Holdoption::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		} else{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to delete hold option ***/
	
	function __destruct() 
	{
    }

}