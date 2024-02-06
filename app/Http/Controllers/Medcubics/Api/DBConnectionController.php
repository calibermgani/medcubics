<?php

namespace App\Http\Controllers\Medcubics\Api;

use File;
use Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;
use Request;
use Validator;
use Input;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\NpiFlag as NpiFlag;
use App\Models\PracticeApiList as PracticeApiList;
use App\Models\AddressFlag as AddressFlag;
use App\Models\Provider as Provider;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\PagePermissions as PagePermissions;
use App\Models\Medcubics\AdminPagePermissions as AdminPagePermissions;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Medcubics\Users as Users;
use DB;
use Auth;
use Config;
use Artisan;
use Session;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Seeder;
use Nwidart\DbExporter\DbExportHandler as DbExportHandler;
use Nwidart\DbExporter\DbSeeding as DbSeeding;
use Redirect;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Facades\Storage;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Dbmigrations as Dbmigrations;
use App\Models\Medcubics\Setapiforusers as Setapiforusers;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\Support\Ticket as Ticket;
use App\Models\EdiEligibility as EdiEligibility;
use App\Models\Medcubics\Managefile as Managefile;
use App\Models\Payments\ClaimInfoV1;
use App\Http\Controllers\Api\StatsApiController as StatsApiController;
use URL;
use Cache;
use Exception;
use App\Traits\ClaimUtil;
use Log;
use Carbon\Carbon;

class DBConnectionController extends Controller {
	use ClaimUtil;
    public $practice_ignore_tables = [];
    public $admin_db = '';

    public function __construct() {
        $this->admin_db = env('DB_DATABASE');
    }

    public function setSessionforDB($id) {
        Session::put('practice_dbid', $id);
    }

    public function clearDBSession() {
        Session::forget('timezone');
        Session::forget('practice_dbid');
        //$url = Request::url();		
        //Redirect::to($url);
    }

    public function configureConnectionByName($tenantName) {
        // Just get access to the config. 
        $config = App::make('config');

        // Will contain the array of connections that appear in our database config file.
        $connections = $config->get('database.connections');

        // This line pulls out the default connection by key (by default it's `mysql`)
        $defaultConnection = $connections[$config->get('database.default')];

        // Now we simply copy the default connection information to our new connection.
        $newConnection = $defaultConnection;
        // Override the database name.
        $newConnection['database'] = $tenantName;

        // This will add our new connection to the run-time configuration for the duration of the request.
        App::make('config')->set('database.connections.' . $tenantName, $newConnection);
    }

    public function connectPracticeDB($practice_id) {
        $practice_info = Practice::where('id', $practice_id)->select('practice_name')->first();
        //$practice_name = ($practice_info ==null || $practice_info =='') ? env('DB_DATABASE'): $practice_info->practice_name;
        $practice_name = ($practice_info == null || $practice_info == '') ? $this->admin_db : $practice_info->practice_name;
        $practice_db_name = $this->getpracticedbname($practice_name);
        /// Need to remove this line after completed practice db
        if (!config('siteconfigs.is_enable_provider_add')) {
            $practice_db_name = config('siteconfigs.connection_database');
            // print_r("connect");
        }
            // print_r("disconnect");
        DB::disconnect();
        $this->configureConnectionByName($practice_db_name);
        Config::set('database.default', $practice_db_name);
    }

    public function disconnectPracticeDB() {
        DB::reconnect();
    }

    public function createSchema($schemaName) {
        try {
            \Log::info("Create schema called" . $schemaName . "##" . $this->admin_db);

            $tenantDB = $schemaName;
            $adminDB = ($this->admin_db != '' ) ? $this->admin_db : config('siteconfigs.connection_database');  //env('DB_DATABASE');
            if ($adminDB == '') {
                \Log::info("Database name empty error occured");
                throw new Exception("Error Processing Request", 1);
            }

            $dbmig_obj = new Dbmigrations($adminDB);
            $dbseed_obj = new DbSeeding($adminDB);
            $dbex = new DbExportHandler($dbmig_obj, $dbseed_obj);

            // Unlink existing migration files if exists
            $migration_files = glob(base_path() . "/database/migrations/practicemigration/*.php");
            foreach ($migration_files as $migratefiles) {
                @unlink($migratefiles);
            }
            \Log::info("Pre migrations removed");

            $this->practice_ignore_tables = ['adminpage_permission', 'clearing_house', 'customernotes', 'customers',
                'page_permissions', 'password_resets', 'roles', 'setapiforusers', 'setpracticeforusers', 'set_adminpagepermissions', 'set_pagepermissions', 'users', 'practice_api_configs', 'adminpage_permission', 'sessions', 'maintenance_sqls','report_export_task','patient_eligibility_waystar','ip_user_group','ip_group','users_verification'];
            \Log::info("Before Migrate ");    
            $dbex->ignore($this->practice_ignore_tables)->migrate();
            \Log::info("Migrate Created");
            //$dbex->ignore('practices')->seed();
            //$dbex->seed();		

            $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
            $db = DB::select($query, [$tenantDB]);
            if (empty($db)) {
                DB::connection()->statement("CREATE DATABASE " . $tenantDB);
                \Log::info("Db not exists. Create DB called");

                try {
                    $this->configureConnectionByName($tenantDB);
                    define('STDIN', fopen("php://stdin", "r"));
                    Artisan::call('migrate', array('--database' => $tenantDB, '--path' => 'database/migrations/practicemigration', '--force' => true));
                    \Log::info("Artisan migrate called");

                    $this->doseedingcron($adminDB, $tenantDB);
                    \Log::info("Do seed finished");
                } catch (Exception $e) {
                    DB::connection()->statement("DROP DATABASE " . $tenantDB);
                    \Log::info("Error on seed, error: " . $e->getMessage() . " Drop database called " . $tenantDB);
                    $admin_database = $this->admin_db; // env('DB_DATABASE');
                    \Log::info("configure into admin database: " . $admin_database);
                    $this->configureConnectionByName($adminDB);

                    throw new Exception("Error Processing Request. Please try again later (or) contact site admin!!!", 1);
                }
            } else {
                $errMSg = 'Database already exists! Please contact site admin!!!';
                \Log::info("Database already exist exception occured.");
                throw new \Exception($errMSg);
            }
        } catch (InvalidDatabaseException $e) {
            \Log::info("Invalid database exception occured. Error: " . $e->getMessage());
            throw new Exception("Error Processing Request. Please try again later (or) contact site admin!!!", 1);
        } catch (Exception $e) {
            $errorMsg = 'Error on line '.$e->getLine().' in '.$e->getFile().': <b>'.$e->getMessage();    
            \Log::info("Error: " . $e->getMessage()."##".$errorMsg);
            throw new Exception("Error Processing Request. Please try again later (or) contact site admin!!!", 1);
        }
        return true;
    }

