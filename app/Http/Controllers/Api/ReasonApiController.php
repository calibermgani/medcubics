<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use View;
use App\Models\ReasonForVisit as Reason;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Collection;
use Requests;
use DB;
use Lang;

class ReasonApiController extends Controller
{
	/*** Start to listing page ***/
	public function getIndexApi($export='')
	{
		$practice_timezone = Helpers::getPracticeTimeZone(); 
		$reason = Reason::select('*', DB::raw('DATE(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") )as created_at'), DB::raw('DATE(CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'")) as updated_at'))->with('creator','modifier')->get();		
		// Export option.
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'reason',
				'heading'		=>	'',
				'fields' 		=>	array(
					'reason'				=>	'Reason',
					'status'				=>	'Status',
					'created_by'		=>	array('table'=>'creator','column' => 'short_name','label' => 'Created By'),
					'updated_by'		=>	array('table'=>'modifier','column' => 'short_name','label' => 'Updated By'),
					'updated_at'		=>	'Updated On',                                       
					)
			);                       
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $reason, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('reason')));
	}
	/*** End to listing page ***/
	
	/*** Start to create the reason ***/
	public function getCreateApi()
	{
		$reason = Reason::all();
		return view('practice/reason_for_visits/create');
	}
	/*** End to create the reason ***/

	/*** Reason for for visit Store ***/	
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
	   // Check the reason name for unique
		$validator = Validator::make($request, Reason::$rules+array('reason' => 'required|unique:reason_for_visits,reason,NULL,id,deleted_at,NULL'), Reason::messages());
		// Check validation.
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$reason = Reason::create(Request::all());
			$user = Auth::user ()->id;
			$reason->created_by = $user;
			$reason->updated_at = '';
			$reason->created_at = date('Y-m-d H:i:s');
			$reason->updated_at = date('0000-00-00 00:00:00');
			$reason->save ();
			//Encode ID for reason
			$temp = new Collection($reason);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$reason = json_decode(json_encode($data), FALSE);
			//Encode ID for reason
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$reason->id));		
		}
	}
	
	/*** Start to view page ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');	
		$practice_timezone = Helpers::getPracticeTimeZone();	
		if(Reason::where('id', $id)->count()>0 && is_numeric($id))   // Check invalid id
		{
			$reason = Reason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('creator','modifier')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('reason')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to view page ***/
	
	/*** Start to edit page ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(Reason::where('id', $id)->count()>0 && is_numeric($id))  // Check invalid id
		{
			$reason = Reason::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('reason')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to edit page ***/
	
	/*** Start to update the reason ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
			$request = Request::all();	
		if(Reason::where('id', $id)->count()>0 && is_numeric($id))  // Check invalid id
		{
			$validator = Validator::make($request, Reason::$rules+array('reason' => 'required|unique:reason_for_visits,reason,'.$id.',id,deleted_at,NULL'), Reason::messages());
			if ($validator->fails())
			{	
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$reason = Reason::findOrFail($id);
				$reason->update(Request::all());
				$user = Auth::user ()->id;
				$reason->updated_by = $user;
				$reason->updated_at = date('Y-m-d H:i:s');
				$reason->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		}
		else
		{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to update the reason ***/

	/*** Start to delete the reason ***/ 
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Reason::where('id', $id)->count()>0 && is_numeric($id))  // Check invalid id
		{
			Reason::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		}
		else
		{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to delete the reason ***/
	
	function __destruct() 
	{
    }
}