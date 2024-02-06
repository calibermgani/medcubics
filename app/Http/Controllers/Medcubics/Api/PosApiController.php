<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Response;
use Request;
use Validator;
use Auth;
use Lang;
use DB;
use App\Models\Medcubics\Pos;
use App\Models\Practice as Practice;
use Config;

class PosApiController extends Controller 
{
	/*** Start to Export the Place of Service	 ***/
	public function getIndexApi($export = "")
	{
		$pos = Pos::with('user','userupdate')->get();
		if($export != "")
		{
			$exportparam = array(
					'filename'	=> 'place-of-service',
					'heading'	=> 'Place of Service(POS)',
					'fields'	=> array(
					'code'		=> 'Code',
					'pos'		=> 'Place of Service',
					'created_by'	=>	array('table'=>'user' ,	'column' => 'short_name' ,	'label' => 'Created By'),
					'created_at' 	=> 'Created On',
					'updated_by' 	=>	array('table'=>'userupdate' ,	'column' => 'short_name' ,	'label' => 'Updated By'),
					'updated_at'	=> 'Updated On',
					));
					
			$callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $pos, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('pos')));
	}
	/*** End to Export the Place of Service	 ***/
		
	/*** Start to Create the Place of Service	 ***/	
	public function getCreateApi()
	{
		return Response::json(array('status'=>'success', 'message'=>null));
	}
	/*** End to Create the Place of Service	 ***/
	
	/*** Start to Store the Place of Service	 ***/
	public function getStoreApi()
	{
		$request = Request::all();
		$validation_rules = Pos::$rules+array('code' => 'required|numeric|not_in:0|unique:pos');
		$validator = Validator::make($request, $validation_rules, Pos::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{			
			$data = Pos::create($request);
			$user = Auth::user ()->id;
			$data->created_by = $user;
			$data->updated_by = $user;
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
					$value = 'rural_physicians_group_pannu_pllc';
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

                        // Update the statement category to statement as 'Yes;, hold_release_date as '0000-00-00', hold_reason as 0
                        $catList = $mysqldbconn->insert("insert into `pos` (`code`, `pos`, `created_at`, `updated_at`, `created_by`, `updated_by`) values (" . $request['code'] . ", '" . $request['pos'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
	                       	if($catList){
	                       		 \Log::info("POS Inserted");	echo "POS Inserted In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("POS NOT Inserted");	echo "POS Inserted NOT In ".$db_name."<br>";
	                       	}              					

	                    } else {
	                        \Log::info("No practice found");	echo "No practice found<br>";
	                    } 
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->encId));
				} else {
					return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$data->encId));
				}
				### update the POS To All Practice thilagavathy P END
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->encId));
		}
	}
	/*** End to Store the Place of Service	 ***/
	
	/*** Start to Edit the Place of Service  ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Pos::where('id', $id)->count())
		{
			$pos = Pos::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('pos')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Place of Service	 ***/

	/*** Start to Update the Place of Service	 ***/
	public function getUpdateApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$request = Request::all();
        $validation_rules = Pos::$rules+array('code' => 'required|numeric|not_in:0|unique:pos,code,'.$id);
		$validator = Validator::make($request,$validation_rules, Pos::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
			$data = Pos::findOrFail($id);
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
                        // UPDATE POS details IN All Practice
                        $catList = $mysqldbconn->update("update pos SET code = '".$request['code']."', pos = '".$request['pos']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);
	                       	if($catList){
	                       		 \Log::info("POS Updated");	echo "POS Updated In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("POS NOT Updated");	echo "POS Updated NOT In ".$db_name."<br>";
	                       	}              					

	                    } else {
	                        \Log::info("No practice found");	echo "No practice found<br>";
	                    } 
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>$data->id));
				} else {
					return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$data->id));
				}*/
				### update the POS To All Practice thilagavathy P END
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>$data->id));
		}
	}
	/*** End to Update the Place of Service	 ***/

	/*** Start to Destory the Place of Service	 ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Pos::where('id', $id )->count())
		{
			$result = Pos::find($id)->delete();
			if($result == 1)
			{
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
			}
			else
			{
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** End to Destory the Place of Service ***/

	/*** Start to Show the Place of Service	 ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Pos::where('id', $id )->count())
		{
			$pos = Pos::with('user','userupdate')->where('id', $id )->first();
			return Response::json(array('status'=>'success', 'message'=>'POS details found.','data'=>compact('pos')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>'No POS details found.','data'=>'null'));
		}
	}
	/*** End to Show the Place of Service	 ***/
	
	function __destruct() 
	{
    }
}