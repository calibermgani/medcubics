<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Redirect;

use Request;
use DB;
use App\Models\Medcubics\Speciality as Speciality;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\Medcubics\NpiFlag as NpiFlag;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\UserVerification as UserVerification;
use App\Http\Helpers\Helpers as Helpers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        //$this->middleware('guest');
    }
	
	
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
	
	public function showRegistrationForm(){
		$specialities = Speciality::orderBy('speciality', 'ASC')->pluck('speciality', 'id')->all();
		$taxanomies = [];
		return view('auth/register',compact('specialities','taxanomies'));
	}
	
	public function practiceCreation(){
		$customerID = 19;
		$request = Request::all();
		
		$token = $request['token'];
		$action = $request['action'];
		
		/* Author : Selvakumar  DESC : Server side validation for email  JIRA : MEDV2-1428 */
		if(isset($request['email'])){
			$userEmailCount = Users::where('email',$request['email'])->count();
			if($userEmailCount > 0){
				return Redirect::to('auth/register')->with('error','Email already exists');
			}
		}
		  
		// call curl to POST request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => '6LdJQMIbAAAAABy2liyw9ocvY4fXVEYsZUjF8LRb', 'response' => $token)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		$arrResponse = json_decode($response, true);
		  
		// verify the response
		if($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
			$request['practice_description'] = date('m/d/Y');
			$request['doing_business_s'] = $request['practice_name'];
			$request['entity_type'] = 'Individual';
			$request['billing_entity'] = 'No';
			$request['customer_id'] = $customerID;
			$request['api_ids'] = '2,1';
			$practice_db_name = str_replace(" ", "_", $request['practice_name']);
			$phone = $request['phone'];
			unset($request['phone']);
			DB::beginTransaction();   
			try {
				$request['primary_add_1'] = $request['address_line_1']; 
				$request['primary_add_2'] = $request['address_line_2']; 
				$request['primary_city'] = $request['city']; 
				$request['primary_state'] = $request['state']; 
				$request['primary_zip5'] = $request['zip5']; 
				$request['primary_zip4'] = $request['zip4']; 
				if($request['same_as_paytoaddress'] == 'on'){
					$request['mail_add_1'] = $request['address_line_1']; 
					$request['mail_add_2'] = $request['address_line_2']; 
					$request['mail_city'] = $request['city']; 
					$request['mail_state'] = $request['state']; 
					$request['mail_zip5'] = $request['zip5']; 
					$request['mail_zip4'] = $request['zip4']; 
				}else{
					$request['mail_add_1'] = $request['mailing_address_line_1'];
					$request['mail_add_2'] = $request['mailing_address_line_2'];
					$request['mail_city'] = $request['mailing_city'];
					$request['mail_state'] = $request['mailing_state'];
					$request['mail_zip5'] = $request['mailing_zip5'];
					$request['mail_zip4'] = $request['mailing_zip4'];
				}
				$request['language_id'] = 5;
				$practices = Practices::create($request);
				$apiListString = (!empty($request['apilist'])) ? implode(",", $request['apilist']) : '';
				
				$practice_id = $practices->id;
				
				$practices->api_ids = $apiListString;
				$practices->save();

				$practice_name = $practices->practice_name;
				
				
				/* 
				 *
				 * Practice Admin User Creation Process Start
				 *
				 */
				
				$userArr['customer_id'] = $customerID;
				$userArr['role_id'] = 0;
				$userArr['name'] = $request['name'];
				$userArr['short_name'] = substr($request['name'],0,3);
				$userArr['email'] = $request['email'];
				$userArr['password'] = Hash::make($request['password']);
				$userArr['phone'] = $phone;
				$userArr['user_type'] = 'Practice';
				$userArr['practice_user_type'] = 'practice_admin';
				$userArr['admin_practice_id'] = $practice_id;
				$userArr['status'] = 'Active';
				$userArr['is_logged_in'] = '0';
				$userArr['useraccess'] = 'Web';
				$userArr['practice_access_id'] = $practice_id;
				$userArr['app_name'] = 'WEB';
				$userArr['security_code'] = 'No';
				
				$userInfo = Users::create($userArr);
				Users::where('id',$userInfo->id)->update(['security_code'=>'No']);
				
				
				$verificationArr['user_id'] = $userInfo->id; 
				$verificationArr['email_is_verified'] = 'No';
				UserVerification::create($verificationArr);
				
				
				/* 
				 *
				 * Practice Admin User Creation Process End
				 *
				 */
				
				
				$address_flag = array();
				$address_flag['type'] = 'practice';
				$address_flag['type_id'] = $practice_id;
				$address_flag['type_category'] = 'primary_address';
				$address_flag['address2'] = $request['address_line_1'];
				$address_flag['city'] = $request['city'];
				$address_flag['state'] = $request['state'];
				$address_flag['zip5'] = $request['zip5'];
				$address_flag['zip4'] = $request['zip4'];
				$address_flag['is_address_match'] = '';
				$address_flag['error_message'] = '';
				AddressFlag::checkAndInsertAddressFlag($address_flag);
				
				
				if($request['same_as_paytoaddress'] == 'on'){
					$address_flag = array();
					$address_flag['type'] = 'practice';
					$address_flag['type_id'] = $practice_id;
					$address_flag['type_category'] = 'mailling_address';
					$address_flag['address2'] = $request['address_line_1'];
					$address_flag['city'] = $request['city'];
					$address_flag['state'] = $request['state'];
					$address_flag['zip5'] = $request['zip5'];
					$address_flag['zip4'] = $request['zip4'];
					$address_flag['is_address_match'] = '';
					$address_flag['error_message'] = '';
					AddressFlag::checkAndInsertAddressFlag($address_flag);
				}else{
					$address_flag = array();
					$address_flag['type'] = 'practice';
					$address_flag['type_id'] = $practice_id;
					$address_flag['type_category'] = 'mailling_address';
					$address_flag['address2'] = $request['mailing_address_line_1'];
					$address_flag['city'] = $request['mailing_city'];
					$address_flag['state'] = $request['mailing_state'];
					$address_flag['zip5'] = $request['mailing_zip5'];
					$address_flag['zip4'] = $request['mailing_zip4'];
					$address_flag['is_address_match'] = '';
					$address_flag['error_message'] = '';
					AddressFlag::checkAndInsertAddressFlag($address_flag);
				}
				
				
				/* Starts - NPI flag update */
				$request['company_name'] = 'npi';
				$request['type'] = 'practice';
				$request['type_id'] = $practice_id;
				$request['type_category'] = 'Individual';
				NpiFlag::checkAndInsertNpiFlag($request);
				/* Ends - NPI flag update */

				
				$dbconnection = new DBConnectionController();
				$dbconnection->updatePracticeDBID($practice_id);
				
				
				if (config('siteconfigs.is_enable_provider_add')) {
					$practice_db_name = $dbconnection->getpracticedbname($practice_name);
					if ($dbconnection->createSchema($practice_db_name)) {
						\Log::info("Create schema done");
						$migration_files = glob(base_path() . "/database/migrations/practicemigration/*.php");
						foreach ($migration_files as $migratefiles) {
							@unlink($migratefiles);
						}
						
						$request['practice_db_id'] = $practice_id;
						$dbconnection->deleteotherpractices($practice_id, $practice_db_name);
					} else {
						\Log::info("Create schema not success Please check log");
					}
					$dbconnection->updateApiInfoinPracticeDB($request, $practice_id, $practice_db_name, 'add');
				}
				
				$Subject = "Medcubics email verification";
				$tempEmail = $this->emailEncryption($request['email']);
				$link = url('/').'/verification/'.$tempEmail;
				$deta = array('name'=> 'dose','email'=> $request['email'],'subject'=> $Subject,'msg' => 'Successfully created medcubics account please verify your email '.$link,'attachment'=>'');
				Helpers::sendMail($deta);
				
				DB::commit();
				return Redirect::to('auth/register')->with('success','Practice created successfully');
			} catch (\Exception $e) {
				\Log::info('catch'.$e->getMessage().' line '.$e->getLine());
				DB::rollback();
				return Redirect::to('auth/register')->with('error','Something went wrong');
			}
		} else {
			return Redirect::to('auth/register')->with('error','Something went wrong');
		}
		
	}
	
	
	public function emailEncryption($email){
		$ciphering = "AES-128-CTR";
		$iv_length = openssl_cipher_iv_length($ciphering);
		$options = 0;
		$encryption_iv = '1234567891011121';
		$encryption_key = "ABCDEFGHIJKLMNOPQRSWUVZ";
		$encryption = openssl_encrypt($email, $ciphering,
            $encryption_key, $options, $encryption_iv);
		$encryption = str_replace("/","=MED=",$encryption);
		return $encryption;
	}
	
	public function emailDecryption($email){
		$email = str_replace("=MED=","/",$email);
		$ciphering = "AES-128-CTR";
		$decryption_iv = '1234567891011121';
		$options = 0;
		$decryption_key = "ABCDEFGHIJKLMNOPQRSWUVZ";
		$decryption=openssl_decrypt ($email, $ciphering, 
				 $decryption_key, $options, $decryption_iv);
		return $decryption;
	}

	public function emailVerification($email){
		$email = $this->emailDecryption($email);
		$userInfo = Users::where('email',$email)->get()->first();
		if(isset($userInfo->id)){
			$userVerificationInfo = UserVerification::where('user_id',@$userInfo->id)->count();
		}else{
			return Redirect::to('auth/login')->with('error','Invalid email');
		}
		if($userVerificationInfo > 0){
			UserVerification::where('user_id',@$userInfo->id)->update(['email_is_verified'=>'Yes']);
		}else{
			if(isset($userInfo->id))
				UserVerification::create(['user_id'=>@$userInfo->id,'email_is_verified'=>'Yes']);
		}
		
		return Redirect::to('auth/login')->with('success','Successfully verified');
	}
}
