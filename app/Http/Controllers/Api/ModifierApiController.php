<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Lang;
use DB;
use App\Models\Modifier as Modifier;
use App\Models\Modifierstype as Modifierstype;
use App\Models\Practice as Practice;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class ModifierApiController extends Controller 
{
	/*** lists page Starts ***/
	public function getIndexApi($export='',$search='')
	{
		$modifiers = Modifier::with('modifierstype')->where('modifiers_type_id', '1');
		if($export != "")
		{	
			$search = (isset($search) && $search =='') ? Request::all() :[];
		}
		if(isset($search) && $search !='' && count($search)>0)
		{
			$modifiers = $modifiers->where(function($query) use($search){ return $query->where('code', 'LIKE', '%'.$search['code'].'%')->where('name', 'LIKE', '%'.$search["name"].'%')->where('description', 'LIKE', '%'.$search["description"].'%')->where('status','LIKE',$search["status"].'%'); });
		}
		$modifiers = $modifiers->get();
		if($export != "")
		{
			$exportparam = 	array(
								'filename'	=>	'modifiers',
								'heading'	=>	'',
								'fields' 	=>	array(
													'code'			=>	'Code',
													'name'			=>	'Name',
													'description'	=>	'Description',
													'status'		=>	'Status',							
												)
								);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $modifiers, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('modifiers')));		
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function getCreateApi()
	{
		$modifierstype = Modifierstype::pluck('modifiers_types','id')->all();
		$modifiers_type_id ='';
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('modifierstype','modifiers_type_id')));
	}	
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request 	= Request::all();
		$validation_rules = Modifier::$rules+array('code' => 'required|regex:/^[A-Za-z0-9]+$/i|not_in:0|unique:modifiers,code,NULL,id,deleted_at,NULL');
		
		$validator = Validator::make($request,$validation_rules, Modifier::messages());
		if ($validator->fails())
		{
			$errors 	= $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$request['code'] = strtoupper($request['code']);
			$data 	= Modifier::create($request);
			$user 	= Auth::user ()->id;
			$data->created_at = date('Y-m-d h:i:s');
			$data->created_by = $user;
			$data->save();
			$id = Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			$type_id = $data->modifiers_type_id;
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.modifier_create_msg"),'data'=>compact('id','type_id')));
		}
	}
	/*** Store Function Ends ***/
	
	/*** Edit page Starts ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Modifier::where('id',$id)->where('modifiers_type_id', '1')->count()>0 && is_numeric($id))
		{
			$modifiers 			= 	Modifier::where("id",$id)->where('modifiers_type_id', '1')->first();
			$modifierstype 		= 	Modifierstype::pluck('modifiers_types','id')->all();
			$modifiers_type_id	=	$modifiers->modifiers_type_id;
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('modifiers','modifierstype','modifiers_type_id')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function getUpdateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Modifier::where('id',$id)->where('modifiers_type_id', '1')->count()>0 && is_numeric($id))
		{
			$request 	= Request::all();
			$validation_rules = Modifier::$rules+array('code' => 'required|regex:/^[A-Za-z0-9]+$/i|not_in:0|unique:modifiers,code,'.$id.',id,deleted_at,NULL');
			$validator = Validator::make($request,$validation_rules, Modifier::messages());
			if($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{			
				$request['code'] = strtoupper($request['code']);
				$modifiers 	= Modifier::findOrFail($id);
				$modifiers->update($request);
				$user 		= Auth::user ()->id;
				$modifiers->updated_at = date('Y-m-d h:i:s');
				$modifiers->updated_by = $user;
				$modifiers->save ();
				$type_id = $modifiers->modifiers_type_id;
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.modifier_update_msg"),'data'=>$type_id));					
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function getDeleteApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Modifier::where('id', $id )->where('modifiers_type_id', '1')->count()>0 && is_numeric($id))
		{
			Modifier::where('id',$id)->where('modifiers_type_id', '1')->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.modifier_delete_msg"),'data'=>''));	
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Delete Function Ends ***/
	
	/*** Show Function Starts ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Modifier::where('id', $id )->where('modifiers_type_id', '1')->count()>0 && is_numeric($id))
		{
			$modifiers = Modifier::with('user')->where("id",$id)->where('modifiers_type_id', '1')->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('modifiers')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Show Function Ends ***/
	
	/*** Mass import modifier start ***/
	public function importMasterModifier(){
		$adminDB=env('DB_DATABASE');
		// Getting current practice database details
		$practice_details = Practice::getPracticeDetails();
		$dbconnection = new DBConnectionController();
		$tenantDB = $dbconnection->getpracticedbname($practice_details['practice_name']);
		
        try {
            $table_name = 'modifiers';        
            // Truncate table - @todo if necessary trucate table.
            DB::statement("TRUNCATE TABLE $table_name");
            $insert = "INSERT INTO $tenantDB.$table_name SELECT * FROM $adminDB.$table_name";         				       	   						       
            DB::insert($insert);    
            return Response::json(array('status' => 'success', 'message' => 'Modifier imported from master'));            
        } catch (Exception $e) {
            \Log::info("Exception on importMasterModifier. Error: " . $e->getMessage());
            return Response::json(array('status'=>'error', 'message'=> "Unable to import Modifier. Please contact admin"));	
        }		
	}
	/*** Mass import modifier end ***/

	function __destruct() 
	{
    }
}
