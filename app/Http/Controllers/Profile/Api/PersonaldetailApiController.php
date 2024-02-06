<?php namespace App\Http\Controllers\Profile\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Users as Customer;
use App\Models\Medcubics\Customer as Customers;
use App\Models\Profile\Blog as Blog;
use App\Models\Profile\PersonalNotes as PersonalNotes;
use App\Models\Profile\MessageDetailStructure as MessageDetailStructure;
use Response;
use Lang;
use Config;
use Validator;
use Input;
use Auth;
use Illuminate\Http\Request;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Http\Helpers\Helpers as Helpers;


class PersonaldetailApiController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function personaldetailApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
            if(Customer::where('id', $id )->count())
		{
			$customers = Customer::with('language','ethnicity')->findOrFail($id);
                        $blogs = Blog::with('user')->where('user_id','=',$id)->get();
                        $notes = PersonalNotes::where('deleted_at',Null)->where('user_id',$id)->get();
                        $messages = MessageDetailStructure::with(['message_detail'=>function($query){ $query->select('subject','message_body','id','created_at','attachment_file'); }, 'sender_details'=>function($query){ $query->select('id','email','name','avatar_name','avatar_ext'); }])->where('receiver_id',$id)->where('deleted_at',Null)->orderBy('created_at','desc')->select('sender_id','receiver_id','message_id','read_status','created_at')->get();
			$general_address_flag 	= AddressFlag::getAddressFlag('customer',$customers->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customers','addressFlag','blogs','notes','messages')));
        }
        else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}   
	}
        
        
        public function personaldetailviewApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
            if(Customer::where('id', $id )->count())
		{
			$customers 				= Customer::findOrFail($id);
			$general_address_flag 	= AddressFlag::getAddressFlag('customer',$customers->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			$customers->id = Helpers::getEncodeAndDecodeOfId($customers->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('customers','addressFlag')));
        }
        else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}   
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function UpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');   
        if($request == '')
			$request = Request::all();
		// Get user id for email unique validation
		$get_userid = Customer::on("responsive")->where('customer_id',$id)->where('user_type',['Practice','Customer'])->where('practice_user_type','customer')->first(); 
		$validate_customers = Customer::$rules+array('email'  => 'required|unique:users,email,'.$get_userid.',id,deleted_at,NULL|email', 'con_password' => 'same:password')+array('image'=>Config::get('siteconfigs.customer_image.defult_image_size'));
		$validator 			= Validator::make($request, Customer::$rules, Customer::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			if(Auth::user()->practice_user_type == 'customer'){
				$customers = Customer::on("responsive")->findOrFail($id);
				$customers->update($request);
				$user = Auth::user()->id;
				$customers->updated_by = $user;
				$customers->name = $request['lastname'].', '.$request['firstname'];
				$customers->save();
				$user_array = [];
				$user_array['email']= $request['email'];
				$user_array['firstname']= $request['firstname'];
				$user_array['lastname']= $request['lastname'];
				//$user_array['status']= $request['status'];
				$user_array['addressline1']= $request['addressline1'];
				$user_array['addressline2']= $request['addressline2'];
				$user_array['gender']= $request['gender'];
				$user_array['city']= $request['city'];
				$user_array['state']= $request['state'];
				$user_array['zipcode5']= $request['zipcode5'];
				$user_array['zipcode4']= $request['zipcode4'];
				$user_array['avatar_ext']= $customers->avatar_ext;
				$user_array['avatar_name']= $customers->avatar_name ;
				$user_array['updated_by']= Auth::user()->id;
				$user_result = Customers::on("responsive")->where('id',$customers->customer_id)->first();
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
			}
			else{

				$customers = Customer::on("responsive")->findOrFail($id);
				$customers->update($request);
				$user = Auth::user()->id;
				$customers->updated_by = $user;
				$customers->name = $request['lastname'].', '.$request['firstname'];
				$customers->save();
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
			}

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
				if(Auth::user()->practice_user_type == 'customer')
					Helpers::mediauploadpath('admin','customers',$image,$resize,$filestoreName,$filestoreoldName);  
				else
					Helpers::mediauploadpath('admin','user',$image,$resize,$filestoreName,$filestoreoldName);  
				$customers->avatar_name  = $filename;
				$customers->avatar_ext 	= $extension;
				$customers->save();
			}
			
			/* Ends - General address flag update */
        	return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));	
		}	
	}

	public function avatarapipicture($id,$p_name)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$delete_avr = Customer::where('id',$id)->first();
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	
}