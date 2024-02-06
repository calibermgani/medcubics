<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Roles as Roles;
use App\Models\Medcubics\Practice as Practices;
use App\Models\Medcubics\PagePermissions as PagePermissions;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\SetPagepermissions as SetPagepermissions;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Setapiforusers as Setapiforusers;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use Lang;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use DB;

class SetPracticeforUsersApiController extends Controller {
	
	public function getIndexApi($customer_id,$customer_user_id,$export = "")
	{ 
		$customer_id 		= Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$customer_user_id 	= Helpers::getEncodeAndDecodeOfId($customer_user_id,'decode');
		$user_practices		= Setpracticeforusers::where('user_id',$customer_user_id)->get();$customerusers 		= Users::find($customer_user_id);
        $practice_ids 		= array();
		foreach ($user_practices as $practice_id){
			$practice_ids[] = $practice_id->practice_id;
		}		
		$practices = Practices::with('user','update_user')->whereIn('id',$practice_ids)->get();		
		/*if($export != "")
		{
			$exportparam 	= 	array(
				'filename'=>	'Customer Medcubics',
				'heading'=>	'Customer Medcubics',
				'fields' =>	array(
								'customer_name'		=> 'Name',
								'customer_desc'		=> 'Desc',
								'customer_type' 	=> 'Type',
								'contact_person'	=> 'Person',
				));
			$export = new CommonExportApiController();
			return $export->generateExports($exportparam, $customers);
		}*/		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customerusers','customer_id','customer_user_id','practices')));
		
	}
	public function getCreateApi($customer_id,$customer_user_id)
	{

		$customers_id 		= Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$customer_users_id 	= Helpers::getEncodeAndDecodeOfId($customer_user_id,'decode');
		$already_set_practice = Setpracticeforusers::where('user_id', $customer_users_id)->pluck('practice_id')->all();
		$customerusers = Users::find($customer_users_id);
		$practice_admin_users = Users::where('id',$customer_users_id)->where('practice_user_type', 'practice_admin')->pluck('admin_practice_id')->all();
        // To get practice id (whose admin are choosen already) starts
        $practice_admin_users = array_filter($practice_admin_users);
		$practice_lists = '';
		if(!empty($practice_admin_users)){
			foreach($practice_admin_users as $customer_user){
			   $practice_lists.= is_array($customer_user) ? implode(',', $customer_user) : $customer_user.',';			  
		   }
		}

		$practice_id	= array_filter(explode(',',$practice_lists));
		// To get practice id (whose admin are choosen already) ends	
        $already_set_practice = array_merge($already_set_practice,$practice_id);
		$practices = Practices::orderBy('practice_name','ASC')->where('customer_id',$customers_id)->whereNotIn('id', $already_set_practice)->pluck( 'practice_name', 'id' )->all();
		$roles = []	;
		$role_lists = Roles::with('SetpracticePagePermissions')->whereHas('SetpracticePagePermissions', function($q){ 					
		})->select('id', 'role_name')->where('role_type', 'Practice')->get();

		foreach($role_lists as $role_list) {
               $roles[$role_list->id]  = $role_list->role_name;
		} 
		$menus = PagePermissions::groupby('menu')->orderBy('menu','ASC')->get();
		$module = PagePermissions::groupby('module')->orderBy('id','ASC')->get(); 
		//$menus = PagePermissions::groupby('menu')->orderBy('menu','ASC')->pluck('menu', 'id')->all();
		//dd($menus);
		$role_id = '';
		$practice_id = '';
		$practice_permissions = '';
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customerusers','role_id','practice_id','customer_id','customer_user_id','practices','roles','pagepermissions', 'menus', 'module')));
	}
	public function getrolespermissions()
	{
		$role_id = Helpers::getEncodeAndDecodeOfId(Request::input('selected_role_id'),'decode');		
		$page_permissions = SetPagepermissions::where('role_id',$role_id)->first();
		$role_page_permissions = $page_permissions->page_permission_id;
		$role_permissions_arr = explode(',',$role_page_permissions);		
        $role_permissions_arr = array_filter($role_permissions_arr);
		$page_permissions_details = array();
		foreach($role_permissions_arr as $val){
			$encoded_id = Helpers::getEncodeAndDecodeOfId($val,'encode');	
			$page_permission_info = PagePermissions::where('id',$val)->first();
			$page_permissions_details[] = $page_permission_info->menu.'_'.$page_permission_info->submenu.'_'.$page_permission_info->title.'|'.$encoded_id;			
		}
		return  $page_permissions_details;
	}
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		$request['customer_id']		= Helpers::getEncodeAndDecodeOfId($request['customer_id'],'decode');
		$request['user_id']			= Helpers::getEncodeAndDecodeOfId($request['user_id'],'decode');
		$validator = Validator::make($request, Setpracticeforusers::$rules, Setpracticeforusers::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{			
			$user_id = $request['user_id'];	
			$role_id = Helpers::getEncodeAndDecodeOfId( $request['role_id'],'decode');	
			$practice_id = Helpers::getEncodeAndDecodeOfId($request['practice_id'],'decode'); 	

			$page_permission_id = '';
			//Setpracticeforusers::where('role_id',$role_id)->forceDelete();				
			//Setpracticeforusers::where('user_id',$user_id)->where('practice_id', $practice_id)->forceDelete(); 
			//dd($request) ;				
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
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>''));					
		}
	}

	public function getEditApi($customer_id,$customer_user_id,$practice_id)
	{
		$customer_id 		= Helpers::getEncodeAndDecodeOfId($customer_id,'decode');
		$customer_user_id 	= Helpers::getEncodeAndDecodeOfId($customer_user_id,'decode');
		$practice_id	 	= Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$practice_permissions = Setpracticeforusers::where('practice_id',$practice_id)->where('user_id', $customer_user_id)->first();		
		$customerusers = Users::find($customer_user_id);
        // To display only the added pratice		
		$practices = Practices::orderBy('practice_name','ASC')->where('customer_id',$customer_id)->where('id', $practice_id)->pluck('practice_name', 'id' )->all();	
		// to get the role only who have page permission set by admin (To avaid 500 error)
		$role_lists = Roles::with('SetpracticePagePermissions')->whereHas('SetpracticePagePermissions', function($q){ 					
		})->select('id', 'role_name')->where('role_type', 'Practice')->get();
		$roles = [];
		foreach($role_lists as $role_list) {
               $roles[$role_list->id]  = $role_list->role_name;
		} 		
		$role_id = $practice_permissions->role_id;
		$menus = PagePermissions::groupby('menu')->orderBy('menu','ASC')->get();
		
		
		$practice 	 = Practices::where('id',$practice_id)->first();
		
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
	
	public function getUpdateApi($customer_id,$user_id,$practice_id,$request='')
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
	
	public function getDestroyApi($user_id, $practice_id)
	{
		$practice_id 	= Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$user_id 		= Helpers::getEncodeAndDecodeOfId($user_id,'decode');
		Setpracticeforusers::where('user_id', $user_id)->where('practice_id', $practice_id)->delete();
		Setapiforusers::where('user_id', $user_id)->where('practice_id', $practice_id)->delete();
		$dbconnection = new DBConnectionController();
		$dbconnection->create_APIJSON($practice_id);
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	
	public function updateUserInfoInpracticedb($practice_id, $user_id, $type)
	{
	   $practice_list = Practices::where('id',$practice_id)->select('id', 'practice_name')->first();
	   $dbconnection = new DBConnectionController();
	   $practice_db_name = $dbconnection->getpracticedbname($practice_list->practice_name);
	   $admin_database_name = getenv('DB_DATABASE');
	   if($type == 'insert') {     
	   		$insert= "INSERT INTO $practice_db_name.users SELECT * FROM $admin_database_name.users WHERE id=".$user_id;
	   } else {
	   	    $dbconnection->configureConnectionByName($practice_db_name);
	   		$insert= "DELETE FROM $practice_db_name.users WHERE id=".$user_id;
	   }
	   DB::statement($insert);   
	}/*
	public function setPracticeApi($practice_id)
	{
		$dbconnection = new DBConnectionController();
		$dbconnection->setSessionforDB($practice_id);
		return Response::json(array('status'=>'success', 'data'=>''));
	}
	
	public function setPracticeforUsersApi($customer_id,$customerusers_id)
	{		
		$practices = Practices::where('customer_id',$customer_id)->get();		
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practices')));
	}*/
	
	
	public function getUserAPI()
	{
		$practice_id = Helpers::getEncodeAndDecodeOfId(Request::input('practice_id'),'decode'); 

		$user_id 	 = Helpers::getEncodeAndDecodeOfId(Request::input('userid'),'decode');
		$practice 	 = Practices::where('id',$practice_id)->first();
		
		$dbconnection 	  = new DBConnectionController();
		//dd($practice);
		$practice_db_name = $dbconnection->getpracticedbname($practice->practice_name); 

		$dbconnection->configureConnectionByName($practice_db_name);
		
		// Get active practice api list.
		$getActivePracticeAPI = DB::connection($practice_db_name)->table('practice_api_list')->where('status', '=', 'Active')->whereNull('deleted_at')->pluck('api_id')->all();
		
		$Setapiforusers	= Setapiforusers::where('practice_id',$practice_id)->where('user_id',$user_id)->select(DB::raw('group_concat(api_id) AS api'))->first();

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
		
		return view('admin/customer/setpracticeforusers/apisettings', compact('getActivePracticeAPI','apilist','practice','apilist_subcat','Setapiforusers','api_name'));	
	}
	public function practiceUserApi($cus_id, $practice_id,$export="")
	{
		$practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode'); 
		$cus_id 	 = Helpers::getEncodeAndDecodeOfId($cus_id,'decode');
		$setpracticeforusers = Setpracticeforusers::with('practice','updatedBy','createdBy','user')->where('practice_id',$practice_id)->whereNull('deleted_at')->get();			
		$practice 		= Practice::with('taxanomy_details', 'speciality_details','languages_details')->where('id', $practice_id)->first();
		if($export != "") {
            $exportparam = array(
							'filename' 	=> 'Practice User',
							'heading' 	=> 'Practice User',
							'fields' 	=> array(
											'user_id' => array('table'=>'user' ,'column' => 'name' ,'label' => 'User Name'),
											'practice_id' => array('table'=>'practice' ,'column' => 'practice_name' ,'label' => 'Practice Name'),
											'created_by' => array('table'=>'createdBy' ,'column' => 'short_name' ,'label' => 'Created By'),
											'updated_by' => array('table'=>'updatedBy' ,'column' => 'short_name' ,'label' => 'Updated By'),
											'updated_at' 	=> 'Updated On',												
											)
								);
             $callexport = new CommonExportApiController();
             return $callexport->generatemultipleExports($exportparam, $setpracticeforusers, $export);
        }
		$practice->encid = Helpers::getEncodeAndDecodeOfId($practice_id,'encode');
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('setpracticeforusers','practice')));
			
	}
}