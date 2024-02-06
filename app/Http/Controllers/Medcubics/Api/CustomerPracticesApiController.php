<?php

namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Medcubics\Language as Language;
use App\Models\Medcubics\Speciality as Speciality;
use App\Models\Medcubics\Taxanomy as Taxanomy;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\Medcubics\NpiFlag as NpiFlag;
use Intervention\Image\ImageManagerStatic as Image;
use Auth;
use Response;
use Request;
use Config;
use Validator;
use Input;
use Hash;
use Schema;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use DB;
use App;
use Artisan;
use File;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Seeder;
use Nwidart\DbExporter\DbExportHandler as DbExportHandler;
use Nwidart\DbExporter\DbMigrations as DbMigrations;
use Nwidart\DbExporter\DbSeeding as DbSeeding;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Medcubics\Api\PracticeProvidersApiController as PracticeProvidersApiController;
use App\Models\Medcubics\Provider as Provider;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use Lang;
use App\Models\Medcubics\Provider_degree as Provider_degree;

class CustomerPracticesApiController extends Controller {

    public function getIndexApi($cust_id, $export = '') {
        $cust_id = Helpers::getEncodeAndDecodeOfId($cust_id, 'decode');
        if (Customer::where('id', $cust_id)->count()) {
            $practices = Practices::with('speciality_details', 'taxanomy_details', 'languages_details')->where('customer_id', $cust_id)->get()->all();
            $customer = Customer::where('id', $cust_id)->first();
            $tabs = "yes";
            if ($export != "") {
                $exportparam = array(
                    'filename' => 'Practice',
                    'heading' => 'Practice',
                    'fields' => array(
                        'practice_name' => 'Practice Name',
                        'practice_description' => 'Description',
                        'email' => 'Email',
                        'phone' => 'Phone',
                        'fax' => 'Fax',
                        'doing_business_s' => 'Doing Business as',
                    )
                );
                $callexport = new CommonExportApiController();
                return $callexport->generatemultipleExports($exportparam, $practices, $export);
            }
            $customer->encid = Helpers::getEncodeAndDecodeOfId($customer->id, 'encode');
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('practices', 'customer', 'tabs')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.not_found_msg"), 'data' => null));
        }
    }

