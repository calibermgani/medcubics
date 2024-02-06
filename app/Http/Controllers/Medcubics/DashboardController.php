<?php

namespace App\Http\Controllers\Medcubics;

use App\Http\Controllers\Controller;
use Request;
use Response;
use Input;
use Redirect;
use Auth;
use Session;
use View;
use App\Models\Practice as Practice;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Insurance as Insurance;
use App\Models\Medcubics\Modifier as Modifier;
use App\Models\Medcubics\Code as Code;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Medcubics\Icd as Icd;
use App\Models\Medcubics\Speciality as Speciality;
use App\Models\Medcubics\Users as Users;
use DB;
use Carbon\Carbon;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Profile\UserLoginHistory as UserLoginHistory;
use App\Http\Controllers\Medcubics\Api\LogApiController;
use App\Http\Controllers\Medcubics\Api\UserLoginHistoryApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class DashboardController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct(LogApiController $LogApiController,UserLoginHistoryApiController $userloginhistory) {
        View::share('heading', 'Dashboard');
        View::share('selected_tab', 'dashboard');
        View::share('heading_icon', 'dashboard');
        $this->LogApiController = $LogApiController;
        $this->userloginhistory = $userloginhistory;
    }

    public function index() {
        //Session::put('practice_dbid','');
        $customers = Customer::latest('updated_at')->first();
        // dd($customers);
        $cus_count = Customer::count();
        $insurance = Insurance::latest('updated_at')->first();
        $insurance_count = Insurance::count();
        $modifier = Modifier::latest('updated_at')->first();
        $modifier_count = Modifier::count();
        $codes = Code::latest('updated_at')->first();
        $codes_count = Code::count();
        $cpt = Cpt::latest('updated_at')->first();
        $cpt_count = Cpt::count();
        $icd = Icd::latest('updated_at')->first();
        $icd_count = Icd::count();
        $speciality = Speciality::latest('updated_at')->first();
        $speciality_count = Speciality::count();
       /*practices database connectvity*/
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $practices = Practice::pluck('practice_name', 'id')->all();
        $pra_list = [];
        foreach ($practices as $pra) {
            $tenantDBs = str_replace(' ', '_', strtolower($pra));
            $stats = DB::select($query, [$tenantDBs]);
            $pra_list[$pra] = (empty($stats)) ? "In Progress" : "Active";             
        }
        
        //$practice = Practice::get();
        //logged in users count
        $users = Users::where('is_logged_in', '1')->get();
        $users_count = $users->count();
        //tickets count
        $tickets_count = DB::table('ticket')->where('status', 'Open')->count();
        //time difference last err log created
        $log_path = storage_path('logs/');
        $getLastErrLogCreated = date("m/d/y h:i:s", filemtime($log_path));
        $lastErrLogCreated = Carbon::parse($getLastErrLogCreated);
        $diffLastErrLogCreated = $lastErrLogCreated->diffForHumans();       
        $api_response =  $this ->userloginhistory->getIndexApi('pendingApproval'); 
        $api_response_data = $api_response->getData();        
        $userLoginInfo = @$api_response_data->data->userLoginInfo;
      
        return view('admin/dashboard/index', 
                compact('practices', 'users', 'users_count', 'diffLastErrLogCreated', 'tickets_count', 'pra_list',
                        'customers','cus_count','insurance','insurance_count','modifier','modifier_count',
                        'codes','codes_count','cpt','cpt_count','icd','icd_count','speciality','speciality_count','userLoginInfo'));
    }

    public function practiceStatsDashboard() {
        $practices = Practice::where('status', 'Active')->pluck('practice_name', 'id')->all();
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $pra_list = $practices_names = [];
        foreach ($practices as $pra) {
            $tenantDBs = str_replace(' ', '_', strtolower($pra));
            $stats = DB::select($query, [$tenantDBs]);
            $pra_list[$pra] = (empty($stats)) ? "In Progress" : "Active";             
            if(!empty($stats))
                $practices_names[$pra] = $pra;
        }
        //$practices_names 	= array_keys($pra_list);
        $dbconnection       = new DBConnectionController();
        $practice_details	= $dbconnection->getPracticeStatsDetail($practices_names);  
        return view('admin/dashboard/practice_statistics',compact('pra_list', 'practice_details'));
    }

    public function view_log($file_name){ 
        $api_response = $this->LogApiController->getViewLogApi($file_name);     
        $api_response_data = $api_response->getData();
        $file_content =  $api_response_data->data->file_content;
        return $file_content; 
    }

    public function get_recent_errors(){ 
        $log_path = storage_path('logs/');
        $log_data = array();
        $count = 0;       
        foreach(glob($log_path."*.log") as $list){
            $filename = explode($log_path,$list);
            $log_data[$count]['file_name'] = $filename[1];
            $log_data[$count]['file_date'] = date("m/d/y",strtotime(substr($filename[1],8,10)));
            $log_data[$count]['file_created_time'] = date("m/d/y h:i:s",filectime($list));
            $log_data[$count]['file_last_update'] = date("m/d/y h:i:s",filemtime($list));
            $log_data[$count]['file_size'] = filesize($list);
            $count++;
        } 
        $file_content = $this->view_log($filename[1]);
        return view('admin/log/show',  compact('file_content'));
    }

    public function viewErrorLog($file_name, $errType = 'error'){         
        $api_response = $this->LogApiController->getViewErrorLogApi($file_name, $errType);     
        $api_response_data = $api_response->getData();
        $file_content =  $api_response_data->data->file_content;
        return $file_content; 
    }
   
    public function getRecentErrorLog(){ 
        $request = Request::all();
        $type = (isset($request['type']) && $request['type'] == 'json') ? 'json' : 'html';
        $logType = (isset($request['log']) && $request['log'] <> '') ? $request['log'] : 'error';
        
        // Have to exclude testing practices        
        $ex_cust_ids = [];
        $ex_cust_ids = DB::connection('responsive')->table('customers')
                        ->where('customer_name', 'Demo Customer')
                        ->orWhere('customer_name', 'Testing Customer')
                        ->pluck('id')->all();
        // Users Details
        $users['all'] = DB::connection('responsive')->table('users')->whereNotIn('customer_id',$ex_cust_ids)->count();
        $users['active'] = DB::connection('responsive')->table('users')->where('status', 'Active')->whereNotIn('customer_id',$ex_cust_ids)->count();       
        $users['percentage'] = ceil((@$users['active'] * 100) / @$users['all']).'%';

        // Provider Details
        $providers['all'] = DB::connection('responsive')->table('providers')->whereNotIn('customer_id',$ex_cust_ids)->count();
        $providers['active'] = DB::connection('responsive')->table('providers')->whereNotIn('customer_id',$ex_cust_ids)->where('status', 'Active')->count();    
        $providers['percentage'] = ceil((@$providers['active'] * 100) / @$providers['all']).'%';

        $log_path = storage_path('logs/');
        $log_data = array();
        $count = 0;       
        // To get the last created / updated file 
        $files = array_merge(glob($log_path."*.log"), glob($log_path."*.log"));
        $files = array_combine($files, array_map("filemtime", $files));
        arsort($files);
        $latest_file = key($files);
        $filename = explode($log_path, $latest_file); 
        $file_content = $this->viewErrorLog($filename[1], $logType);           
        if($type == 'html') {
            $view_html = Response::view('admin/log/logMessage',  compact('file_content'));
            $content_html = $view_html->getContent(); 
            //dd($content_html);     
            return Response::json($content_html);
        } else {        
            $resp['log'] = $file_content;
            $resp['users'] = $users;
            $resp['providers'] = $providers;
            $array = json_decode(json_encode($resp));    //json_decode(json_encode($file_content));    
            return Response::json($array);
        }
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        //
    }
    
    //update user logout in admin dashboard
    public function updateUserLogout() {
        $data = Request::all();
        $user_logout = DB::table('users')
                ->where('id', $data['id'])
                ->update(['is_logged_in' => '0']);
        $session_login_id = Session::get('login_session_id');
        if ($session_login_id != '' && $session_login_id != null) {
            $session_id = Helpers::getEncodeAndDecodeOfId($session_login_id, "decode");
            $get_login_qry = explode("::::", $session_id);
            if (count($get_login_qry) > 1)
                $current_login = UserLoginHistory::where("user_id", $get_login_qry[1])->where("created_at", $get_login_qry[0])->update(array('logout_time' => date('Y-m-d H:i:s')));
        }

        return ;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

}
