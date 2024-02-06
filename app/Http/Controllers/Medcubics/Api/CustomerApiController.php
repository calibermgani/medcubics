<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Customer as Customer;
use App\Models\Medcubics\Users as Users;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Models\Medcubics\Practice;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Redirect;
use Hash;
use Lang;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Illuminate\Support\Collection;
use Config;

class CustomerApiController extends Controller {
	
	public function getIndexApi($export = "")
	{
		$customers 	= Customer::with('user','userupdate','practice')->get();
		$tabs 		= "yes";
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'Customer',
								'heading'	=>	'Customer',
								'fields' 	=>	array(
												'customer_name'		=> 'Customer Name',
												'customer_type'		=> 'Customer Type',
												'contact_person' 	=> 'Contact Person',
												'designation'       => 'Designation',
                                                'phone'             => 'Phone',
                                                'mobile'            => 'Cell phone',
												)
								);
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $customers, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customers','tabs')));
	}
	
	public function getCreateApi()
	{
		$customers = Customer::all();
		// dd($customers);
		$addressFlag['general']['address1'] = '';
		$addressFlag['general']['city'] 	= '';
		$addressFlag['general']['state'] 	= '';
		$addressFlag['general']['zip5'] 	= '';
		$addressFlag['general']['zip4'] 	= '';
		$addressFlag['general']['is_address_match'] = '';
		$addressFlag['general']['error_message'] 	= '';
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customers','addressFlag')));
	}
	
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
        $validate_customers = Customer::$rules+array('email' 	=> 'required|unique:users,email,NULL,id,deleted_at,NULL|email','password' => 'required', 'con_password' => 'required|same:password')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
		$validator 			= Validator::make($request, $validate_customers, Customer::messages());
		
		
       if ($validator->fails())
		{
			$errors = $validator->errors();
			
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			if (Input::hasFile('image'))
			{ 
				$image              = Input::file('image');
				$filename           = rand(11111,99999);
				$extension          = $image->getClientOriginalExtension();
				$filestoreName      = $filename .'.'.$extension;
				$resize             = array('150','150');
				Helpers::mediauploadpath('admin','customers',$image,$resize,$filestoreName); 
				$request['avatar_name']  = $filename;
				$request['avatar_ext']   = $extension;
			}
			$request['password']=Hash::make($request['password']);
			unset($request['con_password']);
			$customers 	= Customer::create($request);
			$user 		= Auth::user ()->id;
			$customers->created_by = $user;
			@$customers->avatar_name = $request['avatar_name'];
			@$customers->avatar_ext = $request['avatar_ext'];
			$customers->save ();
			$request['name'] 			= $request['customer_name'];					
			$request['user_type'] 		= 'practice';
			$request['useraccess'] 		= 'web';
			$request['department'] 		= '';
			$request['practice_user_type'] = 'customer';
			$default_view = Config::get('siteconfigs.language_id.defult_language_id');
			$request['language_id'] 	= $default_view;					
			$request['ethnicity_id'] 	= '';
			$request['customer_id'] 	= $customers->id;
			$user_result = Users::create($request);					
			$user_result->lastname = $request['lastname'];
			$user_result->firstname = $request['firstname'];
			$user_result->created_by =Auth::user()->id;
			$user_result->save();
			/* Starts - address flag update */				
			$address_flag 				= array();
			$address_flag['type'] 		= 'adminuser';
			$address_flag['type_id'] 	= $customers->id;
			$address_flag['type_category'] = 'general_information';
			$address_flag['address2'] 	= $request['general_address1'];
			$address_flag['city'] 		= $request['general_city'];
			$address_flag['state'] 		= $request['general_state'];
			$address_flag['zip5'] 		= $request['general_zip5'];
			$address_flag['zip4'] 		= $request['general_zip4'];
			$address_flag['is_address_match'] 	= $request['general_is_address_match'];
			$address_flag['error_message'] 		= $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			/* Ends - address flag update  */
			//Encode ID for customers
			$temp = new Collection($customers);
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp['id'], 'encode');
			$temp->pull('id');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$customers = json_decode(json_encode($data), FALSE);
			//Encode ID for customers
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$customers->id));					
		}
	}
	public function getShowApi($id)
	{		
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$tabs = "yes";
		if(Customer::where('id', $id )->count())
		{
			$customers 				= Customer::with('user','userupdate')->where('id', $id )->first();
			$general_address_flag 	= AddressFlag::getAddressFlag('adminuser',$customers->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('customers','addressFlag','tabs')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}

	public function getEditApi($id)
	{
            $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
            if(Customer::where('id', $id )->count())
		{
			$customers 				= Customer::findOrFail($id);
			$general_address_flag 	= AddressFlag::getAddressFlag('customer',$customers->id,'general_information');
			$addressFlag['general'] = $general_address_flag;

			//Encode ID for customers
			$temp = new Collection($customers);
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp['id'], 'encode');
			$temp->pull('id');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$customers = json_decode(json_encode($data), FALSE);
			//Encode ID for customers
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customers','addressFlag')));
        }
        else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}   
    }
	
	public function getUpdateApi($id, $request='')
	{
        $id = Helpers::getEncodeAndDecodeOfId($id,'decode');   
        if($request == '')
			$request = Request::all();
		if(!empty($request['website'])){
			if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
				$request['website'] = "http://".$request['website'];
		}
		// Get user id for email unique validation
		$get_userid = Users::where('customer_id',$id)
							->where('user_type','Practice')
							->where('practice_user_type','customer')
							->value('id'); 
					
		$validate_customers = Customer::$rules+array('email'  => 'required|unique:users,email,'.$get_userid.',id,deleted_at,NULL|email', 'con_password' => 'same:password')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
		$validator 			= Validator::make($request, $validate_customers, Customer::messages());

		if ($validator->fails()) {
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		} else {	
			$customers = Customer::findOrFail($id);
			if($request['password']!='') {
				$request['password']= Hash::make($request['password']);
			} elseif ($request['password']=='') {
				unset($request['password']);
			}
			unset($request['con_password']);
			if (Input::hasFile('image'))
			{
				$image 				= Input::file('image');
				$filename  			= rand(11111,99999);
				$old_filename   	= $customers->avatar_name;
				$old_extension  	= $customers->avatar_ext;
				$extension 			= $image->getClientOriginalExtension();
				$filestoreName 		= $filename .'.'.$extension;
				$filestoreoldName 	= $old_filename .'.'.$old_extension;
				$resize 			= array('150','150');
				Helpers::mediauploadpath('admin','customers',$image,$resize,$filestoreName,$filestoreoldName);  
				$customers->avatar_name  = $filename;
				$customers->avatar_ext 	= $extension;
				$customers->save();
			}
			$customers->update($request);
			$user = Auth::user()->id;
			$customers->updated_by = $user;
			$request['firstname'];
			$customers->save();
			$user_array = [];
			$user_array['name']= $request['customer_name'];
			$user_array['email']= $request['email'];
			$user_array['short_name']= $request['short_name'];
			$user_array['firstname']= $request['firstname'];
			$user_array['lastname']= $request['lastname'];
			if(@$request['password']!='')
			{
				$user_array['password']= $request['password'];
			}
			elseif (@$request['password']=='')
			{
				unset($user_array['password']);
			}
			$user_array['status']= $request['status'];
			$user_array['phone']= $request['phone'];
			$user_array['fax']= $request['fax'];
			$user_array['addressline1']= $request['addressline1'];
			@$user_array['addressline2']= $request['addressline2'];
			$user_array['designation']= $request['designation'];
			$user_array['gender']= $request['gender'];
			$user_array['city']= $request['city'];
			$user_array['state']= $request['state'];
			$user_array['zipcode5']= $request['zipcode5'];
			$user_array['zipcode4']= $request['zipcode4'];
			$user_array['avatar_ext']= $customers->avatar_ext;
			$user_array['avatar_name']= $customers->avatar_name ;
			$user_array['updated_by']= Auth::user()->id;
			$user_result = Users::where('customer_id',$customers->id)->first();
			$user_result->update($user_array);	

			$user_result->save();
			/* Starts - General address flag update $customers = Users::find($id);*/
			$address_flag = array();
			$address_flag['type'] 			= 'adminuser';
			$address_flag['type_id'] 		= $customers->id;
			$address_flag['type_category'] 	= 'general_information';
			$address_flag['address2'] 		= $request['general_address1'];
			$address_flag['city'] 			= $request['general_city'];
			$address_flag['state'] 			= $request['general_state'];
			$address_flag['zip5'] 			= $request['general_zip5'];
			$address_flag['zip4'] 			= $request['general_zip4'];
			$address_flag['is_address_match'] = $request['general_is_address_match'];
			$address_flag['error_message'] = $request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag);
			
			/* Ends - General address flag update */
        	return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
	
	public function getDestroyApi($id)
	{
            $id = Helpers::getEncodeAndDecodeOfId($id,'decode');    
		Customer::find($id)->delete();
		Users::where('customer_id',$id)->where('practice_user_type','customer')->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	
	public function setPracticeApi($practice_id)
	{
		$practice_id = Helpers::getEncodeAndDecodeOfId($practice_id,'decode');
		$dbconnection = new DBConnectionController();
		$dbconnection->setSessionforDB($practice_id);
		return Response::json(array('status'=>'success', 'data'=>''));
	}
	public function CustomerAvatarapi($id,$p_name)
	{
            $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
            $delete_avr = Customer::where('id',$id)->first();
            $delete_avr->avatar_name = "";
            $delete_avr->avatar_ext = "";
            $delete_avr->save();
            return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>''));
	}
	function __destruct() 
	{
    }
	
}
