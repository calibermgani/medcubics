<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contactdetail as Contactdetail;
use App\Models\Practice as Practice;
use App\Models\AddressFlag as AddressFlag;
use Auth;
use Response;
use Request;
use Lang;
use Validator;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Collection;

class ContactdetailApiController extends Controller 
{
	/********************** Start Display contact edit page ***********************************/
	public function getEditApi($id)
	{
		$practice 		= Practice::first();	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Contactdetail::where('id', $id )->count()>0 && is_numeric($id))
		{
			$contactdetail 	= Contactdetail::where('id',$id)->first();	
			$address 		= AddressFlag::getAddressFlag('practice',$practice->id,'billing_service');
			$addressFlag['billing_service'] = $address;
					
					//Encode ID for practice
					$temp = new Collection($practice);
					$temp_id = $temp['id'];
					$temp->pull('id');
					$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
					$temp->prepend($temp_encode_id, 'id');
					$data = $temp->all();
					$practice = json_decode(json_encode($data), FALSE);
					//Encode ID for practice
					//Encode ID for contactdetail
					$temp = new Collection($contactdetail);
					$temp_id = $temp['id'];
					$temp->pull('id');
					$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
					$temp->prepend($temp_encode_id, 'id');
					$data = $temp->all();
					$contactdetail = json_decode(json_encode($data), FALSE);
					//Encode ID for contactdetail

			// $practice->id = Helpers::getEncodeAndDecodeOfId($practice->id,'encode');
			// $contactdetail->id = Helpers::getEncodeAndDecodeOfId($contactdetail->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('practice','contactdetail','addressFlag')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/********************** End Display contact edit page ***********************************/
		
	/********************** Start contact update process ***********************************/
	public function getUpdateApi($request='', $id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Contactdetail::where('id', $id )->count()>0 && is_numeric($id))
		{
			if($request == '')
				$request = Request::all();
			
			if(!empty($request['website'])){
				if(substr($request['website'],0,7) != 'http://' && substr($request['website'],0,8) != 'https://')	
					$request['website'] = "http://".$request['website'];
			}
			
			$validator = Validator::make($request, Contactdetail::$rules, Contactdetail::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{				
				$contactdetail = Contactdetail::find($id);
				$contactdetail->update($request);
				$user = Auth::user ()->id;
				$contactdetail->updated_by = $user;
				$contactdetail->save ();
				/// Starts - Billing service flag update ///
				$practice = Practice::first();	
				$address_flag = array();
				$address_flag['type'] = 'practice';
				$address_flag['type_id'] = $practice->id;
				$address_flag['type_category'] = 'billing_service';
				$address_flag['address2'] = $request['ba_address1'];
				$address_flag['city'] = $request['ba_city'];
				$address_flag['state'] = $request['ba_state'];
				$address_flag['zip5'] =$request['ba_zip5'];
				$address_flag['zip4'] = $request['ba_zip4'];
				$address_flag['is_address_match'] = $request['ba_is_address_match'];
				$address_flag['error_message'] = $request['ba_error_message'];
				AddressFlag::checkAndInsertAddressFlag($address_flag);
				/* Ends - Billing service address */
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.contactdetail_update_msg"),'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/********************** End contact update process ***********************************/
	
	/********************** Start Display a details of the contact ***********************************/
	public function getShowApi($id)
	{
		$contact_detail = Contactdetail::where('id', $id )->count();
		if(empty($contact_detail)){
			$request['id']= 1;
			$contact_detail = Contactdetail::create($request);
			$user = Auth::user ()->id;
			$contact_detail->created_by = $user;
			$contact_detail->created_at = date('Y-m-d h:i:s');
			$contact_detail->save ();
		}		
		$practice = Practice::first();
		$contact_detail = Contactdetail::where('id',$id)->first();
		/// Get Pay to address for usps ///
		$address = AddressFlag::getAddressFlag('practice',$practice->id,'billing_service');
		$addressFlag['billing_service'] = $address;
					//Encode ID for practice
					$temp = new Collection($practice);
					$temp_id = $temp['id'];
					$temp->pull('id');
					$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
					$temp->prepend($temp_encode_id, 'id');
					$data = $temp->all();
					$practice = json_decode(json_encode($data), FALSE);
					//Encode ID for practice
					//Encode ID for contact_detail
					$temp = new Collection($contact_detail);
					$temp_id = $temp['id'];
					$temp->pull('id');
					$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
					$temp->prepend($temp_encode_id, 'id');
					$data = $temp->all();
					$contact_detail = json_decode(json_encode($data), FALSE);
					//Encode ID for contact_detail
		// $contact_detail->id = Helpers::getEncodeAndDecodeOfId($contact_detail->id,'encode');
		return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('contact_detail','practice','addressFlag')));
		
	}
	/********************** End Display a details of the contact ***********************************/
	
	function __destruct() 
	{
    }
	
}
