<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\IdQualifier as IdQualifier;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Lang;
use App\Models\Medcubics\Practice as Practice;
use DB;

class QualifierApiController extends Controller 
{
	/***Listing qualifiers start ***/
	public function getIndexApi($export = "")
	{
		$qualifiers = IdQualifier::with('user','userupdate')->get();
		if($export != "")
		{
			$exportparam = array(
							'filename'	=>	'IDQualifier',
							'heading'	=>	'IDQualifier',
							'fields' 	=>	array(
											'id_qualifier_name'	=> 'ID Qualifiers Name',
											'created_at' 		=> 'Created On',
											'updated_at'		=> 'Updated On',
											'created_by' 		=>	array(
																	'table'	 =>'user' ,
																	'column' => 'short_name' ,
																	'label'  => 'Created By'),
											'updated_by' 		=>	array(
																	'table'  =>'userupdate' ,
																	'column' => 'short_name' ,
																	'label'  => 'Updated By'),
											)
							);
			$callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $qualifiers, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('qualifiers')));
	}
	/*** Listing qualifiers end ***/
	
	/*** Create qualifier details start ***/
	public function getCreateApi()
	{
		$qualifiers = IdQualifier::all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('qualifiers')));
	}
	/*** Create qualifier details end ***/

	/*** Store qualifier details end ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		$validator = Validator::make($request, IdQualifier::$rules, IdQualifier::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$qualifiers = IdQualifier::create($request);
			$user = Auth::user ()->id;
			$qualifiers->created_by = $user;
			$qualifiers->save ();
			$insertedId = Helpers::getEncodeAndDecodeOfId($qualifiers->id,'encode');
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
                     $list = $mysqldbconn->select("SELECT `id_qualifier_name`,`id` FROM `id_qualifiers` WHERE `id` = '" . $qualifiers->id . "' ");
                    if(empty($list)){
                        $catList = $mysqldbconn->insert("insert into `id_qualifiers` (`id_qualifier_name`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['id_qualifier_name'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
	                       	if($catList){
	                       		 \Log::info("Qualifier Inserted");	echo "Qualifier Inserted In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("Qualifier NOT Inserted");	echo "Qualifier Inserted NOT In ".$db_name."<br>";
	                       	}
                       	}            					

                    } else {
                        \Log::info("No practice found");	echo "No practice found<br>";
                    } 
				}
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$insertedId));
			} else {
				return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$insertedId));
			}
			### Thilagavathy End 21 nov 2019
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$insertedId));					
		}
	}
	/*** Create qualifier details end ***/
	
	/*** View the qualifier details start ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(IdQualifier::where('id', $id )->count())
		{
			$qualifiers = IdQualifier::with('user','userupdate')->where('id', $id )->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('qualifiers')));	
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>''));
		}

	}
	/*** View the qualifier details end ***/
	
	/*** Edit the qualifier detail start ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(IdQualifier::where('id', $id )->count())
		{
			$qualifiers = IdQualifier::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('qualifiers')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>''));
		}
	}
	/*** Edit qualifier details end ***/
	
	/*** Update the qualifier details start ***/ 
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
			$request = Request::all();
		$validator = Validator::make(
						Input::all(),
						[
						'id_qualifier_name' => 'required|unique:id_qualifiers,id_qualifier_name,'.$id
						], 
						[ 
						'id_qualifier_name.required' => 'Enter your ID Qualifier name!',
						'id_qualifier_name.unique' => 'ID Qualifier name must be unique!'
						]
					);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$qualifiers = IdQualifier::findOrFail($id);
			$qualifiers->update($request);
			$user = Auth::user ()->id;
			$qualifiers->updated_by = $user;
			$qualifiers->save ();
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

                    // Update the IdQualifier               
                     $catList = $mysqldbconn->update("update id_qualifiers SET id_qualifier_name = '".$request['id_qualifier_name']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);		 
	                   	if($catList){
	                   		 \Log::info("IdQualifier Updated");	echo "IdQualifier Updated In ".$db_name."<br>";
	                   	} else{
	                   		 \Log::info("IdQualifier NOT Updated");	echo "IdQualifier Updated NOT In ".$db_name."<br>";
	                   	}           					

                    } else {
                        \Log::info("No practice found");	echo "No practice found<br>";
                    } 
				}
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));
			} else {
				return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>''));
			}*/
			### Thilagavathy End 21 nov 2019
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
	/*** Update the qualifier detail end ***/
	
	/*** Delete the qualifier detail Start ***/
	public function getDestroyApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		IdQualifier::find($id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	/*** Delete the qualifier detail end ***/
	
	function __destruct() 
	{
    }
}
