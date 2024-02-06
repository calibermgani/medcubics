<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\STMTHoldReason as STMTHoldReason;
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


class StatementHoldReasonApiController extends Controller {

	/**
	 * Display a listing of the statement hold reason.
	 *
	 * @return Response
	 */
	public function getIndexApi($export = "")
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$statementholdreason     = STMTHoldReason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->get()->all();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'STMTHoldReason',
								'heading'	=>	'STMTHoldReason',
								'fields'	=>	array(
												'adjustment_type'   =>    'Adjustment Type',
												'$statementholdreason' =>    'Hold Reason',
												'status'   			=>    'Status',
												'created_by'		=>	array('table'=>'creator','column' => 'short_name','label' => 'Created By'),
												'updated_by'		=>	array('table'=>'modifier','column' => 'short_name','label' => 'Updated By'),
												'updated_at'		=>	'Updated On',
												)
								);

			$callexport    	= new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $statementholdreason, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('statementholdreason')));
	}

	/**
	 * Store statement hold reason
	 *
	 * @return Response
	 */
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all()->get();
		$validator = Validator::make($request, STMTHoldReason::$rules, STMTHoldReason::messages());
		// Check validation.
		if($validator->fails())
		{    
			$errors = $validator->errors();	
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$user = Auth::user ()->id;
			$statementholdreason = STMTHoldReason::create($request);
			$statementholdreason->created_by = $user;
			$statementholdreason->created_at = date('Y-m-d H:i:s');
			$statementholdreason->updated_at = date('Y-m-d H:i:s');
			$statementholdreason->save ();
			$id = Helpers::getEncodeAndDecodeOfId($statementholdreason->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));		
		}	
	}

	/**
	 * View statement hold reason.
	 *
	 * @return Response
	 */	
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(STMTHoldReason::where('id', $id)->count()>0 && is_numeric($id)) 		// Check invalid id
		{
			$statementholdreason = STMTHoldReason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('statementholdreason')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}


	/**
	 * Show the form for editing the statement hold reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */	
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(STMTHoldReason::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			$statementholdreason = STMTHoldReason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('statementholdreason')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}


	/**
	 * Update the specified resource in statement hold reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(STMTHoldReason::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			if($request == '')
				$request = Request::all();
			$validator = Validator::make($request, STMTHoldReason::$rules, STMTHoldReason::messages());

			if ($validator->fails()) {	
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			} else {					
				// Dont allow to inactive while assigned to any patient condition
				if(isset($request['status']) && $request['status'] == 'Inactive') {
					$usedCnt = STMTHoldReason::getHoldReasonAppliedCount($id);
					if($usedCnt > 0) {
						return Response::json(array('status'=>'error', 'message'=>"Unable to deactivate due to Hold Reason assigned to patient account, Please unassign to proceed further",'data'=>''));			
					}
				}

				$statementholdreason = STMTHoldReason::findOrFail($id);
				$statementholdreason->update($request);
				$user = Auth::user ()->id;
				$statementholdreason->updated_by = $user;
				$statementholdreason->updated_at = date('Y-m-d H:i:s');
				$statementholdreason->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		}
		else
		{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	/**
	 * Remove the specified resource from statement hold reason.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(STMTHoldReason::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			// Dont allow to delete while assigned to any patient condition
			$usedCnt = STMTHoldReason::getHoldReasonAppliedCount($id);
			if($usedCnt > 0) {
				return Response::json(array('status'=>'error', 'message'=>"Unable to deactivate due to Hold Reason assigned to patient account, Please unassign to proceed further",'data'=>''));			
			}

			STMTHoldReason::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}


}
