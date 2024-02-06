<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\STMTCategory as STMTCategory;
use App\Models\STMTHoldReason;
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

class StatementCategoryApiController extends Controller {

	/**
	 * Display a listing of the statement category.
	 *
	 * @return Response
	 */
	public function getIndexApi($export = "")
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$statementcategory     = STMTCategory::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->get()->all();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'STMTCategory',
								'heading'	=>	'STMTCategory',
								'fields'	=>	array(
												'adjustment_type'   =>    'Adjustment Type',
												'statementcategory' =>    'Adjustment Reason',
												'status'   			=>    'Status',
												'created_by'		=>	array('table'=>'creator','column' => 'short_name','label' => 'Created By'),
												'updated_by'		=>	array('table'=>'modifier','column' => 'short_name','label' => 'Updated By'),
												'updated_at'		=>	'Updated On',
												)
								);

			$callexport    	= new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $statementcategory, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('statementcategory')));
	}

	/**
	 * Store statement category
	 *
	 * @return Response
	 */
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all()->get();		
		$validator = Validator::make($request, STMTCategory::$rules+array('category' => 'required|unique:stmt_category,category,NULL,id,deleted_at,NULL'), STMTCategory::messages());
		// Check validation.
		if($validator->fails()) {    
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		} else {				
			if(isset($request['stmt_option']) && $request['stmt_option'] == 'Hold') {
				if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
		            $request['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
		        }
			} else {
				$request['hold_release_date'] = "0000-00-00";
				$request['hold_reason'] = 0;
			}
			 
			$user = Auth::user ()->id;
			$statementcategory = STMTCategory::create($request);
			$statementcategory->created_by = $user;
			$statementcategory->created_at = date('Y-m-d H:i:s');
			$statementcategory->updated_at = date('Y-m-d H:i:s');
			$statementcategory->save ();
			$id = Helpers::getEncodeAndDecodeOfId($statementcategory->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));		
		}	
	}

	/**
	 * View statement category.
	 *
	 * @return Response
	 */	
	public function getShowApi($id)
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(STMTCategory::where('id', $id)->count()>0 && is_numeric($id)) 		// Check invalid id
		{
			$statementcategory = STMTCategory::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('holdreason','creator','modifier')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('statementcategory')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}


	/**
	 * Show the form for editing the statement category.
	 *
	 * @param  int  $id
	 * @return Response
	 */	
	public function getEditApi($id)
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(STMTCategory::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			$statementcategory = STMTCategory::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('statementcategory')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}


	/**
	 * Update the specified resource in statement category.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(STMTCategory::where('id',$id)->count()>0 && is_numeric($id)) {		// Check invalid id
			if($request == '')
				$request = Request::all();
			// Check the option for unique
			$validator = Validator::make($request, STMTCategory::$rules+array('category' => 'required|unique:stmt_category,category,'.$id.',id,deleted_at,NULL'), STMTCategory::messages());

			if ($validator->fails()) {	
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			} else {	

				// Dont allow to inactive while it used.
				if(isset($request['status']) && $request['status'] == 'Inactive') {
					$usedCnt = STMTCategory::getCategoryAppliedCount($id);
					if($usedCnt > 0) {
						return Response::json(array('status'=>'error', 'message'=>"Unable to deactivate due to Category assigned in patient account, Please unassign to proceed further",'data'=>''));			
					}
				}
				$statementcategory = STMTCategory::findOrFail($id);
				$suc_msg = Lang::get("common.validation.update_msg");
				// If statment option changed update the correponding entry.
				$relDateChanged = 0;
				if(isset($request['hold_release_date']) ) {
					
					if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
			            $holdRelDate = date('Y-m-d', strtotime($request['hold_release_date']));
			        } else {
			            $holdRelDate = "0000-00-00";
			        }
			        $relDateChanged = ($statementcategory->hold_release_date != $holdRelDate ) ? 1 : 0;
				}

				if((isset($request['stmt_option']) && $statementcategory->stmt_option != $request['stmt_option'] )
					|| (isset($request['hold_reason']) && $statementcategory->hold_reason != $request['hold_reason'])
					|| $relDateChanged ) {
					STMTCategory::updatePatientStmtRecords($id, $request);
					$suc_msg .= "Any Changes made will be reflected to all patients who has this category";
				} 	
				
				if (isset($request['hold_release_date']) &&  ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
					$request['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
				} 
				$statementcategory->update($request);
				$user = Auth::user ()->id;
				$statementcategory->updated_by = $user;
				$statementcategory->updated_at = date('Y-m-d H:i:s');
				$statementcategory->save ();
				return Response::json(array('status'=>'success', 'message'=>$suc_msg,'data'=>''));					
			}
		} else {
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	/**
	 * Remove the specified resource from statement category.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(STMTCategory::where('id',$id)->count()>0 && is_numeric($id))			// Check invalid id
		{
			$usedCnt = STMTCategory::getCategoryAppliedCount($id);
			if($usedCnt > 0) {
				return Response::json(array('status'=>'error', 'message'=>"Unable to deactivate due to Category assigned in patient account, Please unassign to proceed further",'data'=>''));			
			}
					
			STMTCategory::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}


}
