<?php

namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Roles as Roles;
use App\Models\Medcubics\Language as Language;
use App\Models\Medcubics\Ethnicity as Ethnicity;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\Facility as Facility;
use App\Models\Medcubics\Provider as Provider;
use App\Models\Medcubics\IPGroup as IPGroup;
use App\Models\Medcubics\IPUserGroup as IPUserGroup;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use Hash;
use Lang;
use Config;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use DB;
use Log;

class AdminuserApiController extends Controller {
    /*     * * Start to Listing the Admin User ** */

    public function getIndexApi($export = "") {
        //$adminusers = Users::with('user','userupdate','role')->where('user_type', 'Medcubics')->where('role_id', '<>', 1)->get();
        $adminusers = Users::with('practiceName')->get();        
        if ($export != "") {
            $exportparam = array(
                'filename' => 'Admin user',
                'heading' => 'Admin user list',
                'fields' => array(
                    'name' => 'Name',
                    'email' => 'Email',
                    'gender' => 'Gender',
                    'role' => array('table' => 'role', 'column' => 'role_name', 'label' => 'Role'),
                    'created_by' => array('table' => 'user', 'column' => 'short_name', 'label' => 'Created By'),
                    'updated_by' => array('table' => 'userupdate', 'column' => 'short_name', 'label' => 'Updated By'),
            ));
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $adminusers, $export);
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adminusers')));
    }

    /*     * * End to Listing the Admin User ** */

    /*     * * Start to Create the Admin User  ** */

    public function getCreateApi() {

        $adminusers = Users::all();
        $adminrolls = Roles::where('role_type', 'Medcubics')->where('id', '<>', 1)->pluck('role_name', 'id')->all();
        $practicerolls = Roles::where('role_type', 'Practice')->pluck('role_name', 'id')->all();
        $language = Language::orderBy('language', 'ASC')->where("language", "!=", "")->pluck('language', 'id')->all();
        $language_id = 5;
        $ethnicity = Ethnicity::orderBy('name', 'ASC')->pluck('name', 'id')->all();        
        $ip_group = IPGroup::orderBy('group_name', 'ASC')->pluck('group_name', 'group_name')->toArray();        
        $ethnicity_id = '';
		$ip_user_group = [];

        $addressFlag['general']['address1'] = '';
        $addressFlag['general']['city'] = '';
        $addressFlag['general']['state'] = '';
        $addressFlag['general']['zip5'] = '';
        $addressFlag['general']['zip4'] = '';
        $addressFlag['general']['is_address_match'] = '';
        $addressFlag['general']['error_message'] = '';

        $customers = Customer::pluck('customer_name', 'id')->all();
        $customer_practices = [];
        $customer_practices_list = "";
        $facility = "";
        $provider = "";

        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adminusers', 'adminrolls', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'addressFlag', 'customers', 'customer_practices', 'customer_practices_list', 'facility', 'provider', 'practicerolls','ip_group','ip_user_group')));
    }

    /*     * * End to Create the Admin User 	 ** */

    /*     * * Start to Store the Admin User 	 ** */

