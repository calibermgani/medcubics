<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use View;
use App\Models\Medcubics\UserIp as UserIp;
use App\Http\Helpers\Helpers as Helpers;
use Requests;
use DB;
use Lang;
use Session;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\SearchFields as SearchFields;
//use App\Models\Medcubics\searchUserData as searchUserData;

class UserLoginHistoryApiController extends Controller
{
	/*** Start to listing page ***/
	public function getIndexApi($type)
	{
		if($type == 'pendingApproval')
            $approved = 'No';
        else
            $approved = 'Yes';
        $practice_user_arr1 = Setpracticeforusers::pluck('user_id')->all();
        $practice_user_arr2 = Users::pluck('id')->all();
        
        $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
        $user_list = Users::whereIn('id', $practice_user_arr)->pluck('id')->all();
        $userLoginInfo = UserIp::with('user')->whereIn('user_id',$user_list)->where('approved',$approved)->get();
        if($type ==""){
            try{

                if(Request::path() == 'admin/userLoginHistory/settings'){
                    $search_details = SearchFields::where('page_name','security_code_settings')->select('id','search_fields','page_name')->first();
                }else{
                    $search_details = SearchFields::where('page_name','security_code')->select('id','search_fields','page_name')->first();                    
                }
                
                if(empty($search_details)){
                    // If not found search query in current page, get it from master settings.
                    if(Request::path() == 'admin/userLoginHistory/settings'){
                        $master_srch_det = SearchFields::on('responsive')->where('page_name','security_code_settings')->select('id','search_fields','page_name')->first();
                    }else{
                        $master_srch_det = SearchFields::on('responsive')->where('page_name','security_code')->select('id','search_fields','page_name')->first();
                    }
                    if(empty($master_srch_det)){                        
                        // Redirect to dashboard if search not defined in master settings.
                        return Response::json(array('status' => 'error', 'message' => 'Search not defined. Please contact administrator', 'data'=>[]));     
                        exit;
                    } else {
                        $dataArr['search_fields'] = $master_srch_det['search_fields'];
                        $dataArr['page_name'] = $master_srch_det['page_name'];                              
                        $details = SearchFields::create($dataArr);
                        $search_details = $details;
                        $searchUserData = [];
                    }           
                }  else  {
                    $searchUserData = [];
                }     
                return Response::json(array('status' => 'success', 'message' => '','data'=>compact('search_details','searchUserData')));
            } catch (Exception $e) {
                return Response::json(array('status' => 'error', 'message' => $e->getMessage(), 'data'=>[]));
            }
        }
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('userLoginInfo')));
	}
	/*** End to listing page ***/

	public function getHistoryApi($type)
	{
		$request = Request::all();
		if($type == 'pendingApproval')
			$approved = 'No';
		else
			$approved = 'Yes';
		$start = (isset($request['start'])) ? $request['start'] : 0;
        $len = (isset($request['length'])) ? $request['length'] : 50;
        $historyqry = UserIp::join('users','users.id','=','user_ip.user_id')->select('customers.short_name as customer','users.id as userid','users.short_name as user','users.email as email','users.status as status','users.admin_practice_id as admin_practice_id','user_ip.*');
        $search = (!empty($request['search']['value'])) ? trim($request['search']['value']) : "";
        $orderByField = 'user_ip.updated_at';
        $orderByDir = 'DESC';
        if (!empty($request['order'])) {
            $orderByField = (isset($request['order'][0]['column'])) ? $request['order'][0]['column'] : $orderByField;
        // dd($orderByField);
            switch ($orderByField) {
                case '0':
                    $orderByField = 'users.short_name';
                    break;

                case '1':
                    $orderByField = 'users.email';
                    break;

                case '2':
                    $orderByField = 'customers.short_name';                   
                    break;

                case '3':
                    $orderByField = 'practices.id';                
                    break;

                case '4':
                    $orderByField = 'user_ip.security_code';
                    break;

                case '5':
                    $orderByField = 'user_ip.ip_address';                     
                    break;

                case '6':
                    $orderByField = 'user_ip.approved';                
                    break;

                case '7':
                    $orderByField = 'user_ip.security_code_attempt';                    
                    break;

                case '8':
                    $orderByField = 'user_ip.updated_at';                 
                    break;

                default:
                    $orderByField = 'user_ip.updated_at';
                    break;
            }

            $orderByDir = ($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'DESC';
        }

        $historyqry->join('customers', function($join) {
            $join->on('customers.id', '=', 'users.customer_id');
        });

        $historyqry->leftjoin('practices', function($join) {
            $join->on('practices.customer_id', '=', 'customers.id');
        });

        if (!empty(json_decode(@$request['dataArr']['data']['Users'])) && (json_decode(@$request['dataArr']['data']['Users'])) != "null" ) {
            $historyqry->whereIn('users.id', json_decode($request['dataArr']['data']['Users']));
        }
        if (!empty(json_decode(@$request['dataArr']['data']['Practice'])) && (json_decode(@$request['dataArr']['data']['Practice'])) != "null" ) {
            $historyqry->whereIn('practices.id', json_decode($request['dataArr']['data']['Practice']));
        }
        if (!empty(json_decode(@$request['dataArr']['data']['Customer'])) && (json_decode(@$request['dataArr']['data']['Customer'])) != "null" ) {
            $historyqry->whereIn('customers.id', json_decode($request['dataArr']['data']['Customer']));
        }
        if(!empty(json_decode(@$request['dataArr']['data']['Email']))){
            $email = json_decode($request['dataArr']['data']['Email']);
            $historyqry->Where(function ($historyqry) use ($email) {
                $historyqry->Where(function ($query) use ($email) {
                    $historyqry = $query->orWhere('users.email','like', "%{$email}%");
                });
            });
        }
        if(!empty(json_decode(@$request['dataArr']['data']['Date and Time of Attempt']))){
            $date = explode('-',json_decode($request['dataArr']['data']['Date and Time of Attempt']));
            $from = date("Y-m-d", strtotime($date[0]));
            if($from == '1970-01-01'){
                $from = '0000-00-00';
            }
            $to = date("Y-m-d", strtotime($date[1]));
            $historyqry->where(DB::raw('DATE(user_ip.updated_at)'),'>=',$from)->where(DB::raw('DATE(user_ip.updated_at)'),'<=',$to);
        }
        $historyqry->where('user_ip.approved', $approved)->groupBy('security_code');
        $historyqry->orderBy($orderByField,$orderByDir);
        
        $userLoginInfo = $historyqry->get();
        $count = count($userLoginInfo->toArray());
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('userLoginInfo','count')));
    }
    
    public function getSecurityCodeSettingApi(){

        $usersettingInfo = Users::with(['customer'=>function($query){ $query->select('id','customer_name','short_name');},'adminPracticeId'=>function($query){ $query->select('id','practice_name'); }])
        ->where('security_code','No')->select('practice_user_type','customer_id','admin_practice_id','id','name')->get();
        $count = count($usersettingInfo->toArray());
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('usersettingInfo','count')));
    }

	public function userStatusChangeApi(){
		$request = Request::all();
		Users::where('id',$request['user_id'])->update(['status'=>$request['status']]);
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
	}
	
	public function userIpSecurityCodeRestApi(){
		$request = Request::all();
		UserIp::where('id',$request['userip_id'])->update(['security_code_attempt'=>'0']);
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>''));
    }

    public function givApprovalApi(){
        $request = Request::all();
        if(isset($request['user_type'])){            
            $user_type = json_decode($request['user_type']);
            Users::whereIn('practice_user_type',$user_type)->update(['security_code'=>'No']);
            return Response::json(array('status'=>'success','message'=>null,'data'=>''));
        }else{
            $user_id = json_decode($request['sel_user_id']);
            if(in_array("0", $user_id) || $user_id == []) {
                $prac_id = json_decode($request['sel_prac_id']);
                Users::whereIn('admin_practice_id',$prac_id)->update(['security_code'=>'No']);
                return Response::json(array('status'=>'success','message'=>null,'data'=>''));
            }else{
                Users::whereIn('id',$user_id)->update(['security_code'=>'No']);
                return Response::json(array('status'=>'success','message'=>null,'data'=>''));
            }
        }

    }
    
    public function removeApprovalApi(){
        $request = Request::all();
        $user_id = $request['user_id'];
        Users::where('id',$user_id)->update(['security_code'=>'Yes']);
        return Response::json(array('status'=>'success','message'=>null,'data'=>''));
    }
	
	
	function __destruct() 
	{
    }
}