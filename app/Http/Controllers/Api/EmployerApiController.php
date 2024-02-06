<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employer as Employer;
use App\Models\AddressFlag as AddressFlag;
use App\Http\Helpers\Helpers as Helpers;
use Illuminate\Support\Collection;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use File;
use Config;
use Lang;

class EmployerApiController extends Controller 
{
	/*** lists page Starts ***/
	public function getIndexApi($export='')
	{
		$employers 			= 	Employer::where('employer_name', '<>', '')->orderBy('employer_name','ASC')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'employers',
								'heading'	=>	'',
								'fields' 	=>	array(
												'employer_status'		=>	'Employment Status',
												'employer_name'		=>	'Employer Name',
												'work_phone'	=>	'Phone'
												)
								);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $employers, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('employers')));
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function getCreateApi()
	{
		$employers = Employer::all();
		/*** Get address for usps ***/
		$addressFlag['general']['address1'] = '';
		$addressFlag['general']['city'] 	= '';
		$addressFlag['general']['state'] 	= '';
		$addressFlag['general']['zip5'] 	= '';
		$addressFlag['general']['zip4'] 	= '';
		$addressFlag['general']['is_address_match'] = '';
		$addressFlag['general']['error_message'] 	= '';
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('employers','addressFlag')));
	}	
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function getStoreApi($request='')
	{	
		if($request == '')
			$request 	= Request::all();			
		$validator 		= Validator::make($request,Employer::$rules);
		if ($validator->fails()) {
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		} else {	
			$check_address = Employer::where('employer_name',$request['employer_name'])->where('address1',$request['address1'])->where('city',$request['city'])->where('address2',$request['address2'])->where('state',$request['state'])->where('zip5',$request['zip5'])->where('zip4',$request['zip4'])->count();
			if($check_address == 0){
				$data = Employer::create(Request::all());
				$user = Auth::user ()->id;
				$data->created_by = $user;
				$data->save();
			} else {
				return Response::json(array('status'=>'failure', 'message'=>'Already avaliable this employer information','data'=>'null'));
			}

			/*** Starts - address flag update ***/				
			 $address_flag = array();
			$address_flag['type'] 			= 	'employer';
			$address_flag['type_id'] 		= 	$data->id;
			$address_flag['type_category'] 	= 	'general_information';
			$address_flag['address2']	 	= 	$request['address1'];
			$address_flag['city'] 			= 	$request['city'];
			$address_flag['state'] 			= 	$request['state'];
			$address_flag['zip5'] 			=	$request['zip5'];
			$address_flag['zip4'] 			= 	$request['zip4'];
			$address_flag['is_address_match'] = $request['general_is_address_match'];
			$address_flag['error_message'] 	= 	$request['general_error_message'];
			AddressFlag::checkAndInsertAddressFlag($address_flag); 
			/*** Ends - address flag update  ***/
			//Encode ID for data
			$temp = new Collection($data);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$data = json_decode(json_encode($data), FALSE);
			//Encode ID for data
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->id));
		}
	}
	/*** Store Function Ends ***/
	
	/*** Edit page Starts ***/ 
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Employer::where('id', $id )->count()>0 && is_numeric($id))
		{
			$employers 				= Employer::findOrFail($id);
			
			/*** Get address for usps ***/
			$general_address_flag 	= AddressFlag::getAddressFlag('employer',$employers->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('employers','addressFlag')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function getUpdateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Employer::where('id', $id )->count()>0 && is_numeric($id))
		{
			$request 	= Request::all();
			$validator 	= Validator::make($request,Employer::$rules);
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$check_address = Employer::where('employer_name',$request['employer_name'])->where('address1',$request['address1'])->where('city',$request['city'])->where('address2',$request['address2'])->where('state',$request['state'])->where('zip5',$request['zip5'])->where('zip4',$request['zip4'])->where('id','!=',$id)->count();
				if($check_address == 0){
					$employers = Employer::findOrFail($id);
					$data 		= array();
					
					$employers->update($request);
					$user = Auth::user ()->id;
					$employers->updated_by = $user;
					$employers->save ();
				}else{
					return Response::json(array('status'=>'failure', 'message'=>'Already avaliable this employer information','data'=>'null'));
				}
				
				/*** Starts - General address flag update ***/
				$address_flag 			= 	array();
				$address_flag['type'] 	= 	'employer';
				$address_flag['type_id']= 	$employers->id;
				$address_flag['type_category'] 	= 	'general_information';
				$address_flag['address2'] 		= 	$request['address1'];
				$address_flag['city'] 	= 	$request['city'];
				$address_flag['state'] 	= 	$request['state'];
				$address_flag['zip5'] 	=	$request['zip5'];
				$address_flag['zip4'] 	= 	$request['zip4'];
				$address_flag['is_address_match'] 	= $request['general_is_address_match'];
				$address_flag['error_message'] 		= $request['general_error_message'];
				AddressFlag::checkAndInsertAddressFlag($address_flag);
				/*** Ends - General address flag update ***/
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.employer_update_msg"),'data'=>''));	
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	
	}	
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Employer::where('id', $id )->count()>0 && is_numeric($id))
		{
			Employer::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.employer_delete_msg"),'data'=>''));	
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** Delete Function Ends ***/
	
	/*** Show Function Starts ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode'); 
		if(Employer::where('id', $id )->count()>0 && is_numeric($id))
		{
			$employers = Employer::find ( $id );
		/*** Get address for usps ***/
			$general_address_flag 	= AddressFlag::getAddressFlag('employer',$employers->id,'general_information');
			$addressFlag['general'] = $general_address_flag;
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>compact('employers','addressFlag')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** Show Function Ends ***/
	
	/*** Delete Avatar in Employer table start ***/
	public function avatarapipicture($id,$p_name)
	{
		$delete_avr = Employer::where('id',$id)->first();
		$delete_avr->avatar_name = "";
		$delete_avr->avatar_ext = "";
		$delete_avr->save();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
	}
	/*** Delete Avatar in Employer table end ***/
	
	function __destruct() 
	{
    }
}
