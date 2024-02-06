<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Code as Code;
use App\Models\Medcubics\Codecategory as Codecategory;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Lang;
use DB;

class CodeApiController extends Controller
 {
	/*** Listing the codes page start ***/
	public function getIndexApi($export='')
	{
		$codes = Code::with('codecategories','user','userupdate')->orderBy('id','DESC')->get();
		$codecategory = Codecategory::all();
		 if($export != "")
		{
			$exportparam = array(
							'filename' 	=>	'Code',
							'heading'  	=>	'Code',
							'fields' 	=>	array(
											'codecategory_id'	=>	array('table'=>'codecategories', 'column' => 'codecategory', 'label' => 'Code Category'),
											'transactioncode_id'=>	'Transaction Code',
											'description'		=>	'Description',
											'createdby' 	    =>	array('table'=>'user', 'column' => 'short_name',	'label' => 'Created By'),
											'updatedby'         =>	array('table'=>'userupdate'	,	'column' => 'short_name',	'label' => 'Updated By'),
											'status'			=>	'Status',
											)
							);
			$callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $codes, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('codes','codecategory')));
	}
	/*** Listing codes page end ***/
	
	/*** Create codes detail start ***/
	public function getCreateApi()
	{			
		$codecategory = Codecategory::orderBy('codecategory','ASC')->pluck('codecategory', 'id')->all();
		$codecategory_id = '';
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('codecategory','codecategory_id')));
	}
	/*** Create code detail end ***/
	
	/*** Store code detail start ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		Validator::extend('chk_code_exists', function($attribute, $value, $parameters) use($request)
		{
			$transactioncode_id = $value; 
			$codecategory_id	= $request['codecategory_id'];
			$count = Code::where('transactioncode_id',$transactioncode_id)->where('codecategory_id',$codecategory_id)->count();
			return ($count > 0)	?  false : true;
				
		});
		$rules 		= Code::$rules+array('transactioncode_id' => 'required|max:5|chk_code_exists');
		$validator  = Validator::make($request, $rules, Code::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			if($request['start_date'] != '')
				$request['start_date']= date("Y-m-d",strtotime($request['start_date']));
			if($request['last_modified_date']!='')
				$request['last_modified_date']= date("Y-m-d",strtotime($request['last_modified_date']));
			$request['created_at'] = date('Y-m-d h:i:s');	
			$code = Code::create($request);
			$user = Auth::user ()->id;
			$code->created_by = $user;
			$code->save ();
			$insertedId = Helpers::getEncodeAndDecodeOfId($code->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$insertedId));					
		}
	}
	/*** Store the code detail end ***/
	
	/*** Edit the code detail start ***/
	public function getEditApi($id)
	{
		if(Code::where('id', $id )->count()>0 && is_numeric($id)==1)
		{
			$code = Code::find($id);
			$codecategory = Codecategory::orderBy('codecategory','asc')->pluck('codecategory','id')->all();
			$codecategory_id = $code->codecategory_id;
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('code','codecategory','codecategory_id')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}	
	}
	/*** Edit the code detail end  ***/
	
	/*** Update the code details start ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Code::where('id', $id )->count()>0 && is_numeric($id)==1)
		{
			if($request == '')
				$request = Request::all();
			
			Validator::extend('chk_code_exists', function($attribute, $value, $parameters) use($request,$id)
			{
				$transactioncode_id = $value; 
				$codecategory_id	= $request['codecategory_id'];
				$count = Code::where('transactioncode_id',$transactioncode_id)->where('codecategory_id',$codecategory_id)->where('id','!=',$id)->count();
				return ($count > 0)	?  false : true;
			});
			
			$rules 		= Code::$rules+array('transactioncode_id' => 'required|max:5|chk_code_exists');
			$validator = Validator::make($request, $rules, Code::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				if($request['start_date'] != '')
					$request['start_date']= date("Y-m-d",strtotime($request['start_date']));
				if($request['last_modified_date'] != '')
					$request['last_modified_date']= date("Y-m-d",strtotime($request['last_modified_date']));	
				$codes = Code::findOrFail($id);
				$codes->update($request);
				$user = Auth::user ()->id;
				$codes->updated_by = $user;
				$codes->updated_at = date('Y-m-d h:i:s');
				$codes->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Update the code details end ***/
	
	/*** Delete the code detail start ***/
	public function getDeleteApi($id)
	{
		Code::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	/*** Delete the code detail end ***/
	
	/*** View the code detail start ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Code::where('id', $id )->count()>0 && is_numeric($id)==1)
		{
			$codecategory = Codecategory::orderBy('codecategory','ASC')->pluck('codecategory', 'id')->all();
			$code = Code::with('codecategories','user','userupdate')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('code','codecategory')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** View the code detail end ***/
	
	function __destruct() 
	{
    }
}
