<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Taxanomy;
use App\Models\Medcubics\Speciality;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Request;
use Response;
use Validator;
use Auth;
use Lang;
use App\Models\Medcubics\Practice as Practice;
use DB;

class TaxanomyApiController extends Controller 
{
	/*** lists page Starts ***/
	public function getIndexApi($export = "")
	{
		$taxanomies = Taxanomy::with('speciality')->get();
		if($export != "")
		{	
			$table      = "taxanomies";
			$with_table = "specialities.speciality_id";
			$columns    = array('code','speciality','description');
			$filename   = "taxanomies";
			$columnheading = array('Code','Speciality ID','Description');
			$callexport = new CommonExportApiController();
			return $callexport->generatebulkexport($table,$columns, $filename,$columnheading,$export,$with_table);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('taxanomies')));
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/	
	public function getCreateApi()
	{
		$specialities = Speciality::pluck('speciality','id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('specialities')));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function getStoreApi()
	{
		$request = Request::all();
		$validation_rules = Taxanomy::$rules+array('code'  => 'required|alpha_num|not_in:0|unique:taxanomies,code,NULL,id,deleted_at,NULL|max:10');
		$validator = Validator::make($request, $validation_rules, Taxanomy::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$data = Taxanomy::create($request);
			$user = Auth::user ()->id;
			$data->created_by = $user;
			$data->updated_at = date('Y-m-d H:i:s');
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
                     $list = $mysqldbconn->select("SELECT `speciality_id`,`id` FROM `taxanomies` WHERE `id` = '" . $data->id . "' ");
                    if(empty($list)){
                        $catList = $mysqldbconn->insert("insert into `taxanomies` (`speciality_id`,`description`,`code`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['speciality_id'] . "','" . $request['description'] . "','" . $request['code'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
	                       	if($catList){
	                       		 \Log::info("Taxanomy Inserted");	echo "Taxanomy Inserted In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("Taxanomy NOT Inserted");	echo "Taxanomy Inserted NOT In ".$db_name."<br>";
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
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->encId));
		}
	}
	/*** Store Function Ends ***/
	
	/*** Edit page Starts ***/ 
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Taxanomy::where('id', $id)->count())
		{
			$taxanomy			= 	Taxanomy::with('speciality')->findOrFail($id);
            $specialities       =   Speciality::pluck('speciality','id')->all();
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('taxanomy','specialities')));
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
       $request = Request::all();
       $validation_rules = Taxanomy::$rules+array('code'  => 'required|alpha_num|not_in:0|unique:taxanomies,code,'.$id.'|max:10');
	  
        $validator = Validator::make($request,$validation_rules, Taxanomy::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
			$data = Taxanomy::findOrFail($id);
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
                  	// update taxanomy
                     $catList = $mysqldbconn->update("update taxanomies SET speciality_id = '".$request['speciality_id']."',code = '".$request['code']."',description = '".$request['description']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);		 
                       	if($catList){
                       		 \Log::info("Taxanomy Updated");	echo "Taxanomy Updated In ".$db_name."<br>";
                       	} else{
                       		 \Log::info("Taxanomy NOT Updated");	echo "Taxanomy Updated NOT In ".$db_name."<br>";
                       	}
                   
                    } else {
                        \Log::info("No practice found");	echo "No practice found<br>";
                    } 
				}
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->id));
			} else {
				return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$data->id));
			}*/
			### Thilagavathy End 21 nov 2019
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>$data->id));
		}        
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Taxanomy::where('id', $id )->count())
		{
			$result = Taxanomy::find($id)->delete();
			if($result == 1)
			{
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.error_msg"),'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** Delete Function Ends ***/
	
	/*** Show Function Starts ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
        if(Taxanomy::where('id', $id )->count())
        {
            $taxanomy = Taxanomy::with('speciality','user','userupdate')->where('id', $id )->first();
            return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>compact('taxanomy')));
        }
        else
        {
            return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
        }
	}
	/*** Show Function ends ***/
	
	function __destruct() 
	{
    }
}
