<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Profile\UserLoginHistory as UserLoginHistory;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Request;
use Response;

class UserHistoryApiController extends Controller {

	public function getIndexApi($export='')
	{
		/*
		$current_login = UserLoginHistory::with('user')->where("logout_time","")->orderBy('id',"DESC")->get();
		$logout_history =UserLoginHistory::with('user')->where("logout_time","!=","")->orderBy('logout_time',"DESC")->get();
		*/
		$history = UserLoginHistory::with('user')->orderBy('id',"DESC")->limit(500)->get();
		$customers 	= Customer::where('status', 'Active')->pluck('customer_name','id')->all();
		if($export != "") {
			$exportparam 	= 	array(
								'filename'	=>	'Userhistory',
								'heading'	=>	'',
								'fields' 	=>	array(
												'ip_address'		=>	'IP Address',
												'browser_name'		=>	'Browser Name',
												'Login Time'		=>	array('table'=>'','column' => 'login_time', 'use_function'		=> ['App\Http\Helpers\Helpers','dateFormat'], 'label' => 'Login Time'),
												'Logout Time'		=>	array('table'=>'','column' => 'logout_time', 'use_function'=> ['App\Models\Profile\UserLoginHistory','LogoutTime'], 'label' => 'Logout Time'),
												'user'		=>	array('table'=>'user','column' => 'short_name','label' => 'User'),
												'user_type'		=>	array('table'=>'user','column' => 'user_type','label' => 'User Type'),
											)
								);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $history, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('history', 'customers')));
	}

	public function getListIndexApi($data='')
	{
		$request = Request::all();
        $start = isset($request['start']) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
		$history = UserLoginHistory::with('user')
					->join('users','users.id', '=', 'user_login_histories.user_id');

		if(isset($data['customer']) && !empty($data['customer'])){
			$history->where('users.customer_id',$data['customer']);
		}

		if(isset($request['dataArr']['data']['customer'])) {
            $customer = json_decode(@$request['dataArr']['data']['customer']);
            $history->where('users.customer_id',$customer);
        }

		$count = $result['count'] = $history->distinct('user_login_histories.id')->count();

		$history->selectRaw('user_login_histories.id,
            user_login_histories.login_time,
            user_login_histories.logout_time,
            user_login_histories.ip_address,
            user_login_histories.browser_name,
            users.short_name,
            users.user_type');

		$history->orderBy('user_login_histories.id',"DESC");
		$history->skip($start)->take($len);
		$history = $history->get();

		$result['history'] = $history;

		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('history', 'count')));
	}

}