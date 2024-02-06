<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use DB;
use Auth;
use Response;
use Request;
use Lang;
use Validator;
use App\Models\Medcubics\Insurance as Insurance;
use App\Models\Medcubics\insuranceappealaddress as Insuranceappealaddress;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Medcubics\AddressFlag as AddressFlag;
use App\Http\Helpers\Helpers as Helpers;

class InsuranceAppealAddressApiController extends Controller 
{
	/*** Start to Listing & Export the Insurance Appeal Address	 ***/
	public function getIndexApi($id, $export = "")
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Insurance::where('id', $id)->count()>0 && is_numeric($id))
		{
			$insurance     = Insurance::where('id', $id)->first();
			$appealaddress = Insuranceappealaddress::where('insurance_id',$id)->orderBy('id','asc')->get();
			if($export != "")
			{
				$exportparam 	= 	array(
				'filename'	=>	'insurance_Appeal_Address',
				'heading'	=>	'insurance Appeal Address',
				'fields' 	=> array(
				'address_1' => 'Address 1',
				'city' 		=> 'City',
				'state' 	=> 'State',
				'zipcode' 	=> array('table'=>'','column' =>array('zipcode5','zipcode4'),	'label' => 'Zipcode'),
				));
				$callexport = new CommonExportApiController();
				return $callexport->generatemultipleExports($exportparam, $appealaddress, $export); 
			}
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance', 'appealaddress')));
		}
		else 
		{
			 return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));   
		}
	}
	/*** End to Listing & Export the Insurance Appeal Address	 ***/
	
	/*** Start to Create the Insurance Appeal Address	 ***/	
	public function getCreateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Insurance::where('id',$id)->count()) 
		{
			$insurance = Insurance::where('id',$id)->first();
			 /*** Start to Get address for usps ***/
			$addressFlag['appeal']['address1'] = '';
			$addressFlag['appeal']['city'] = '';
			$addressFlag['appeal']['state'] = '';
			$addressFlag['appeal']['zip5'] = '';
			$addressFlag['appeal']['zip4'] = '';
			$addressFlag['appeal']['is_address_match'] = '';
			$addressFlag['appeal']['error_message'] = '';
			/*** End to Get address for usps ***/
			
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurance','addressFlag')));
		}
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Create the Insurance Appeal Address	 ***/
    
	/*** Start to Store the Insurance Appeal Address  ***/	
	public function getStoreApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Insurance::where('id',$id)->count()>0 && is_numeric($id)) 
		{
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
				$result = Insuranceappealaddress::create($request); 
				$user = Auth::user ()->id;
				$result->created_by = $user;
				$result->save ();
				$LastInsertId = $result->id;
				
				 /*** Starts - address flag update ***/				
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
				/*** Ends - address flag update ***/
				 $LastInsertId = Helpers::getEncodeAndDecodeOfId($LastInsertId,'encode');  
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$LastInsertId));					
			}
		}
		else 
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Store the Insurance Appeal Address	 ***/

	/*** Start to Edit the Insurance Appeal Address	 ***/
	public function getEditApi($ids,$id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$ids = Helpers::getEncodeAndDecodeOfId($ids,'decode');
		if(Insurance::where('id',$ids)->count()>0 && is_numeric($ids)) 
		{
			$insurance = Insurance::where('id',$ids)->first();
			if(Insuranceappealaddress::where('insurance_id',$ids)->where('id',$id)->count())
			{
				$appealaddress = Insuranceappealaddress::find($id);

				/*** Start to Get address for usps ***/
			   $general_address_flag = AddressFlag::getAddressFlag('insurance',$id,'appeal_address');
			   $addressFlag['appeal'] = $general_address_flag;
			   /*** End to Get address for usps ***/
			   
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
	/*** End to Edit the Insurance Appeal Address	 ***/
	
	/*** Start to Update the Insurance Appeal Address	 ***/
    public function getUpdateApi($insurance_id,$id, $request='')
	{
		$insurance_id = Helpers::getEncodeAndDecodeOfId($insurance_id,'decode');
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		
		// check invalid id for insurance.
		if(Insurance::where('id',$insurance_id)->count()>0 && is_numeric($insurance_id)) 
		{
			// check invalid id for appeal address.
			if(Insuranceappealaddress::where('insurance_id',$insurance_id)->where('id',$id)->count()>0 && is_numeric($id))
			{
				if($request == '')
					$request = Request::all();

				$validator = Validator::make($request, Insuranceappealaddress::$rules, Insuranceappealaddress::messages());

				if ($validator->fails())
				{
					$errors = $validator->errors();
					return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
				}
				else
				{	
					$appealaddress = Insuranceappealaddress::findOrFail($id); 
					$appealaddress->update($request);
					$appealaddress->insurance_id = $insurance_id;
					$user = Auth::user ()->id;
					$appealaddress->updated_by = $user;
					$appealaddress->save ();
										
					 /*** Starts - address flag update ***/				
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
					/*** Ends - address flag update ***/
										
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
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
	/*** End to Update the Insurance Appeal Address	 ***/
	
	/*** Start to Destory the Insurance Appeal Address	 ***/
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
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
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
	/*** End to Destory the Insurance Appeal Address	 ***/

	/*** Start to Show the Insurance Appeal Address	 ***/
	public function getShowApi($insid,$id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$insid = Helpers::getEncodeAndDecodeOfId($insid,'decode');
		// check invalid id for insurance.
		if(Insurance::where('id', $insid)->count()>0 && is_numeric($insid)) 
		{
			// check invalid id for appeal address.
			if(Insuranceappealaddress::where('id', $id )->count()>0 && is_numeric($id))
			{
				$insurance = Insurance::where('id',$insid)->first();
				$appealaddress = Insuranceappealaddress::with('user','userupdate')->where('id',$id)->first();
                        
				 /*** Start to Get address for usps ***/
				$general_address_flag = AddressFlag::getAddressFlag('insurance',$id,'appeal_address');
				$addressFlag['appeal'] = $general_address_flag;
				/*** End to Get address for usps ***/
				
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
	/*** End to Show the Insurance Appeal Address	 ***/
	
	function __destruct() 
	{
    }
}