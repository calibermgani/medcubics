<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Support\Facades\Input;
use App\Models\Medcubics\IPGroup as IPGroup;
use App\Models\Medcubics\IPUserGroup as IPUserGroup;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Http\Controllers\HomeController as HomeController;
use App\Http\controllers\SessionController as SessionController;
use App\Models\Medcubics\Users as User;
use App\Models\Medcubics\UserVerification as UserVerification;
use App\Models\Medcubics\UserIp as UserIp;
use App\Models\EmailTemplate;
use App\Models\Profile\UserLoginHistory as UserLoginHistory;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Customer as Customer;
use Redirect;
use Request;
use Validator;
use Auth;
use Session;
use Carbon;
use Config;
use Lang;
use URL;
use Hash;
use DB;
use Cache;
use Log;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function show()
    {
        $this->getLogin();
    }

    public function getLogin()
    {           
        $cacheemail = $cachepassword = $cachechecked = $remember_me ='';
        // Store the last URL into session for carryforward it after login needs to redirect it. 
        Session::put('preLink', URL::previous());
        
        /// Check logged in user has already set cookie or not. If yes, get cookie credential and display it ///
        if (isset($_COOKIE['medcubics_user_email']) && isset($_COOKIE['medcubics_user_password']) && isset($_COOKIE['medcubics_remember_me']) && $_COOKIE['medcubics_remember_me'] !="")
        {
            $cacheemail = $_COOKIE['medcubics_user_email'];
            $cachepassword = $_COOKIE['medcubics_user_password'];
            $remember_me = $_COOKIE['medcubics_remember_me'];
        }
        else
        {
            $cacheemail = $cachepassword = $cachechecked = $remember_me ='';
        }
        return view('auth/login', compact('cacheemail','cachepassword','remember_me'));
    }

    ### End Hide "Security Code" when typing a new email ID ###
    public function postLogin(Request $request)
    {        
        $request = Request::all();
        $config_attempt_count   = Config::get('siteconfigs.password.attempt');
        $rules = array('email' => 'required|email', 'password' => 'required');
        $validator = Validator::make($request, $rules);
        $credentials = Request::only('email', 'password');
    
        if($validator->fails())  {
            $errors = $validator->errors();
            return Redirect::to('auth/login')->withInput(Request::except("password"))->withErrors($errors);
        } else {       
            $username = $request['email'];
            $password = $request['password'];
            if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
                //user sent their email 
                $credentials = ['email' => $username, 'password' => $password]; 
            } else {
                //they sent their username instead 
                $credentials = ['username' => $username, 'password' => $password];
            }
            
            $security_code_approval = User::where('email',$request['email'])->value('security_code');
 
            $user_details = User::where('email',$request['email'])->get()->first();
            if(!empty($user_details) && $user_details->login_attempt >= 3){ 
                $error_msg = $this->getAttemptmsg($user_details->login_attempt,$user_details->attempt_updated,$request['email'],"error");
                if($error_msg =="success") {
                    return Redirect::to('/');
                } else  {
                    Auth::logout();
                    return Redirect::to('auth/login')->withInput()->withErrors($error_msg);
                }
            } else if(Auth::attempt($credentials) ) {
                //$user_details = User::where('email',$request['email'])->firstOrFail();
                if(empty($user_details)) {
                    $error_msg = "Invalid User" ;
                    $this->auth->logout();
                    return Redirect::to('auth/login')->withInput(Request::except("password"))->withErrors($error_msg);
                }
            
                if($user_details->status =='Inactive')  {
                    $error_msg = Lang::get("common.validation.act_inactive") ;
                    $this->auth->logout();
                    return Redirect::to('auth/login')->withInput(Request::except("password"))->withErrors($error_msg);
                }
                /* Get browser and device name - Anjukaselvan*/
                $browserAndDeviceName = Helpers::getBrowserAndDeviceName();
                $browser = $browserAndDeviceName['browser_name'];
                $device = $browserAndDeviceName['device_name'];
            
                /* Author : Selvakumar | Desc : Login Security system | Created : 10JUL2018 */
                $this->getAttemptReset($request['email'], 0);
                $ip_lat_longitude = Helpers::GetIpAndLatAndLongitude();
                $ipAddress = $ip_lat_longitude['ipaddress'];
                $userIp_details = UserIp::where('user_id',$user_details->id)->where('ip_address',$ipAddress);
                $userIp = $userIp_details->count();
                $userIpInfo = $userIp_details->first();
				
				
				/* IP Group function */
				$ipUserGroupInfo = IPUserGroup::where('user_id',524)->where('status','Active')->whereNull('deleted_at');
				$ipUserGroupCount = $ipUserGroupInfo->count();
				\Log::info('ip Address'.$ipAddress);
				if($ipUserGroupCount > 0){
					$ipUserGroupdetails = $ipUserGroupInfo->get();
					$ipGroup = [];
					foreach($ipUserGroupdetails as $ipUGList){
						$ipGroup[] = $ipUGList->group_name;
					}
					$ipGroupInfo = IPGroup::whereIn('group_name',$ipGroup)->pluck('ip_address')->toArray();
					\Log::info($ipGroupInfo);
					if(!in_array($ipAddress,$ipGroupInfo)){
						$error_msg = "Access restricted for this location" ;
						Auth::logout();
						return Redirect::to('auth/login')->withInput(Request::except("password"))->withErrors($error_msg);
					}
				}
				/* IP Group function */
				
				
                
                if($userIp == 0 && $security_code_approval == 'Yes') {
                    if($user_details->role_id == 1 || $user_details->id == 170) {
                        // access permissions stored in cache
                        $user_id = $user_details->id;
                        $dbconnection = new DBConnectionController();
                        $dbconnection->setAccessCache($user_id);
                        
                        return Redirect::to('/');
                    }
                    if($user_details->practice_user_type == 'customer' || $user_details->id == 94) {
                        $domain = explode('/',Request::root());
                        /* if(($user_details->customer_id != 15 && $user_details->customer_id != 0) && $domain[2] == 'avec.medcubics.com'){
                            $error_msg = "This workspace not allowed";
                            $this->auth->logout();
                            return Redirect::to('auth/login')->withInput(Request::except("password"))->withErrors($error_msg);
                        }
                        
                        if($user_details->customer_id == 15 && $domain[2] == 'pms.medcubics.com') {
                            $error_msg = "This workspace not allowed";
                            $this->auth->logout();
                            return Redirect::to('auth/login')->withInput(Request::except("password"))->withErrors($error_msg);
                        }  */  
                        // access permissions stored in cache
                        $user_id = $user_details->id;
                        $dbconnection = new DBConnectionController();
                        $dbconnection->setAccessCache($user_id);
                        
                        return Redirect::to('/');                   
                    }               
                    $error_msg = Lang::get("common.validation.security");
                    $digits = 4;
                    $security_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
                    $security_code_attempt = 1;
                    $dataArr['user_id'] = $user_details->id;
                    $dataArr['ip_address'] = $ipAddress;
                    $dataArr['security_code'] = $security_code;
                    $dataArr['security_code_attempt'] = $security_code_attempt;
                    $dataArr['browser_name'] = $browser;
                    $dataArr['device_name'] = $device;
                    UserIp::create($dataArr);

                    // Send mail to registered user cc to admin, Customer Admin, Practice Admin

                    // Start  Mail send for notification of security code
					$customers = Customer::where('id',$user_details->customer_id)->first();

                     $customer_admin = User::where('customer_id',$user_details->customer_id)->where('practice_user_type', 'customer')->first();
                     $practice_admin = User::where('customer_id',$user_details->customer_id)->where('practice_user_type', 'practice_admin')->first();

                    $message = trans("email/email.security_email");         
                    $oldPhrase = ["VAR_CUSTOMER_USER", "VAR_USER_NAME", "VAR_USER_EMAIL", "VAR_LOGIN_ATTEMPT", "VAR_IP_ADDRESS", "VAR_DATE","VAR_SECURITY_CODE","VAR_SITE_NAME"];
                    
                    $Subject = "Security Code Notification";
                    // Customer , customer admin , practice admin are comment the mail sending process
                    // Send Email to Customer                   
                    $newPhrase   = [$customers->customer_name,$user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"), $security_code, "Medcubics"];
                    $newMessage = str_replace($oldPhrase, $newPhrase, $message);
                    $deta = array('name'=> $customers->customer_name,'email'=> $customers->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
                    Helpers::sendMail($deta);   
                    
                    // Send Email to Customer Admin 
                    $newPhrase   = [@$customer_admin->name, $user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
                    $newMessage = str_replace($oldPhrase, $newPhrase, $message);
                    $deta = array('name'=> @$customer_admin->name,'email'=> $customer_admin->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
                    Helpers::sendMail($deta);           
                    
                    // Send Email to  Practice Admin
                    $newPhrase   = [$practice_admin->name, $user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
                    $newMessage = str_replace($oldPhrase, $newPhrase, $message);
                    $deta = array('name'=> $practice_admin->name,'email'=> $practice_admin->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
                    Helpers::sendMail($deta);
                    
					if($user_details->practice_user_type == 'provider' && $user_details->provider_access_id != 0){
						// Send Email to  registered User and bcc to admin mail
						$newPhrase   = [$user_details->name,$user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
						$newMessage = str_replace($oldPhrase, $newPhrase, $message); 

						// Security code email needs send only the logged user along with admin@medcubics.com
						// Rev. 1 - Ref# MR-2789 - 04-09-2019 - Author: Ravi
						$deta = array('name'=> $user_details->name,'email'=> $user_details->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
						Helpers::sendMail($deta);
					}

                    // End Mail send for notification of security code
                    Auth::logout();                  
                    return Redirect::to('auth/login')->withInput(Request::except("security"))->withErrors($error_msg);
                } elseif($security_code_approval == 'No'){
					
					if($user_details->customer_id == 19){
						$verifyCount = UserVerification::where('user_id',$user_details->id)->where('email_is_verified','Yes')->count();
						if($verifyCount == 0){
							$error_msg = "Please verify your email id";
							Auth::logout(); 
							return Redirect::to('auth/login')->with('error',$error_msg);
						}
					}
					
					// access permissions stored in cache
                    $user_id = $user_details->id;
                    $dbconnection = new DBConnectionController();
                    $dbconnection->setAccessCache($user_id);
                    
                    return Redirect::to('/');
                } elseif(isset($userIpInfo->approved) && $userIpInfo->approved == 'Yes') {
                    // access permissions stored in cache
                    $user_id = $user_details->id;
                    $dbconnection = new DBConnectionController();
                    $dbconnection->setAccessCache($user_id);            

                    return Redirect::to('/');
                } elseif(isset($userIpInfo->approved) && $userIpInfo->approved == 'No' && !isset($request['security'])) {
                    $error_msg = Lang::get("common.validation.security");
					
					// Send mail to registered user cc to admin, Customer Admin, Practice Admin
					
					$security_code_attempt = $userIpInfo->security_code_attempt;
					$security_code = $userIpInfo->security_code;
					$ipAddress = $userIpInfo->ip_address;
                    // Start  Mail send for notification of security code
					$customers = Customer::where('id',$user_details->customer_id)->first();

                     $customer_admin = User::where('customer_id',$user_details->customer_id)->where('practice_user_type', 'customer')->first();
                     $practice_admin = User::where('customer_id',$user_details->customer_id)->where('practice_user_type', 'practice_admin')->first();

                    $message = trans("email/email.security_email");         
                    $oldPhrase = ["VAR_CUSTOMER_USER", "VAR_USER_NAME", "VAR_USER_EMAIL", "VAR_LOGIN_ATTEMPT", "VAR_IP_ADDRESS", "VAR_DATE","VAR_SECURITY_CODE","VAR_SITE_NAME"];
                    
                    $Subject = "Security Code Notification";
                    // Customer , customer admin , practice admin are comment the mail sending process
                    // Send Email to Customer                   
                    $newPhrase   = [$customers->customer_name,$user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
                    $newMessage = str_replace($oldPhrase, $newPhrase, $message);
                    $deta = array('name'=> $customers->customer_name,'email'=> $customers->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
                    Helpers::sendMail($deta);   
                    
                    // Send Email to Customer Admin 
                    $newPhrase   = [@$customer_admin->name, $user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
                    $newMessage = str_replace($oldPhrase, $newPhrase, $message);
                    $deta = array('name'=> @$customer_admin->name,'email'=> @$customer_admin->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
                    Helpers::sendMail($deta);           
                    
                    // Send Email to  Practice Admin
                    $newPhrase   = [$practice_admin->name, $user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
                    $newMessage = str_replace($oldPhrase, $newPhrase, $message);
                    $deta = array('name'=> $practice_admin->name,'email'=> $practice_admin->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
                    Helpers::sendMail($deta);
                    
					if($user_details->practice_user_type == 'provider' && $user_details->provider_access_id != 0){
						// Send Email to  registered User and bcc to admin mail
						$newPhrase   = [$user_details->name,$user_details->name,$user_details->email, $security_code_attempt,$ipAddress,date("Y-m-d H:i:s"),$security_code, "Medcubics"];
						$newMessage = str_replace($oldPhrase, $newPhrase, $message); 

						// Security code email needs send only the logged user along with admin@medcubics.com
						// Rev. 1 - Ref# MR-2789 - 04-09-2019 - Author: Ravi
						$deta = array('name'=> $user_details->name,'email'=> $user_details->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'');
						Helpers::sendMail($deta);
					}
                    // End Mail send for notification of security code
					
					
                    Auth::logout();
                    return Redirect::to('auth/login')->withInput(Request::except("security"))->withErrors($error_msg);      
                } elseif(isset($userIpInfo->approved) && $userIpInfo->approved == 'No' && isset($request['security'])) { 
                    $totalDays = Helpers::daysSinceCreated($userIpInfo->created_at);
                    if(($request['security'] == $userIpInfo->security_code) && ($totalDays == 0)) {
                        UserIp::where('id',$userIpInfo->id)->update(['approved'=>'Yes','first_login'=>date("Y-m-d H:i:s")]);
                    } else {
						// remove the 24 hrs expired option in different ip login
						if($totalDays != 0){
							// Security code issues fixed ( Onces code generated and then send code again in email )
							// Revision 1 : Feedback : 25 Sep 2019 : Selva
							$customers = Customer::where('id',$user_details->customer_id)->first();

							$customer_admin = User::where('customer_id',$user_details->customer_id)->where('practice_user_type', 'customer')->first();
							$practice_admin = User::where('customer_id',$user_details->customer_id)->where('practice_user_type', 'practice_admin')->first();

							$message = trans("email/email.security_email");			
							$oldPhrase = ["VAR_CUSTOMER_USER", "VAR_USER_NAME", "VAR_USER_EMAIL", "VAR_LOGIN_ATTEMPT", "VAR_IP_ADDRESS", "VAR_DATE", "VAR_SECURITY_CODE","VAR_SITE_NAME"];
							
							$Subject = "Security Code Notification";
							
							$newPhrase   = [$user_details->name,$user_details->name,$user_details->email, $userIpInfo->security_code_attempt,$ipAddress,date("Y-m-d h:i:s"),$userIpInfo->security_code, "Medcubics"];
							$newMessage = str_replace($oldPhrase, $newPhrase, $message); 

							$deta = array('name'=> $user_details->name,'email'=> $user_details->email,'subject'=> $Subject,'msg' => $newMessage,'attachment'=>'', 'cc_email' => @$customers->email);
							Helpers::sendMail($deta);
						
						}
                        if($userIpInfo->security_code_attempt > 2 && isset($request['security']) && !empty($request['security'])) {
                            $error_msg = Lang::get("common.validation.un_security");
                            Auth::logout();
                            return Redirect::to('auth/login')->withInput(Request::except("security"))->withErrors($error_msg);
                        } elseif(isset($request['security']) && !empty($request['security'])) {
                            $security_code_attempt = $userIpInfo->security_code_attempt + 1;
                            UserIp::where('id',$userIpInfo->id)->update(["security_code_attempt"=>$security_code_attempt]);
                            $error_msg = Lang::get("common.validation.in_security");
                            Auth::logout();
                            return Redirect::to('auth/login')->withInput(Request::except("security"))->withErrors($error_msg);
                        } else {
                            $error_msg = Lang::get("common.validation.security");
                            Auth::logout();
                            return Redirect::to('auth/login')->withInput(Request::except("security"))->withErrors($error_msg);
                        }
                    }
                } else {
                    $error_msg = Lang::get("common.validation.in_security");
					Auth::logout();
                    return Redirect::to('auth/login')->withInput(Request::except("security"))->withErrors($error_msg);  
                }
                
                /* Author : Selvakumar | Desc : Login Security system | Created : 10JUL2018 */
                
                if($config_attempt_count > $user_details->login_attempt) {
                    // Get Browser name
                    //$browser = Helpers::browserName();

                    // Check user has checked remembe me option or not. If checked, set cookie for user credential //
                    if(Input::has('remember')) {
                        setcookie('medcubics_user_email', $request['email'], time() + (3600000));
                        setcookie('medcubics_user_password', $request['password'], time() + (3600000));
                        setcookie('medcubics_remember_me', $request['remember'], time() + (3600000));
                    } else {
                        setcookie('medcubics_remember_me', '');
                    }   
                    // Reset login attempt
                    $this->getAttemptReset($request['email'], 0);
                    return Redirect::to('/');
                } else {
                    $error_msg = $this->getAttemptmsg($user_details->login_attempt,$user_details->attempt_updated,$request['email'],"success");
                    if($error_msg =="success") {
                        return Redirect::to('/');
                    } else {
                        Auth::logout();
                        return Redirect::to('auth/login')->withInput()->withErrors($error_msg);
                    }
                }
            } else {   
                if(User::where('email',$request['email'])->count()) {
                    $user_details = User::where('email',$request['email'])->firstOrFail();
                    $user_details->timestamps = false;
                    $attempt_updated = $user_details->attempt_updated;
                    $user_details->login_attempt = $user_details->login_attempt+1;
            
                    $password_attempt_dtl   = Config::get('siteconfigs.password');
                    $config_attempt_count   = $password_attempt_dtl['attempt'];
                    $config_attempt_expire  = $password_attempt_dtl['attempt_expire'];
                    $time = strtotime($attempt_updated);
                    
                    $access_time = date("Y-m-d H:i:s", strtotime("+".$config_attempt_expire." minutes",$time));
                    if(strtotime($access_time) >= strtotime(date('Y-m-d H:i:s'))) {
                    if($user_details->login_attempt <= $config_attempt_count)

                        $user_details->attempt_updated = date('Y-m-d H:i:s');
                        $user_details->save();
                        $error_msg = $this->getAttemptmsg($user_details->login_attempt,$user_details->attempt_updated,$request['email'],"error");
                    } else {
                        $this->getAttemptReset($request['email'],0);
                        $error_msg = $this->getAttemptmsg($user_details->login_attempt,$user_details->attempt_updated,$request['email'],"error");
                    }
                } else {
                    $error_msg = Lang::get('passwords.wrong_password');
                }
                return Redirect::to('auth/login')->withInput()->withErrors($error_msg);
            }           
        }
    }   
    
    
    public function logout(Request $request) {        
        $practice_id = Session::get('practice_dbid');

        $dbconnection = new DBConnectionController();   
        $dbconnection->clearDBSession();

        $user_details = Auth::user(); 
        // Auth user is empty or not 
        if($user_details != null) {
            $user_id = Auth::user()->id; 
            $msg = Request::input('msg');
            $result = User::where('id',$user_id)->update(array('is_logged_in' => '0'));
            // Logout start 
            
            $session_login_id = Session::get('login_session_id');
            if($session_login_id  !='' && $session_login_id !=null)
            {
                $session_id     =   Helpers::getEncodeAndDecodeOfId($session_login_id,"decode");
                $get_login_qry  =   explode("::::",$session_id);
                if(count($get_login_qry)>1)
                    $current_login = UserLoginHistory::where("user_id",$get_login_qry[1])->where("created_at",$get_login_qry[0])->update(array('logout_time' => date('Y-m-d H:i:s')));
            }            
			Cache::forget('default_view');
            Cache::forget('default_view_list_id');
            Cache::forget('access_permission'.$user_id);
            Cache::forget('user_module_cnt_'.$user_id);            
            Cache::forget('practice_details'.$practice_id );
            Auth::logout();
            return redirect('/login');
            
            if($msg != '') {  
                $errors =  $msg;
                return Redirect::to('auth/login')->withInput(Request::except("password"))->withErrors($errors);
            } else {
                return Redirect::to('/');
            }
        } else {
            return redirect('/login');
        }
        
    }


    public function getAttemptmsg($user_attempt_count,$user_last_attempt_time,$email,$status_part)
    {
        $password_attempt_dtl   = Config::get('siteconfigs.password');
        $config_attempt_count   = $password_attempt_dtl['attempt'];
        $config_attempt_expire  = $password_attempt_dtl['attempt_expire'];
        $time = strtotime($user_last_attempt_time);
        $access_time = date("Y-m-d H:i:s", strtotime("+".$config_attempt_expire." minutes",$time));
        
        $get_min = ceil(abs(strtotime($access_time) - strtotime(date('Y-m-d H:i:s'))) / 60);
        if((strtotime($access_time) < strtotime(date('Y-m-d H:i:s'))) && $status_part =="success")
        {
            $reset = $this->getAttemptReset($email,0);
            return "success";
        }
        if((strtotime($access_time) < strtotime(date('Y-m-d H:i:s'))) && $status_part == "error")
        {
            $reset = $this->getAttemptReset($email,1);
            $user_attempt_count =1;
        }
            
        if($user_attempt_count == 1)            
            $msg = Lang::get('passwords.wrong_password');

        elseif($user_attempt_count < $config_attempt_count)
        {   
            $remain_count = $config_attempt_count-$user_attempt_count;
            $old_msg = Lang::get('passwords.attempt_remain');
            $msg = str_replace("##COUNT##",$remain_count, $old_msg);
        }
        elseif($user_attempt_count >= $config_attempt_count)
        {
            $old_msg = Lang::get('passwords.attempt_expire');
            $msg = str_replace("##CONFIGCOUNT##",$config_attempt_count,$old_msg);
            $msg = str_replace("##CONFIGTIME##",$get_min,$msg);
        }
        //dd($msg);
        return $msg;
    }
    /*** Attempt error message end ***/
    
    /*** Attempt count reset function starts ***/
    public function getAttemptReset($email,$attempt)
    {
        $user_details = User::where('email',$email)->firstOrFail();
        $user_details->timestamps = false;
        $user_details->login_attempt = $attempt;
        $user_details->attempt_updated = date('Y-m-d H:i:s');
        $user_details->save();
        return "success";
    }   
    /*** Attempt count reset function end ***/
    
    // Display the forgot password 
    public function getEmail()
    {
        return view('auth/password');
    }
    
    // Send and update token for the forgot password 
    public function postEmail(Request $request)
    {
        $request = Request::all();
        $config_attempt_count   = Config::get('siteconfigs.password.attempt');
     
        $rules      = array('email' => 'required|email|exists:users');
        
        $validation_msg     = array('exists'  => Lang::get("common.validation.emailexist"));
        $validator  = Validator::make($request, $rules,$validation_msg);
        //Back end validation check 
        if ($validator->fails()) {   
            $errors = $validator->errors();
            return Redirect::to('password/email')->withInput()->withErrors($errors);
        } else {   
            $activationhours = Config::get('siteconfigs.login.resetpasswordactivationhours');  
            $email = $request['email']; 
            //Condition check Email is non empty
            if($email != '') {
                $get_user_details = User::where('email', '=', $email)->first(); 
                //user Details check
                if(!empty($get_user_details)) {
                    $error_msg = $this->getAttemptmsg($get_user_details->login_attempt,$get_user_details->attempt_updated,$request['email'],"success");
                    
                    if($config_attempt_count > $get_user_details->login_attempt || $error_msg == 'success') {   
                        $current_date = date('Y-m-d H:i:s');
                        $token        = rand(1000,10000);
                        //Encode the Email, token
                        $generate_email = base64_encode($email);
                        $generate_token = base64_encode($token);
                        $reseturl = URL::to('resetpassword/'.$generate_email.'/'.$generate_token);                        
                        User::where('email','=',$email)->update(['token'=>$token,'reset_start_date'=>$current_date]);                        
                        //Email template count check                                        
                            $arr = [
                                'name' => $get_user_details->name,                              
                                "active_link" =>'<a href="'.$reseturl.'" target="_blank">'.$reseturl.'</a>',
                                "activation_hours" => $activationhours
                            ]; 

                            $msg_content = Lang::get("auth.forget_password_content");
                            $msg_content = str_replace('##VAR-NAME##', $arr['name'], $msg_content);
                            $msg_content = str_replace('##VAR-RESETURL##', $arr['active_link'], $msg_content);
                            $msg_content = str_replace('##VAR-RESETHOUR##', $arr['activation_hours'], $msg_content);
                                                                    
                            $res = array('email'=>  @$email,
                                'subject'   =>  "Forgot password Request",
                                'msg'       =>  $msg_content,
                                'name'      =>  @$get_user_details->name
                            );   
                        $msg_status = $this->common_send_mail($res);                               
                        return Redirect::to('password/email')->with('success',"Password reset link sent to your email ID");
                    } else {
                        $error = array('email'=>$error_msg);
                        return Redirect::to('password/email')->withInput()->withErrors($error); 
                    }
                }
                
            }
        }
    }

    public function common_send_mail($data){ 
        \Log::info("Send mail function called from common send mail");
        // Send common mail function itself.
        Helpers::sendMail($data);
        return 'Success';       
    }

    // Show the reset password form
    public function getReset($email,$token) {
        //Decode the Email, token
        $emails = base64_decode($email);
        $tokens = base64_decode($token); 
        $current_date = date('Y-m-d H:i:s'); 
        //Get user details 
        $get_user_details = User::where('email', '=', $emails)->where('token', '=', $tokens)->first();
        if(!empty($get_user_details)) {
            $activationhours = Config::get('siteconfigs.login.resetpasswordactivationhours');
            $reset_start_date   = $get_user_details->reset_start_date; 
            $statementDate = date("Y-m-d H:i:s", strtotime($reset_start_date.'+'.$activationhours.' hours'));   
            if($current_date<=$statementDate) {
                $invalid = '0'; 
                return view('auth/reset',  compact('get_user_details','invalid','email','token'));
            } else {
                $invalid = '1'; 
                return view('auth/reset',  compact('get_user_details','invalid','email','token'));
            }
        } else {
            $invalid = '2';
            return view('auth/reset',  compact('get_user_details','invalid','email','token'));
        }
    }
    
    // Update the new password in reset module.
    public function postReset($email='',$token='') {
        $request = Request::all();
        //Encode the Email id
        $emails = base64_encode($request['email']);
        if($token=='')
            $token  = $request['token'];
        
        Validator::extend('checkpassword', function($attribute, $value, $parameters) {
            if(preg_match('/[A-Za-z]/', $value) && preg_match('/[0-9]/', $value)) {
                return $value == true;
            } else {
                return $value == false;
            }
        });
        
        
        $rules = array('password' => 'required|min:6|max:20|checkpassword', 'confirmpassword' => 'required|same:password');
        
        $validation_msg     = array('checkpassword'  => Lang::get("common.validation.userresetpassword"));
        
        $validator = Validator::make($request, $rules,$validation_msg);
        if ($validator->fails())  {
            $errors = $validator->errors();
            return Redirect::to('resetpassword/'.$emails.'/'.$token)->withErrors($errors)->withInput();
        } else {
            $new_password = '';
            if(isset($request['password']))
                $new_password = Hash::make($request['password']);
            
            $email  = $request['email'];
            User::where('email','=',$email)->update(['token'=>'','reset_start_date'=>'','password'=>$new_password]);
            return Redirect::to('auth/login')->with('infocus',"Your new password is reset successfully. So you can logged in.");
        }   
    }

}
