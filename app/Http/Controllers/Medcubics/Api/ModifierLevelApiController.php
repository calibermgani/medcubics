<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Medcubics\Modifier as Modifier;
use App\Models\Medcubics\Modifierstype as Modifierstype;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Helpers\Helpers as Helpers;
use Lang;
use DB;
use App\Models\Medcubics\Practice as Practice;

class ModifierLevelApiController extends Controller 
{
	/*** lists page Starts ***/
	public function getIndexApi($export='')
	{
		$modifiers = Modifier::with('modifierstype')->where('modifiers_type_id', '2')->get();
		if($export != "")
		{
			$exportparam = array(
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
			$request = Request::all();
		$validation_rules = Modifier::$rules+array('code' => 'required|regex:/^[A-Za-z0-9]+$/i|not_in:0|unique:modifiers,code,NULL,id,deleted_at,NULL');
		$validator = Validator::make($request, $validation_rules, Modifier::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$request['code'] = strtoupper($request['code']);
			$data = Modifier::create($request);
			$user = Auth::user ()->id;
			$data->created_by = $user;
			$data->created_at = date('Y-m-d h:i:s');
			$data->updated_at = $user;
			$data->save();
			$modifiers_type_id = $data->modifiers_type_id;
			$id = Helpers::getEncodeAndDecodeOfId($data->id,'encode');

			### INSERT the POS To All Practice thilagavathy P start
			$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
			$practices = Practice::where("status","Active")->pluck('practice_name', 'id')->all();
		        $practice_list = [];
		        foreach ($practices as $key => $pra) {
		            $tenantDBs = str_replace(' ', '_', strtolower($pra));
		            $stats = DB::select($query, [$tenantDBs]);
		            if(!empty($stats))
			            $practice_list[$key] = $tenantDBs;   
		        }
		        // Included admin db name for execute query.
		        $adminDB = env('DB_DATABASE');		        
		        array_push($practice_list, $adminDB);
		      
				if(!empty($practice_list)){
					$success_practice = $failure_practice = $message = [];					
					foreach ($practice_list as $k=>$value) {					
						$db = new DBConnectionController();
						$db_name = $db->getpracticedbname($value);
						$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
	          			$dbs = DB::select($query, [$db_name]);      
	          			echo "<br> Start processing :".$db_name."<br>";					       
		    		if(!empty($dbs)){
                        $db = new DBConnectionController();			 	
                        $db->configureConnectionByName($db_name);
                        $mysqldbconn = DB::Connection($db_name);
                        $current_date =  date('Y-m-d');

                        // Update the Insurance types
                         $list = $mysqldbconn->select("SELECT code,description,name,id as modifier_id FROM `modifiers` WHERE `id` = '" . $data->id . "' ");
                        if(empty($list)){
	                        $catList = $mysqldbconn->insert("insert into `modifiers` (`code`, `name`, `modifiers_type_id`, `description`, `anesthesia_base_unit`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['code'] . "', '" . $request['name'] . "', '" . $request['modifiers_type_id'] . "', '" . $request['description'] . "', '" . $request['anesthesia_base_unit'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
		                       	if($catList){
		                       		 \Log::info("Modifier Inserted");	echo "Modifier Inserted In ".$db_name."<br>";
		                       	} else{
		                       		 \Log::info("Modifier NOT Inserted");	echo "Modifier Inserted NOT In ".$db_name."<br>";
		                       	}
	                       	}            					

	                    } else {
	                        \Log::info("No practice found");	echo "No practice found<br>";
	                    } 
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>compact('modifiers_type_id','id')));
				} else {
					return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>compact('modifiers_type_id','id')));
				}
				### Thilagavathy End 11 nov 2019

				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>compact('modifiers_type_id','id')));					
		}
	}
	/*** Store Function Ends ***/
	
	/*** Edit page Starts ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Modifier::where('id',$id)->where('modifiers_type_id', '2')->count()>0 && is_numeric($id))
		{
			$modifiers 			= 	Modifier::findOrFail($id);
			$modifierstype 		= 	Modifierstype::pluck('modifiers_types','id')->all();
			$modifiers_type_id 	=	$modifiers->modifiers_type_id;
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
		if(Modifier::where('id',$id)->count()>0 && is_numeric($id))
		{
			$request 	= Request::all();
			$validation_rules = Modifier::$rules+array('code' => 'required|regex:/^[A-Za-z0-9]+$/i|not_in:0|unique:modifiers,code,'.$id.',id,deleted_at,NULL');
			$validator = Validator::make($request,$validation_rules, Modifier::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{			
				$request['code'] = strtoupper($request['code']);
				$modifiers = Modifier::findOrFail($id);
				$modifiers->update($request);
				$user = Auth::user ()->id;
				$modifiers->updated_by = $user;
				$modifiers->updated_at = date('Y-m-d H:i:s');
				$modifiers->save();
				### INSERT the POS To All Practice thilagavathy P start
				/*$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
				$practices = Practice::where("status","Active")->pluck('practice_name', 'id')->all();
		        $practice_list = [];
		        foreach ($practices as $key => $pra) {
		            $tenantDBs = str_replace(' ', '_', strtolower($pra));
		            $stats = DB::select($query, [$tenantDBs]);
		            if(!empty($stats))
			            $practice_list[$key] = $tenantDBs;   
		        }
		        // Included admin db name for execute query.
		        $adminDB = env('DB_DATABASE');		        
		        array_push($practice_list, $adminDB);
		      
				if(!empty($practice_list)){
					$success_practice = $failure_practice = $message = [];					
					foreach ($practice_list as $k=>$value) {					
						$db = new DBConnectionController();
						$db_name = $db->getpracticedbname($value);
						$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
	          			$dbs = DB::select($query, [$db_name]);      
	          			echo "<br> Start processing :".$db_name."<br>";					       
		    		if(!empty($dbs)){
                        $db = new DBConnectionController();			 	
                        $db->configureConnectionByName($db_name);
                        $mysqldbconn = DB::Connection($db_name);
                        $current_date =  date('Y-m-d');

	                        // Update the modifiers types                       
	                        $catList = $mysqldbconn->update("update modifiers SET code = '".$request['code']."', name = '".$request['name']."',modifiers_type_id = '".$request['modifiers_type_id']."',description = '".$request['description']."',anesthesia_base_unit = '".$request['anesthesia_base_unit']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);
	                       	if($catList){
	                       		 \Log::info("Insurance Type Updated");	echo "Insurance Type Updated In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("Insurance Type NOT Updated");	echo "Insurance Type Updated NOT In ".$db_name."<br>";
	                       	}              					

	                    } else {
	                        \Log::info("No practice found");	echo "No practice found<br>";
	                    } 
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>compact('modifiers')));
				} else {
					return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>compact('modifiers')));
				}*/
				### Thilagavathy End 11 nov 2019
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>compact('modifiers')));					
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
		if(Modifier::where('id',$id)->count()>0 && is_numeric($id))
		{
			Modifier::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
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
		if(Modifier::where('id', $id )->where('modifiers_type_id', '2')->count())
		{
			$modifiers = Modifier::with('user')->find ( $id );
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('modifiers')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** Show Function Ends ***/
	
	function __destruct() 
	{
    }
}
