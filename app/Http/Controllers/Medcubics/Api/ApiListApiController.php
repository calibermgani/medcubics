<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use View;
use App\Models\Medcubics\Apilist as Apilist;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Medcubics\Setapiforusers as Setapiforusers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Requests;
use DB;
use Lang;

class ApiListApiController extends Controller {

	/*** Start to Export the API List ***/
	public function getIndexApi($export='')
	{
		$apilist = Apilist::with('created_by','updated_by')->get();
		
		 if($export != "")
		 {
			$exportparam 	= 	array(
				'filename'		=>	'api_name',
				'heading'		=>	'api_list',
				'fields' 		=>	array(
					'api_name'				=>	'API name',
					'status'				=>	'status'
					)
			);
		$callexport = new CommonExportApiController();
		return $callexport->generatemultipleExports($exportparam, $apilist, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('apilist')));
	}
	/*** End to Export the API List	 ***/
	
	/*** Start to Create the API List ***/
	public function getCreateApi()
	{
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
	}
	/*** End to Create the API List	 ***/

	/*** Start to Store the API List ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		
		$validate_apilist_rules = Apilist::$rules+array('api_name' => 'required|unique:api_list');
		$validator = Validator::make($request, $validate_apilist_rules , Apilist::$messages);
	
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{
			$Apilist = Apilist::create(Request::all());
			$user = Auth::user ()->id;
		 	$Apilist->created_by = $user;
			$Apilist->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$Apilist->id));
		}	
	}
	/*** End to Store the API List	 ***/
	
	/*** Start to Show the API List ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode'); 
		if(Apilist::where('id',$id)->count())
		{
			$apilist = Apilist::where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('apilist')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/*** End to Show the API List	 ***/
	
	/*** Start to Edit the API List ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode'); 
		if(Apilist::where('id',$id)->count())
		{
			$apilist = Apilist::find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('apilist')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Edit the API List	 ***/

	/*** Start to Update the API List ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$request = Request::all();
		$validate_apilist_rules = Apilist::$rules+array('api_name' => 'required|unique:api_list,api_name,'.$id);
		$validator = Validator::make($request, $validate_apilist_rules, Apilist::$messages);
	
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{
			$Apilist = Apilist::findOrFail($id);
			if($request['status'] == 'Inactive')
			{
				$this->removeAPIFromSite($id);
			}
			$Apilist->update(Request::all());
			$user = Auth::user ()->id;
			$Apilist->updated_by = $user;
			$Apilist->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));
		}
	}
	/*** End to Update the API List	 ***/

	/*** Start to Delete the API List ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode'); 
		$this->removeAPIFromSite($id);
		Apilist::Where('id',$id)->delete();		
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	/*** End to Delete the API List	 ***/
	
	public function removeAPIFromSite($id)
	{
		$get_Practices = Practices::where('api_ids','!=','')->get();
		$current_date = date('Y-m-d h:i:s');

		foreach($get_Practices as $practices)
		{
			$get_practiceids = explode(",",$practices->api_ids);
			if(in_array($id,$get_practiceids))
			{
				$practice_id = $practices->id;
				if(($key = array_search($id, $get_practiceids)) !== false) 
				{
					unset($get_practiceids[$key]);
				}
				$new_practiceids = implode(",",$get_practiceids);
				$practice = Practices::find($practice_id);
				$practice->api_ids = $new_practiceids;
				$practice->save();
				
				$dbconnection 	  = new DBConnectionController();
				$practice_db_name = $dbconnection->getpracticedbname($practices->practice_name);
				$dbconnection->configureConnectionByName($practice_db_name);
				DB::connection($practice_db_name)->table('practice_api_list')->where('api_id', '=', $id)->update(['deleted_at'=>$current_date]);
			}
		}
		Setapiforusers::where('api_id',$id)->delete();	
		
		$get_APIIds = Setapiforusers::select('practice_id')->groupBy('practice_id')->get();
		
		foreach($get_APIIds as $userpractice)
		{
			$dbconnection = new DBConnectionController();
			$dbconnection->create_APIJSON($userpractice->practice_id);
		}
	}
	
	function __destruct() 
	{
    }
}