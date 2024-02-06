<?php namespace App\Http\Controllers\Medcubics\Api;

use Auth;
use Response;
use Request;
use Validator;
use Lang;
use App\Models\Medcubics\Speciality;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Medcubics\Practice as Practice;
use DB;

class SpecialityApiController extends Controller 
{
	/*** Start Display a listing of the speciality ***/
	public function getIndexApi($export = "")
	{
		$specialities = Speciality::with('user','userupdate')->get();
		if($export != "")
        {
            $exportparam = array(
							'filename' 	=> 'speciality',
							'heading' 	=> 'Specialities',
							'fields' 	=> array(
											'speciality'	=> 'Speciality',
											'created_at' 	=> 'Created On',
											'updated_at'	=> 'Updated On',
											'created_by'    => array('table'=>'user' ,'column' => 'short_name' ,'label' => 'Created by'),
											'updated_by'    => array('table'=>'userupdate' ,'column' => 'short_name' ,'label' => 'Updated by'),
										)
							);
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $specialities, $export);
        }
        return Response::json(array('status'=>'success', 'message'=>null, 'data'=>compact('specialities')));
	}
	/*** End Display a listing of the speciality ***/
		
	/*** Start speciality page display ***/
	public function getCreateApi()
	{
		$specialities = Speciality::get()->all();
		return Response::json(array('status'=>'success', 'message'=>null, 'data'=>compact('specialities')));
	}
	/*** End speciality page display ***/
	
	/*** Start speciality store process ***/
	public function getStoreApi()
	{
		$request 	= Request::all();
		$validate_Speciality_rules = array('speciality' => 'required|unique:specialities,speciality,NULL,id,deleted_at,NULL');
        $validator 	= Validator::make($request, $validate_Speciality_rules, Speciality::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors, 'data'=>''));	
		}
		else
		{	
			$data = Speciality::create($request);
			$user = Auth::user ()->id;
			$data->created_by = $user;		
			$data->save();
			$data->encId = Helpers::getEncodeAndDecodeOfId($data->id,'encode');

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
                     $list = $mysqldbconn->select("SELECT `speciality`,`id` FROM `specialities` WHERE `id` = '" . $data->id . "' ");
                    if(empty($list)){
                        $catList = $mysqldbconn->insert("insert into `specialities` (`speciality`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['speciality'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
	                       	if($catList){
	                       		 \Log::info("Speciality Inserted");	echo "Speciality Inserted In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("Speciality NOT Inserted");	echo "Speciality Inserted NOT In ".$db_name."<br>";
	                       	}
                       	}            					

                    } else {
                        \Log::info("No practice found");	echo "No practice found<br>";
                    } 
				}
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->encId));
			} else {
				return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$data->encId));
			}
			### Thilagavathy End 21 nov 2019

			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"), 'data'=>$data->encId));
		}
	}
	/*** End speciality store process ***/
	
	/*** Start speciality Edit page Display ***/
	public function getEditApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Speciality::where('id', $id)->count())
		{
			$speciality			= 	Speciality::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null, 'data'=>compact('speciality')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"), 'data'=>null));
		}
	}
	/*** End speciality Edit page Display ***/

	/*** Start speciality updated process ***/
	public function getUpdateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        $request = Request::all();
		$validate_speciality_rules = array('speciality' => 'required|unique:specialities,speciality,'.$id.',id,deleted_at,NULL');
        $validator = Validator::make($request, $validate_speciality_rules, Speciality::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors, 'data'=>''));	
		}
		else
		{
			$data = Speciality::findOrFail($id);
			$data->update($request);
			$user = Auth::user ()->id;
			$data->updated_by = $user;
			$data->save();
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
                        $catList = $mysqldbconn->update("update specialities SET speciality = '".$request['speciality']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);	                 
                       	if($catList){
                       		 \Log::info("Speciality Inserted");	echo "Speciality Update In ".$db_name."<br>";
                       	} else{
                       		 \Log::info("Speciality NOT Inserted");	echo "Speciality Update NOT In ".$db_name."<br>";
                       	}	                            					

                    } else {
                        \Log::info("No practice found");	echo "No practice found<br>";
                    } 
				}
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>$data->id));
			} else {
				return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$data->id));
			}*/
			### Thilagavathy End 21 nov 2019
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"), 'data'=>$data->id));
		}
	}
	/*** End speciality updated process ****/

	/*** Start speciality deleted process ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
		if(Speciality::where('id', $id )->count())
		{
			$result = Speciality::find($id)->delete();
			if($result == 1)
			{
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"), 'data'=>''));	
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.error_msg"), 'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"), 'data'=>'null'));
		}
	}
	/*** End speciality deleted process ***/

	/*** Start speciality  details show page ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        if(Speciality::where('id', $id )->count())
        {
            $speciality = Speciality::with('user','userupdate')->where('id',$id )->first();
            return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.empty_record_msg"), 'data'=>compact('speciality')));
        }
        else
        {
            return Response::json(array('status'=>'error', 'message'=>'Invalid Speciality ID!','data'=>'null'));
        }
	}
	/*** End speciality  details show page ***/
	
	function __destruct() 
	{
    }
}
