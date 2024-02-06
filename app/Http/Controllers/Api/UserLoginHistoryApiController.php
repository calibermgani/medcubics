<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use View;
use App\Models\Medcubics\UserIp as UserIp;
use App\Http\Helpers\Helpers as Helpers;
use Requests;
use DB;
use Lang;
use Session;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users as Users;

class UserLoginHistoryApiController extends Controller
{
	/*** Start to listing page ***/
	public function getIndexApi($type)
	{
		$practice_timezone = Helpers::getPracticeTimeZone();  
		if($type == 'pendingApproval')
			$approved = 'No';
		else
			$approved = 'Yes';
		$user_type = Auth::user()->practice_user_type;
		$practice_id = Session::get('practice_dbid');
		$practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
		$admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
		if(Auth::user()->practice_user_type == 'customer'){
			$practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = '".Auth::user()->practice_user_type."' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
		}else{ 
			$practice_user_arr2 = Users::whereRaw("((practice_user_type != '".Auth::user()->practice_user_type."' and status = 'Active') and ($admin_practice_id_like))")->pluck('id')->all();
		}
		$practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
		$user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('id')->all();
		$userLoginInfo = UserIp::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('user')->whereIn('user_id',$user_list)->where('approved',$approved)->get();

		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('userLoginInfo')));
	}
	/*** End to listing page ***/

	public function userStatusChangeApi(){
		$request = Request::all();
		Users::where('id',$request['user_id'])->update(['status'=>'Inactive']);
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
	}
	
	public function userIpSecurityCodeRestApi(){
		$request = Request::all();
		UserIp::where('id',$request['userip_id'])->update(['security_code_attempt'=>'0']);
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
	}
	
	
	function __destruct() 
	{
    }
}