    public function getStoreApi($request = '') {
        if ($request == '')
            $request = Request::all();
        //dd($request);
        if (isset($request['admin_practice_id'])) {
            $request['admin_practice_id'] =  implode(',',$request['admin_practice_id']);
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
                $request['useraccess'] = "app";
                $request['app_name'] = "";
            }
            if ($request['app_name'] == "CHARGECAPTURE" && $request['useraccess'] == "app") {
                $request['facility_access_id'] = 0;
            } elseif($request['practice_user_type'] == 'provider') {
                // User type provider related changes
                // Rev.1 - Ref.MR-2662 - 09-08-2019 - Ravi                
                $request['provider_access_id'] = isset($request['provider_access_id']) ? $request['provider_access_id'] : 0;    
            } elseif ($request['app_name'] == "WEB" && $request['useraccess'] == "app") {
                $request['provider_access_id'] = 0;
            } else {
                $request['facility_access_id'] = $request['provider_access_id'] = $request['practice_access_id'] = 0;
            }
//            if ($request['useraccess'] == 'app') {
//                $request['practice_user_type'] = "";
//            }
            if ($request['useraccess'] == 'app') {
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
			
			if(isset($request['ip_group']) && !empty($request['ip_group'])){
				foreach($request['ip_group'] as $list){
					IPUserGroup::create(['user_id'=>$adminusers->id,'group_name'=>$list,'status'=>'Active']);
				}
			}
			
            @$customerusers->avatar_name = isset($request['avatar_name']) ? $request['avatar_name'] : '';
            @$customerusers->avatar_ext = isset($request['avatar_ext']) ? $request['avatar_ext'] : '';
            $user = Auth::user()->id;
            $adminusers->created_by = $user;
            $adminusers->name = $request['name'];
            $adminusers->password = $request['password'];
            $adminusers->remember_token = $request['_token'];
            $adminusers->save();
            $insertedId = $adminusers->id;
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
            $insertedId = Helpers::getEncodeAndDecodeOfId($insertedId, 'encode');
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $insertedId));
        }
    }

    /*     * * End to Store the Admin User 	 ** */

    /*     * * Start to Show the Admin User 	 ** */

    public function getShowApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Users::where('id', $id)->count()) {
            $adminusers = Users::with('language', 'ethnicity', 'user', 'userupdate', 'role', 'customer', 'practice')->where('id', $id)->first();
            $practicelist = Practice::pluck('practice_name', 'id')->all();
            $general_address_flag = AddressFlag::getAddressFlag('adminuser', $adminusers->id, 'general_information');
            $addressFlag['general'] = $general_address_flag;
            if($adminusers->practice_user_type == 'provider'){
                $customer_practices1 = str_replace(' ', '_', strtolower(Practice::where('id', $adminusers->admin_practice_id)->value('practice_name')));
            } else {
                $customer_practices1 = str_replace(' ', '_', strtolower(Practice::where('id', $adminusers->practice_access_id)->value('practice_name')));
            }
            $dbconnection = new DBConnectionController();
            $practice_db_name = $customer_practices1;
            $dbconnection->configureConnectionByName($practice_db_name);
            $facility = Facility::on($practice_db_name)->where('status', 'Active')->pluck('facility_name', 'id')->all();
            $provider = Provider::on($practice_db_name)->where('status', 'Active')->whereIn('provider_types_id', [Config::get('siteconfigs.providertype.Rendering')])->pluck('provider_name', 'id')->all();
            return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('adminusers', 'addressFlag', 'practicelist', 'facility', 'provider')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.not_found_msg"), 'data' => null));
        }
    }

    /*     * * End to Show the Admin User  ** */

    /*     * * Start to Edit the Admin User 	 ** */

    public function getEditApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $admin_db_name = getenv('DB_DATABASE');
        if (is_numeric($id) && Users::where('id', $id)->count()) {
            $adminusers = Users::find($id);
            if ($adminusers['dob'] != '0000-00-00') {
                $adminusers['dob'] = date('m/d/Y', strtotime($adminusers['dob']));
            } else {
                $adminusers['dob'] = '';
            }
            $adminrolls = Roles::where('role_type', 'Medcubics')->where('id', '<>', 1)->pluck('role_name', 'id')->all();
            $practicerolls = Roles::where('role_type', 'Practice')->pluck('role_name', 'id')->all();
            $language = Language::orderBy('language', 'asc')->where("language", "!=", "")->pluck('language', 'id')->all();
            $language_id = $adminusers->language_id;
            $ethnicity = Ethnicity::orderBy('name', 'asc')->pluck('name', 'id')->all();
            $ethnicity_id = $adminusers->ethnicity_id;
            $general_address_flag = AddressFlag::getAddressFlag('adminuser', $adminusers->id, 'general_information');
            $addressFlag['general'] = $general_address_flag;
			$ip_group = IPGroup::orderBy('group_name', 'ASC')->pluck('group_name', 'group_name')->toArray();     
			$ip_user_group = IPUserGroup::where('user_id', $id)->pluck('group_name', 'group_name')->toArray();
     
            $customers = Customer::pluck('customer_name', 'id')->all();
            $customer_practices = Practice::where('customer_id', $adminusers->customer_id)->pluck('practice_name', 'id')->all();
            $customer_practices_list = Practice::where('customer_id', $adminusers->customer_id)->pluck('practice_name', 'id')->all();
			// Based on user type provider list populated.
			// For provider type select use admin_practice_id field - Rev. 1 - Ravi - 19-08-2019 
            if($adminusers->practice_user_type == 'provider'){
                $customer_practices1 = str_replace(' ', '_', strtolower(Practice::where('id', $adminusers->admin_practice_id)->value('practice_name')));
            } else {
                $customer_practices1 = str_replace(' ', '_', strtolower(Practice::where('id', $adminusers->practice_access_id)->value('practice_name')));
            }
            
            $dbconnection = new DBConnectionController();
            $practice_db_name = $customer_practices1;
            $dbconnection->configureConnectionByName($practice_db_name);
            $facility = Facility::on($practice_db_name)->where('status', 'Active')->pluck('facility_name', 'id')->all();
            $provider = Provider::on($practice_db_name)->where('status', 'Active')->whereIn('provider_types_id', [Config::get('siteconfigs.providertype.Rendering')])->pluck('provider_name', 'id')->all();
            $dbconnection->configureConnectionByName($admin_db_name);

            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('adminrolls', 'adminusers', 'language', 'language_id', 'ethnicity', 'ethnicity_id', 'addressFlag', 'customers', 'customer_practices', 'customer_practices_list', 'facility', 'provider', 'practicerolls','ip_group','ip_user_group')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    /*     * * End to Edit the Admin User  ** */

    /*     * * Start to Update the Admin User  ** */

    public function getUpdateApi($id, $request = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ($request == '')
            $request = Request::all();
       if (isset($request['admin_practice_id'])) {
            $request['admin_practice_id'] =  implode(',',$request['admin_practice_id']);
        }
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
            } elseif($request['practice_user_type'] == 'provider') {
                // User type provider related changes
                // Rev.1 - Ref.MR-2662 - 09-08-2019 - Ravi                
                $request['provider_access_id'] = isset($request['provider_access_id']) ? $request['provider_access_id'] : 0;
            } elseif ($request['app_name'] == "WEB" && $request['useraccess'] == "app" ) {
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
         
            if($request['logged_setting'] == 'yes' && $adminusers->login_attempt > 0){
                $request['login_attempt'] = 0; 
            }         
            
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
                } elseif (($adminusers->practice_user_type == 'provider' ) && $request['practice_user_type'] == 'provider') {   
                    // User type provider related changes
                    // Rev.1 - Ref.MR-2662 - 09-08-2019 - Ravi 
                    $request['admin_practice_id'] = $request['admin_practice_id'];
                    $request['provider_access_id'] = isset($request['provider_access_id']) ? $request['provider_access_id'] : 0;
                    $adminusers->update($request);
                } else {
                    if (isset($request['admin_practice_id'])){
                        $request['admin_practice_id'] = (is_array($request['admin_practice_id'])) ? implode(',', $request['admin_practice_id']) : $request['admin_practice_id'];
                    }
                    $adminusers->update($request);
                }
            }
			
			if(isset($request['ip_group']) && !empty($request['ip_group'])){
				IPUserGroup::where('user_id',$id)->delete();
				foreach($request['ip_group'] as $list){
					IPUserGroup::create(['user_id'=>$id,'group_name'=>$list,'status'=>'Active']);
				}
			}else{
				IPUserGroup::where('user_id',$id)->delete();
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

    /*     * * End to Update the Admin User  ** */

    /*     * * Start to Destory the Admin User  ** */

    public function getDeleteApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        Users::Where('id', $id)->delete();
        return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.delete_msg"), 'data' => ''));
    }

    /*     * * End to Destory the Admin User 	 ** */

    /* login attempt update in users */

    public function updateLoginAttempt() {
        $request = Request::all();
        if ($request['id'] != "") {
            $id = (!is_numeric(($request['id']))) ? Helpers::getEncodeAndDecodeOfId($request['id'], 'decode') : $request['id'];
            $login_attempt = Users::where('id', $id)->update(['login_attempt' => 0]);
            return Response::json(array('status' => 'success', 'message' => $id));
        } else {
            return Response::json(array('status' => 'error', 'message' => $id));
        }
    }

    /* is_logged_in update logout user */

    public function logoutUser() {
        $request = Request::all();
        if ($request['id'] != "") {
            $id = (!is_numeric(($request['id']))) ? Helpers::getEncodeAndDecodeOfId($request['id'], 'decode') : $request['id'];
            $updatelogoutUser = Users::where('id', $id)->update(['is_logged_in' => 0]);
            return Response::json(array('status' => 'success', 'message' => $id));
        } else {
            return Response::json(array('status' => 'error', 'message' => $id));
        }
    }

    /* active or inactive status update in users table */

    public function updateUserStatus() {
        $request = Request::all();
        if ($request['id'] != "") {
            $id = (!is_numeric(($request['id']))) ? Helpers::getEncodeAndDecodeOfId($request['id'], 'decode') : $request['id'];
            $user_status = Users::where('id', $id)->pluck('status')->all();
            if ($user_status == 'Active')
                $update_status = Users::where('id', $id)->update(['status' => 'Inactive']);
            else
                $update_status = Users::where('id', $id)->update(['status' => 'Active']);
            return Response::json(array('status' => 'success', 'message' => $id));
        } else {
            return Response::json(array('status' => 'error', 'message' => $id));
        }
    }

    /* customer related practice lists in user create */

    /*  public function customerPractice($id) {
        $customer_practices = Practice::where('customer_id', $id)->pluck('practice_name', 'id')->all();

        $customer_practice = strtolower(Practice::where('customer_id', $id)->pluck('practice_name'));
        $dbconnection = new DBConnectionController();
        $practice_db_name = $customer_practice;
        $practice_db_name = $dbconnection->getpracticedbname($practice_db_name);
        $dbconnection->configureConnectionByName($practice_db_name);
        $facility = Facility::on($practice_db_name)->where('status', 'Active')->lists('facility_name', 'id');
        $provider = Provider::on($practice_db_name)->where('status', 'Active')->whereIn('provider_types_id', [Config::get('siteconfigs.providertype.Rendering')])->lists('provider_name', 'id');
        return Response::json(compact('customer_practices', 'facility', 'provider'));
    }
    */

    ## Admin:provider user id mapping Thilagavathy Start
    public function customerPractice($id) {
        $customer_practices = Practice::where('customer_id', $id)->pluck('practice_name', 'id')->all();
        return Response::json(compact('customer_practices'));
    }

    public function PracticeProviders($id) {   
        $customer_practice = strtolower(Practice::where('id', $id)->pluck('practice_name')->first());
        $dbconnection = new DBConnectionController();
        $practice_db_name = $customer_practice; 
        $practice_db_name = $dbconnection->getpracticedbname($practice_db_name);
        $dbconnection->configureConnectionByName($practice_db_name);
        $facility = Facility::on($practice_db_name)->where('status', 'Active')->pluck('facility_name', 'id')->all();
        $provider = Provider::on($practice_db_name)->where('status', 'Active')->whereIn('provider_types_id', [Config::get('siteconfigs.providertype.Rendering')])->pluck('provider_name', 'id')->all();
        return Response::json(compact( 'facility', 'provider'));
    }
    
    ### Admin:provider user id mapping. Thilagavathy End
    public function userEmailValidate() {
        $request = Request::all();
        if(!isset($request['user_id']) && empty($request['user_id'])) {
            $user = Users::where('email', $request['email'])->first();
            if($user == null) {
                $valid = "true";
            } else {
                $valid = "false";
            }
            return json_encode(array('valid' => $valid));
        } else {
            $id = Helpers::getEncodeAndDecodeOfId($request['user_id'], 'decode');
            $users = Users::where('id', '!=', $id)->where('email', $request['email'])->get();
            $count = count($users);
            if($count == 0) {
                $valid = "true";
            } else {
                $valid = "false";
            }
            return json_encode(array('valid' => $valid, 'request' => $request));
        }
    }
	
	public function practiceNameValidate() {
        $request = Request::all();
        if(isset($request['practice_name']) && !empty($request['practice_name'])) {
            $practice = Practice::where('practice_name', $request['practice_name'])->first();
            if($practice == null) {
                $valid = "true";
            } else {
                $valid = "false";
            }
            return json_encode(array('valid' => $valid));
        }
    }

    public function userShortNameValidate() {
        $request = Request::all();
        if($request['user_id'] == "") {
            $user = Users::where('short_name', $request['short_name'])->first();
            if($user == null) {
                $valid = "true";
            } else {
                $valid = "false";
            }
            return json_encode(array('valid' => $valid));
        } else {
            $id = Helpers::getEncodeAndDecodeOfId($request['user_id'], 'decode');
            $users = Users::where('id', '!=', $id)->where('short_name', $request['short_name'])->get();
            $count = count($users);
            if($count == 0) {
                $valid = "true";
            } else {
                $valid = "false";
            }
            return json_encode(array('valid' => $valid, 'request' => $request));
        }
    }

	/*
     * This Function For Security Code Updation
     * Author		: Kriti Srivastava
     * Created on	: 30July2021
	 * JIRA Id		: MED3-8
     */
	public function updateSecurityCode() {	
	
        $request = Request::all();
        if ($request['id'] != "") {
            $id = (!is_numeric(($request['id']))) ? Helpers::getEncodeAndDecodeOfId($request['id'], 'decode') : $request['id'];
			$security_code = Users::where('id', $id)->select('id','security_code')->get()->first();
            if ($security_code->security_code == 'Yes'){
                $update_security_code = Users::where('id', $id)->update(['security_code' => 'No']);
			}else{
                $update_security_code = Users::where('id', $id)->update(['security_code' => 'Yes']);
			}
			return Response::json(array('status' => 'success', 'message' => $id));
       
      }
	}

    function __destruct() {
        
    }

}