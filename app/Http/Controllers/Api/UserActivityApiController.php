<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Useractivity as UserActivity;
use App\Models\Medcubics\Practice as Practice;
use Response;
use Request;
use Session;
use DB;

class UserActivityApiController extends Controller 
{

	public function getIndexApi($request='')
	{
		//Accessing for ajax request in search page
		if(Request::ajax()) 
			$request 	  	= Request::all();
		  
		//Get practice id from session storage		
		$pracitice_id = Session::get('practice_dbid'); 
		$useractivity = UserActivity::with('user')->where('main_directory',$pracitice_id);
		$user_id = '';
		if(Request::ajax())
		{
			if(!empty($request['user']))
			{
				$useractivity->where('userid',$request['user']);
				$user_id = $request['user'];
			}
			
		}
		$user_activity_list = $useractivity->orderBy('id','DESC')->get();
		
		// Get user list 
		$practice_id 	 = Session::get('practice_dbid'); 
		
		$practice_user	 = DB::connection('responsive')->table('setpracticeforusers')->where('practice_id', '=', $practice_id)->whereNull('deleted_at')->select(DB::raw('group_concat(user_id) AS userid'))->first(); 
		
		$customer_id = Practice::where('id',$practice_id)->select('customer_id')->first()->customer_id; 
		$practice_user 		= Users::whereIn('id', explode(',',$practice_user->userid))->pluck('name','id')->all();
		
		$practice_admin 	= Users::whereRaw("(customer_id = '".$customer_id."' AND user_type = 'Practice' AND practice_user_type='Customer') OR (user_type = 'Practice' AND practice_user_type='practice_admin' AND find_in_set('".$pracitice_id."',admin_practice_id) <> 0)")->orwhere('id','!=','1')->pluck('name','id')->all();
		
		$user = $practice_user + $practice_admin;
		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('user','user_id','user_activity_list')));
	}
	/*** End to Listing the User Activity ***/
	
	
}
