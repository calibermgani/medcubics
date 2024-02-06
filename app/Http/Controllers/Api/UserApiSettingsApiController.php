<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\PracticeApiList as PracticeApiList;
use App\Models\Practice as Practice;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Setapiforusers as Setapiforusers;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Helpers\Helpers as Helpers;
use Input;
use DB;
use Session;
use Lang;

class UserApiSettingsApiController extends Controller 
{
	
	public function getIndexApi($export='')
	{
		$practice_id 	 = Session::get('practice_dbid'); 
		
		// Get user list 
		$practice_user	 = DB::connection('responsive')->table('setpracticeforusers')->where('practice_id', '=', $practice_id)->whereNull('deleted_at')->select(DB::raw('group_concat(user_id) AS userid'))->first(); 
		$userlist 		 = Users::where('user_type','!=','Medcubics')->whereIn('id', explode(',',$practice_user->userid))->pluck('name','id')->all();
		
		// Get practice list 
		$practiceApiList = PracticeApiList::where('status','Active')->select('id','api_id','status')->get();
		$apilist		 = collect(DB::connection('responsive')->select('select id,api_name,category,api_for,api_status from practice_api_configs where api_status="Active"'))->keyBy('id');
		//dd($practiceApiList);
		$maincat_api		= ['eligible','apex','twilio'];
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practice_user','practiceApiList','userlist','apilist','maincat_api')));
	}
	
	
	/*** Start to Store the Helps	 ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		
			$new_user_api = Input::get('practice_api');
			$practice_id  = Session::get('practice_dbid'); 
			$userlist	  = Helpers::getEncodeAndDecodeOfId(Input::get('userlist'),'decode'); 
			$current_date = date('Y-m-d h:i:s');
			$user         = Auth::user ()->id;
			
			// Set api to user.
			Setapiforusers::where('practice_id', '=', $practice_id)->where('user_id', '=', $userlist)->update(['deleted_at'=>$current_date]);
			
			// Check the value is array or not	
			if(is_array($new_user_api)) 
			{
				foreach($new_user_api as $val) 
				{	
					$data['user_id'] 	 = $userlist;	
					$data['api_id'] 	 = $val;
					$data['practice_id'] = $practice_id;
					$data['created_by']  = $user;
					$data['updated_by']  = $user;
					$data['created_at']  = $current_date;
					$data['updated_at']  = $current_date;
					Setapiforusers::create($data); 
				}	
			}		
			
			$dbconnection = new DBConnectionController();
			$dbconnection->create_APIJSON($practice_id);		
			
		return Response::json(array('status'=>'success','message'=>Lang::get("common.validation.update_msg")));					
	}
	/*** End to Store the Helps	 ***/
	
	/*** Start to get user selected API ***/
	public function getPracticeUserApi($request='')
	{
		if($request == '')
			$request = Request::all();
		
		$user_id		 = Helpers::getEncodeAndDecodeOfId($request['userid'],'decode');
		$practice_id 	 = Session::get('practice_dbid'); 
		
		$get_user_api 	 = Setapiforusers::where('practice_id', '=', $practice_id)->where('user_id', '=', $user_id)->whereNull('deleted_at')->select('api_id')->get();

		
		// select the default api.
		/*if(count($get_user_api)==0)
		{
			$get_user_api = ApiConfig::where('api_status','Active')->whereIn('api_for', ['address', 'npi'])->select('id as api_id')->get();	
		} */
		
		return $apilist_arr  = json_decode(json_encode($get_user_api), True);
	}
	/*** End to get user selected API ***/
	
	function __destruct() 
	{
    }
}