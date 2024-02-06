<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Practice;
use Illuminate\Contracts\Validation\Validator;
//use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Medcubics\Useractivity as Useractivity;
use App\Models\Practice as Practicedb;
use App\Http\Helpers\Helpers as Helpers;
use View;
use App;
use Auth;
use Request;
use DB;
use Session;
use Config;
use Route;
use Cache;


class Controller extends BaseController {

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    var $pnotes = '';
    public function __construct() 
    {          
        $this->alert_notes = $this->getPatientNotes();

        View::share('alert_notes', $this->alert_notes);

        $session_id = Session::get('practice_dbid');
        $current_db = getenv('DB_DATABASE');
        if ($session_id != '') {
            $practice = Cache::remember('practice_details'.$session_id , 30, function() use($session_id) {
                $practice = Practicedb::where('id', $session_id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
                return $practice;
            });
            if ($practice != '') {
                $practice_name = $practice->practice_name;
                $practice_name = str_replace(' ', '_', $practice_name);
                $practice_name = str_replace(',', '', $practice_name);
                preg_replace('/[^A-Za-z0-9\-]/', '', $practice_name);
                $current_db = strtolower($practice_name);
            }
        } 
        $collectuseractivity = [];
        $url = Request::url();
        $split_url = explode("/", $url);

        $name = '';
        $action = '';
        if (in_array("delete", $split_url)) {
            $key = count($split_url) - 2;
            $id = ucwords($split_url[$key]);
            $split_fetch_url = explode("/delete", $url);

            if ($id == 'Delete') {
                $id = ucwords($split_url[count($split_url) - 1]);
                $fetch_url = $split_fetch_url[0] . '/' . $id;
            } else {
                $fetch_url = $split_fetch_url[0];
            }
            $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
            $module = $split_url[count($split_url) - 3];
            $action = 'delete';
        }

        if ($action == 'delete') {
            $collectuseractivity = Config::get('siteconfigs.useractivity');
            $collectuseractivitykeys = array_keys($collectuseractivity);

            if (in_array($module, $collectuseractivitykeys)) {
                $field_name = $collectuseractivity[$module]['field_name'];
                $table = $collectuseractivity[$module]['table'];
                $parent_module = $collectuseractivity[$module]['parent'];
                $child_module = $collectuseractivity[$module]['child'];

                if (Session::get('practice_dbid') != '') {
                    $this->configureConnectionByName($current_db);
                    $get_column_name = DB::connection($current_db)->table($table)->select('*')->where('id', '=', $id)->first();
                } else {
                    $get_column_name = DB::table($table)->select('*')->where('id', '=', $id)->first();
                }

                if ($get_column_name) {
                    $get_name = $get_column_name->$field_name;

                    if ($module == 'managecare' || $module == 'facilitymanagecare' || $module == 'providermanagecare')
                        $get_name = '';
                    elseif ($module == 'budgetplan') {
                        $get_detail = DB::connection($current_db)->table('patients')->select('last_name', 'middle_name', 'first_name')->where('id', '=', $get_name)->first();
                        $get_name = $get_detail->last_name . ', ' . $get_detail->first_name . ' ' . $get_detail->middle_name;
                        $get_url = explode("budgetplan/", $fetch_url);
                        $fetch_url = $get_url[0] . 'budgetplan';
                    } elseif ($module == 'billing') {
                        $get_url = explode("billing/", $fetch_url);
                        $fetch_url = $get_url[0] . $get_url[1] . '/billing';
                    }

                    $this->user_activity($parent_module, $action, $get_name, $fetch_url, $child_module);
                }
            }
        }
        

        //Session forgot for charges choosen providers starts
        /*$curr_route = Route::getCurrentRoute()->getPath();

        if (stripos($curr_route, "charges") === FALSE && Session::has('charge_var') && !Request::ajax()) {
            Session::forget('charge_var');
        }*/
        //Session forgot for charges choosen providers ends
    }

    public function getPatientNotes()
    {   
        if(Auth::check() && strpos(Route::current()->uri(),'patients/') !== FALSE) {
            $patient_id = Route::current()->parameter('id');
            $alert_content = Helpers::getPatientNote($patient_id);
        } else {
            $alert_content = "";
        }
        return $alert_content;
       
    }

    protected function formatValidationErrors(Validator $validator) {
        return $validator->errors()->all();
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

    public function user_activity($parent_module, $action, $name, $fetch_url = '', $child_module = '') {
        $url = Request::url();
        $user = Auth::user();
        $user_type = $user->user_type;
        $id = $user->id;
        $username = $user->name;
        $split_url = explode("/", $url);
        $name = addslashes($name);
        $message = '';
        if ($action == 'delete')
            $message = $name . ' ' . $child_module . ' deleted by ' . $username;
        if ($action == 'add'){
            if($name=="Responsibility") {
                $message = 'Claim No. '.str_pad((isset($_POST['claimId']) ? $_POST['claimId'] : $_POST['claim_id']), 5, '0', STR_PAD_LEFT).' '.$name . ' ' . $child_module . ' changed by ' . $username;
            } elseif(end($split_url)=="getclaimstatusfinalnotesadded") {
                $message = 'Claim No. '.str_pad($_POST['curr_claim_val'], 5, '0', STR_PAD_LEFT).' '.$name . ' ' . $child_module . ' added by ' . $username;
            } elseif(end($split_url)=="notes") {
                if(isset($_POST['claim_id']) && isset($_POST['claim_id'][0])) {
                    $message = 'Claim No. '.str_pad($_POST['claim_id'][0], 5, '0', STR_PAD_LEFT).' '.$name . ' ' . $child_module . ' added by ' . $username;
                } else {
                    $nid = DB::table('patient_notes')->select('id')->orderBy('id','desc')->first();
                    $message = 'Patient '.$child_module . ' '.$nid->id.' added by ' . $username;
                }
            } elseif (isset($_POST['note_id']) && end($split_url)) {
                $message = Helpers::getEncodeAndDecodeOfId($_POST['note_id'], 'decode').' '.$name . ' ' . $child_module . ' status changed by ' . $username;
            } elseif ($parent_module=="Claim" && $child_module=="paper claim" || $child_module=="edi claim") {
                $message = $name . ' ' . $child_module . ' submitted by ' . $username;
            } else {
                $message = $name . ' ' . $child_module . ' added by ' . $username;
            }
        }
        
        if ($action == 'edit') {
            $message = $name . ' ' . $child_module . ' edited by ' . $username;
        }

        if ($action == 'status'){
            $message = $name . ' ' . $child_module . ' status changed by ' . $username;
            $action = 'edit';
        }

        if ($action == 'delete' or $action == 'add' or $action == 'edit') {
            $main = '';
            if (Session::get('practice_dbid') != '') {
                $main = Session::get('practice_dbid');
            } else {
                if (strpos($url, 'admin') !== false) {
                    $main = 'admin';
                }
            }

            $year = date("Y");
            $month = date("m");
            $day = date("d");

            if (Useractivity::whereRaw('userid="'.$id.'" and action="' . $action . '" and url="' . $fetch_url . '" and main_directory="' . $main . '" and module="' . $parent_module . '" and usertype="' . $user_type . '"  and user_activity_msg="' . $message . '" and activity_date="' . date('Y-m-d H:i:s').'"')->count() == 0)
                DB::connection('responsive')->insert("insert into `useractivity` (`userid`, `action`, `url`, `main_directory`, `module`, `usertype`, `user_activity_msg`, `activity_date`) values (" . $id . ", '" . $action . "', '" . $fetch_url . "', '" . $main . "', '" . $parent_module . "', '" . $user_type . "', '" . $message . "', '" . date('Y-m-d H:i:s') . "')");
        }
    }

    function __destruct() {
        $current_db = getenv('DB_DATABASE');
        if (Session::get('practice_dbid') != '') {
            //$practice = Practicedb::where('id', Session::get('practice_dbid'))->select('practice_name')->first();
            $session_id = Session::get('practice_dbid');
            $practice = Cache::remember('practice_details'.$session_id , 30, function() use($session_id) {
                $practice = Practicedb::where('id', $session_id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
                return $practice;
            });
            if ($practice != '') {
                $practice_name = $practice->practice_name;
                $practice_name = str_replace(' ', '_', $practice_name);
                $practice_name = str_replace(',', '', $practice_name);
                preg_replace('/[^A-Za-z0-9\-]/', '', $practice_name);
                $current_db = strtolower($practice_name);
            }
        } 

        $collectuseractivity = [];
        $url = Request::url();
        $split_url = explode("/", $url);

        $name = '';
        $action = '';
        if (isset($_POST['_method']) || isset($_POST['edit_activity'])) {
            $key = count($split_url) - 2;
            if(isset($_POST['edit_activity'])){
                $url = $_POST['edit_activity'];
                $split_url = explode("/", $url);
                $key = count($split_url) - 3;
            }
            $module = $split_url[$key];
            $id = end($split_url);
            if($split_url[4]=='charges')
            $id = $split_url[5];
            $fetch_url = $url;
            $action = 'edit';
            if ($split_url[4] == 'questionnaire')
                $module = 'questionnaire';
        }
        elseif (!empty($_POST) && end($split_url) != 'login') {
            if (in_array('patients', $split_url) == 1 && in_array('insurance', $split_url) == 1) {
                $module = 'patients';
            } elseif (in_array('patients', $split_url) == 1 && in_array('notes', $split_url) == 1) {
                $module = 'patients-notes';
            } elseif (in_array('charges', $split_url) == 1 && in_array('charges', $split_url) == 1) {
                $module = 'charges';
            } elseif (in_array('patients', $split_url) == 1 && in_array('send', $split_url) == 1) {
                $module = 'correspondence';
                $g_url = explode("/send", $url);
                $url = $g_url[0];
            } else {
                $module = end($split_url);
            }
            $action = 'add'; 
        }
        if (in_array('addtowallet', $split_url) == 1 && in_array('payments', $split_url) == 1) {
                $module = 'payments';
            }
            //dd($module);
        if ($action == 'add' or $action == 'edit') {
            $collectuseractivity = Config::get('siteconfigs.useractivity');
            $collectuseractivitykeys = array_keys($collectuseractivity);
            if (in_array($module, $collectuseractivitykeys)) {
                $field_name = $collectuseractivity[$module]['field_name'];
                $table = $collectuseractivity[$module]['table'];
                $parent_module = $collectuseractivity[$module]['parent'];
                $child_module = $collectuseractivity[$module]['child'];

                if ($action == 'edit') {
                    $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
                    if (Session::get('practice_dbid') != '') {
                        $get_column_name = DB::connection($current_db)->table($table)->select('*')->where('id', '=', $id)->first();
                    } else {
                        $get_column_name = DB::table($table)->select('*')->where('id', '=', $id)->first();
                    }
                }
                if ($action == 'add') {
                    if (Session::get('practice_dbid') != '') {
                        $get_column_name = DB::connection($current_db)->table($table)->select('*')->orderBy('id', 'desc')->first();
                    } else {
                        $get_column_name = DB::table($table)->select('*')->orderBy('id', 'desc')->first();
                    }
                    $get_id = !empty($get_column_name) ? Helpers::getEncodeAndDecodeOfId($get_column_name->id, 'encode') : '';
                    $fetch_url = $url . '/' . $get_id;
                }

                $get_name = isset($get_column_name->$field_name) ? $get_column_name->$field_name : '';

                if ($module == 'managecare' || $module == 'facilitymanagecare' || $module == 'providermanagecare')
                    $get_name = '';
                elseif ($module == 'budgetplan') {
                    $get_detail = DB::connection($current_db)->table('patients')->select('last_name', 'middle_name', 'first_name')->where('id', '=', $get_name)->first();
                    $get_name = $get_detail->last_name . ', ' . $get_detail->first_name . ' ' . $get_detail->middle_name;
                    $get_url = explode("budgetplan/", $fetch_url);
                    $fetch_url = $get_url[0] . 'budgetplan';
                } elseif ($module == 'billing') {
                    $get_url = explode("billing/", $fetch_url);
                    if(isset($_POST['edit_activity'])){
                        $fetch_url = $fetch_url;
                    } else{
                        $fetch_url = $get_url[0] . $get_url[1] . '/billing';
                    }
                } elseif ($module == 'notes') {
                    $get_facility = stripos($fetch_url, "facility");
                    $get_url = explode("notes/", $fetch_url);
                    $fetch_url = $get_url[0] . 'notes';
                    if ($get_facility) {
                        $parent_module = 'Facility';
                    }
                    $get_provider = stripos($fetch_url, "provider");
                    if ($get_provider) {
                        $parent_module = 'Provider';
                    }
                }
                if(isset($_POST['payment_type']) && $_POST['payment_type'] =='Credit Balance'){
                    $get_name = $_POST['claim_number'][0].' Credit Balance';
                    $child_module = '';
                }
                if(isset($_POST['payment_type']) && $_POST['payment_type'] =='Refund' && in_array('addtowallet', $split_url) == 1){
                    $get_name = $get_name.' Refund from wallet';
                    $child_module = '';
                }

                $this->user_activity($parent_module, $action, $get_name, $fetch_url, $child_module);
            }
        }
    }

}