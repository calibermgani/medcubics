<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Practice as Practice;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Facility as Facility;
use App\Models\Medcubics\Provider as Provider;
use App\Models\Medcubics\Roles as Roles;
use App\Models\Medcubics\Language as Language;
use App\Models\Medcubics\Ethnicity as Ethnicity;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use Auth;
use Hash;
use Response;
use Request;
use Config;
use Validator;
use Input;
use Schema;
use App;
use DB;
use File;
use Lang;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;

class PracticeUserApiController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndexApi($customer_id, $practice_id,$export="")
	{
		$practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$customer_id = Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$adminusers = Users::all();
		$practice = Practice::with('taxanomy_details', 'speciality_details','languages_details')->where('id', $practice_id)->first();
		$customers = Customer::where('id', $customer_id)->pluck('customer_name', 'id')->all();
		$customer_practices = Practice::where('id', $practice_id)->pluck('practice_name', 'id')->all();
        $customer_practices_list = Practice::where('customer_id', $customer_id)->pluck('practice_name', 'id')->all();
        $facility = "";
		$provider = Provider::where('practice_id', $practice_id)->pluck('provider_name', 'id')->all();		
        $adminrolls = Roles::where('role_type', 'Medcubics')->where('id', '<>', 1)->pluck('role_name', 'id')->all();
        $practicerolls = Roles::where('role_type', 'Practice')->pluck('role_name', 'id')->all();
        $language = Language::orderBy('language', 'ASC')->where("language", "!=", "")->pluck('language', 'id')->all();
        ;
        $language_id = 5;
        $ethnicity = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();
        ;
		$ethnicity_id = '';
		$addressFlag['general']['address1'] = '';
        $addressFlag['general']['city'] = '';
        $addressFlag['general']['state'] = '';
        $addressFlag['general']['zip5'] = '';
        $addressFlag['general']['zip4'] = '';
        $addressFlag['general']['is_address_match'] = '';
        $addressFlag['general']['error_message'] = '';

		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('adminusers','practice', 'customers', 'customer_practices', 'customer_practices_list', 'facility', 'provider','adminrolls','practicerolls', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'addressFlag')));
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreateApi($customer_id, $practice_id)
	{

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function getStoreApi($request = '')
	{	
		$practice_id = $request['practice_id'];
        $customer_id = $request['customer_id'];
		$practice = Practice::with('taxanomy_details', 'speciality_details','languages_details')->where('id', $practice_id)->first();
		if ($request == '') {
		$request = Request::all();
		}
		if (isset($request['admin_practice_id'])) {
		$request['admin_practice_id'] = implode(',', $request['admin_practice_id']);
		}
        $adminuser = Users::$adminuser_rules + array('email' => 'required|unique:users,email|email', 'password' => 'required', 'confirmpassword' => 'required|same:password');
        $validator = Validator::make($request, $adminuser, Users::adminuser_messages());
        if ($request['dob'] != '')
            $request['dob'] = date('Y-m-d', strtotime($request['dob']));
        $request['password'] = Hash::make($request['password']);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            if ($request['practice_user_type'] != "customer") {
                $request['user_type'] = "Practice";
            } else {
                $request['useraccess'] = "web";
                $request['app_name'] = "";
            }
            if ($request['app_name'] == "CHARGECAPTURE" && $request['useraccess'] == "app") {
                $request['facility_access_id'] = 0;
            } elseif ($request['app_name'] == "WEB" && $request['useraccess'] == "app") {
                $request['provider_access_id'] = 0;
            } else {
                $request['facility_access_id'] = $request['provider_access_id'] = $request['practice_access_id'] = 0;
            }
//            if ($request['useraccess'] == 'app') {
//                $request['practice_user_type'] = "";
//            }
            if ($request['useraccess'] == 'web') {
                $request['app_name'] = "";
            }
            if (Input::hasFile('image')) {
                $image = Input::file('image');
                $filename = rand(11111, 99999);
                $extension = $image->getClientOriginalExtension();
                $filestoreName = $filename . '.' . $extension;
                $resize = array('150', '150');
                Helpers::mediauploadpath('admin', 'user', $image, $resize, $filestoreName);
                $request['avatar_name'] = $filename;
                $request['avatar_ext'] = $extension;
            }

            $adminusers = Users::create($request);
            @$customerusers->avatar_name = isset($request['avatar_name']) ? $request['avatar_name'] : '';
            @$customerusers->avatar_ext = isset($request['avatar_ext']) ? $request['avatar_ext'] : '';
            $user = Auth::user()->id;
            $adminusers->created_by = $user;
            $adminusers->name = $request['name'];
            $adminusers->password = $request['password'];
            $adminusers->remember_token = $request['_token'];
            $adminusers->save();
			$practice_user_id = $adminusers->id;
            // Starts - address flag update 
            $address_flag = array();
            $address_flag['type'] = 'adminuser';
            $address_flag['type_id'] = $adminusers->id;
            $address_flag['type_category'] = 'general_information';
            $address_flag['address2'] = $request['general_address1'];
            $address_flag['city'] = $request['general_city'];
            $address_flag['state'] = $request['general_state'];
            $address_flag['zip5'] = $request['general_zip5'];
            $address_flag['zip4'] = $request['general_zip4'];
            $address_flag['is_address_match'] = $request['general_is_address_match'];
            $address_flag['error_message'] = $request['general_error_message'];
            AddressFlag::checkAndInsertAddressFlag($address_flag);
            // Ends - address flag update  

            //Starts - Setpractice Table insert
            $setprac_user = Users::where('id', $practice_user_id)->first();

            $page_permission_id = '';
			$numItems = count($request);
			$i = 0;
			foreach($request as $key=>$val){					
				$page_permission_values = explode('|',$key);
                $page_id = isset($page_permission_values[1])?$page_permission_values[1]:"";
                $page_permission_id_val = Helpers::getEncodeAndDecodeOfId($page_id,'decode');                                                     
                if(++$i === $numItems)
                        $page_permission_id .= $page_permission_id_val;
                elseif(count($page_permission_values)>0)
                        $page_permission_id .= $page_permission_id_val.',';
			}
            $data = array('user_id' => $practice_user_id, 'role_id' => $setprac_user->role_id, 'practice_id' => $practice_id, 'page_permission_ids' => $page_permission_id);
			$setpractice = Setpracticeforusers::create($data); 
			$user = Auth::user ()->id;
			$setpractice->created_by = $user;
			$setpractice->save();	            

            //Ends - Setpractice Table insert

            $practice_user_id = Helpers::getEncodeAndDecodeOfId($practice_user_id, 'encode');
			return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => compact('practice_id', 'customer_id', 'practice','practice_user_id')));
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShowApi($customer_id, $practice_id, $practice_user_id)
	{	
		$customer_id = Helpers::getEncodeAndDecodeOfId($customer_id, 'decode');
		$practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
		$pra_user_id = Helpers::getEncodeAndDecodeOfId($practice_user_id, 'decode');
		$practice = Practice::with('taxanomy_details', 'speciality_details','languages_details')->where('id', $practice_id)->first();
		$practiceusers = Users::with('customer','language','ethnicity','user','userupdate')->where('id',$pra_user_id)->first();
		$general_address_flag 	= 	AddressFlag::getAddressFlag('practiceusers',$practiceusers->id,'general_information');
		$addressFlag['general'] = 	$general_address_flag;
        $tabs = "yes";
        $practicelist 	= 	Practice::pluck('practice_name', 'id')->all();
        $user_practices     = Setpracticeforusers::where('user_id',$pra_user_id)->get();
        $practice_ids       = array();
        foreach ($user_practices as $practice_id){
            $practice_ids[] = $practice_id->practice_id;
        }       
        $practices = Practice::with('user','update_user')->whereIn('id',$practice_ids)->get();
		$customer_id = Helpers::getEncodeAndDecodeOfId($customer_id, 'encode');
		$practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'encode');
		$practice->encid = Helpers::getEncodeAndDecodeOfId($practice->id,'encode');
		return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => compact('practice_id', 'customer_id', 'practice', 'practiceusers', 'addressFlag', 'tabs', 'pra_user_id', 'practicelist','practices','practice')));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEditApi($customer_id, $practice_id, $id)
	{	
		$customer_id = Helpers::getEncodeAndDecodeOfId($customer_id, 'decode');
		$practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $admin_db_name = getenv('DB_DATABASE');
        if (is_numeric($id) && Users::where('id', $id)->count()) {
            $adminusers = Users::find($id);
            if ($adminusers['dob'] != '0000-00-00') {
                $adminusers['dob'] = date('m/d/Y', strtotime($adminusers['dob']));
            } else {
                $adminusers['dob'] = '';
			}
            $practice = Practice::with('taxanomy_details', 'speciality_details','languages_details')->where('id', $practice_id)->first();
            $practice->id = Helpers::getEncodeAndDecodeOfId($practice->id, 'encode');
            $adminrolls = Roles::where('role_type', 'Medcubics')->where('id', '<>', 1)->pluck('role_name', 'id')->all();
            $practicerolls = Roles::where('role_type', 'Practice')->pluck('role_name', 'id')->all();
            $language = Language::orderBy('language', 'asc')->where("language", "!=", "")->pluck('language', 'id')->all();
            $language_id = $adminusers->language_id;
            $ethnicity = Ethnicity::orderBy('name', 'asc')->pluck('name', 'id')->all();
            $ethnicity_id = $adminusers->ethnicity_id;
            $general_address_flag = AddressFlag::getAddressFlag('adminuser', $adminusers->id, 'general_information');
            $addressFlag['general'] = $general_address_flag;

            $customers = Customer::pluck('customer_name', 'id')->all();
            $customer_practices = Practice::where('customer_id', $adminusers->customer_id)->pluck('practice_name', 'id')->all();
            $customer_practices_list = Practice::where('customer_id', $adminusers->customer_id)->pluck('practice_name', 'id')->all();
            $customer_practices1 = str_replace(' ', '_', strtolower(Practice::where('id', $adminusers->practice_access_id)->value('practice_name')));
            @$dbconnection = new DBConnectionController();
            @$practice_db_name = $customer_practices1;
            @$dbconnection->configureConnectionByName($practice_db_name);
            @$facility = Facility::on($practice_db_name)->where('status', 'Active')->pluck('facility_name', 'id')->all();
            $provider = Provider::on($practice_db_name)->where('status', 'Active')->whereIn('provider_types_id', [Config::get('siteconfigs.providertype.Rendering')])->pluck('provider_name', 'id')->all();
            $dbconnection->configureConnectionByName($admin_db_name);
            $adminusers->encid = Helpers::getEncodeAndDecodeOfId($adminusers->id, 'encode');
            $user_practices     = Setpracticeforusers::where('user_id',$id)->get();
            $practice_ids       = array();
            foreach ($user_practices as $practice_id){
                $practice_ids[] = $practice_id->practice_id;
            }       
            $practices = Practice::with('user','update_user')->whereIn('id',$practice_ids)->get();
			return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('practice','adminrolls', 'adminusers', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'addressFlag', 'customers', 'customer_practices', 'customer_practices_list', 'facility', 'provider', 'practicerolls','practices')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getUpdateApi($customer_id, $practice_id, $id, $request = '')
	{
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ($request == '')
            $request = Request::all();

        $validator = Validator::make(Input::all(), [
                    //'role_id' => 'required',
                    'name' => 'required',
                    'password1' => 'same:confirmpassword1',
                    'confirmpassword1' => 'same:password1',
                    'email' => 'required|unique:users,email,' . $id . '|email',
                    'language_id' => 'required',
                        ], [
                    'role_id.required' => Lang::get("admin/adminuser.validation.roletype"),
                    'name.required' => Lang::get("admin/adminuser.validation.name"),
                    'password1.same' => Lang::get("admin/adminuser.validation.password"),
                    'confirmpassword1.same' => Lang::get("admin/adminuser.validation.confirmpassword"),
                    'email.required' => Lang::get("admin/adminuser.validation.email"),
                    'email.unique' => Lang::get("admin/adminuser.validation.email_unique"),
                    'email.email' => Lang::get("admin/adminuser.validation.email_email"),
                    'language_id.required' => Lang::get("admin/adminuser.validation.email_email"),
                        ]
        );
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            $user = Users::findOrFail($id);
            if ($request['practice_user_type'] != "customer") {
                $request['user_type'] = "Practice";
            } else {
                $request['useraccess'] = "web";
                $request['app_name'] = "";
            }
            if ($request['app_name'] == "CHARGECAPTURE" && $request['useraccess'] == "app") {
                $request['facility_access_id'] = 0;
            } elseif ($request['app_name'] == "WEB" && $request['useraccess'] == "app") {
                $request['provider_access_id'] = 0;
            } else {
                $request['facility_access_id'] = $request['provider_access_id'] = $request['practice_access_id'] = 0;
            }
            if ($request['useraccess'] == 'web') {
                $request['app_name'] = "";
            }
            if (Input::hasFile('image')) {
                $image = Input::file('image');
                $filename = rand(11111, 99999);
                $old_filename = $user->avatar_name;
                $old_extension = $user->avatar_ext;
                $extension = $image->getClientOriginalExtension();
                $filestoreName = $filename . '.' . $extension;
                $filestoreoldName = $old_filename . '.' . $old_extension;
                $resize = array('150', '150');
                Helpers::mediauploadpath('admin', 'user', $image, $resize, $filestoreName, $filestoreoldName);
                $user->avatar_name = $filename;
                $user->avatar_ext = $extension;
                $user->save();
            }
            if (isset($request['password']) && trim($request['password']) != "") {
                $request['password'] = Hash::make($request['password']);
            } else {
                unset($request['password']);
            }
            unset($request['confirmpassword1']);
            if ($request['dob'] != '')
                $request['dob'] = date('Y-m-d', strtotime($request['dob']));
            $adminusers = Users::find($id);
            //$adminusers->update($request);
            if ($request['useraccess'] == 'app') {
                //$request['practice_user_type'] = "";
                $request['admin_practice_id'] = "";
                $adminusers->update($request);
            } else {
                $request['practice_access_id'] = 0;
                $request['facility_access_id'] = 0;
                if (($request['practice_user_type'] == 'practice_user' || $request['practice_user_type'] == 'customer') && $adminusers->practice_user_type == 'practice_admin') {
                    $request['admin_practice_id'] = '';
                    $adminusers->update($request);
                } elseif (($adminusers->practice_user_type == 'practice_user' || $adminusers->practice_user_type == 'customer') && $request['practice_user_type'] == 'practice_admin') {
                    $request['admin_practice_id'] = implode(',', $request['admin_practice_id']);
                    $adminusers->update($request);
                } else {
                    if (isset($request['admin_practice_id']))
                        $request['admin_practice_id'] = implode(',', $request['admin_practice_id']);
                    $adminusers->update($request);
                }
            }
           $exist_perm = Setpracticeforusers::where('user_id', $id)->pluck('practice_id')->all();
            if(isset($request['admin_practice_permission']))
            {                   
                if(count($exist_perm) <= count($request['admin_practice_permission'])){
                    $insert_ids = array_diff($request['admin_practice_permission'],$exist_perm);
                        $data['user_id'] = $id; 
                        $data['role_id'] = isset($request['pra_role_id']) ? $request['pra_role_id'] : 0;
                        foreach($insert_ids as $insert_id){
                            $data['practice_id'] = $insert_id;                                  
                            $setpractice = Setpracticeforusers::create($data); 
                            $user = Auth::user ()->id;
                            $setpractice->created_by = $user;
                            $setpractice->save();
                        }   
                }else{
                    $update_ids = array_diff($exist_perm,$request['admin_practice_permission']);
                    Setpracticeforusers::whereIn('practice_id', $update_ids)->where('user_id', $id)->delete();
                }           
            }else{
                Setpracticeforusers::whereIn('practice_id', $exist_perm)->where('user_id', $id)->delete();
            } 

            $user = Auth::user()->id;
            $adminusers->updated_by = $user;
            if (isset($request['password']) && $request['password'] != "")
                @$adminusers->password = $request['password'];
            $adminusers->name = $request['name'];
            $adminusers->remember_token = $request['_token'];
            $adminusers->save();

            /* Starts - General address flag update */
            $address_flag = array();
            $address_flag['type'] = 'adminuser';
            $address_flag['type_id'] = $adminusers->id;
            $address_flag['type_category'] = 'general_information';
            $address_flag['address2'] = $request['general_address1'];
            $address_flag['city'] = $request['general_city'];
            $address_flag['state'] = $request['general_state'];
            $address_flag['zip5'] = $request['general_zip5'];
            $address_flag['zip4'] = $request['general_zip4'];
            $address_flag['is_address_match'] = $request['general_is_address_match'];
            $address_flag['error_message'] = $request['general_error_message'];
            AddressFlag::checkAndInsertAddressFlag($address_flag);
            /* Ends - General address flag update */
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.update_msg"), 'data' => ''));
        }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDeleteApi($id)
	{
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        Users::Where('id', $id)->delete();
        Setpracticeforusers::where('user_id', $id)->delete();
        return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.delete_msg"), 'data' => ''));		
	}

}
