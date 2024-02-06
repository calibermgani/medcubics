<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\Facility as Facility;
use App\Models\Medcubics\Provider as Provider; 
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Language as Language;
use App\Models\Medcubics\Ethnicity as Ethnicity;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Setapiforusers as Setapiforusers;

use Auth;
use Response;
use Request;
use Validator;
use Input;
use Hash;
use Config;
use Lang;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Medcubics\Api\SetPracticeforUsersApiController as SetPracticeforUsersApiController;

class CustomerUsersApiController extends Controller 
{
	
	/********************** Start Display a listing of the customer users ***********************************/
	public function getIndexApi($cust_id,$export='')
	{	
		$cust_id = Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
		if(Customer::where('id', $cust_id )->count())
		{
			$customerusers 	= Users::with('customer','ethnicity','language','user','userupdate')->where('customer_id',$cust_id)->where('practice_user_type', '!=', 'customer')->orderBy('id','DESC')->get();		
			$customer 		= Customer::where('id',$cust_id)->first();
			$language 		= Language::all();
			$ethnicity 		= Ethnicity::all();
			$tabs 			= "yes";
			if($export != "")
			{
				$exportparam 	= 	array(
									'filename'		=>	'Customer Users',
									'heading'		=>	'Customer Users',
									'fields' 		=>	array(
														'name'			=>	array('table'=>''	,'column' =>array('lastname','firstname'),	'label' => 'Name'),
														'useraccess'	=>	'User Type',
														'designation'	=> 	'Designation',
														'department'    => 	'Department',
														'phone'			=>	'Cell Phone',
														'email'			=>	'Email',
														'language_id'	=>	array('table'=>'language'		,	'column' => 'language'		,	'label' => 'Language'),
														'ethnicity_id'	=>	array('table'=>'ethnicity'		,	'column' => 'name',	'label' => 'Ethnicity'),
														)
									);
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $customerusers, $export);
			}
			// $customer->id = Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customer','customerusers','language','ethnicity','tabs')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
		}
	}
	/********************** End Display a listing of the customer users ***********************************/
	
	/********************** Start Display the customer user create page ***********************************/
	public function getCreateApi($cust_id)
	{	
        $cust_id = Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
		if(Customer::where('id', $cust_id )->count())
		{
			$customer 		= customer::where('id', $cust_id)->first();
			$language 		= Language::orderBy('language','ASC')->pluck('language', 'id')->all();
			$language_id 	= 5;
			$ethnicity 		= Ethnicity::orderBy('name','ASC')->pluck('name', 'id')->all();
			$ethnicity_id 	= '';
			$tabs 			= "yes";
			$addressFlag['general']['address1'] = '';
			$addressFlag['general']['city'] 	= '';
			$addressFlag['general']['state'] 	= '';
			$addressFlag['general']['zip5'] 	= '';
			$addressFlag['general']['zip4'] 	= '';
			$addressFlag['general']['is_address_match'] = '';
			$addressFlag['general']['error_message'] 	= '';
			$customer_practices = array();
			// To get practice id (whose admin are choosen already) starts
			$admin_user_db_ids = Users::where('customer_id', $cust_id)->where('practice_user_type', 'practice_admin')->pluck('admin_practice_id')->all(); 
			$customer_admin_users 	= array_filter($admin_user_db_ids);
			$practice_lists 		= '';
			$facility= "";
			$provider= "";
			if(!empty($customer_admin_users))
			{
				foreach($customer_admin_users as $admin_user)
				{
					$practice_lists.= is_array($admin_user) ? implode(',', $admin_user) : $admin_user.',';			  
				}
			}
			$practice_id		= array_filter(explode(',',$practice_lists));
			$customer_practices = Practice::where('customer_id', $cust_id)->pluck('practice_name', 'id')->all();
			$customer_practices_list = Practice::where('customer_id', $cust_id)->pluck('practice_name', 'id')->all();
			// To get practice id (whose admin are choosen already) ends
			$customer->encid = Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customer','language','language_id','ethnicity','ethnicity_id','addressFlag','tabs', 'customer_practices','facility','customer_practices_list', 'provider')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
		}
	}
	/********************** End Display the customer user create page ***********************************/

	/********************** Start customer user added process ***********************************/
	/*	public function getStoreApi($cust_id, $request='')
	{		
		if($request == '')
			$request = Request::all();
        if(isset($request['admin_practice_id']))
		{	
			$request['admin_practice_id'] = implode(',', $request['admin_practice_id']);
		}
        $validation_rule = Users::$rules+array('email' 	=> 'required|unique:users,email|email','password' => 'required', 'confirmpassword' => 'required|same:password');
        $validator = Validator::make($request, $validation_rule, Users::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			if (Input::hasFile('filefield'))
			{ 
				$image              = Input::file('filefield');
				$filename           = rand(11111,99999);
				$extension          = $image->getClientOriginalExtension();
				$filestoreName      = $filename .'.'.$extension;
				$resize             = array('150','150');
				Helpers::mediauploadpath('admin','user',$image,$resize,$filestoreName); 
				$request['avatar_name']  = $filename;
				$request['avatar_ext']   = $extension;
			}	
			if($request['dob'] != '')
				$request['dob'] = date('Y-m-d', strtotime($request['dob']));		
			$request['password'] 	= Hash::make($request['password']);		
			$request['name'] 		= $request['lastname'].' '.$request['firstname'];
			$customerusers 			= Users::create($request);
			$user 					= Auth::user ()->id;
			$customerusers->created_by = $user;
			$customerusers->avatar_name = isset($request['avatar_name'])?$request['avatar_name']:'';
			$customerusers->avatar_ext = isset($request['avatar_ext'])?$request['avatar_ext']:'';
			$customerusers->save ();
			

			if(isset($request['admin_practice_id']) && config('app.is_enable_provider_add'))
			{
				$admin_practice_ids = explode(',',$request['admin_practice_id']); 
				
				$setpractice = new SetPracticeforUsersApiController();
				foreach($admin_practice_ids as $admin_practice_id)
				{
					$setpractice->updateUserInfoInpracticedb($admin_practice_id, $customerusers->id, 'insert');	
				}                
				
			}
			
						
			$address_flag = array();
			$address_flag['type'] 			= 'customerusers';
			$address_flag['type_id'] 		= $customerusers->id;
			$address_flag['type_category'] 	= 'general_information';
			$address_flag['address2'] 		= $request['general_address1'];
			$address_flag['city'] 			= $request['general_city'];
			$address_flag['state'] 			= $request['general_state'];
			$address_flag['zip5'] 			= $request['general_zip5'];
			$address_flag['zip4'] 			= $request['general_zip4'];
			$address_flag['is_address_match'] 	= $request['general_is_address_match'];
			$address_flag['error_message'] 		= $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			
			
			return Response::json(array('status'=>'success', 'message'=>'User added successfully','data'=>$customerusers->id));
		}
	} */
	public function getStoreApi($cust_id, $request='')
	{

		
		if($request == '')
			$request = Request::all();
		//dd($request);
		$request['customer_id'] = Helpers::getEncodeAndDecodeOfId($request['customer_id'],'decode');
		if(isset($request['admin_practice_id']))
		{	
			$request['admin_practice_id'] = implode(',', $request['admin_practice_id']);
		}
		$validation_rule = Users::$rules+array('email' 	=> 'required|unique:users,email,NULL,id,deleted_at,NULL|email','password' => 'required', 'confirmpassword' => 'required|same:password');
		//$validation_rule['addressline1'] = "regex:/^[A-Za-z0-9 \t]*$/i";
		//$validation_rule['city'] = "regex:/^[A-Za-z0-9 \t]*$/i";
		//$validation_rule['state'] = "max:2|regex:/^[A-Za-z]*$/i";
		//$validation_rule['zipcode5'] = "digits:5";
		$validator = Validator::make($request, $validation_rule+array('filefield'=>Config::get('siteconfigs.customer_image.user_image_size')), Users::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
	    if($request['app_name'] == "CHARGECAPTURE" && $request['useraccess'] == "app")	{
                $request['facility_access_id'] = 0;
            } elseif($request['app_name'] == "WEB" && $request['useraccess'] == "app")	{
                $request['provider_access_id'] = 0;
            } else {
                $request['facility_access_id'] = $request['provider_access_id'] = $request['practice_access_id'] = 0; 
            }
			if (Input::hasFile('filefield'))
			{ 
				$image              = Input::file('filefield');
				$filename           = rand(11111,99999);
				$extension          = $image->getClientOriginalExtension();
				$filestoreName      = $filename .'.'.$extension;
				$resize             = array('150','150');
				Helpers::mediauploadpath('admin','user',$image,$resize,$filestoreName); 
				$request['avatar_name']  = $filename;
				$request['avatar_ext']   = $extension;
			}	
			if($request['useraccess'] =='app')
			{
				$request['practice_user_type']="";
			}
			if($request['dob'] != '')
			$request['dob'] = date('Y-m-d', strtotime($request['dob']));		
			$request['password'] 	= Hash::make($request['password']);		
			$request['name'] 		= $request['lastname'].' '.$request['firstname'];
			$customerusers 			= Users::create($request);
			$user 					= Auth::user ()->id;
			$customerusers->created_by = $user;
			$customerusers->avatar_name = isset($request['avatar_name'])?$request['avatar_name']:'';
			$customerusers->avatar_ext = isset($request['avatar_ext'])?$request['avatar_ext']:'';
			$customerusers->save ();		

			/* Starts - address flag update */				
			$address_flag = array();
			$address_flag['type'] 			= 'customerusers';
			$address_flag['type_id'] 		= $customerusers->id;
			$address_flag['type_category'] 	= 'general_information';
			$address_flag['address2'] 		= $request['general_address1'];
			$address_flag['city'] 			= $request['general_city'];
			$address_flag['state'] 			= $request['general_state'];
			$address_flag['zip5'] 			= $request['general_zip5'];
			$address_flag['zip4'] 			= $request['general_zip4'];
			$address_flag['is_address_match'] 	= $request['general_is_address_match'];
			$address_flag['error_message'] 		= $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/* Ends - address flag update  */
			$customerusers->id = Helpers::getEncodeAndDecodeOfId($customerusers->id,'encode');
                        
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$customerusers->id));
		}
	}
	/********************** End customer user added process ***********************************/

	/********************** Start display customer user details edit page ***********************************/
	public function getEditApi($cust_id, $id)
	{ 	
             $cust_id = Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
              $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
			  $admin_db_name = getenv('DB_DATABASE');
		if(Customer::where('id', $cust_id )->count())
		{
			if(Users::where('id', $id )->count())
			{
				$customer 		= Customer::with('user')->where('id',$cust_id)->first();
				$customerusers 	= Users::find($id);
                if($customerusers['dob'] != '0000-00-00')             
					$customerusers['dob']  =       date('m/d/Y',strtotime($customerusers['dob']));
                else
                    $customerusers['dob']  =   '';
                   
				$tabs 			= "yes";
				$language 		= Language::orderBy('language','asc')->pluck('language','id')->all();
				$language_id 	= $customerusers->language_id;
				$ethnicity 		= Ethnicity::orderBy('name','asc')->pluck('name','id')->all();
				$ethnicity_id 	= $customerusers->ethnicity_id;
				$general_address_flag 	= AddressFlag::getAddressFlag('customerusers',$customerusers->id,'general_information');
				$addressFlag['general'] = $general_address_flag;
				$customer_practices 	= array();
				$customer_practices_list = Practice::where('customer_id', $cust_id)->pluck('practice_name', 'id')->all();
		        // To get practice id (whose admin are choosen already) starts
				$admin_user_db_ids = Users::where('customer_id', $cust_id)->where('practice_user_type', 'practice_admin')->where('id','!=',$id)->pluck('admin_practice_id')->all(); 
				$customer_admin_users = array_filter($admin_user_db_ids);
				$user = Users::where('id', $id)->first();
				$customer_practices1 = str_replace(' ','_',strtolower(Practice::where('id', $user->practice_access_id)->pluck('practice_name')->first()));
				$dbconnection 		= new DBConnectionController();
				$practice_db_name = $customer_practices1;
				$dbconnection->configureConnectionByName($practice_db_name);
				$facility = Facility::on($practice_db_name)->where('status','Active')->pluck('facility_name', 'id')->all();
				$provider = Provider::on($practice_db_name)->where('status','Active')->whereIn('provider_types_id',[Config::get('siteconfigs.providertype.Rendering')])->pluck('provider_name', 'id')->all();
				$dbconnection->configureConnectionByName($admin_db_name);
				$practice_lists = '';
				if(!empty($customer_admin_users))
				{
					foreach($customer_admin_users as $admin_user)
					{
						$practice_lists.= is_array($admin_user) ? implode(',', $admin_user) : $admin_user.',';			  
					}
				}
				$practice_id 			= [];
				$admin_practice_ids 	= [];
				$excluded_val 			= [];
				$practice_id			= array_filter(explode(',',$practice_lists));
				$admin_practice_ids 	= explode(',',$customerusers->admin_practice_id);
				$excluded_val 			= array_diff($practice_id,$admin_practice_ids);
				$customer_practices 	= Practice::where('customer_id', $cust_id)->pluck('practice_name', 'id')->all();
				$user_practices		= Setpracticeforusers::where('user_id',$id)->get();

				$practice_ids 		= array();
				foreach ($user_practices as $practice_id){
					$practice_ids[] = $practice_id->practice_id;
				}		
				$practices = Practice::with('user','update_user')->whereIn('id',$practice_ids)->get();
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customer','user','customer_practices','customer_practices_list','customerusers','language','language_id','ethnicity','ethnicity_id','addressFlag','tabs','facility', 'provider','practices')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
        }
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
		}
	}
	/********************** End display customer user details edit page ***********************************/
	
	/********************** Start customer user details update process ***********************************/
	/*public function getUpdateApi($cust_id, $id, $request='')
	{
		if($request == '')
			$request = Request::all();
                
        $validation_rule 	= Users::$rules+array('email'  => 'required|unique:users,email,'.$id.'|email');
        $validator 			= Validator::make($request, $validation_rule, Users::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$user = Users::findOrFail($id);
			if (Input::hasFile('filefield'))
			{
				$image 				= Input::file('filefield');
				$filename  			= rand(11111,99999);
				$old_filename   	= $user->avatar_name;
				$old_extension  	= $user->avatar_ext;
				$extension 			= $image->getClientOriginalExtension();
				$filestoreName 		= $filename .'.'.$extension;
				$filestoreoldName 	= $old_filename .'.'.$old_extension;
				$resize 			= array('150','150');
				Helpers::mediauploadpath('admin','user',$image,$resize,$filestoreName,$filestoreoldName);  
				$user->avatar_name  = $filename;
				$user->avatar_ext 	= $extension;
				$user->save();
			}
			if($request['dob']!='')
				$request['dob'] = date('Y-m-d', strtotime($request['dob']));
			if(isset($request['admin_practice_id']))
			{	
				$setpractice = Setpracticeforusers::where('user_id', $id)->pluck('practice_id')->first();
				$value = array_intersect($setpractice,$request['admin_practice_id']);
				if(!empty($value))
				{
					Setpracticeforusers::whereIn('practice_id', $value)->where('user_id', $id)->delete();
					$admin_practice_ids = $value;
					foreach($admin_practice_ids as $admin_practice_id)
					{
						$setpractice->updateUserInfoInpracticedb($admin_practice_id, $id, 'delete');	
					}			      		
				}
			}
		  
			$customerusers = Users::find($id);

			if($request['password']!='')
			{
				$request['password'] = Hash::make($request['password']);
			}
			if($request['password']=='')
			{
				unset($request['password']);
			}
			if(!empty($request['lastname']) || !empty($request['firstname']))
			{
				$request['name'] = $request['lastname'].' '.$request['firstname'];
			}	
		   
			$admin_practice_ids = $customerusers->admin_practice_id;
			$setpractice 		= new SetPracticeforUsersApiController();
			if($request['practice_user_type'] == 'practice_user' && $customerusers->practice_user_type == 'practice_admin' && config('app.is_enable_provider_add'))
			{ 
				$request['admin_practice_id'] = '';
				$customerusers->update($request);
				$admin_practice_ids = explode(',', $admin_practice_ids); 
					                
				foreach($admin_practice_ids as $admin_practice_id)
				{
					$setpractice->updateUserInfoInpracticedb($admin_practice_id, $customerusers->id, 'delete');	
				}                				
			elseif(($customerusers->practice_user_type== 'practice_user' && $request['practice_user_type'] == 'practice_admin')&& config('app.is_enable_provider_add'))
			{
				$request['admin_practice_id'] = implode(',', $request['admin_practice_id']);
				$customerusers->update($request);	
				$admin_practice_ids = explode(',',$request['admin_practice_id']); 
				               
				foreach($admin_practice_ids as $admin_practice_id)
				{  	                	
					$setpractice->updateUserInfoInpracticedb($admin_practice_id, $customerusers->id, 'insert');	
				}                
				
			}
			elseif($customerusers->practice_user_type== 'practice_admin' && $request['practice_user_type'] == 'practice_admin' && config('app.is_enable_provider_add'))
			{ 
				$admin_practice_ids = explode(',',$customerusers->admin_practice_id);
				foreach($admin_practice_ids as $admin_practice_id)
				{
					$setpractice->updateUserInfoInpracticedb($admin_practice_id, $customerusers->id, 'delete');	
				}
				$admin_ids 						= $request['admin_practice_id']; 
				$request['admin_practice_id'] 	= implode(',', $request['admin_practice_id']);
				$customerusers->update($request);
				$requested_admin_practice_ids 	= $admin_ids;
				foreach($requested_admin_practice_ids as $admin_practice_id)
				{  	                	
					$setpractice->updateUserInfoInpracticedb($admin_practice_id, $customerusers->id, 'insert');	
				}    
			}
			if(!config('app.is_enable_provider_add'))
			{
				$customerusers->update($request);
			}
			
			$user 						= Auth::user ()->id;
			$customerusers->updated_by 	= $user;
			$customerusers->save ();
			$address_flag = array();
			$address_flag['type'] 				= 'customerusers';
			$address_flag['type_id'] 			= $customerusers->id;
			$address_flag['type_category'] 		= 'general_information';
			$address_flag['address2'] 			= $request['general_address1'];
			$address_flag['city'] 				= $request['general_city'];
			$address_flag['state'] 				= $request['general_state'];
			$address_flag['zip5'] 				= $request['general_zip5'];
			$address_flag['zip4'] 				= $request['general_zip4'];
			$address_flag['is_address_match'] 	= $request['general_is_address_match'];
			$address_flag['error_message'] 		= $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			
			return Response::json(array('status'=>'success', 'message'=>'User updated successfully','data'=>''));					
		}
	} */
	/********************** End customer user details update process ***********************************/

		/********************** Start customer user details update process ***********************************/
	public function getUpdateApi($cust_id, $id, $request='')
	{ 
		$cust_id = Helpers::getEncodeAndDecodeOfId($cust_id,'decode');
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        if($request == '')
		$request = Request::all();
		$request['customer_id'] = Helpers::getEncodeAndDecodeOfId($request['customer_id'],'decode');
		$validation_rule 	= Users::$rules+array('email'  => 'required|unique:users,email,'.$id.',id,deleted_at,NULL|email');
		$validation_rule['addressline1'] = "nullable|regex:/^[A-Za-z0-9 \t]*$/i";
		$validation_rule['city'] = "nullable|regex:/^[A-Za-z0-9 \t]*$/i";
		$validation_rule['state'] = "nullable|max:2|regex:/^[A-Za-z]*$/i";
		$validation_rule['zipcode5'] = "nullable|digits:5";		
        $validator 	= Validator::make($request, $validation_rule+array('filefield'=>Config::get('siteconfigs.customer_image.user_image_size')), Users::messages());
        if ($validator->fails())  {
			$errors = $validator->errors();
            return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
        } else {
            $user = Users::findOrFail($id);
            if (Input::hasFile('filefield'))
            {
                $image 				= Input::file('filefield');
                $filename  			= rand(11111,99999);
                $old_filename   	= $user->avatar_name;
                $old_extension  	= $user->avatar_ext;
                $extension 			= $image->getClientOriginalExtension();
                $filestoreName 		= $filename .'.'.$extension;
                $filestoreoldName 	= $old_filename .'.'.$old_extension;
                $resize 			= array('150','150');
                Helpers::mediauploadpath('admin','user',$image,$resize,$filestoreName,$filestoreoldName);  
                $user->avatar_name  = $filename;
                $user->avatar_ext 	= $extension;
                $user->save();
            }
            if($request['dob']!='')
                    $request['dob'] = date('Y-m-d', strtotime($request['dob']));
			if(isset($request['admin_practice_id']) && $request['admin_practice_id'] == ''){
				Setpracticeforusers::where('user_id', $id)->delete();
			}	
					
            if(isset($request['admin_practice_id']))
            {	
                $setpractice = Setpracticeforusers::where('user_id', $id)->pluck('practice_id')->all();
                $value = array_intersect($setpractice,$request['admin_practice_id']);
                if(!empty($value))
                {
                    Setpracticeforusers::whereIn('practice_id', $value)->where('user_id', $id)->delete();						      		
                }						
            }
            $exist_perm = Setpracticeforusers::where('user_id', $id)->pluck('practice_id')->all();
          	if(isset($request['admin_practice_permission']))
            {	               	
	        	if(count($exist_perm) <= count($request['admin_practice_permission'])){
	        		$insert_ids = array_diff($request['admin_practice_permission'],$exist_perm);
	        			$data['user_id'] = $id;	
						$data['role_id'] = 0;
						foreach($insert_ids as $insert_id){
							$data['practice_id'] = $insert_id;									
							$setpractice = Setpracticeforusers::create($data); 
							$user = Auth::user ()->id;
							$setpractice->created_by = $user;
							$setpractice->save();
						}	
	            }else{
	            	$update_ids = array_diff($exist_perm,$request['admin_practice_permission']);
	            	Setpracticeforusers::whereIn('practice_id', $update_ids)->where('user_id', $id)->delete();
	            }	        
            }else if(isset($request['admin_practice_permission']) && isset($request['admin_practice_id'])){
        		Setpracticeforusers::whereIn('practice_id', $exist_perm)->where('user_id', $id)->delete();
        	} else{
        		Setpracticeforusers::whereIn('practice_id', $exist_perm)->where('user_id', $id)->delete();
        	}   
    
	   if($request['app_name'] == "CHARGECAPTURE" && $request['useraccess'] == "app"){
            	$request['facility_access_id'] = 0;
            } elseif($request['app_name'] == "WEB" && $request['useraccess'] == "app")	{
                $request['provider_access_id'] = 0;
            } else {
                $request['facility_access_id'] = $request['provider_access_id'] = $request['practice_access_id'] = 0; 
            }
            $customerusers = Users::find($id);
	   if($request['password']!='') {
                $request['password'] = Hash::make($request['password']);
            } elseif($request['password']=='') {
                unset($request['password']);
            }
            if(!empty($request['lastname']) || !empty($request['firstname']))
            {
                $request['name'] = $request['lastname'].' '.$request['firstname'];
            }
			if($request['useraccess'] =='app')
			{	
				$request['practice_user_type']="";
				$request['admin_practice_id']="";
				$customerusers->update($request);
			}
			else
			{  
				$request['practice_access_id'] = 0;
				$request['facility_access_id'] = 0;
				if(isset($request['practice_user_type']) && $request['practice_user_type'] == 'practice_user' && $customerusers->practice_user_type == 'practice_admin')
				{ 
					$request['admin_practice_id'] = '';
					$customerusers->update($request);
				}
				elseif(($customerusers->practice_user_type == 'practice_user' && $request['practice_user_type'] == 'practice_admin'))
				{   
					$request['admin_practice_id'] = implode(',', $request['admin_practice_id']);
					$customerusers->update($request);

				} else{
					if(isset($request['admin_practice_id']))
					$request['admin_practice_id'] = implode(',', $request['admin_practice_id']);	
					$customerusers->update($request);
				}
			}		
			
            /* Starts - General address flag update */
            $user 						= Auth::user ()->id;
            $customerusers->updated_by 	= $user;
            $customerusers->save ();
            $address_flag = array();
            $address_flag['type'] 				= 'customerusers';
            $address_flag['type_id'] 			= $customerusers->id;
            $address_flag['type_category'] 		= 'general_information';
            $address_flag['address2'] 			= $request['general_address1'];
            $address_flag['city'] 				= $request['general_city'];
            $address_flag['state'] 				= $request['general_state'];
            $address_flag['zip5'] 				= $request['general_zip5'];
            $address_flag['zip4'] 				= $request['general_zip4'];
            $address_flag['is_address_match'] 	= $request['general_is_address_match'];
            $address_flag['error_message'] 		= $request['general_error_message'];
            AddressFlag::checkAndInsertAddressFlag($address_flag);
            /* Ends - General address flag update */
            $request['customer_id']=Helpers::getEncodeAndDecodeOfId($request['customer_id'],'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
        }
	}
	/********************** End customer user details update process ***********************************/
	
	/********************** Start customer user deleted process ***********************************/
	public function getDeleteApi($type,$id)
	{
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Users::Where('id',$id)->count()){
			Users::Where('id',$id)->delete();
			Setpracticeforusers::where('user_id', $id)->delete(); // Remove corresponding entry from practice user entry
			Setapiforusers::where('user_id', $id)->delete(); // Remove corresponding entry from api user entry
		    return Response::json(array('status'=>'success', 'message'=>'User deleted successfully','data'=>''));		
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));		
		}
		
	}
	/********************** End customer user deleted process ***********************************/

	/********************** Start display customer user details show page ***********************************/
	public function getShowApi($ids,$id)
	{
		$ids	= Helpers::getEncodeAndDecodeOfId($ids,'decode');
		$id 	= Helpers::getEncodeAndDecodeOfId($id,'decode');
		$tabs = "yes";
		if(Customer::where('id', $ids )->count())
		{       
			if(Users::where('id', $id )->count())
			{
				$customer 		= 	Customer::with('user')->where('id',$ids)->first();
				$customerusers 	= 	Users::with('customer','language','ethnicity','user','userupdate')->where('id',$id)->first();
				$practicelist 	= 	Practice::pluck('practice_name', 'id')->all();
				$user 			= 	Users ::where('id', $id)->first();
				$general_address_flag 	= 	AddressFlag::getAddressFlag('customerusers',$customerusers->id,'general_information');
				$addressFlag['general'] = 	$general_address_flag;
				$user_practices		= Setpracticeforusers::where('user_id',$customerusers->id)->get();
				$customer_practices 	= Practice::where('customer_id', $customer->id)->pluck('practice_name', 'id')->all();
				$practice_ids 		= array();
				foreach ($user_practices as $practice_id){
					$practice_ids[] = $practice_id->practice_id;
				}		
				$practices = Practice::with('user','update_user')->whereIn('id',$practice_ids)->get();		
				$customer->encid = Helpers::getEncodeAndDecodeOfId($customer->id,'encode');
				return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('customer','customerusers','addressFlag','tabs','user','practicelist','practices','customer_practices')));
			} 
			else
			{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/********************** End display customer user details show page ***********************************/
	public function userAccess($id)
	{
		$practiceName = Practice::where('id', $id)->pluck('practice_name')->all();

		$customer_practices = strtolower (implode('', $practiceName));
		$dbconnection 		= new DBConnectionController();
		$practice_db_name = $customer_practices;
		$practice_db_name 	= $dbconnection->getpracticedbname($practice_db_name);
		$dbconnection->configureConnectionByName($practice_db_name);
		$facility = Facility::on($practice_db_name)->where('status','Active')->pluck('facility_name', 'id')->all();
		$provider = Provider::on($practice_db_name)->where('status','Active')->whereIn('provider_types_id',[Config::get('siteconfigs.providertype.Rendering')])->pluck('provider_name', 'id')->all();
		/*$facility_list= '<option value="">--Select--</option>';
		foreach($facility as $facility)
		{
		$facility_list= <select><option value=".$facility->id.">$facility->facility_name</option></select>
Try it Yourself »

		}*/
		return Response::json(compact('facility', 'provider'));
	}
	
	
	function __destruct() 
	{
    }
}