    public function createmigrationfile() {
        //Config::set('db-exporter::export_path.migrations', base_path().'/database/migrations/');
        //$dbmig_obj = new DbMigrations(env('DB_DATABASE'));
        $dbmig_obj = new Dbmigrations($this->admin_db);  //dd($dbmig_obj);
        //$dbseed_obj = new DbSeeding(env('DB_DATABASE'));
        $dbseed_obj = new DbSeeding($this->admin_db);
        $dbex = new DbExportHandler($dbmig_obj, $dbseed_obj);
        $dbex->ignore('icd_09')->migratenew();
        //$dbex->ignore('icd_10')->seed();
    }

    /* the below function is only to test seeding functionality */
    /* public function createSchema($schemaName)
      {
      $this->configureConnectionByName($schemaName);
      $admin_database = env('DB_DATABASE');
      $this->doseedingcron($admin_database);
      return true;
      } */

    public function getpracticedbname($practice_name) {
        $practice_name = str_replace(' ', '_', $practice_name);
        $practice_name = str_replace(',', '', $practice_name);
        preg_replace('/[^A-Za-z0-9\-]/', '', $practice_name);
        return strtolower($practice_name);
    }

    public function createProviderinOtherDB($request, $practice_db_name, $data = null) {
        $practice_db_name = ($practice_db_name != '') ? $practice_db_name : $this->admin_db ;
        if($practice_db_name != '') {
            \Log::info("Create Pro in other DB"); \Log::info($request); \Log::info("Other DB:".$practice_db_name);
            $this->configureConnectionByName($practice_db_name);
            $result = new Provider;
            $result->setConnection($practice_db_name);
            $result->fill($request);
            $result->save();
            if (Input::hasFile('image')) {
                $result->avatar_name = $data['avatar_name'];
                $result->avatar_ext = $data['avatar_ext'];
                $result->save();
            }
            if (Input::hasFile('digital_sign')) {
                $result->digital_sign_name = $data['digital_sign_name'];
                $result->digital_sign_ext = $data['digital_sign_ext'];
                $result->save();
            }
            /// Starts - address flag update ///				
            $address_flag = array();
            $address_flag['type'] = 'provider';
            $address_flag['type_id'] = $result->id;
            $address_flag['type_category'] = 'general_information';
            $address_flag['address2'] = $request['general_address1'];
            $address_flag['city'] = $request['general_city'];
            $address_flag['state'] = $request['general_state'];
            $address_flag['zip5'] = $request['general_zip5'];
            $address_flag['zip4'] = $request['general_zip4'];
            $address_flag['is_address_match'] = $request['general_is_address_match'];
            $address_flag['error_message'] = $request['general_error_message'];
            AddressFlag::checkAndInsertAddressFlag($address_flag, $practice_db_name);
            /* Ends - address flag update  */

            /* Starts - NPI flag update */
            $request['company_name'] = 'npi';
            $request['type'] = 'provider';
            $request['type_id'] = $result->id;
            $request['type_category'] = 'Individual';
            //NpiFlag::checkAndInsertNpiFlag($request,$practice_db_name);		

            $npi_flag_array = NpiFlag::on($practice_db_name)->where('type', $request['type'])->where('type_id', $request['type_id'])->where('type_category', $request['type_category'])->first();

            if (!$npi_flag_array) {
                /* $this->connectPracticeDB($request['practice_id']);
                  NpiFlag::create($request);
                  $this->disconnectPracticeDB(); */
                $npi_result = new NpiFlag;
                $npi_result->setConnection($practice_db_name);
                $npi_result->fill($request);
                $npi_result->save();
            } else {
                //$npi_flag_array->update($request);
                $npi_flag_array->setConnection($practice_db_name)->update($request);
            }

            /* Ends - NPI flag update */
            return $result->id;
        }
    }

    public function updatepracticedbprovider($request, $id, $practice_db_name, $data_val = null) {
        $this->configureConnectionByName($practice_db_name);
        $provider = Provider::on($practice_db_name)->where('practice_db_provider_id', $id)->first();
        if(!empty($provider) && isset($provider->id)) {
            if (Input::hasFile('image')) {
                $provider->avatar_name = $data_val['avatar_name'];
                $provider->avatar_ext = $data_val['avatar_ext'];
            }

            if (Input::hasFile('digital_sign')) {
                $provider->digital_sign_name = $data_val['digital_sign_name'];
                $provider->digital_sign_ext = $data_val['digital_sign_ext'];
            }

            /// Starts - Pay to address flag update ///
            $address_flag = array();
            $address_flag['type'] = 'provider';
            $address_flag['type_id'] = $provider->id;
            $address_flag['type_category'] = 'general_information';
            $address_flag['address2'] = $request['general_address1'];
            $address_flag['city'] = $request['general_city'];
            $address_flag['state'] = $request['general_state'];
            $address_flag['zip5'] = $request['general_zip5'];
            $address_flag['zip4'] = $request['general_zip4'];
            $address_flag['is_address_match'] = $request['general_is_address_match'];
            $address_flag['error_message'] = $request['general_error_message'];
            AddressFlag::checkAndInsertAddressFlag($address_flag, $practice_db_name);
            /* Ends - Pay to address */

            /* Starts - NPI flag update */
            $request['company_name'] = 'npi';
            $request['type'] = 'provider';
            $request['type_id'] = $provider->id;
            $request['type_category'] = 'Individual';

            //NpiFlag::checkAndInsertNpiFlag($request,$practice_db_name);		
            $npi_flag_array = NpiFlag::on($practice_db_name)->where('type', $request['type'])->where('type_id', $request['type_id'])->where('type_category', $request['type_category'])->first();

            if (!$npi_flag_array) {
                /* $this->connectPracticeDB($request['practice_id']);
                  NpiFlag::create($request);
                  $this->disconnectPracticeDB(); */
                $npi_result = new NpiFlag;
                $npi_result->setConnection($practice_db_name);
                $npi_result->fill($request);
                $npi_result->save();
            } else {
                //$npi_flag_array->update($request);
                $npi_flag_array->setConnection($practice_db_name)->update($request);
            }
            /* Ends - NPI flag update */
            $provider->setConnection($practice_db_name)->update($request);
        }
    }

