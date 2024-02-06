<?php namespace App\Http\Controllers;

use Auth;
use DB;
use View;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Users as User;
use App\Models\Profile\UserLoginHistory as UserLoginHistory;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Speciality as Speciality;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Roles as Roles;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Helpers\Helpers as Helpers;
use Redirect;
use Session;
use Response;
use Request;
use Validator;
use DateTime;
use Input;
use Hash;
use Route;
class HomeController extends Controller {

    public function __construct()
    {
		$this->middleware('auth');
    }

    /**
    * Show the application dashboard to the user.
    *
    * @return Response
    */

    public function Index()
    {

        $dbconnection = new DBConnectionController();
        $user_details = Auth::user();

		$user_id = $user_details->id;
        $delete_at = $user_details->deleted_at;
        $user_staus = $user_details->status;
		$customer_id = $user_details->customer_id;
        $usertype = $user_details->user_type;
        $cus_id = Auth::user()->customer_id;
		$customer_name = Customer::where('id',$cus_id)->select('status', 'deleted_at')->first();
		$cus_status = $customer_name['status'];
		$cus_deleted_at = $customer_name['deleted_at'];
		$practice = Practices::where('customer_id',$cus_id)->where('status','Active')->select('status')->first();
		$practice_status = $practice['status'];
		if($user_details->practice_user_type == 'practice_user' && $user_details->user_access == 'app')
			$practice_count = Setpracticeforusers::where('user_id', Auth::user()->id)->count();
		else
			$practice_count	= 1;

		UserLoginHistory::ClearLoginHistorySession();

		if ($usertype != '') {
			$result      =   Helpers::login_history($user_id);
			$ip_address = request()->ip();

			$data =	UserLoginHistory::create(
			['user_id' =>$result['user_id'], 'latitude'=>$result['latitude'], 'logitude'=>$result['logitude'], 'browser_name'=> $result['browser_name'], 'ip_address'=> $ip_address,'login_time' => date('Y-m-d H:i:s'), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
			$hash_string	=	$data->created_at."::::".$data->user_id;
			$session_id  =   Helpers::getEncodeAndDecodeOfId($hash_string,"encode");
			$data->session_id	= $session_id;
			$data->save();
			Session::put('login_session_id', $session_id);
			// Start sidebar notification by baskar -04/02/19
			Session::put('sidebar_notify', 'sidebar_notify');
			// End sidebar notification by baskar -04/02/19
		}

        if ($usertype == 'Medcubics' && $delete_at == null && $user_staus == "Active") {
            $dbconnection->clearDBSession();
			$admin_practice_ids = User::where('id',$user_id)->first();
			$admin_practice_ids->is_logged_in = 1;
			$admin_practice_ids->save();

			// Encode rand algorithm process
			$encode_decode_alg		=	config('siteconfigs.encode_decode_alg');
			if($encode_decode_alg=='base64_rand_alg' || $encode_decode_alg=='base64_rot13_rand_alg'){
				$new_encode_decode_val 	=   Helpers::code_gen();
				Session::put('new_encode_decode_val', $new_encode_decode_val);
			}

			/*$customers 	= Customer::all();
			if($user_details->role_id > 0 )
			{
				$roles = Roles::where('id',$user_details->role_id)->first();
				if($roles->deleted_at != null)	{
					return Redirect::to('admin/dashboard');
				} else {
					return Redirect::to('admin/customer');
				}
			}
			else
			{
				return Redirect::to('admin/dashboard');
			}*/
			return Redirect::to('admin/dashboard');
        } elseif ($usertype == 'Practice' && $delete_at == null && $user_staus == "Active" && $cus_status == "Active" && $cus_deleted_at == null  && $practice_status == "Active" && ($practice_count > 0)) {
            $practiceusertype = $user_details->practice_user_type;
            $customer_id = $user_details->customer_id;
            //$customer_name = Customer::where('id',$customer_id)->pluck('customer_name');
            $customer_name = User::where('customer_id',$customer_id)->pluck('short_name')->first();
			if($practiceusertype == 'customer') {
				$practice_ids = Practices::select('id')->where('customer_id',$customer_id)->where('status','Active')->pluck('id')->all();
			} elseif($practiceusertype == 'practice_admin' || $practiceusertype == 'practice_user'
				|| $practiceusertype == 'provider') {
				if(Session::has('practiceid')) {
					Session::forget('practiceid');
				}

                /// Get all practices ids under practice admin user
				$admin_practice_ids = User::where('id',$user_id)->pluck('admin_practice_id')->first();

				if($admin_practice_ids != '')
                    $admin_practice_ids = explode(',', $admin_practice_ids);
				else
					$admin_practice_ids = [];

				$user_practices = Setpracticeforusers::select(DB::raw('group_concat(practice_id) as practice_ids'))->where('user_id',$user_id)->first();

                $user_practice_ids = [];
				if( $user_practices->practice_ids != '' && $user_practices->deleted_at == null)
                    $user_practice_ids = explode(',', $user_practices->practice_ids);

                $practice_ids = array_merge($admin_practice_ids,$user_practice_ids);
			} else {
				$user_practices = Setpracticeforusers::select(DB::raw('group_concat(practice_id) as practice_ids'))->where('user_id',$user_id)->first();
                $practice_ids = $user_practices->practice_ids;
            }

			if (is_array($practice_ids)) {
				$practice_obj = Practices::whereIn('id',$practice_ids)->where('status','Active');
				$practices = $practice_obj->get();
				$practices_name = $practice_obj->pluck("practice_name","id")->all();
			} else {
				$practice_id = explode(",",$practice_ids);
				$practice_obj = Practices::whereIn('id',$practice_id)->where('status','Active');
				$practices = $practice_obj->get();
				$practices_name = $practice_obj->pluck("practice_name","id")->all();
			}
			$stats_details	= $db_details =[];
			$dbconnection			= new DBConnectionController();
			$get_practice_details 	= $dbconnection->GetPracticeDetails($practices_name);
			$stats_details			= $get_practice_details["total"];
			$db_details				= $get_practice_details["individual"];
			$admin_practice_ids = User::where('id',$user_id)->first();
			$admin_practice_ids->is_logged_in = "1";
			$admin_practice_ids->save();

			// Encode rand algorithm process
			$encode_decode_alg		=	config('siteconfigs.encode_decode_alg');
			if($encode_decode_alg=='base64_rand_alg' || $encode_decode_alg=='base64_rot13_rand_alg'){
				$new_encode_decode_val 	=   Helpers::code_gen();
				Session::put('new_encode_decode_val', $new_encode_decode_val);
			}
			return view('practice/practice/practice',compact('practices','customer_name','stats_details','db_details'));
        } else {
			if($user_staus != "Active")
				return Redirect::to('/auth/logout?msg=Your user has Inactived');
			elseif($cus_status != "Active")
				return Redirect::to('/auth/logout?msg=Your customer has Inactived');
			elseif($practice_status !="Active")
				return Redirect::to('/auth/logout?msg=Your Practice has Processed');
			elseif($practice_count == 0 )
				return Redirect::to('/auth/logout?msg=Your Practice has not assigned');
			else
				return Redirect::to('/auth/logout?msg=You are not a valid user');
		}
    }

	/* Get active user list - Anjukaselvan*/
	public static function getActiveUserList() {
		$practice_id = Session::get('practice_dbid');
		$practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();
		$admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
		$practice_user_arr2 = User::whereRaw("(($admin_practice_id_like))")->pluck('id')->all();
		$practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
		$data = User::whereIn('id', $practice_user_arr)->select('id','name','is_logged_in','avatar_name','avatar_ext','practice_user_type')->where('status', 'Active')->get();
		$ajax_data = Request::all();

		if( $ajax_data != [] || $ajax_data != null ){
			$users = $data;
			return view('profile/layouts/rightside-tabs',  compact('users'));
		}else{
			return $data;
		 }
	}

	public function  getPrivacyPolicy() {
		return view('privacypolicy/privacypolicy');
	}
}