<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Users;
use App\Models\Profile\UserLoginHistory as UserLoginHistory;
use App\Models\Medcubics\Practice as Practice;
use Response;
use Request;
use Session;
use DB;

class UserHistoryApiController extends Controller {

	public function getIndexApi($export='')
	{
		$current_login = UserLoginHistory::with('user')->where("logout_time","")->orderBy('id',"DESC")->get();
		$logout_history =UserLoginHistory::with('user')->where("logout_time","!=","")->orderBy('logout_time',"DESC")->get();
		$history = $current_login->merge($logout_history);
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'Userhistory',
								'heading'	=>	'',
								'fields' 	=>	array(
												'ip_address'		=>	'IP Address',
												'browser_name'		=>	'Browser Name',
												'Login Time'		=>	array('table'=>'','column' => 'login_time', 'use_function'		=> ['App\Http\Helpers\Helpers','dateFormat'], 'label' => 'Login Time'),
												'Logout Time'		=>	array('table'=>'','column' => 'logout_time', 'use_function'=> ['App\Models\Profile\UserLoginHistory','LogoutTime'], 'label' => 'Logout Time'),
												'user'		=>	array('table'=>'user','column' => 'short_name','label' => 'User'),
											)
								);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $history, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('history')));
	}
	/*** End to Listing the User Activity ***/

}