    public function updatepracticeInfoinAdminDB($request, $id, $admin_db_name, $filename = null, $extension = null) {
        $this->configureConnectionByName($admin_db_name);
        $practice = Practice::on($admin_db_name)->where('practice_db_id', $id)->first();

        if (Input::hasFile('image')) {
            $practice->avatar_name = $filename;
            $practice->avatar_ext = $extension;
        }
        $practice->setConnection($admin_db_name)->update($request);

        /// Starts - Pay to address flag update ///
        $address_flag = array();
        $address_flag['type'] = 'practice';
        $address_flag['type_id'] = $practice->id;
        $address_flag['type_category'] = 'pay_to_address';
        $address_flag['address2'] = $request['pta_address1'];
        $address_flag['city'] = $request['pta_city'];
        $address_flag['state'] = $request['pta_state'];
        $address_flag['zip5'] = $request['pta_zip5'];
        $address_flag['zip4'] = $request['pta_zip4'];
        $address_flag['is_address_match'] = $request['pta_is_address_match'];
        $address_flag['error_message'] = $request['pta_error_message'];
        AddressFlag::checkAndInsertAddressFlag($address_flag, $admin_db_name);
        /* Ends - Pay to address */

        /// Starts - Mailling address flag update ///
        $address_flag = array();
        $address_flag['type'] = 'practice';
        $address_flag['type_id'] = $practice->id;
        $address_flag['type_category'] = 'mailling_address';
        $address_flag['address2'] = $request['ma_address1'];
        $address_flag['city'] = $request['ma_city'];
        $address_flag['state'] = $request['ma_state'];
        $address_flag['zip5'] = $request['ma_zip5'];
        $address_flag['zip4'] = $request['ma_zip4'];
        $address_flag['is_address_match'] = $request['ma_is_address_match'];
        $address_flag['error_message'] = $request['ma_error_message'];
        AddressFlag::checkAndInsertAddressFlag($address_flag, $admin_db_name);
        /* Ends - Mailling address */

        /// Starts - Primary address flag update ///
        $address_flag = array();
        $address_flag['type'] = 'practice';
        $address_flag['type_id'] = $practice->id;
        $address_flag['type_category'] = 'primary_address';
        $address_flag['address2'] = $request['pa_address1'];
        $address_flag['city'] = $request['pa_city'];
        $address_flag['state'] = $request['pa_state'];
        $address_flag['zip5'] = $request['pa_zip5'];
        $address_flag['zip4'] = $request['pa_zip4'];
        $address_flag['is_address_match'] = $request['pa_is_address_match'];
        $address_flag['error_message'] = $request['pa_error_message'];
        AddressFlag::checkAndInsertAddressFlag($address_flag, $admin_db_name);
        /* Ends - Primary address */

        /* Starts - NPI flag update */
        $request['company_name'] = 'npi';
        $request['type'] = 'practice';
        $request['type_id'] = $practice->id;
        if ($request['entity_type'] == 'Group')
            $request['type_category'] = 'Group';
        else
            $request['type_category'] = 'Individual';
        NpiFlag::checkAndInsertNpiFlag($request, $admin_db_name);
    }

    public function updateApiInfoinPracticeDB($request, $practiceid, $dbname, $action, $oldapi = null) {
        $this->configureConnectionByName($dbname);
        $practice = Practice::on($dbname)->where('practice_db_id', $practiceid)->first();
        $practice->setConnection($dbname)->update($request);
        if ($action == 'add') {
            if (!empty($request['apilist'])) {
                foreach ($request['apilist'] as $key => $val) {
                    DB::connection($dbname)->insert("insert into `practice_api_list` (`api_id`,`created_at`,`updated_at`) values (" . $val . ", '" . date('Y-m-d h:i:s') . "', '" . date('Y-m-d h:i:s') . "')");
                }
            }
            $this->create_APIJSON($practiceid, 'new');
        }
        if ($action == 'update') {
            $this->create_APIJSON($practiceid);
            if (empty($request['apilist'])) {
                DB::connection($dbname)->table('practice_api_list')->truncate();
                Setapiforusers::where('practice_id', $practiceid)->delete();
            } else {
                $old_api = explode(",", $oldapi);
                $new_api = $request['apilist'];
                $current_date = date('Y-m-d h:i:s');
                $remove_api = array_diff($old_api, $new_api);
                $add_api = array_diff($new_api, $old_api);

                if (count($remove_api) > 0) {
                    foreach ($remove_api as $val) {
                        DB::connection($dbname)->table('practice_api_list')->where('api_id', '=', $val)->update(['deleted_at' => $current_date]);
                        Setapiforusers::where('practice_id', $practiceid)->where('api_id', $val)->delete();
                    }
                }

                if (count($add_api) > 0) {
                    foreach ($add_api as $val) {
                        DB::connection($dbname)->insert("insert into `practice_api_list` (`api_id`,`created_at`,`updated_at`) values (" . $val . ", '" . date('Y-m-d h:i:s') . "', '" . date('Y-m-d h:i:s') . "')");
                    }
                }
            }
        }
    }

    public function updatepracticedbproviderid($provider_id) {
        $provider = Provider::find($provider_id);
        if (!empty($provider)) {
            $provider->practice_db_provider_id = $provider_id;
            $provider->save();
        }
    }

    public function updatePracticeDBID($practice_id) {

        $practice = Practice::find($practice_id);
        if (!empty($practice)) {
            $practice->practice_db_id = $practice_id;
			if(Auth::check())
				$practice->created_by = Auth::user()->id;
			else
				$practice->created_by = '';
            $practice->save();
        }

    }

    public function deleteotherpractices($current_practice_id, $practice_db_name) {
        $this->configureConnectionByName($practice_db_name);
        $practice = new Practice;
        $practice->setConnection($practice_db_name);
        $practice->whereNotIn('id', [$current_practice_id])->forceDelete();
    }

    /**
    * Check is allow to access based on the user privilage
    * UT: 'Practice','Medcubics'  /  PUT: 'customer','practice_admin','practice_user'
    * Allow => UT: Medcubics / UT: Practice && PUT: customer
    * Dont Allow: UT: Practice && PUT: practice_user
    * Return Boolean 
    */
    public function checkAllowToAccess($module='') {        
        $CheckResp = true;
        $user = Auth::user();
        $module = strtolower(trim($module));
        if($module != '') {

            switch ($module) {
                case 'practice':
                    if(($user->user_type == 'Medcubics') || 
                        ($user->user_type == 'Practice' && $user->practice_user_type == 'customer') ||
                        ($user->user_type == 'Practice' && $user->practice_user_type == 'practice_admin')   ){
                        $CheckResp = true;
                    } elseif($user->user_type == 'Practice' && $user->practice_user_type == 'practice_user') {
                        $CheckResp = false;
                    } elseif($user->user_type == 'Practice' && $user->practice_user_type == 'provider') {
                        $CheckResp = false;
                    }
                    break;
                
                default:
                    $CheckResp = true;
                    break;
            }
        }
        return $CheckResp;
    }

