<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Roles as Roles;
use App\Models\Medcubics\PagePermissions as PagePermissions;
use App\Models\Medcubics\SetPagepermissions as SetPagepermissions;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Setapiforusers as Setapiforusers;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Http\Helpers\Helpers as Helpers;

use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use Lang;
use DB;


class SetUsersforPracticeApiController extends Controller {
	
	public function getIndexApi($customer_id,$practice_id,$customer_user_id)
	{	
		$customer_id 		= Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$practice_id 		= Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$customer_user_id 	= Helpers::getEncodeAndDecodeOfId($customer_user_id,'decode');
		//$practice_id 	= Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$user_practices		= Setpracticeforusers::where('user_id',$customer_user_id)->get();$customerusers 		= Users::find($customer_user_id);
		$practices = Practice::with('user','update_user')->where('id',$practice_id)->first();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customerusers','customer_id','customer_user_id','practices','practice_id')));
	}	
	
	public function getEditApi($customer_id,$practice_id,$customer_user_id)
	{
		$customer_id 		= Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$customer_user_id 	= Helpers::getEncodeAndDecodeOfId($customer_user_id,'decode');
		$practice_id	 	= Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$practice_permissions = Setpracticeforusers::where('practice_id',$practice_id)->where('user_id', $customer_user_id)->first();		
		$customerusers = Users::find($customer_user_id);
        // To display only the added pratice		
		$practices = Practice::orderBy('practice_name','ASC')->where('customer_id',$customer_id)->where('id', $practice_id)->pluck('practice_name', 'id' )->all();	
		// to get the role only who have page permission set by admin (To avaid 500 error)
		$role_lists = Roles::with('SetpracticePagePermissions')->whereHas('SetpracticePagePermissions', function($q){ 					
		})->select('id', 'role_name')->where('role_type', 'Practice')->get();
		$roles = [];
		foreach($role_lists as $role_list) {
               $roles[$role_list->id]  = $role_list->role_name;
		} 		
		$role_id = $practice_permissions->role_id;
		$menus = PagePermissions::groupby('menu')->orderBy('menu','ASC')->get();
		
		
		$practice 	 = Practice::where('id',$practice_id)->first();
		
		$dbconnection 	  = new DBConnectionController();
		$practice_db_name = $dbconnection->getpracticedbname($practice->practice_name); 
		$dbconnection->configureConnectionByName($practice_db_name);
		$getActivePracticeAPI = DB::connection($practice_db_name)->table('practice_api_list')->where('status', '=', 'Active')->whereNull('deleted_at')->pluck('api_id')->all();
		
		$apilist			= ApiConfig::where('api_status','Active')->groupBy("api_name")->pluck('api_name','id')->all();
		$api_name			= ApiConfig::where('api_status','Active')->pluck('category','id')->all();
		$maincat_api		= ['eligibile','apex','twilio'];
		$getAPIsettings 	= ApiConfig::select('id','api_for','api_name','category')->where('api_status','Active')->whereIn('api_name',$maincat_api)->get();	
		$apilist_subcat = [];
		// Get Sub Category API
		foreach($getAPIsettings as $mainapi){
			if($mainapi->api_for!='medicare_eligibility') {
				$apilist_subcat[$mainapi->api_name][$mainapi->id] = $mainapi->api_for;
			}
		}
		$module = PagePermissions::groupby('module')->orderBy('id','ASC')->get(); 
		$Setapiforusers	= Setapiforusers::where('practice_id',$practice_id)->where('user_id',$customer_user_id)->select(DB::raw('group_concat(api_id) AS api'))->first();
		$practice_id	 	= Helpers::getEncodeAndDecodeOfId($practice_id,'encode');
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('getActivePracticeAPI','apilist','practice','Setapiforusers','customerusers','role_id','practice_id','roles','practice_permissions','practices', 'menus','apilist_subcat','api_name', 'module')));
	}

	public function getUpdateApi($customer_id,$practice_id,$user_id,$request='')
	{	
		$customer_id 	= Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$user_id 		= Helpers::getEncodeAndDecodeOfId($user_id,'decode');
		$practice_id	= Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, Setpracticeforusers::$rules, Setpracticeforusers::$messages);

		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{				
			$role_id = Helpers::getEncodeAndDecodeOfId($request['role_id'],'decode'); 					
			$page_permission_id = '';
			Setpracticeforusers::where('practice_id',$practice_id)->where('user_id', $user_id)->forceDelete();				
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
			$data['user_id'] = $user_id;	
			$data['role_id'] = $role_id;
			$data['practice_id'] = $practice_id;
			$data['page_permission_ids'] = $page_permission_id;				
			$setpractice = Setpracticeforusers::create($data);
			$user = Auth::user ()->id;
			$setpractice->created_by = $user;
			$setpractice->save();	

			$get_API_list =  (isset($request['apilist'])) ? $request['apilist']: [];
			Setapiforusers::where('user_id', $user_id)->where('practice_id', $practice_id)->delete();
			if(count($get_API_list)>0) 
			{
				foreach($get_API_list as $key=>$val)
				{	
					$data['user_id'] 	 = $user_id;	
					$data['api_id'] 	 = $val;
					$data['practice_id'] = $practice_id;
					$data['created_by']  = Auth::user ()->id;
					Setapiforusers::create($data); 
				}
				$dbconnection = new DBConnectionController();
				$dbconnection->create_APIJSON($practice_id);
			}
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
	public function getDestroyApi($cus_id,$user_id, $practice_id)
	{
		$practice_id 	= Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$user_id 		= Helpers::getEncodeAndDecodeOfId($user_id,'decode');
		Setpracticeforusers::where('user_id', $user_id)->where('practice_id', $practice_id)->delete();
		Setapiforusers::where('user_id', $user_id)->where('practice_id', $practice_id)->delete();
		$dbconnection = new DBConnectionController();
		$dbconnection->create_APIJSON($practice_id);
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}

}
