<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use View;
use Config;

class SetPracticeforUsersController extends Api\SetPracticeforUsersApiController {	
	
	public function __construct() {
        View::share ( 'heading', 'Customer' );  
		View::share ( 'selected_tab', 'admin/customerpractices' );
		View::share( 'heading_icon', Config::get('cssconfigs.admin.users'));
    }  
	
	public function index($customer_id,$customer_user_id)
	{
            $api_response = $this->getIndexApi($customer_id,$customer_user_id);
			$api_response_data = $api_response->getData();
			$practices = $api_response_data->data->practices;			
            $customerusers = $api_response_data->data->customerusers;
            return view('admin/customer/setpracticeforusers/setpracticeforusers', compact('customerusers','customer_id','customer_user_id','practices'));
	}
	
	function myfunction($num)
	{
	  return(Helpers::getEncodeAndDecodeOfId($num,'encode'));
	}
		
	public function create($customer_id,$customer_user_id)
	{			
			$api_response = $this->getCreateApi($customer_id,$customer_user_id);
			$api_response_data = $api_response->getData();
			$practices = $api_response_data->data->practices;
			$practices 	 = array_flip(json_decode(json_encode($practices), True)); 
			$practices	 = array_flip(array_map(array($this,'myfunction'),$practices));
			
			$roles = $api_response_data->data->roles;

			$roles 	 = array_flip(json_decode(json_encode($roles), True)); 
			$roles	 = array_flip(array_map(array($this,'myfunction'),$roles));
			
			$role_id = $api_response_data->data->role_id;
			$practice_id = $api_response_data->data->practice_id;
			$menus = $api_response_data->data->menus;		
			$modules = $api_response_data->data->module;			
            $customerusers = $api_response_data->data->customerusers;
			$returnHTML =  view('admin/customer/setpracticeforusers/setpracticecreate', compact('customerusers','role_id','practice_id','customer_id','customer_user_id','practices','roles','pagepermissions', 'menus', 'modules'))->render();
			return response()->json( array('success' => true, 'html'=>$returnHTML) );	
	}	 
	
	public function store(Request $request)
	{ 
		$request_data = $request::all();		
        $api_response = $this->getStoreApi($request_data);
		$api_response_data = $api_response->getData();		
		if($api_response_data->status == 'success')
			{
				return Redirect::to('admin/customer/'.$request_data['customer_id'].'/customerusers/'.$request_data['user_id'].'/setpracticeforusers')->with('success', $api_response_data->message);
			}
		else
			{
				return Redirect::to('admin/customer/'.$request_data['customer_id'].'/customerusers/'.$user_id.'/setpracticeforusers/create')->withInput()->withErrors($api_response_data->message);
			}      
              
	}
	
	public function edit($customer_id,$user_id,$practice_id)
	{ 
        $api_response = $this->getEditApi($customer_id,$user_id,$practice_id);
		$api_response_data = $api_response->getData();		
		$practice_permissions = $api_response_data->data->practice_permissions;
		$practices = $api_response_data->data->practices;
		$roles = $api_response_data->data->roles;	
		$roles 	 = array_flip(json_decode(json_encode($roles), True)); 
		$roles	 = array_flip(array_map(array($this,'myfunction'),$roles));
		
		$role_id = $api_response_data->data->role_id;
		$role_id = Helpers::getEncodeAndDecodeOfId($role_id,'encode');	
		$menus 	 = $api_response_data->data->menus;			
        $customerusers = $api_response_data->data->customerusers;
		$getActivePracticeAPI = $api_response_data->data->getActivePracticeAPI;
		
		$apilist = $api_response_data->data->apilist;
		$practice = $api_response_data->data->practice;
		$practice_id = $api_response_data->data->practice_id;
		$Setapiforusers = $api_response_data->data->Setapiforusers;
		$api_name 		= json_decode(json_encode($api_response_data->data->api_name), True);
		$apilist_subcat = $api_response_data->data->apilist_subcat;
		$modules = $api_response_data->data->module;	

		$html =  view('admin/customer/setpracticeforusers/setpracticeedit', compact('apilist','practice','Setapiforusers','customerusers','customer_id','user_id','practice_id','role_id','pagepermissions','practice_permissions','practices','roles', 'menus','getActivePracticeAPI','apilist_subcat','api_name', 'modules'))->render();
		return response()->json(array('success' => true, 'html'=>$html));

	}

	
    /////////////////////////////////
	
	
	public function update($customer_id,$user_id,$practice_id,Request $request)
	{
		$api_response = $this->getUpdateApi($customer_id,$user_id,$practice_id,Request::all());
		$api_response_data = $api_response->getData();	

		if($api_response_data->status == 'success')
			{
				return Redirect::to('admin/customer/'.$customer_id.'/customerusers/'.$user_id.'/setpracticeforusers')->with('success',$api_response_data->message);
			}
		else
			{
				return Redirect::to('admin/customer/'.$customer_id.'/customerusers/'.$user_id.'/setpracticeforusers/'.$practice_id.'/edit')->withInput()->withErrors($api_response_data->message);
			}        
	}

	
    /////////////////////////////////
	
	
	public function destroy($user_id, $practice_id,$customer_id)
	{
		$api_response = $this->getDestroyApi($user_id, $practice_id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/customer/'.$customer_id.'/customerusers/'.$user_id.'/setpracticeforusers')->with('success', $api_response_data->message);
	}
	/*
        public function getImport()
	{
		$table_name = "icd_09";
		$mysqlconn = DB::Connection('mysql');
		$results = $mysqlconn->select('DESC '.$table_name . ';');
		$fields = [];
		$neglectedfields = ['id','created_at','updated_at'];
		foreach ($results as $result){
			if(!in_array($result->Field, $neglectedfields)){
				$fields[] = $result->Field;
			}
		}
		$fields = array_flatten($fields);
		
		$delimiters = array(''=>'Select','tab'=>'Tab', '|'=>'Pipe', ','=>'Comma');
		return view('admin/icd/import-09/upload',['delimiters' => $delimiters, 'fields'=>$fields]);
	}*/
	public function practiceUser($customer_id,$practice_id)
	{ 
		$api_response = $this->practiceUserApi($customer_id, $practice_id);
		$api_response_data = $api_response->getData();
		$setpracticeforusers = $api_response_data->data->setpracticeforusers;
		$practice = $api_response_data->data->practice;
		$selected_tab= 'users';
		return view('admin/customer/customerusers/practiceuser', compact('setpracticeforusers','practice','customer_id','practice_id','selected_tab'));
	}
}