    public function check_url_permission($url, $type = 'url') {
        try{
            // For guest user return false.
            if(!isset(Auth::user()->id))
                return false;

            $accessid = (isset(Auth::user()->id)) ? Auth::user()->id : 0;            
            $resp = 1;
            if (Cache::has('access_permission'.$accessid)){
                $permissions = Cache::get('access_permission'. $accessid);                
                if($type == 'url') {
                    if((isset($permissions[$url]) && $permissions[$url])) {
                        $resp = 1;
                    }
                }
                return $resp;
            } else {
                //\Log::info("Permission not added in cache");
                return true;
            }
        } catch(Exception $e) {
            \Log::info("While checking permission error occured. Error:". $e->getMessage() );
            return true;
        }
    }

    public function check_url_permission_old($current_url) {
        try{
            // For guest user return false.
            if(!isset(Auth::user()->id))
                return false;

            $user_id = Auth::user()->id;
            $config = App::make('config');
            $user = Auth::user();

            // Browse practice by medcubics user.
            $check_url = array();
            if (Auth::user()->user_type == 'Medcubics' && $user_id != 1) {
                $check_url = $this->get_admin_urls($current_url);
            }

            $practice_id = Session::get('practice_dbid');
            $admin_db_name = $this->admin_db; // getenv('DB_DATABASE'); 
            $this->configureConnectionByName($admin_db_name);
            if ($user->practice_user_type == 'practice_admin') {
                $getpractice = explode(",", $user->admin_practice_id);
            }

            if ($user->practice_user_type == 'customer') {
                $customer_id = Auth::user()->customer_id;
                $practice_ids = Practices::on($admin_db_name)->select('id')->where('customer_id', $customer_id)->where('status', 'Active')->pluck('id')->all();
            }

            if (($user->user_type == 'Medcubics' && $user->role_id == 1) || ($user->practice_user_type == 'practice_admin' && in_array($practice_id, $getpractice)) || ($user->practice_user_type == 'customer' && in_array($practice_id, $practice_ids)) || $config->get('siteconfigs.pagepermission.practice') == '' || (in_array('admin/customer/setpractice/{id}', $check_url) == 1 && Session::get('practice_dbid') != '')) {
                return true;
            }

            if (Session::get('practice_dbid') == '')
                $login_users_permissions = Setpracticeforusers::where('user_id', $user_id)->first();
            else
                $login_users_permissions = Setpracticeforusers::where('practice_id', $practice_id)->where('user_id', $user_id)->first();

            //  $login_users_permissions = 1;

            $allowed_url = array();
            if ($login_users_permissions == '') {
                $practices = "";
                if ($current_url == '/')
                    return view('practice/practice/practice', compact('practices'));
            }
            else {
                $page_permission_ids = $login_users_permissions->page_permission_ids;
                $page_permission_ids_arr = explode(",", $page_permission_ids);
                $page_permissions_info = PagePermissions::whereIn('id', $page_permission_ids_arr)->pluck('title_url')->all();
                foreach ($page_permissions_info as $page_url) {
                    if (strpos($page_url, ',') !== false) {
                        $page_permissions_arr = explode(',', $page_url);
                        foreach ($page_permissions_arr as $key => $val) {
                            $allowed_url[] = $page_permissions_arr[$key];
                        }
                    } else {
                        $allowed_url[] = $page_url;
                    }
                }
            }
            //echo $current_url;
            //dd($allowed_url);
            $allowed_url[] = '/';
            $this->disconnectPracticeDB();
            $allowedAPIurl = ['searchcpt', 'searchicd', 'advanced/keywordsearch', 'api/addresscheck', 'api/npicheck', 'patients/checkEligibility', 'api/get_superbill_search_icd_cpt_list', 'patients/getEligibility', 'stats/listchange/{data}'];
            if ((in_array($current_url, $allowed_url)) || (in_array($current_url, $allowedAPIurl)) || stripos($current_url, 'setpractice') > 1) {
                return true;
            } else {
                return true;
            }
        } catch(Exception $e){			
            \Log::info("Exception Occurred. Msg: ".$e->getMessage()."##".$e->getLine() ."##".$e->getFile());
			return false;
        }
    }

    // Collect all admin urls
    public function get_admin_urls($current_url) {
        $user_id = Auth::user()->id;

        if (Auth::user()->user_type == 'Medcubics') {
            $admin_db_name = $this->admin_db; //getenv('DB_DATABASE'); 
            $this->configureConnectionByName($admin_db_name);
            $login_users_permissions = Users::on($admin_db_name)->with('SetAdminPagePermissions')->where('id', $user_id)->first();

            if ($login_users_permissions->role_id == 0) {
                $customers = "";
                $tabs = "";
                if ($current_url == '/admin')
                    return view('admin/customer/customerlist', compact('customers', 'tabs'));
            }

            $page_permission_ids = @$login_users_permissions->SetAdminPagePermissions->page_permission_id;
            $page_permission_ids_arr = explode(",", $page_permission_ids);

            $allowed_url = array();
            for ($i = 0; $i < sizeof($page_permission_ids_arr); $i++) {
                $page_permissions_info = AdminPagePermissions::on($admin_db_name)->where('id', $page_permission_ids_arr[$i])->first();
                if (strpos($page_permissions_info['title_url'], ',') !== false) {
                    $page_permissions_arr = explode(',', $page_permissions_info['title_url']);
                    foreach ($page_permissions_arr as $key => $val) {
                        $allowed_url[] = $page_permissions_arr[$key];
                    }
                } else {
                    $allowed_url[] = $page_permissions_info['title_url'];
                }
            }
            return $allowed_url;
        } else {
            $allowed_url = array();
            return $allowed_url;
        }
    }

