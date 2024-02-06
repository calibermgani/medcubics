<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\ClaimSubStatus as ClaimSubStatus;
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

class ClaimSubStatusApiController extends Controller
{
    /**
	 * Display a listing of the Claim Sub Status.
	 *
	 * @return Response
	 */
	public function getIndexApi($export = "") {
		$practice_timezone = Helpers::getPracticeTimeZone();
		$claimsubstatus     = ClaimSubStatus::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->get()->all();

		if($export != "") {
			$exportparam 	= 	array(
								'filename'	=>	'ClaimSubStatus',
								'heading'	=>	'ClaimSubStatus',
								'fields'	=>	array(
												'adjustment_type'   =>    'Adjustment Type',
												'$claimsubstatus' =>    'Claim Sub-Status',
												'status'   			=>    'Status',
												'created_by'		=>	array('table'=>'creator','column' => 'short_name','label' => 'Created By'),
												'updated_by'		=>	array('table'=>'modifier','column' => 'short_name','label' => 'Updated By'),
												'updated_at'		=>	'Updated On'	)
								);

			$callexport    	= new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $claimsubstatus, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('claimsubstatus')));
	}

	/*** Start to create claim sub status ***/
	public function getCreateApi()
	{
		$claimsubstatus = ClaimSubStatus::all();
		return view('practice/claimsubstatus/create');
	}
	/*** End to create hold option ***/

	/**
	 * Store Claim Sub Status
	 *
	 * @return Response
	 */
	public function getStoreApi($request='') {
		if($request == '')
			$request = Request::all();
		
		$rules = ClaimSubStatus::$rules;
		$rules['sub_status_desc'] = 'unique:claim_sub_status,sub_status_desc';
		$messages = ClaimSubStatus::messages();
		$validator = Validator::make($request, $rules, $messages);		
		// Check validation.
		if($validator->fails()) {    
			$errors = $validator->errors();	
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		} else {	
			$user = Auth::user ()->id;
			$claimsubstatus = ClaimSubStatus::create($request);
			$claimsubstatus->created_by = $user;
			$claimsubstatus->created_at = date('Y-m-d H:i:s');
			$claimsubstatus->updated_at = date('Y-m-d H:i:s');
			$claimsubstatus->save ();
			$id = Helpers::getEncodeAndDecodeOfId($claimsubstatus->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"), 'data'=>$id));		
		}	
	}

	/**
	 * View Claim Sub Status.
	 *
	 * @return Response
	 */	
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(ClaimSubStatus::where('id', $id)->count()>0 && is_numeric($id)) {		// Check invalid id		
			$claimsubstatus = ClaimSubStatus::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('id',$id)->orderBy('id')->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('claimsubstatus')));	
		} else {
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	/**
	 * Show the form for editing the claim sub status.
	 *
	 * @param  int  $id
	 * @return Response
	 */	
	public function getEditApi($id) {
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(ClaimSubStatus::where('id',$id)->count()>0 && is_numeric($id)) {			// Check invalid id 
			$claimsubstatus = ClaimSubStatus::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('claimsubstatus')));
		} else {
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}


	/**
	 * Update the specified resource in claim sub status.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getUpdateApi($id, $request='') {		
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(ClaimSubStatus::where('id',$id)->count()>0 && is_numeric($id)) {			// Check invalid id		
			if($request == '')
				$request = Request::all();

			$rules = ClaimSubStatus::$rules;
			$rules['sub_status_desc'] = 'unique:claim_sub_status,sub_status_desc,'.$id;
			$messages = ClaimSubStatus::messages();
			$validator = Validator::make($request, $rules, $messages);		

			if ($validator->fails()) {	
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			} else {	
				
				// Dont allow to inactive while assigned to any claim condition
				if(isset($request['status']) && $request['status'] == 'Inactive') {
					$usedCnt = ClaimSubStatus::getClaimSubStatusAppliedCount($id);
					if($usedCnt > 0) {						
						return Response::json(array('status'=>'failure', 'message'=>"Unable to deactivate due to Claim Sub Status assigned to claim, Please unassign to proceed further", 'data'=>''));			
					}
				}				

				$claimsubstatus = ClaimSubStatus::findOrFail($id);
				$res = $claimsubstatus->update($request);
				$user = Auth::user ()->id;
				$claimsubstatus->updated_by = $user;
				$claimsubstatus->updated_at = date('Y-m-d H:i:s');
				$claimsubstatus->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		} else {
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	/**
	 * Remove the specified resource from claim sub status.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDeleteApi($id) {
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(ClaimSubStatus::where('id',$id)->count()>0 && is_numeric($id)) {			// Check invalid id		
			$usedCnt = ClaimSubStatus::getClaimSubStatusAppliedCount($id);
			if($usedCnt > 0) {						
				return Response::json(array('status'=>'failure', 'message'=>"Unable to delete due to Claim Sub Status assigned to claim, Please unassign to proceed further", 'data'=>''));			
			}
			ClaimSubStatus::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		} else {
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
}
