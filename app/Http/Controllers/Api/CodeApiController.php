<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Code as Code;
use App\Models\Codecategory as Codecategory;
use App\Models\Practice as Practice;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Lang;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class CodeApiController extends Controller 
{
	/*** Code listing page start ***/
	public function getIndexApi($export='',$search ='')
	{
		$codes = Code::with(['codecategories', 'rule_engine'])->orderBy('id','DESC');
		
		//Export all codes format likes pdf,excel,csv
		if($export != "")
		{	
			$search = (isset($search) && $search =='') ? Request::all() :[];
		}
		if(isset($search) && $search !='' && count($search)>0)
		{
			$codecategory_id = $search['codecategory_id'];
			$codes = $codes->where(function($query) use($search,$codecategory_id){ return $query->where('transactioncode_id', 'LIKE', '%'.$search['code'].'%')->whereHas('codecategories', function($q) use ($codecategory_id){ $q->where('codecategory', 'LIKE', '%'.$codecategory_id.'%');})->where('description', 'LIKE', '%'.$search["description"].'%')->where('status','LIKE',$search["status"].'%'); });
		}
		$codes = $codes->get();
		if($export != "")
		{
			$exportparam 	= 	array(
								'filename'	=>	'code',
								'heading'	=>	'',
								'fields' 	=>	array(
											'codecategory_id'=>	array('table'=>'codecategories','column' => 'codecategory','label' => 'Code Category'),
											'transactioncode_id'=>	'Transaction Code',							
											'description'		=>	'Description',
											'status'			=>	'Status',
													)
								);
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $codes, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('codes')));
	}
	/*** Code listing page end ***/
	
	/*** Code create page start ***/
	public function getCreateApi()
	{			
		$codecategory = Codecategory::orderBy('codecategory','ASC')->pluck('codecategory', 'id')->all();	
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('codecategory')));
	}
	/*** Code create page end ***/
	
	/*** Code store create page start ***/
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
		//Validation check mistaske means if condition otherwise else 
		if($validator->fails())
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
			$code->created_by = Auth::user ()->id;
			$code->save ();
			$id = Helpers::getEncodeAndDecodeOfId($code->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));					
		}
	}
	/*** Code store create page end ***/
	
	/*** Code edit page start ***/	
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(is_numeric($id)==1 && Code::where('id', $id )->count()>0 )
		{
			$code = Code::find($id);
			$codecategory = Codecategory::orderBy('codecategory','asc')->pluck('codecategory','id')->all();
			$codecategory_id = $code->codecategory_id;
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('code','codecategory','codecategory_id')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
		
	}
	/*** Code edit page end ***/
	
	/*** Update code page start ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(is_numeric($id)==1 && Code::where('id', $id )->count()>0 )
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
			//Validation check mistaske means if condition otherwise else 
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$codes = Code::findOrFail($id);
				if($request['start_date'] != '') 
					$request['start_date']= date("Y-m-d",strtotime($request['start_date']));
				if($request['last_modified_date'] != '')
					$request['last_modified_date']= date("Y-m-d",strtotime($request['last_modified_date']));
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
	/*** Update code page end ***/
	
	/*** Code view page start ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(is_numeric($id)==1 && Code::where('id', $id )->count()>0 )
		{
			$code = Code::select("*",DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->with('codecategories','user','userupdate')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('code')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Code view page start ***/
	
	/*** Delete code page start ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(is_numeric($id)==1 && Code::where('id', $id )->count()>0 )
		{
			Code::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Delete code page start ***/
	
	/*** Mass import code starts ***/
	public function massImportCode(){
		$adminDB=env('DB_DATABASE');
		// Getting current practice database details
		$practice_details = Practice::getPracticeDetails();
		$dbconnection = new DBConnectionController();
		$tenantDB = $dbconnection->getpracticedbname($practice_details['practice_name']);
		
        try {
            $table_name = 'codes';        
            // Truncate table - @todo if necessary trucate table.
            DB::statement("TRUNCATE TABLE $table_name");
            $insert = "INSERT INTO $tenantDB.$table_name SELECT * FROM $adminDB.$table_name";         			       	   		
            DB::insert($insert);    
            return Response::json(array('status' => 'success', 'message' => 'Code imported from master'));            
        } catch (Exception $e) {            
            \Log::info("Exception on massImportCode. Error: " . $e->getMessage());
            return Response::json(array('status'=>'error', 'message'=> "Unable to import Codes. Please contact admin"));	
        }		
	}
	/*** Mass import code ends ***/	

	function __destruct() 
	{
    }
}