    public function check_adminurl_permission($current_url) {
        if(!isset(Auth::user()->id))
            return false;
        $user_id = Auth::user()->id;
        $config = App::make('config');
        $user = Auth::user();

        if (($user->user_type == 'Medcubics' && $user->role_id == 1) || ($config->get('siteconfigs.pagepermission.medcubics') == '') || ($user->user_type == 'Medcubics' && $current_url == 'admin/dashboard') || $current_url == '' || $current_url == 'help/{type}') {
            return true;
        }

        // Get admin urls
        $allowed_url = $this->get_admin_urls($current_url);

        // If yes, then go to practice by admin user.
        if (in_array('admin/customer/setpractice/{id}', $allowed_url) == 1 && Session::get('practice_dbid') != '') {
            return true;
        }

        $allowed_url[] = '/';
        $this->disconnectPracticeDB();

        $allowedAPIurl = ['api/addresscheck', 'api/npicheck'];

        if (in_array($current_url, $allowed_url) or in_array($current_url, $allowedAPIurl)) {
            return true;
        } else {
            return false;
        }
        // return true;
    }

    public function check_customer_url_permission($practice_id) {
        //checking with practices whether this customer is belongs to this practice or not
        $admin_db_name = (!empty($this->admin_db)) ? $this->admin_db : getenv('DB_DATABASE'); //getenv('DB_DATABASE');
        $this->configureConnectionByName($admin_db_name);
        $customer_id = Auth::user()->customer_id;
        $practice = Practice::on($admin_db_name)->where('id', $practice_id)->where('customer_id', $customer_id)->first();
        if (!is_null($practice)) {
            return true;
        } else {
            return false;
        }
    }

    public function check_url_practice_permission($practice_id, $current_url) {        
        if (Auth::check()) {
            $admin_db_name = $this->admin_db; // getenv('DB_DATABASE');
            $this->configureConnectionByName($admin_db_name);
            $admin_practice = (Auth::user()) ? Auth::user()->admin_practice_id : 0;
            // This is commented by revathi on April 13 2016 reason: If we choose more than one practice for practiceadmin for a user it did not worked
            /* if(stripos($admin_practice, ',') >1)
              {
              echo "if";
              $admin_practice = explode(',', $admin_practice);
              }
              else
              {
              echo "else";
              $admin_practice  = array($admin_practice);
              } */
            $admin_practice = explode(',', $admin_practice);
            if (in_array($practice_id, $admin_practice, true)) {
                return true;
            } else {
                return $this->check_url_permission($current_url);
            }
        } else {
            return false;
        }
    }

    public function getpracticelist($database_name) {
        $this->configureConnectionByName($database_name);
        $list = DB::connection($database_name)->select("select id,customer_id,practice_name from practices where status='In Progress' order by id DESC limit 1");
        return $list;
    }