    public function getCreateApi($id) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Customer::where('id', $id)->count()) {
            $customer = Customer::where('id', $id)->first();
            $specialities = Speciality::orderBy('speciality', 'ASC')->pluck('speciality', 'id')->all();
            //$taxanomies 		= Taxanomy::orderBy('code','ASC')->pluck('code','id');
            $taxanomies = $apilist_subcat = [];
            $language = Language::orderBy('language', 'ASC')->pluck('language', 'id')->all();
            $language_id = $taxanomy_id = $speciality_id = '';

            // Get Parent Category API
            $apilist = ApiConfig::where('api_status', 'Active')->groupBy("api_name")->pluck('api_name', 'id')->all();
            $api_name = ApiConfig::where('api_status', 'Active')->pluck('category', 'id')->all();
            $maincat_api = ['eligible', 'apex', 'twilio'];
            $getAPIsettings = ApiConfig::select('id', 'api_for', 'api_name', 'category')->where('api_status', 'Active')->whereIn('api_name', $maincat_api)->get();
            // Get Sub Category API
            foreach ($getAPIsettings as $mainapi) {
                if ($mainapi->api_for != 'medicare_eligibility') {
                    $apilist_subcat[$mainapi->api_name][$mainapi->id] = $mainapi->api_for;
                }
            }
            // Select default address, npi fields
            $setapi = ApiConfig::where('api_status', 'Active')->whereIn('api_for', ['address', 'npi'])->pluck('id')->all();

            /// Get address for usps ///
            $addressFlag['general']['address1'] = '';
            $addressFlag['general']['city'] = '';
            $addressFlag['general']['state'] = '';
            $addressFlag['general']['zip5'] = '';
            $addressFlag['general']['zip4'] = '';
            $addressFlag['general']['is_address_match'] = '';
            $addressFlag['general']['error_message'] = '';
            $addressFlag['pta']['address1'] = '';
            $addressFlag['pta']['city'] = '';
            $addressFlag['pta']['state'] = '';
            $addressFlag['pta']['zip5'] = '';
            $addressFlag['pta']['zip4'] = '';
            $addressFlag['pta']['is_address_match'] = '';
            $addressFlag['pta']['error_message'] = '';
            $addressFlag['ma']['address1'] = '';
            $addressFlag['ma']['city'] = '';
            $addressFlag['ma']['state'] = '';
            $addressFlag['ma']['zip5'] = '';
            $addressFlag['ma']['zip4'] = '';
            $addressFlag['ma']['is_address_match'] = '';
            $addressFlag['ma']['error_message'] = '';
            $addressFlag['pa']['address1'] = '';
            $addressFlag['pa']['city'] = '';
            $addressFlag['pa']['state'] = '';
            $addressFlag['pa']['zip5'] = '';
            $addressFlag['pa']['zip4'] = '';
            $addressFlag['pa']['is_address_match'] = '';
            $addressFlag['pa']['error_message'] = '';
            $npiflag_columns = Schema::getColumnListing('npiflag');
            foreach ($npiflag_columns as $columns) {
                $npi_flag[$columns] = '';
            }

            $time['monday_forenoon'] = '00;720';
            $time['tuesday_forenoon'] = '00;720';
            $time['wednesday_forenoon'] = '00;720';
            $time['thursday_forenoon'] = '00;720';
            $time['friday_forenoon'] = '00;720';
            $time['saturday_forenoon'] = '00;720';
            $time['sunday_forenoon'] = '00;720';

            $time['monday_afternoon'] = '720;1480';
            $time['tuesday_afternoon'] = '720;1480';
            $time['wednesday_afternoon'] = '720;1480';
            $time['thursday_afternoon'] = '720;1480';
            $time['friday_afternoon'] = '720;1480';
            $time['saturday_afternoon'] = '720;1480';
            $time['sunday_afternoon'] = '720;1480';

            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('customer', 'specialities', 'speciality_id', 'taxanomies', 'taxanomy_id', 'language', 'language_id', 'addressFlag', 'time', 'npi_flag', 'apilist', 'apilist_subcat', 'setapi', 'api_name')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.not_found_msg"), 'data' => null));
        }
    }

    public function getStoreApi($cust_id, $request = '') {
        $cust_id = Helpers::getEncodeAndDecodeOfId($cust_id, 'decode');
        if ($request == '')
            $request = Request::all();

        $npi_value = ($request['entity_type'] == 'Group') ? $request['group_npi'] : $request['npi'];
        $is_valid_npi = Helpers::checknpi_valid_process($npi_value); // check npi valid or not back end validation
        $request['customer_id'] = Helpers::getEncodeAndDecodeOfId($request['customer_id'], 'decode');
        $practice_db_name = str_replace(" ", "_", $request['practice_name']);
        $request['mail_add_1'] = trim($request['mail_add_1']);
        $request['pay_add_1'] = trim($request['pay_add_1']);
        Validator::extend('check_npi_api_validator', function($attribute) use($is_valid_npi) {
            if ($is_valid_npi == 'No')
                return false;
            else
                return true;
        });

        $validate_practice_rules = Practices::$rules + array('practice_name' => "required", 'image' => Config::get('siteconfigs.customer_image.defult_image_size'));
        $validate_practice_rules['npi'] = 'nullable|required_if:entity_type,Individual|digits:10|check_npi_api_validator';
        $validate_practice_rules['group_npi'] = 'nullable|required_if:entity_type,Group|digits:10|check_npi_api_validator';

        $validate_practice_msg = Practices::messages() + array('npi.check_npi_api_validator' => Lang::get("common.validation.npi_validcheck"));

        $validator = Validator::make($request, $validate_practice_rules, $validate_practice_msg);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {

            // Beging transcation            
            DB::beginTransaction();     
            \Log::info("Txn started");

            try {
                $practices = Practices::create($request);
                $apiListString = (!empty($request['apilist'])) ? implode(",", $request['apilist']) : '';
                $practices->api_ids = $apiListString;
                $practices->save();

                $practice_id = $practices->id;
                \Log::info("Created entry in responsive");
                $practice_name = $practices->practice_name;

                if (Input::hasFile('image')) {
                    $image = Input::file('image');
                    $filename = rand(11111, 99999);
                    $extension = $image->getClientOriginalExtension();
                    $filestoreName = $filename . '.' . $extension;
                    $unique_practice = md5('P' . $practice_id);
                    $resize = array('150', '150');
                    Helpers::mediauploadpath($unique_practice, 'practice', $image, $resize, $filestoreName);
                    $practices->avatar_name = $filename;
                    $practices->avatar_ext = $extension;
                    $practices->save();
                }

                /* Starts - address flag update */
                $address_flag = array();
                $address_flag['type'] = 'practice';
                $address_flag['type_id'] = $practice_id;
                $address_flag['type_category'] = 'pay_to_address';
                $address_flag['address2'] = $request['pay_add_1'];
                $address_flag['city'] = $request['pay_city'];
                $address_flag['state'] = $request['pay_state'];
                $address_flag['zip5'] = $request['pay_zip5'];
                $address_flag['zip4'] = $request['pay_zip4'];
                $address_flag['is_address_match'] = $request['pta_is_address_match'];
                $address_flag['error_message'] = $request['pta_error_message'];
                AddressFlag::checkAndInsertAddressFlag($address_flag);
                $address_flag['type'] = 'practice';
                $address_flag['type_id'] = $practice_id;
                $address_flag['type_category'] = 'mailling_address';
                $address_flag['address2'] = $request['mail_add_2'];
                $address_flag['city'] = $request['mail_city'];
                $address_flag['state'] = $request['mail_state'];
                $address_flag['zip5'] = $request['mail_zip5'];
                $address_flag['zip4'] = $request['mail_zip4'];
                $address_flag['is_address_match'] = $request['ma_is_address_match'];
                $address_flag['error_message'] = $request['ma_error_message'];
                AddressFlag::checkAndInsertAddressFlag($address_flag);
                $address_flag['type'] = 'practice';
                $address_flag['type_id'] = $practice_id;
                $address_flag['type_category'] = 'primary_address';
                $address_flag['address2'] = $request['primary_add_2'];
                $address_flag['city'] = $request['primary_city'];
                $address_flag['state'] = $request['primary_state'];
                $address_flag['zip5'] = $request['primary_zip5'];
                $address_flag['zip4'] = $request['primary_zip4'];
                $address_flag['is_address_match'] = $request['pa_is_address_match'];
                $address_flag['error_message'] = $request['pa_error_message'];
                AddressFlag::checkAndInsertAddressFlag($address_flag);
                /* Ends - address flag update */

                /* Starts - NPI flag update */
                $request['company_name'] = 'npi';
                $request['type'] = 'practice';
                $request['type_id'] = $practice_id;
                $request['type_category'] = 'Individual';
                NpiFlag::checkAndInsertNpiFlag($request);
                /* Ends - NPI flag update */

                if ($request['billing_entity'] == 'Yes') {
                    $request['from_admin_new'] = 'yes';
                    $this->create_default_provider($request, $practices);
                }
                /**
                  Commit and roll back will not work as expected for 2 databases at a same time have to check this.
                 * */
                //create practice db and insert that practice record
                $dbconnection = new DBConnectionController();
                //update practice id in admin practice table	
                $dbconnection->updatePracticeDBID($practice_id);

                if (config('siteconfigs.is_enable_provider_add')) {
                    $practice_db_name = $dbconnection->getpracticedbname($practice_name);

                    if ($dbconnection->createSchema($practice_db_name)) {
                        \Log::info("Create schema done");
                        $migration_files = glob(base_path() . "/database/migrations/practicemigration/*.php");
                        foreach ($migration_files as $migratefiles) {
                            @unlink($migratefiles);
                        }
                        //$seeder_name = getenv('DB_DATABASE').'TableSeeder.php';
                        //unlink(base_path(). "/database/seeds/".$seeder_name);
                        $request['practice_db_id'] = $practice_id;
                        $dbconnection->deleteotherpractices($practice_id, $practice_db_name);
                    } else {
                        \Log::info("Create schema not success Please check log");
                    }
                    $dbconnection->updateApiInfoinPracticeDB($request, $practice_id, $practice_db_name, 'add');
                }

                DB::commit();
                \Log::info("Txn Committed");
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $practice_id));
            } catch (\Exception $e) {
                // If practice creation not success then delete the practice entry in coreDB
                $practice_info = Practices::where('id', $practice_id)->first();
                if(!empty($practice_info)) {
                    \Log::info("Removing un completed practice entry. Practice: ".$practice_info['practice_name']." #".$practice_id);                    
                    Practices::where('id', $practice_id)->delete();
                }
                DB::rollback();
                $errorMsg = 'Error on line '.$e->getLine().' in '.$e->getFile().'Msg :'.$e->getMessage();    
                \Log::info("Rollbacked Txn. Due to error: " . $errorMsg);
                return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data' => $practice_id));
            }
            // End Transaction            
        }
    }

    public function getEditApi($customer_id, $id) {
        $customer_id = Helpers::getEncodeAndDecodeOfId($customer_id, 'decode');
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Customer::where('id', $customer_id)->count()) {
            if (Practices::where('id', $id)->count()) {
                $practice = Practices::where('id', $id)->first();
                $specialities_id = $practice->speciality_id;
                $specialities = Speciality::orderBy('speciality', 'ASC')->pluck('speciality', 'id')->all();
                $speciality_id = $practice->specialities_id;
                $taxanomies = Taxanomy::where('speciality_id', $specialities_id)->pluck('code', 'id')->all();
                $taxanomy_id = $practice->taxanomy_id;
                $language = Language::orderBy('language', 'ASC')->pluck('language', 'id')->all();
                $language_id = $practice->language_id;

                // Get Parent Category API
                $apilist = ApiConfig::where('api_status', 'Active')->groupBy("api_name")->pluck('api_name', 'id')->all();
                $api_name = ApiConfig::where('api_status', 'Active')->pluck('category', 'id')->all();
                $maincat_api = ['eligible', 'apex', 'twilio'];
                $getAPIsettings = ApiConfig::select('id', 'api_for', 'api_name', 'category')->where('api_status', 'Active')->whereIn('api_name', $maincat_api)->get();
                $apilist_subcat = [];
                // Get Sub Category API
                foreach ($getAPIsettings as $mainapi) {
                    if ($mainapi->api_for != 'medicare_eligibility') {
                        $apilist_subcat[$mainapi->api_name][$mainapi->id] = $mainapi->api_for;
                    }
                }

                $setapi = explode(",", $practice->api_ids);

                /// Get Pay to address for usps ///
                $pta_address_flag = AddressFlag::getAddressFlag('practice', $practice->id, 'pay_to_address');
                $addressFlag['pta'] = $pta_address_flag;

                /// Get mailling address for usps ///
                $ma_address_flag = AddressFlag::getAddressFlag('practice', $practice->id, 'mailling_address');
                $addressFlag['ma'] = $ma_address_flag;

                /// Get primary address for usps ///
                $pa_address_flag = AddressFlag::getAddressFlag('practice', $practice->id, 'primary_address');
                $addressFlag['pa'] = $pa_address_flag;

                /// Get NPI details ///
                $npi_flag = NpiFlag::getNpiFlag('practice', $practice->id, $practice->entity_type);

                if (!$npi_flag) {
                    $npiflag_columns = Schema::getColumnListing('npiflag');
                    foreach ($npiflag_columns as $columns) {
                        $npi_flag[$columns] = '';
                    }
                }

                $time['monday_forenoon'] = $practice->monday_forenoon;
                $time['tuesday_forenoon'] = $practice->tuesday_forenoon;
                $time['wednesday_forenoon'] = $practice->wednesday_forenoon;
                $time['thursday_forenoon'] = $practice->thursday_forenoon;
                $time['friday_forenoon'] = $practice->friday_forenoon;
                $time['saturday_forenoon'] = $practice->saturday_forenoon;
                $time['sunday_forenoon'] = $practice->sunday_forenoon;
                $time['monday_afternoon'] = $practice->monday_afternoon;
                $time['tuesday_afternoon'] = $practice->tuesday_afternoon;
                $time['wednesday_afternoon'] = $practice->wednesday_afternoon;
                $time['thursday_afternoon'] = $practice->thursday_afternoon;
                $time['friday_afternoon'] = $practice->friday_afternoon;
                $time['saturday_afternoon'] = $practice->saturday_afternoon;
                $time['sunday_afternoon'] = $practice->sunday_afternoon;

                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('practice', 'specialities', 'speciality_id', 'taxanomies', 'taxanomy_id', 'language', 'language_id', 'addressFlag', 'npi_flag', 'time', 'apilist', 'setapi', 'apilist_subcat', 'api_name')));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.not_found_msg"), 'data' => null));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    public function getUpdateApi($id, $request = '') {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if ($request == '')
            $request = Request::all();
        $request['customer_id'] = Helpers::getEncodeAndDecodeOfId($request['customer_id'], 'decode');
        $rules = Practices::$rules;
        if ($request['entity_type'] == 'Group') {
            $rules = $rules + array(
                'group_tax_id' => 'required|digits:9',
                'group_npi' => 'required|digits:10'
            );
        } elseif ($request['entity_type'] == 'Individual') {
            $rules = $rules + array(
                'tax_id' => 'required|digits:9',
                'npi' => 'required|digits:10'
            );
        }
        $validator = Validator::make($request, $rules + array('image' => Config::get('siteconfigs.customer_image.defult_image_size')), Practices::messages());
        if ($validator->fails()) {
            $errors = $validator->errors();
            return Response::json(array('status' => 'error', 'message' => $errors, 'data' => ''));
        } else {
            $apiListString = (!empty($request['apilist'])) ? implode(",", $request['apilist']) : '';
            $practice = Practices::find($id);
            $billing_entity_old = $practice->billing_entity;
            $filename = $extension = '';
            if (isset($request['imagefile'])) {
                $request['avatar_name'] = "";
                $request['avatar_ext'] = "";
                unset($request['imagefile']);
                $practice->avatar_name = $filename;
                $practice->avatar_ext = $extension;
            }
            if (Input::hasFile('image')) {
                $image = Input::file('image');
                $filename = rand(11111, 99999);
                $old_filename = $practice->avatar_name;
                $old_extension = $practice->avatar_ext;
                $extension = $image->getClientOriginalExtension();
                $filestoreName = $filename . '.' . $extension;
                $filestoreoldName = $old_filename . '.' . $old_extension;
                $unique_practice = md5('P' . $id);
                $resize = array('150', '150');
                Helpers::mediauploadpath($unique_practice, 'practice', $image, $resize, $filestoreName, $filestoreoldName);
                $practice->avatar_name = $filename;
                $practice->avatar_ext = $extension;
            }
            $practice->bcbs_id = $request['bcbs_id'];

            $old_api_ids = $practice->api_ids;
            $practice->api_ids = $apiListString;

            if ($request['hostname'] == "")
                unset($request['hostname']);
            else
                $practice->hostname = $request['hostname'];
            if ($request['hostpassword'] == "")
                unset($request['password']);
            else
                $practice->hostpassword = $request['hostpassword'];

            if ($request['ipaddress'] == "")
                unset($request['ipaddress']);
            else
                $practice->ipaddress = $request['ipaddress'];

            $practice->update($request);

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
            AddressFlag::checkAndInsertAddressFlag($address_flag);
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
            AddressFlag::checkAndInsertAddressFlag($address_flag);
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
            AddressFlag::checkAndInsertAddressFlag($address_flag);
            /* Ends - Primary address */

            /* Starts - NPI flag update */
            $request['company_name'] = 'npi';
            $request['type'] = 'practice';
            $request['type_id'] = $practice->id;
            if ($request['entity_type'] == 'Group')
                $request['type_category'] = 'Group';
            else
                $request['type_category'] = 'Individual';
            NpiFlag::checkAndInsertNpiFlag($request);
            /* Ends - NPI flag update */
            if (config('siteconfigs.is_enable_provider_add')) {
                //update practice DB table			
                $admin_db = DB::getDatabaseName();
                $practice_info = Practices::where('id', $id)->first();
                $dbconnection = new DBConnectionController();
                $practice_db_name = $dbconnection->getpracticedbname($practice_info->practice_name);
                $dbconnection->updatepracticeInfoinAdminDB($request, $practice->practice_db_id, $admin_db, $filename, $extension);
                $dbconnection->updateApiInfoinPracticeDB($request, $practice->practice_db_id, $practice_db_name, 'update', $old_api_ids);
            }

            if ($request['billing_entity'] == 'Yes') {
                $this->create_default_provider($request, $practice);
            }
            return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.update_msg"), 'data' => ''));
        }
    }

    public function getShowApi($customer_id, $id) {
        $customer_id = Helpers::getEncodeAndDecodeOfId($customer_id, 'decode');
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        if (Customer::where('id', $customer_id)->count()) {
            if (Practices::where('id', $id)->count()) {
                $practice = Practices::with('taxanomy_details', 'speciality_details', 'languages_details')->where('id', $id)->first();
                $specialities = Speciality::orderBy('speciality', 'ASC')->pluck('speciality', 'id')->all();
                $speciality_id = $practice->specialities_id;
                $taxanomies = Taxanomy::orderBy('code', 'ASC')->pluck('code', 'id')->all();
                $taxanomy_id = $practice->taxanomies_id;
                $language = Language::orderBy('language', 'ASC')->pluck('language', 'id')->all();
                $language_id = $practice->language_id;
                $customer = Customer::where('id', $practice->customer_id)->first();
                $apilist = ApiConfig::pluck('api_for', 'id')->all();

                /// Get Pay to address for usps ///
                $pta_address_flag = AddressFlag::getAddressFlag('practice', $practice->id, 'pay_to_address');
                $addressFlag['pta'] = $pta_address_flag;

                /// Get mailling address for usps ///
                $ma_address_flag = AddressFlag::getAddressFlag('practice', $practice->id, 'mailling_address');
                $addressFlag['ma'] = $ma_address_flag;

                /// Get primary address for usps ///
                $pa_address_flag = AddressFlag::getAddressFlag('practice', $practice->id, 'primary_address');
                $addressFlag['pa'] = $pa_address_flag;

                /// Get NPI details ///
                $npi_flag = NpiFlag::getNpiFlag('practice', $practice->id, $practice->entity_type);
                //dd($npi_flag);

                if (!$npi_flag) {
                    $npiflag_columns = Schema::getColumnListing('npiflag');
                    foreach ($npiflag_columns as $columns) {
                        $npi_flag[$columns] = '';
                    }
                }
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('customer', 'practice', 'specialities', 'speciality_id', 'taxanomies', 'taxanomy_id', 'language', 'language_id', 'addressFlag', 'npi_flag', 'apilist')));
            } else {
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.not_found_msg"), 'data' => null));
            }
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => null));
        }
    }

    public function getProviderCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        return Practices::getProviderCount($practice_id);
    }

    public function getFacilityCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        return Practices::getFacilityCount($practice_id);
    }

    public function getPatientrCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        return Practices::getPatientrCount($practice_id);
    }

    public function getVistiCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        return Practices::getVistiCount($practice_id);
    }

    public function getClaimCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        return Practices::getClaimCount($practice_id);
    }

    public function getCollectionCount($practice_id) {
        $practice_id = Helpers::getEncodeAndDecodeOfId($practice_id, 'decode');
        return Practices::getCollectionCount($practice_id);
    }

    public function getTaxanomies() {
        if (!empty(Request::input('specialities_id'))) {
            $specialities_id = Request::input('specialities_id');
            $taxanomy = Taxanomy::where('speciality_id', $specialities_id)->get();
            $taxanomy_arr = "<option value=''>-- Select --</option>";
            foreach ($taxanomy as $taxanomies) {
                $taxanomy_arr .= "<option value='" . $taxanomies['id'] . "'>" . $taxanomies['code'] . "</option>";
            }
            return $taxanomy_arr;
        }
    }

    public function create_default_provider($request, $practice) {
        $def_provider_array = $request;

        if ($request['enumeration_type'] == "NPI-2") {
            $def_provider_array['organization_name'] = $request['basic_organization_name'];
            $def_provider_array['short_name'] = substr(strtoupper($request['basic_organization_name']), 0, 3);
        } elseif ($request['enumeration_type'] == "NPI-1") {
            $def_provider_array['last_name'] = $request['basic_last_name'];
            $def_provider_array['first_name'] = $request['basic_first_name'];
            $def_provider_array['middle_name'] = $request['basic_middle_name'];
            $def_provider_array['short_name'] = substr(strtoupper($request['basic_last_name']), 0, 3);
        } else {
            $def_provider_array['last_name'] = $practice->practice_name;
            $def_provider_array['first_name'] = $practice->practice_name;
            $def_provider_array['middle_name'] = "";
            $def_provider_array['short_name'] = substr(strtoupper($practice->practice_name), 0, 3);
        }

        if ($request['enumeration_type'] == 'NPI-2') {
            if (Provider::where('organization_name', $def_provider_array['organization_name'])->where('provider_types_id', '5')->where('npi', $request['npi'])->where('practice_id', $practice->id)->count()) {
                $provider_err = 'yes';
            } else {
                $provider_err = 'no';
            }
        } else {
            if (Provider::where('last_name', $def_provider_array['last_name'])->where('first_name', $def_provider_array['first_name'])->where('provider_types_id', '5')->where('npi', $request['npi'])->where('practice_id', $practice->id)->count()) {
                $provider_err = 'yes';
            } else {
                $provider_err = 'no';
            }
        }

        if ($provider_err == 'no') {
            $def_provider_array['npi'] = ($request['npi'] != '' ? $request['npi'] : $request['group_npi']);
            $def_provider_array['provider_types_id'] = '5';
            $def_provider_array['etin_type'] = 'TAX ID';
            $def_provider_array['etin_type_number'] = ($request['tax_id'] != '' ? $request['tax_id'] : $request['group_tax_id']);
            $def_provider_array['phone'] = @$request['phone'];
            $def_provider_array['fax'] = @$request['fax'];
            $def_provider_array['email'] = @$practice->email;
            $def_provider_array['practice_id'] = $practice->id;
            $def_provider_array['website'] = '';
            $def_provider_array['address_1'] = (@$request['location_address_1'] != '' ? @$request['location_address_1'] : @$request['pay_add_1']);
            $def_provider_array['address_2'] = (@$request['location_address_2'] != '' ? @$request['location_address_2'] : @$request['pay_add_2']);
            $def_provider_array['city'] = (@$request['location_city'] != '' ? @$request['location_city'] : @$request['pay_city']);
            $def_provider_array['state'] = (@$request['location_state'] != '' ? @$request['location_state'] : @$request['pay_state']);
            ## for maskibng phone number and fax number
            $phone_number = '('.substr($request['location_telephone_number'],0,3).')'.' '.substr(@$request['location_telephone_number'],-8);
            $fax_number = '('.substr($request['location_fax_number'],0,3).')'.' '.substr(@$request['location_fax_number'],-8);
            $def_provider_array['phone'] = (@$request['location_telephone_number'] != '' ? $phone_number : @$request['phone']);
            $def_provider_array['fax'] = (@$request['location_fax_number'] != '' ? $fax_number : @$request['fax']);

            $def_provider_array['zipcode5'] = (@$request['location_postal_code'] != '' ? substr(@$request['location_postal_code'],0,5) : @$request['pay_zip5']);
            $def_provider_array['zipcode4'] = (@$request['mail_zip4'] != '' ? substr(@$request['location_postal_code'],-4) : @$request['pay_zip4']);

            $def_provider_array['medicareptan'] = ($request['identifiers_identifier'] != '' ? $request['identifiers_identifier'] : '');
            $def_provider_array['enumeration_type'] = $request['enumeration_type'];
            $def_provider_array['speciality_id'] = "";
            $def_provider_array['provider_dob'] = "";
            $def_provider_array['def_provider_added'] = "yes";
            $def_provider_array['general_address1'] = $def_provider_array['general_city'] = $def_provider_array['general_state'] = $def_provider_array['general_zip5'] = $def_provider_array['general_zip4'] = $def_provider_array['general_is_address_match'] = $def_provider_array['general_error_message'] = "";
            $def_provider_array['practice_id'] = Helpers::getEncodeAndDecodeOfId($def_provider_array['practice_id'], 'encode');
            $def_provider_array['customer_id'] = Helpers::getEncodeAndDecodeOfId($def_provider_array['customer_id'], 'encode');
            $provider_degrees_id = Provider_degree::where('degree_name',@$request['basic_credential'])->get();           
            $def_provider_array['provider_degrees_id'] = (@$provider_degrees_id[0]->id != '' ? @$provider_degrees_id[0]->id : '');
            $def_provider_array['status'] = 'Active';
           
            $practiceprovider_obj = new PracticeProvidersApiController();
            $practiceprovider_obj->getStoreApi($def_provider_array);
        }
        return 0;
    }

    public function getDeleteApi($id, $p_name) {
        $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
        $delete_avr = Practices::where('id', $id)->first();
        echo $delete_avr->avatar_name . "<br>";
        $delete_avr->avatar_name = "";
        $delete_avr->avatar_ext = "";
        $delete_avr->save();
        return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.delete_msg"), 'data' => ''));
    }

    function __destruct() {
        //
    }

}
