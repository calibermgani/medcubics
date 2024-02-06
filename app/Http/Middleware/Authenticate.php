<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Controllers\App\Api\AppMainApiController as AppMainApiController;
use Session;
use View;
use Redirect;
use DB;
use App\Models\Medcubics\Users as Users;
use App\Models\Provider as Provider;
use App\Models\Practice as Practice;
use App\Models\Medcubics\Customer as Customer;
use App\Http\Helpers\Helpers as Helpers;
use Request;
use Response;
use Lang;
use config;
use Auth;


class Authenticate
{
	protected $auth;
	
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$chk_app_url = $request->route()->uri();
		if(strpos($chk_app_url,'pi/app') != false) {
			$request_arg = Request::all();
			$authenticationid = @$request_arg['authenticationid'];
			$device_id = "TEST123";//@$request_arg['device_id'];
			$app_obt = new AppMainApiController();
			$result  = $app_obt->checkapp_authenticate($authenticationid,$device_id);			
			if($result=='success') {
				return $next($request);
				/*$pass_result  = $app_obt->checkapp_passwordchange($authenticationid,$device_id);
				if($pass_result=='success')
					return $next($request);
				else
					return Response::json(array('status'=>'101', 'StatusMessage'=>'Password changed. Please login again'));*/
			} else {
				return Response::json(array('status'=>'1', 'StatusMessage'=>'Invalid authentication'));
			}
		}
		if ($this->auth->guest())
		{
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			} else {
				return redirect()->guest('auth/login');
			}
		}
		
		$dbconnection = new DBConnectionController();		
        View::share ( 'checkpermission', $dbconnection ); 
		$prefixname = $request->route()->getPrefix();
		//dd($prefixname);		
		if($prefixname[0] == '/') {
			$prefixname = substr($prefixname, 1, 6);
		} else {
			$prefixname = substr($prefixname, 0, 5);
		}
		
		if(Auth::user()->customer_id == 19 && Session::has('practice_dbid')){
			$providerCount = Provider::where('customer_id',19)->where('practice_id',Session::get('practice_dbid'))->count();
			if($providerCount == 0 &&  Request::segment(1) != 'trail' && Request::segment(1) != 'api'){
				return Redirect::to('trail/provider/create')->with('error','first you have to add provider');	
			}
		}
		
		if($prefixname == 'admin') {			
			$dbconnection->clearDBSession();
		}
		$current_url = $request->route()->uri();	
		$user = $this->auth->user();
		if(Session::has('practice_dbid')) {		
		  // if(stripos($current_url, 'checkicdexist') == false && stripos($current_url, 'select_api_search_icd_cpt_list') == false && stripos($current_url, 'checkcptexist') == false) {
		   	 	$dbconnection->connectPracticeDB(Session::get('practice_dbid'));			
			    Practice::getmediapracticenamefromdb(Session::get('practice_dbid'));
			//}			
		} else {
			//\Log::info("Current URL ".$current_url." Session ".Session::get('practice_dbid') );
			$dbconnection->clearDBSession();
			$dbconnection->disconnectPracticeDB();
			// If not set practice redirect to the practice listing # MR-2214
			if ($user->user_type == 'Medcubics' && $user->role_id == 1) {
				// Admin no need to handle	
			} else {
				if($current_url != '/' && $current_url != 'admin/customer/setpractice/{id}')
					return Redirect::to('/'); 
			}
		}

										
		/*if($user->user_type == 'practice'){
			Redirect::to('/home');			
		} */
        if ($user->user_type == 'Medcubics' && $user->role_id == 1) {
            return $next($request);
        }
        
        //temp removed for common logout function
       	/* if ($user->status != 'Inactive' && $current_url != '/') {
            if (\Auth::User()->is_logged_in != 1) {
                \Auth::logout();
            }
        } */
        // Check user account status
		if($user->status == 'Inactive') {
			return Redirect::to('/auth/logout?msg='.Lang::get("admin/user.validation.inactiveaccount"));
		}
                
		// App user not allow to access the web.
		if($user->useraccess == 'app'){
			return Redirect::to('/auth/logout?msg='.Lang::get("admin/user.validation.invalidaccess"));
		}

		// Check customer account status
		if($user->user_type == 'Practice') {
			$getcustomerinfo = Customer::where('id',$user->customer_id)->select('status')->first();
			$cus_status = $getcustomerinfo['status'];	
			if($cus_status == 'Inactive') {
				return Redirect::to('/auth/logout?msg='.Lang::get("admin/user.validation.customerinactive"));
			}
		}
		
        if($user->user_type == 'Medcubics') { 
            $permission_result = $dbconnection->check_adminurl_permission($current_url);	
		}
                
        if($user->user_type == 'Practice') {
			//For customer we are only checking the practice not any urls
			if($user->practice_user_type == 'customer') {			  
			   $permission_result = 1;
			   if($request->route('practice') != null) {
					$practice_id = $request->route('practice');
					$practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
					$permission_result = $dbconnection->check_customer_url_permission($practice_id);
			   }
			} elseif($user->practice_user_type == 'practice_admin') {						
				if(stripos($current_url, 'setpractice')>1) {
					Session::put('practiceid',$request->route('id'));	
					$practice_id = Helpers::getEncodeAndDecodeOfId($request->route('id'),'decode');
				} else {
					$practice_id = Session::get('practiceid');
				}
				
				if(empty($practice_id))	{
					$permission_result = 1;
				} else {						
					$permission_result = $dbconnection->check_url_practice_permission($practice_id,$current_url);
				}
			} else {
				$permission_result = $dbconnection->check_url_permission($current_url);		
			}
		}

		// Check and allow practice admin and customer alone allowed to access practice module
		if($user->practice_user_type == 'practice_user'){
			$practice_url = array('practice/{practice}', 'managecare', 'contactdetail', 'notes', 'facility', 'facility/create', 'provider', 'provider/create', 'insurance', 'insurance/create', 'icd', 'icd/create', 'listfavourites', 'cpt', 'cpt/create', 'modifierlevel1', 'modifierlevel1/create', 'modifierlevel2', 'modifierlevel2/create', 'code', 'code/create', 'employer', 'employer/create', 'templates', 'templates/create', 'feeschedule', 'feeschedule/create', 'practiceproviderschedulerlist', 'practicefacilityschedulerlist', 'patientstatementsettings', 'bulkstatement', 'individualstatement', 'statementhistory', 'reason', 'holdoption', 'adjustmentreason', 'insurancetypes', 'emailtemplate', 'apisettings', 'userapisettings' );
			// Check the URLs belongs to practice module then redirect to dashboard
			if(in_array($current_url, $practice_url)) {
				return Redirect::to('dashboard')->with('error', 'You are not authorized to view this page.');	
			}
		}
		
	    if($permission_result or $current_url == 'admin/dashboard' 
	    	or $current_url == 'dashboard' or $current_url == 'practice/switchuser' or $current_url == 'help/{type}') {
			return $next($request);
		} else {	
			$message = 'You are not authorized to view this page.';
			
			if($prefixname == 'admin')
				return Redirect::to('admin/dashboard')->with('error',$message);	
			else
				return Redirect::to('dashboard')->with('error',$message);				
		}				
	}
}