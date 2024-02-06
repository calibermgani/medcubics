<?php
/*
     * This Function For Cutomer Report Code Updation
     * Author		: Kriti Srivastava
     * Created on	: 23August2021
	 * JIRA Id		: MEDV2-1431
     */
namespace App\Http\Controllers\Medcubics;
use Log;
use View;
use Config;
use Request;
use Session;
use Response;
use Redirect;
use Url;
use Auth;
use App\Models\Medcubics\Users as Users;
use DB;
use App\Models\Medcubics\Customer;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use App\Exports\BladeExport;
use App\Models\Payments\ClaimInfoV1 as ClaimInfoV1;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class ReportController   extends Api\CustomerApiController {
	
	 public function __construct() {
		View::share('heading','Report');  
		View::share('selected_tab','admin/report/customer');
		View::share('heading_icon', Config::get('cssconfigs.admin.report'));
    }  
	public function index() {
		 $customers = Customer::orderBy('customer_name', 'ASC')->pluck('customer_name', 'id')->all();	
		 return view('admin.report.report',compact('customers','practices','providers'));						          
		  }	  
		
	 public function show_practice($id)
	 {	
		 $customer_data = DB::table("practices")			
							->where('customer_id',$id)
							->pluck('practice_name','id')
							->toArray();
		 return response()->json($customer_data);
			
	 }
	 
	 public function show_practice_data($id)
	 {	
		$date_range = $_REQUEST['transaction_date'];		
		$customer_practice = Practice::where('id', $id)->value('practice_name');
		/*PRACTICES DATABASE CONNECTVITY*/
        $dbconnection = new DBConnectionController();		
        $practice_db_name = $dbconnection->getpracticedbname($customer_practice);
		$dbconnection->configureConnectionByName($practice_db_name);
        Config::set('database.default', $practice_db_name);
		$end_split = explode('-',$date_range);
		$start_date= $end_split[0];
		$end_date=$end_split[1];
		$s_date = date('Y-m-d',strtotime($start_date));
		$e_date = date('Y-m-d',strtotime($end_date));
		$claim_cnt = DB::table('claim_info_v1')->whereDate('created_at','>=',$s_date)->whereDate('created_at','<=',$e_date)->count();
		$payment = DB::table('pmt_info_v1')->whereDate('created_at','>=',$s_date)->whereDate('created_at','<=',$e_date)->count();		
		$facilty = Facility::whereDate('created_at','>=',$s_date)->whereDate('created_at','<=',$e_date)->count();
		$provider = DB::table('providers')->whereDate('created_at','>=',$s_date)->whereDate('created_at','<=',$e_date)->count();		
		$patients = DB::table('patients')->whereDate('created_at','>=',$s_date)->whereDate('created_at','<=',$e_date)->count();	
		return response()->json(['customer_practice'=>$customer_practice,
					'patients'=>$patients,
					'claim_cnt'=>$claim_cnt,
					'payment'=>$payment,
					'provider'=>$provider,
					'facilty'=>$facilty]);
	
	
	 }
}
