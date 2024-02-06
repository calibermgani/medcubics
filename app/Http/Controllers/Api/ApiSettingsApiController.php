<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\PracticeApiList as PracticeApiList;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\Medcubics\Setapiforusers as Setapiforusers;
use App\Models\Medcubics\Users as User;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Helpers\Helpers as Helpers;
use Input;
use DB;
use Session;
use Lang;

class ApiSettingsApiController extends Controller 
{
	/*** Start to listing the Helps  ***/
	public function getIndexApi($export='')
	{
		$practiceApiList = PracticeApiList::select('id','api_id','status')->get();
		$apilist		 = collect(DB::connection('responsive')->select('select id,api_name,category,api_for,api_status from practice_api_configs where api_status="Active"'))->keyBy('id');
		
		$maincat_api		= ['eligible','apex','twilio'];
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practiceApiList','apilist','maincat_api')));
	}
	/*** End to listing the Helps  ***/
	
	/*** Start to Store the Helps	 ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		
			$new_practice_api = Input::get('practice_api');
			$old_practice_api = PracticeApiList::where('status','Active')->pluck('api_id')->all();
			$practice_id = Session::get('practice_dbid');; 
			$current_date = date('Y-m-d h:i:s');
			
			if(!is_array($new_practice_api))
				$new_practice_api = array();
			
			$remove_api = array_diff($old_practice_api,$new_practice_api);
			$add_api 	= array_diff($new_practice_api,$old_practice_api);
			
			// Check to admin disable the api or not.
			if(count($remove_api)>0) 
			{
				foreach($remove_api as $val) 
				{
					PracticeApiList::where('api_id',$val)->update(['status'=>'Inactive']);	
					Setapiforusers::where('practice_id', '=', $practice_id)->where('api_id', '=', $val)->update(['deleted_at'=>$current_date]);
					
					$dbconnection = new DBConnectionController();
					$dbconnection->create_APIJSON($practice_id);	
				}	
			}
			
			// Check to admin add the api or not.
			if(count($add_api)>0) 
			{
				foreach($add_api as $val) 
				{
					PracticeApiList::where('api_id',$val)->update(['status'=>'Active']);	
				}
			}
		return Response::json(array('status'=>'success','message'=>Lang::get("common.validation.update_msg")));					
	}
	/*** End to Store the Helps	 ***/
	
	/*** Start to confirmation to disable API ***/ 
	public function getDisabledUserApi($request='')
	{
		if($request == '')
			$request = Request::all();
		
		$new_practice_api = Input::get('remove_api'); 
		$get_practiceapiids = explode(",",$new_practice_api);
		
		$get_user		 = User::pluck('name','id')->all();
		$practice_id 	  = Session::get('practice_dbid');
		
		$practice_apiid = array();
		
		// Check to admin disable the api or not. If yes then check the api assigned to particular user. If yes then display alert message.
		if(count($get_practiceapiids)>0) {
			
			foreach($get_practiceapiids as $api_ids) 
			{
				$practice_apiid[] = Helpers::getEncodeAndDecodeOfId($api_ids,'decode'); 
			}
			
			$get_user_apiid = Setapiforusers::where('practice_id', '=', $practice_id)->whereIn('api_id', $practice_apiid)->whereNull('deleted_at')->select('user_id')->get();

			$get_user_info = array();
			foreach($get_user_apiid as $userapi) 
			{
				$get_user_info[] = $get_user[$userapi->user_id];	
			}
			$unique_user 	= array_unique($get_user_info);
			return implode(', ',$unique_user);
		}
	}
	/*** End to confirmation to disable API ***/ 
	
	function __destruct() 
	{
    }
}