    public function doseedingcron($adminDB, $tenantDB) {
        try {
            $practice_lists = $this->getpracticelist($adminDB); // Get practices list from admin database

            $tables = DB::connection($adminDB)->select('SHOW TABLES'); // Get default tables from 'responsive';

            $ignore_tables = $this->practice_ignore_tables + array('api_list', 'setapiforusers', 'icd_09', 'adminpage_permission', 'customernotes', 'customerusers', 'employers', 'facilities', 'facilityaddresses', 'facilitymanagecares', 'favouritecpts', 'feeschedules', 'insuranceoverrides', 'insuranceoverrides', 'patients', 'patient_contacts', 'patient_eligibility', 'patient_insurance', 'patient_authorizations', 'practiceoverrides', 'users', 'customers', 'page_permissions', 'setpracticeforusers', 'set_adminpagepermissions', 'set_pagepermissions', 'icd_10', 'cpts', 'useractivity', 'clearing_house', 'faqs', 'icd_10_old', 'icdcategory', 'icdmodifier', 'practice_api_configs', 'users_app_details', 'user_login_histories', 'pmt_info_v1', 'claim_info_v1', 'claim_cpt_info_v1', 'payment_transaction_histories', 'addressflag', 'adjustment_reasons', 'claim_ambulance_billings', 'claim_other_details', 'clearing_house', 'assign_tickethistory', 'blog', 'blogcomments_vote', 'blogreplycomments_vote', 'blog_comments', 'blog_comments_favourite', 'blog_comments_reply', 'blog_favourite', 'blog_group', 'blog_url', 'blog_vote', 'documents', 'reason_for_visits', 'migrations', 'insurances', 'sessions', 'cheatsheets', 'check_returns', 'edi_eligibility', 'edi_eligibility_contact_details', 'edi_eligibility_demo', 'edi_eligibility_insurance', 'edi_eligibility_insurance_sp_physicians', 'edi_eligibility_medicare', 'edi_reports', 'edi_transmissions', 'eras', 'favouriteicds', 'favouriteinsurances', 'manage_files', 'medical_secondary', 'message_detail_structure', 'notes', 'npiflag', 'password_resets', 'patient_appointments', 'patient_budget', 'patient_correspondence', 'patient_insurance_archive', 'patient_notes', 'patientstatement_settings', 'patientstatement_track', 'personal_notes', 'practicemanagecares', 'private_message', 'private_message_details', 'private_message_label_list', 'private_message_settings', 'problem_lists', 'profile_coverphoto', 'profile_events', 'provider_scheduler_time', 'provider_schedulers', 'providermanagecares', 'questionnaries_template', 'roles', 'superbill_existing_cpt', 'superbill_existing_icd', 'superbill_template', 'ticket', 'ticket_details', 'transmission_claim_details', 'transmission_cpt_details','maintenance_sqls','templates','patient_eligibility_waystar'
            );

            if (!empty($practice_lists)) {
                foreach ($practice_lists as $practice_list) {
                    //$db_name = $this->getpracticedbname($practice_list->practice_name);
                    $this->configureConnectionByName($tenantDB);
                    //$app_database_name = config('siteconfigs.connection_database');
                    $table_name_merge = 'Tables_in_' . $adminDB;

                    foreach ($tables as $table) {
                        $table_name = $table->$table_name_merge; // Statically given the database name
                        if (!in_array($table_name, $ignore_tables)) {
                            // Truncate table - @todo if necessary trucate table.
                            //DB::statement("TRUNCATE TABLE $db_name.$table_name");

                            if ($table_name == 'practices') {
                                $insert = "INSERT INTO $tenantDB.$table_name SELECT * FROM $adminDB.$table_name WHERE id=" . $practice_list->id;
                            } elseif ($table_name == 'providers') {
                                $insert = "INSERT INTO $tenantDB.$table_name SELECT * FROM $adminDB.$table_name WHERE practice_id=" . $practice_list->id;
                            } else {
                                $insert = "INSERT INTO $tenantDB.$table_name SELECT * FROM $adminDB.$table_name";
                            }
                            //\Log::info($insert);				       	   						       
                            DB::insert($insert);
                            /*
                            if ($table_name == 'practices') {
                                DB::statement("ALTER TABLE $tenantDB.$table_name DROP COLUMN customer_id");
                            }
                            */
                        }
                    }
                    $this->movemediafolder($practice_list->id);
                    DB::statement("UPDATE practices SET status='Active' WHERE id=" . $practice_list->id);
                    $this->configureConnectionByName($adminDB);
                    DB::connection($adminDB)->update("UPDATE practices SET status='Active' WHERE id=" . $practice_list->id);
                }
            } else {
                //DB::connection()->statement("DROP DATABASE ".$tenantDB);
                \Log::info("No pending practice found to proceed.");
            }
        } catch (Exception $e) {
            //DB::connection()->statement("DROP DATABASE ".$tenantDB);
            \Log::info("Exception on seeding. Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function movemediafolder($practice_id) {
        $main_dir_name = md5('P' . $practice_id);
        $chk_env_site = getenv('APP_ENV');
        if ($chk_env_site == Config::get('siteconfigs.production.defult_production')) {
            $storage_disk = "s3_production";
            $bucket_name = "medcubicsproduction";
        } else {
            $storage_disk = "s3";
            $bucket_name = "medcubicslocal";
        }

        $main_dir_arr = Storage::disk($storage_disk)->directories();
        if (!in_array($main_dir_name, $main_dir_arr))
            Storage::disk($storage_disk)->makeDirectory($main_dir_name);

        $main_dir_arrimg = Storage::disk($storage_disk)->directories($main_dir_name);
        if (!in_array($main_dir_name . "/image", $main_dir_arrimg))
            Storage::disk($storage_disk)->makeDirectory($main_dir_name . "/image");
    }

    /*     * * Start  to get user API Ids ** */

    public static function getUserAPIIds($apiname) { 
        $practice_id = Session::get('practice_dbid');
        $user = Auth::user();
            if($user->role_id == 1) return true;

        $getpractice = array();
        if ($user->practice_user_type == 'practice_admin') {
            $getpractice = explode(",", $user->admin_practice_id);			
        }

        $practice_ids = array();
        
        if ($user->practice_user_type == 'customer') {
            $practice_ids = Practices::where('customer_id', Auth::user()->customer_id)->where('status', 'Active')->pluck('id')->all();			
        }
        
        // Check if the API is currently active
        $get_apiid = ApiConfig::where('api_for', $apiname)->where('api_status','Active')->pluck('id')->all();
        $apiid = (count($get_apiid) > 0) ? $get_apiid['0'] : '';

        if ($practice_id != '') {
            $get_practicename = Practices::where('id', $practice_id)->select('practice_name')->first();
            $practice_name = $get_practicename->practice_name;
            $practice_name = str_replace(' ', '_', $practice_name);
            $practice_name = str_replace(',', '', $practice_name);
            preg_replace('/[^A-Za-z0-9\-]/', '', $practice_name);
            $practice_name = strtolower($practice_name);

            // Get practice db name 
            if (file_exists('apijson/' . $practice_name . '.json')) {
                $pracurl = 'apijson/' . $practice_name . '.json';
               // $fp = fopen('apijson/' . $practice_name . '.json', 'r');
               // $str = fread($fp, filesize('apijson/' . $practice_name . '.json'));

                $contextOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    )
                );

                $str = file_get_contents($pracurl, false, stream_context_create($contextOptions));
                
                $get_json = json_decode($str, true);
                $user_id = $user->id;

                if (isset($get_json['userapiids'][$user_id])) {
                    $getApiarr = explode(',', $get_json['userapiids'][$user_id]);
                    if (in_array($apiid, $getApiarr)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        return false;
    }

    /*     * * End to get user API Ids ** */

    /*     * * Start to create Json file ** */

    public function create_APIJSON($practiceid, $action = '') {
        // get already set user api id 
        $get_APIIds = Setapiforusers::select('user_id', DB::raw('group_concat(api_id) as api_id'))->where('practice_id', $practiceid)->groupBy('user_id')->get();

        $responseapi = array();

        if ($action == '') {
            $collect_apiids = array();
            foreach ($get_APIIds as $userAPIids) {
                $api_ids = $userAPIids->api_id;
                $user_ids = $userAPIids->user_id;
                $collect_apiids[$user_ids] = $api_ids;
            }
            $responseapi['userapiids'] = $collect_apiids;
        }

        // If new then set default api in json file and table.
        if ($action == 'new') {
            $get_UserIds = Setpracticeforusers::where('practice_id', $practiceid)->select('user_id')->get();

            $get_user_api = ApiConfig::where('api_status', 'Active')->whereIn('api_for', ['address', 'npi'])->pluck('id')->all();
            $current_user_api = implode(',', $get_user_api);

            $current_date = date('Y-m-d h:i:s');
            $user = (isset(Auth::user()->id) ? Auth::user()->id : 0);

            foreach ($get_UserIds as $userids) {
                $api_ids = $current_user_api;
                $user_ids = $userids->user_id;
                $collect_apiids[$user_ids] = $api_ids;

                foreach ($get_user_api as $getapiuserids) {
                    $data['user_id'] = $userids->user_id;
                    $data['practice_id'] = $practiceid;
                    $data['created_by'] = $user;
                    $data['updated_by'] = $user;
                    $data['created_at'] = $current_date;
                    $data['updated_at'] = $current_date;
                    $data['api_id'] = $getapiuserids;
                    Setapiforusers::create($data);
                }
            }
            $responseapi['userapiids'] = @$collect_apiids;
        }

        $practice = Practices::where('id', $practiceid)->first();
        $practice_db_name = $this->getpracticedbname($practice->practice_name);

        if (!file_exists('apijson')) {
            mkdir("apijson");
        }

        if (file_exists('apijson/' . $practice_db_name . '.json')) {
            unlink('apijson/' . $practice_db_name . '.json');
        }

        $fp = fopen('apijson/' . $practice_db_name . '.json', 'w');
        fwrite($fp, json_encode($responseapi));
        fclose($fp);
    }

    /*     * * End to create Json file ** */

    public function get_PatientBalance($patient_id) {
        return ClaimInfoV1::getBalance($patient_id, 'patient_balance');
    }

    public static function getUnreadTicket() {
        return Ticket::where('read', '0')->where('assigned', '0')->count();
    }

    public static function getMyreadTicket() {
        $user_id = Auth::user()->id;
        return Ticket::where('assigned', $user_id)->where('read', '0')->count();
    }

    public static function storeFileRecord($image_info) {
        $affectedRows = Managefile::where('source', $image_info['source'])->where('module', $image_info['module'])->where('record_id', $image_info['record_id'])->where('filename', $image_info['filename'])->delete();

        if ($image_info['filename'] != '')
            Managefile::create($image_info);
    }

    // Get patient insurance plan end date 
    public static function getPatientPlanEndDate($patient_id, $insurance_id, $policy_id, $type) {
        if ($type == 'Primary') {
            $query = PatientInsurance::where('patient_id', $patient_id)->where('category', 'Primary')->first();
            if (!empty($query)) {
                $insurance_id = $query->insurance_id;
                $policy_id = $query->policy_id;
            }
        }

        $edi_data = EdiEligibility::where('patient_id', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->orderBy('id', 'desc')->first();
        if (!empty($edi_data)) {
            return $edi_data->plan_end_date;
        }
    }

    // Check already checked patient insurance eligigibility.
    public static function checkInsEligiblity($patient_id, $insurance_id, $policy_id) {
        if (EdiEligibility::where('patient_id', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->count() > 0) {
            $geteligibilityinfo = EdiEligibility::where('patient_id', $patient_id)->where('insurance_id', $insurance_id)->where('policy_id', $policy_id)->orderBy('id', 'desc')->first()->coverage_status;

            if ($geteligibilityinfo == '') {
                return '1';
            } elseif ($geteligibilityinfo == 'Active Coverage') {
                return '2';
            } elseif ($geteligibilityinfo == 'Inactive') {
                return '3';
            }
        } else {
            return '1';
        }
    }

    /*** Get practice listing page details start ***/

    public function GetPracticeDetails($practices_list) {  
        $result = $total_app_arr = $total_unbilled_arr = $total_unbilled_arr_all = $total_workbench = $total_rej_arr = $total_charge_arr = $total_collection_arr = $total_outstanding_arr = array();
        foreach ($practices_list as $practice_key => $practice_name) {
            ### Set practice DB config ###
            $practice_db_name = $this->getpracticedbname($practice_name);
            $this->configureConnectionByName($practice_db_name);
            Config::set('database.default', $practice_db_name);
            ### Get practice stat details ###
            $statsdetails = new StatsApiController();
			$statDetails = $this->getClaimStats('all','','',$practice_key);
			$monthDate = date('m/01/Y').' - '.date('m/t/Y');
			$yearDate = date('01/01/Y').' - '.date('m/t/Y');
			$monthStatDetails = Helpers::getCurrentMonthChargeAmount($practice_key);
			// switch practice page added condition for provider login based showing values
			// Revision 1 - Ref: MR-2719 22 Aug 2019: Selva
            $practice_timezone = Helpers::getPracticeTimeZone($practice_key);  
            $cur_start_date = Carbon::now($practice_timezone)->startOfYear()->toDateString();
            $cur_end_date = Carbon::now($practice_timezone)->toDateString(); 
			if(Auth::check() && Auth::user()->isProvider()){
				$yearStatDetails = ClaimInfoV1::whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$cur_start_date."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'")->where('rendering_provider_id',Auth::user()->provider_access_id)->sum('total_charge');//$this->getClaimStats('all',$yearDate);
            }
			else{
                     
				$yearStatDetails = ClaimInfoV1::whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$cur_start_date."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'")->sum('total_charge');
            }
            $app_count = $statsdetails->TodayAndMonthAppointments();
            $unbilled = $statsdetails->TodayAndMonthUnbilled();
            $charges = $statsdetails->CurrentMonthYearCharges();   			
            $collection = $statsdetails->CurrentMonthYearCollections($practice_key);
            $month_prob_list = $statsdetails->CurrentMonthProblemList();
            $year_outstanding = $this->getClaimTotalOutstandingAr($practice_key);
            $total_app_arr[] = $app_count["month"];
            $total_unbilled_arr[] = $unbilled["month"];
            $total_unbilled_arr_all[] = $statDetails['unbilled']['total_amount']; 
            $total_rej_arr[] = $statDetails['rejected']['total_amount'];
            $total_charge_arr[] = $statDetails['billed']['total_amount'];// Subtract unbilled charges from total charges to find billed amount
            $total_collection_arr[] = $collection["year"];
            $total_outstanding_arr[] = $year_outstanding;
			//Practice Total Assigned Document Count - Ignores deleted document even if the document is in followup list
			//Revision 1 - Ref: MR-1264 06 Aug 2019: Selva
            $total_workbench[] = $month_prob_list['total'];
            $result[$practice_key] = [
                "name" => $practice_name,
                "app_count" => $app_count["today"],
                "unbilled" => $statDetails['unbilled']['total_amount'],
                "rejection" => $statDetails['rejected']['total_amount'],
                "month_charges" => ($monthStatDetails),
                "month_collection" => Helpers::priceFormat($collection["month"], "no"),
                "problem_list" => $month_prob_list['total'],
                "year_charges" => $yearStatDetails,//($yearStatDetails['billed']['total_amount'] + $yearStatDetails['unbilled']['total_amount']),
                "year_collection" => Helpers::priceFormat($collection["year"], "no"),
                "year_outstanding" => Helpers::priceFormat($year_outstanding, "no")
            ];
            $this->disconnectPracticeDB();
        }
        $total_app_count = array_sum($total_app_arr);
        $total_unbilled = array_sum($total_unbilled_arr);
        $total_unbilled_arr_all = array_sum($total_unbilled_arr_all);
        $total_rejection = array_sum($total_rej_arr);
        $total_charges = array_sum($total_charge_arr);
        $total_collection = array_sum($total_collection_arr);
        $total_outstanding = array_sum($total_outstanding_arr);
        $total_workbench = array_sum($total_workbench);
        $response["total"] = [
            "app_count" => $total_app_count,
            "month_prob_list" => $total_workbench,
            "unbilled" => Helpers::priceFormat($total_unbilled_arr_all, 'yes'),
            "rejection" => $total_rejection,
            "charges" => Helpers::priceFormat($total_charges, 'yes'),
            "collection" => Helpers::priceFormat($total_collection, 'yes'),
            "outstanding" => Helpers::priceFormat($total_outstanding, 'yes')
        ];
        $response["individual"] = $result;
        return $response;
    }

    /*** Get practice listing page details end ***/


    /*** Get practice dashboard page details start ***/

    public function getPracticeStatsDetail($practices_list) { 

        $result = $total_app_arr = $total_unbilled_arr = $total_unbilled_arr_all = $total_workbench = $total_rej_arr = $total_charge_arr = $total_collection_arr = $total_outstanding_arr = array();
        try{
            foreach ($practices_list as $practice_key => $practice_name) {
                ### Set practice DB config ###
                $practice_db_name = $this->getpracticedbname($practice_name);
                $this->configureConnectionByName($practice_db_name);
                Config::set('database.default', $practice_db_name);

                $statsdetails = new StatsApiController();
                $details = ['patient_statement_sent', 'patient_statement_api_usage', 'document_upload_size', 'document_count','eligibility_api_usage', 'twilio_sms_usage', 'twilio_call_usage', 'twilio_fax_usage','total_patients', 'total_charges','total_payments', 'total_adjustment', 'total_denial', 'total_rejections', 'total_submitted_claims', 'frequently_generated_reports', 'total_reports_generated', 'templates_sent', 'patient_intake_usage', 'charge_capture_usage', 'users', 'providers', 'address_npi_api', 'appointments', 'error_log_count','patient_payments','insurance_payments'];
                $statDetails = $statsdetails->getPracticeStatsDetail($details);
                $result[$practice_key] = $statDetails;
                /*
                dd($statDetails);
                $patient_statement_sent = 0;
                $patient_statement_api_usage = 0;
                $document_upload_size = 0;
                $document_count = 0;
                $eligibility_api_usage = 0;
                $twilio_sms_usage = 0;
                $twilio_call_usage = 0;
                $twilio_fax_usage = 0;
                $total_patients = 0;
                $total_charges = 0;
                $total_payments = 0;
                $total_adjustment = 0;
                $total_denial = 0;
                $total_rejections = 0;
                $total_submitted_claims = 0;
                $frequently_generated_reports = 0;
                $total_reports_generated = 0;
                $templates_sent = 0;
                $patient_intake_usage = 0;
                $charge_capture_usage = 0;
                $users = 0;
                $providers = 0;
                $address_npi_api = 0;
                $appointments = 0;
                $error_log_count = 0;
                
                $result[$practice_key] = [
                    "patient_statement_sent" => $patient_statement_sent,
                    "patient_statement_api_usage" => $patient_statement_api_usage,
                    "document_upload_size" => $document_upload_size,
                    "document_count" => $document_count,
                    "eligibility_api_usage" => $eligibility_api_usage,
                    "twilio_sms_usage" => $twilio_sms_usage,
                    "twilio_call_usage" => $twilio_call_usage,
                    "twilio_fax_usage" => $twilio_fax_usage,
                    "total_patients" => $total_patients,
                    "total_charges" => $total_charges,
                    "total_payments" => $total_payments,
                    "total_adjustment" => $total_adjustment,
                    "total_denial" => $total_denial,
                    "total_rejections" => $total_rejections,
                    "total_submitted_claims" => $total_submitted_claims,
                    "frequently_generated_reports" => $frequently_generated_reports,
                    "total_reports_generated" => $total_reports_generated,
                    "templates_sent" => $templates_sent,
                    "patient_intake_usage" => $patient_intake_usage,
                    "charge_capture_usage" => $charge_capture_usage,
                    "users" => $users,
                    "providers" => $providers,
                    "address_npi_api" => $address_npi_api,
                    "appointments" => $appointments,
                    "error_log_count" => $error_log_count
                ];
                */
                /*
                ### Get practice stat details ###
                $statsdetails = new StatsApiController();
                $statDetails = $this->getClaimStats('all');
                $monthDate = date('m/01/Y').' - '.date('m/t/Y');
                $yearDate = date('01/01/Y').' - '.date('m/t/Y');
                $monthStatDetails = $this->getClaimStats('all',$monthDate);
                $yearStatDetails = $this->getClaimStats('all',$yearDate);
                $app_count = $statsdetails->TodayAndMonthAppointments();
                $unbilled = $statsdetails->TodayAndMonthUnbilled();
                $charges = $statsdetails->CurrentMonthYearCharges();            
                $collection = $statsdetails->CurrentMonthYearCollections();
                $month_prob_list = $statsdetails->CurrentMonthProblemList();
                $year_outstanding = $this->getClaimTotalOutstandingAr();
                $total_app_arr[] = $app_count["month"];
                $total_unbilled_arr[] = $unbilled["month"];
                $total_unbilled_arr_all[] = $statDetails['unbilled']['total_amount']; 
                $total_rej_arr[] = $statDetails['rejected']['total_amount'];
                $total_charge_arr[] = $statDetails['billed']['total_amount'];// Subtract unbilled charges from total charges to find billed amount
                $total_collection_arr[] = $collection["year"];
                $total_outstanding_arr[] = $year_outstanding;
                $total_workbench[] = $month_prob_list['total_workbench'];
                $result[$practice_key] = [
                    "name" => $practice_name,
                    "app_count" => $app_count["today"],
                    "unbilled" => $statDetails['unbilled']['total_amount'],
                    "rejection" => $statDetails['rejected']['total_amount'],
                    "month_charges" => ($monthStatDetails['billed']['total_amount'] + $monthStatDetails['unbilled']['total_amount']),
                    "month_collection" => Helpers::priceFormat($collection["month"], "no"),
                    "problem_list" => $month_prob_list['total_workbench'],
                    "year_charges" => ($yearStatDetails['billed']['total_amount'] + $yearStatDetails['unbilled']['total_amount']),
                    "year_collection" => Helpers::priceFormat($collection["year"], "no"),
                    "year_outstanding" => Helpers::priceFormat($year_outstanding, "no")
                ];
                */
                $this->disconnectPracticeDB();
            }
            return $result;        
        } catch(Exception $e) {
            \Log::info("Error Occured. Msg".$e->getMessage() );
        }
    }

    /*** Get practice dashboard page details end ***/

    public function setAccessCache($user_id) {
        // Store permissions in cache        
        //$permissionList = Cache::remember('access_permission'.$user_id , 30, function() use($user_id) {        
        $permissionList = Cache::rememberForever('access_permission'.$user_id , function() use($user_id) { 
            $permissionList['profile/blogs/{order?}/{keyword?}'] = 1;
            $permissionList['profile/message'] = 1;
            $permissionList['profile'] = 1;
            $permissionList['payments'] = 1;
            $permissionList['scheduler'] = 1;
            $permissionList['patients'] = 1;
            $permissionList['charges'] = 1;
            $permissionList['payments'] = 1;
            $permissionList['armanagement'] = 1;
            $permissionList['claims'] = 1;
            $permissionList['reports'] = 1;
            $permissionList['practice'] = 1;
            $permissionList['documents'] = 1;      
            $permissionList['analytics/providers'] = 1;      
            $permissionList['/'] = 1;      
            $permissionList['admin/customer/setpractice/{id}'] = 1;      
            $permissionList['admin/customer/setpractice/{id}'] = 1;      
            return $permissionList;
        }); 
    }

}