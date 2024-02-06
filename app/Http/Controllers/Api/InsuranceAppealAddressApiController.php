<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DB;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Insurance as Insurance;
use App\Models\Insuranceappealaddress as Insuranceappealaddress;
use Illuminate\Support\Collection;
use App\Models\AddressFlag as AddressFlag;
use App\Http\Helpers\Helpers as Helpers;
use Lang;

class InsuranceAppealAddressApiController extends Controller 
{
	/*** Start to listing the Appeal Address  ***/
	public function getIndexApi($id, $export = "")
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id', $id)->count()>0 && is_numeric($id))
		{
			$insurance     = Insurance::where('id', $id)->first();
			$appealaddress = Insuranceappealaddress::where('insurance_id',$id)->orderBy('id','asc')->get();
			//Export
			if($export != "")
			{
				$exportparam 	= 	array(
					'filename'=>	'insurance_Appeal_Address',
					'heading'=>	'insurance Appeal Address',
					'fields' => array(
						'address_1' => 'Address 1',
						'city' => 'City',
						'state' => 'State',
						'Zipcode' => array('table'=>'' , 'column' =>array('zipcode5','zipcode4'),	'label' => 'Zipcode'),
					));
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $appealaddress, $export); 
			}
			
			//Encode ID for insurance
			$temp = new Collection($insurance);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$insurance = json_decode(json_encode($data), FALSE);
			//Encode ID for insurance
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance', 'appealaddress')));
		}
		else 
		{
			 return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));   
		}
	}
	/*** End to listing the Appeal Address  ***/
	
	/*** Start to Create the Appeal Address	 ***/	
	public function getCreateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id',$id)->count()>0 && is_numeric($id)) 
		{
			$insurance = Insurance::where('id',$id)->first();
			 /// Get address for usps ///
			$addressFlag['appeal']['address1'] = '';
			$addressFlag['appeal']['city'] = '';
			$addressFlag['appeal']['state'] = '';
			$addressFlag['appeal']['zip5'] = '';
			$addressFlag['appeal']['zip4'] = '';
			$addressFlag['appeal']['is_address_match'] = '';
			$addressFlag['appeal']['error_message'] = '';
			//Encode ID for insurance
			$temp = new Collection($insurance);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$insurance = json_decode(json_encode($data), FALSE);
			//Encode ID for insurance
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance','addressFlag')));
		}
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Create the Appeal Address	 ***/
    
	/*** Start to Store the Appeal Address	 ***/	
	public function getStoreApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id 
		if(Insurance::where('id',$id)->count()>0 && is_numeric($id)) 
		{
			$request = Request::all();
			$request['insurance_id'] = Helpers::getEncodeAndDecodeOfId($request['insurance_id'],'decode');
			$rules = Insuranceappealaddress::$rules;
			if((is_null($request['email']) == true)) {
				unset($rules['email']);
			}
			// dd($rules);
			$validator = Validator::make($request, $rules, Insuranceappealaddress::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$result = Insuranceappealaddress::create($request); 
				$user = Auth::user ()->id;
				$result->created_by = $user;
				$result->save ();
				$LastInsertId = $result->id;
				
				 /* Starts - address flag update */				
				$address_flag = array();
				$address_flag['type'] = 'insurance';
				$address_flag['type_id'] = $LastInsertId;
				$address_flag['type_category'] = 'appeal_address';
				$address_flag['address2'] = $request['address_1'];
				$address_flag['city'] = $request['city'];
				$address_flag['state'] = $request['state'];
				$address_flag['zip5'] =$request['zipcode5'];
				$address_flag['zip4'] = $request['zipcode4'];
				$address_flag['is_address_match'] = $request['appeal_is_address_match'];
				$address_flag['error_message'] = $request['appeal_error_message'];
				AddressFlag::checkAndInsertAddressFlag($address_flag);
				/* Ends - address flag update */
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.appeal_create_msg"),'data'=>''));
			}
		}
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Store the Appeal Address	 ***/
	
	/*** Start to Edit the Appeal Address	 ***/ 
    public function getEditApi($ids,$id)
	{
		$ids = Helpers::getEncodeAndDecodeOfId($ids,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$insurance = Insurance::where('id',$ids)->first();
		// check invalid id for appeal address.
		if(Insuranceappealaddress::where('insurance_id',$ids)->where('id',$id)->count()>0 && is_numeric($id))
		{
			// check invalid id for insurance.
			if(Insurance::where('id',$ids)->count()>0 && is_numeric($ids)) 
			{
				$appealaddress = Insuranceappealaddress::find($id);
				/// Get address for usps ///
				$general_address_flag = AddressFlag::getAddressFlag('insurance',$id,'appeal_address');
				$addressFlag['appeal'] = $general_address_flag;
				//Encode ID for insurance
				$temp = new Collection($insurance);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$insurance = json_decode(json_encode($data), FALSE);
				//Encode ID for insurance
				//Encode ID for appealaddress
				$temp = new Collection($appealaddress);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$appealaddress = json_decode(json_encode($data), FALSE);
				//Encode ID for appealaddress
				return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('appealaddress','insurance','addressFlag')));
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else 
		{ 
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Appeal Address	 ***/
	
	/*** Start to Update the Appeal Address	 ***/
	public function getUpdateApi($insurance_id,$id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$insurance_id = Helpers::getEncodeAndDecodeOfId($insurance_id,'decode');
		// check invalid id for insurance.
		if(Insurance::where('id',$insurance_id)->count()>0 && is_numeric($insurance_id)) 
		{
			// check invalid id for appeal address.
			if(Insuranceappealaddress::where('insurance_id',$insurance_id)->where('id',$id)->count()>0 && is_numeric($id))
			{
				if($request == '')
					$request = Request::all();
				$request['insurance_id'] = Helpers::getEncodeAndDecodeOfId($request['insurance_id'],'decode');
				$validator = Validator::make($request, Insuranceappealaddress::$rules, Insuranceappealaddress::messages());
				if ($validator->fails())
				{
					$errors = $validator->errors();
					return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
				}
				else
				{	
					$override = Insuranceappealaddress::findOrFail($id); 
					$override->update($request);
					$user = Auth::user ()->id;
					$override->updated_by = $user;
					$override->save ();
									
					 /* Starts - address flag update */				
					$address_flag = array();
					$address_flag['type'] = 'insurance';
					$address_flag['type_id'] = $id;
					$address_flag['type_category'] = 'appeal_address';
					$address_flag['address2'] = $request['address_1'];
					$address_flag['city'] = $request['city'];
					$address_flag['state'] = $request['state'];
					$address_flag['zip5'] =$request['zipcode5'];
					$address_flag['zip4'] = $request['zipcode4'];
					$address_flag['is_address_match'] = $request['appeal_is_address_match'];
					$address_flag['error_message'] = $request['appeal_error_message'];
					AddressFlag::checkAndInsertAddressFlag($address_flag);
					/* Ends - address flag update */
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.appeal_update_msg"),'data'=>''));					
				}
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else 
		{ 
			return Response::json(array('status'=>'failure_ins', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Update the Appeal Address	 ***/
	
	/*** Start to Destory the Appeal Address	 ***/
	public function getDeleteApi($type,$id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$insurance_id = Helpers::getEncodeAndDecodeOfId($type,'decode');
		// check invalid id for insurance.
		if(Insurance::where('id',$insurance_id)->count()>0 && is_numeric($insurance_id)) 
		{
			// check invalid id for appeal address.
			if(Insuranceappealaddress::where('id',$id)->count()>0 && is_numeric($id))
			{
				Insuranceappealaddress::where('id',$id)->delete();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.appeal_delete_msg"),'data'=>''));
			}
			else
			{
				return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else 
		{ 
			return Response::json(array('status'=>'failure_ins', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Destory the Appeal Address	 ***/

	/*** Start to Show the Appeal Address	 ***/
	public function getShowApi($insid,$id)
	{
		$insid = Helpers::getEncodeAndDecodeOfId($insid,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		// check invalid id for insurance.
		if(Insurance::where('id', $insid)->count()>0 && is_numeric($insid)) 
		{
			// check invalid id for appeal address.
			if(Insuranceappealaddress::where('id', $id )->count()>0 && is_numeric($id))
			{
				$insurance = Insurance::where('id',$insid)->first();
				$appealaddress = Insuranceappealaddress::with('user','userupdate')->where('id',$id)->first();
                        
				 /// Get address for usps ///
				$general_address_flag = AddressFlag::getAddressFlag('insurance',$id,'appeal_address');
				$addressFlag['appeal'] = $general_address_flag;
				//Encode ID for insurance
				$temp = new Collection($insurance);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$insurance = json_decode(json_encode($data), FALSE);
				//Encode ID for insurance
				//Encode ID for appealaddress
				$temp = new Collection($appealaddress);
				$temp_id = $temp['id'];
				$temp->pull('id');
				$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
				$temp->prepend($temp_encode_id, 'id');
				$data = $temp->all();
				$appealaddress = json_decode(json_encode($data), FALSE);
				//Encode ID for appealaddress
				return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('appealaddress','insurance','addressFlag')));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Show the Appeal Address	 ***/
	
	function __destruct() 
	{
    }
}