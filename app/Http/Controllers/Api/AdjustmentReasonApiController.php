<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AdjustmentReason as AdjustmentReason;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Http\Request;
use Input;
use File;
use Auth;
use Response;
use Validator;
use Schema;
use DB;
use Config;
use Lang;

class AdjustmentReasonApiController extends Controller {

	/*** Start to listing page ***/
	public function getIndexApi($export = "")
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$adjustment_reason     = AdjustmentReason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('add_type','User')->get()->all();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'AdjustmentReason',
								'heading'	=>	'AdjustmentReason',
								'fields'	=>	array(
												'adjustment_type'   =>    'Adjustment Type',
												'adjustment_reason' =>    'Adjustment Reason',
												'status'   			=>    'Status',
												'created_by'		=>	array('table'=>'creator','column' => 'short_name','label' => 'Created By'),
												'updated_by'		=>	array('table'=>'modifier','column' => 'short_name','label' => 'Updated By'),
												'updated_at'		=>	'Updated On',
												)
								);

			$callexport    	= new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $adjustment_reason, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('adjustment_reason')));
	}
	/*** End to listing page ***/
	
	/*** Start to store AdjustmentReason ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all()->get();
		$validator = Validator::make($request, AdjustmentReason::$rules, AdjustmentReason::messages());
		// Check validation.
		if($validator->fails())
		{    
			$errors = $validator->errors();	
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$user = Auth::user ()->id;
			$request['add_type'] = 'User';
			$adjustment_reason = AdjustmentReason::create($request);
			$adjustment_reason->created_by = $user;
			$adjustment_reason->created_at = date('Y-m-d H:i:s');
			$adjustment_reason->save ();
			$id = Helpers::getEncodeAndDecodeOfId($adjustment_reason->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));		
		}	
	}
	/*** End to store AdjustmentReason ***/

	/*** Start to view AdjustmentReason ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(AdjustmentReason::where('id', $id)->count()>0 && is_numeric($id)) 		// Check invalid id
		{
			$adjustmentreason = AdjustmentReason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('id',$id)->where('add_type','User')->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('adjustmentreason')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to view AdjustmentReason ***/

	/*** Start to edit AdjustmentReason ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(AdjustmentReason::where('id',$id)->where('add_type','User')->count()>0 && is_numeric($id))			// Check invalid id
		{
			$adjustmentreason = AdjustmentReason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('adjustmentreason')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to edit AdjustmentReason ***/

	/*** Start to update AdjustmentReason ***/
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(AdjustmentReason::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			if($request == '')
				$request = Request::all();
			$validator = Validator::make($request, AdjustmentReason::$rules, AdjustmentReason::messages());
			if ($validator->fails())
			{	$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				/*
				// Dont allow to inactive while assigned to any patient condition
				if(isset($request['status']) && $request['status'] == 'Inactive') {
					// Check if used then dont allow to delete
				}
				*/
				$adjustmentreason = AdjustmentReason::findOrFail($id);
				$adjustmentreason->update($request);
				$user = Auth::user ()->id;
				$adjustmentreason->updated_by = $user;
				$adjustmentreason->updated_at = date('Y-m-d H:i:s');
				$adjustmentreason->add_type = 'User';
				$adjustmentreason->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		}
		else
		{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to update AdjustmentReason ***/

	/*** Start to delete AdjustmentReason ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(AdjustmentReason::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			AdjustmentReason::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to delete AdjustmentReason ***/
	
	function __destruct() 
	{
    }
}
