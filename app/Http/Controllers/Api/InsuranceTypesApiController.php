<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insurancetype as Insurancetype;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Illuminate\Support\Collection;
use Response;
use Request;
use Validator;
use Input;
use Lang;
use Config;
use DB;

class InsuranceTypesApiController extends Controller 
{
	/*** Start to listing the Insurance Types  ***/
	public function getIndexApi($export='')
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$insurancetypes = InsuranceType::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('user','userupdate')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'Insurancetypes',
				'heading'		=>	'Insurance Types',
				'fields' 		=>	array(
				'type_name'		=>	'Type Name',
				'updated_at'	=>	'Updated On',
				'created_by'		=>	array('table'=>'user','column' => 'short_name','label' => 'Created by'),
				'updated_by'		=>	array('table'=>'userupdate','column' => 'short_name','label' => 'Updated by'),
			));
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $insurancetypes, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurancetypes')));
	}
	/*** End to listing the Insurance Types  ***/
	
	/*** Start to Create the Insurance Types ***/
	public function getCreateApi()
	{
		$inscmstypes = array_combine(Config::get('siteconfigs.cms_insurance_types'), Config::get('siteconfigs.cms_insurance_types'));
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('inscmstypes')));
	}
	/*** End to Create the Insurance Types	 ***/
	
	/*** Start to Store the Insurance Types	 ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		
		$val_InsuranceType_rules = array('type_name' => 'required|unique:insurancetypes,type_name,NULL,id,deleted_at,NULL','code' => 'required|unique:insurancetypes,code');
		$validator = Validator::make($request, $val_InsuranceType_rules, InsuranceType::messages());
	
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{
			$InsuranceType = InsuranceType::create(Request::all());
			$user = Auth::user ()->id;			
			// $InsuranceType->updated_at = '';
			$InsuranceType->created_by = $user;$id = $InsuranceType->id;
			$InsuranceType->save ();

			//Encode ID for InsuranceType
			$temp = new Collection($InsuranceType);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$InsuranceType = json_decode(json_encode($data), FALSE);
			//Encode ID for InsuranceType  

			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$InsuranceType->id));
		}
	}
	/*** End to Store the Insurance Types	 ***/
	
	/*** Start to Edit the Insurance Types	 ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && InsuranceType::where('id', $id)->count())
		{
			$insurancetypes = InsuranceType::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->findOrFail($id);
			$inscmstypes = array_combine(Config::get('siteconfigs.cms_insurance_types'), Config::get('siteconfigs.cms_insurance_types'));
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('insurancetypes','inscmstypes')));
		}
		else
		{
            return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Insurance Types ***/
	
	/*** Start to Update the Insurance Types	 ***/
	public function getUpdateApi($id, $request)
	{
		 $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$request = Request::all();
		
		//unset(InsuranceType::$rules['type_name']);
		//InsuranceType::$rules['type_name'] ='required';
		
		$val_InsuranceType_rules = InsuranceType::$rules+array('type_name' => 'required|unique:insurancetypes,type_name,'.$id.',id,deleted_at,NULL');
		
		$validator = Validator::make($request,  $val_InsuranceType_rules, InsuranceType::messages());
	
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{
			$insurancetypes = InsuranceType::findOrFail($id);
			$insurancetypes->update($request);
			$user = Auth::user ()->id;
			$insurancetypes->updated_by = $user;
			$insurancetypes->updated_at = date('Y-m-d H:i:s');
			$insurancetypes->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));
		}
	}
	/*** End to Update the Insurance Types	 ***/
	
	/*** Start to Destory Insurance Types ***/
	public function getDeleteApi($id)
	{
		 $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && InsuranceType::where('id', $id)->count())
		{
			$result = InsuranceType::find($id)->delete();
			if($result == 1)
			{
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>''));
			}
		}
	}
	/*** End to Destory Insurance Types	 ***/
	
	/*** Start to Show the Insurance Types ***/
	public function getShowApi($id)
	{
		 $id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		 $practice_timezone = Helpers::getPracticeTimeZone();
		if(InsuranceType::where('id', $id )->count())
		{
			$insurancetypes = InsuranceType::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('user','userupdate')->where('id', $id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('insurancetypes')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Show the Insurance Types ***/
	
	function __destruct() 
	{
    }
}