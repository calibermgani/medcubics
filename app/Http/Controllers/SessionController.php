<?php namespace App\Http\Controllers;

use Config;
use Session;
use Auth;
use App\Models\Profile\UserLoginHistory as UserLoginHistory;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as User;

class SessionController extends Controller
{
	
	/*** Session time check starts ***/
	public function sessionCheck()
    { 
		$config_lifetime = Config::get('session.lifetime'); //lifetime
		$logout_lifetime = ($config_lifetime-1)*60; //to set timeout function 
		$set_alert = 0;
		if($config_lifetime < 5)$set_alert = $config_lifetime-2; 
		elseif($config_lifetime < 10 && $config_lifetime > 5)$set_alert = $config_lifetime-3; 
		elseif($config_lifetime < 25 && $config_lifetime > 9)$set_alert = $config_lifetime-5; 
		elseif($config_lifetime > 24)$set_alert = 15;	
		$login_time = Session::get('last_updated');
		$sessionout_time = strtotime("+".$set_alert." minutes", $login_time);
		$diff =$sessionout_time-time();
		$updated_time = (int)$diff;
		$result = $updated_time."/".$logout_lifetime;
		print_r($result);exit;
		
    }
	/*** Session time check ends ***/
	
	/*** Session insert starts ***/
	public function sessionInsert()
    { 
		$session_login_id = Session::get('login_session_id');
		if($session_login_id !='' && $session_login_id !=NULL)
		{
			$session_id  =   Helpers::getEncodeAndDecodeOfId($session_login_id,"decode");
			$get_login_qry  =   explode("::::",$session_id);
			if(count($get_login_qry) >1)
			{
				$current_login = UserLoginHistory::where("user_id",$get_login_qry[1])->where("created_at",$get_login_qry[0])->update(array('updated_at' => date('Y-m-d H:i:s')));
			}
		}
		Session::put('last_updated', time());
	}
	/*** Session insert ends ***/
	
	/*** 	Online status checking starts ***/

	public function onlineStatus(){
		$is_logged_in = "1";
		$practice_id = Session::get('practice_dbid');
		$dbuser = User::select('last_access_date','id','admin_practice_id')->where('is_logged_in',$is_logged_in)->get();
		$db_practice_id = json_decode(@$dbuser[0]['admin_practice_id']);

		if($practice_id == $db_practice_id){
			foreach( $dbuser as $dbusers){
				$time_diff = strtotime(date('Y-m-d H:i:s'))-strtotime($dbusers->last_access_date);

				if($time_diff > 600 ){  					//time in seconds, 10 minutes for example
					$users = User::find($dbusers->id);
					$users->is_logged_in = "0";
					$users-> save();
				} else {
					$users = User::find($dbusers->id);
					$users->is_logged_in = "1";
					$users-> save();
				}
			}
		}
		return 1;
	}

	/*** 	Online status checking starts ***/
}