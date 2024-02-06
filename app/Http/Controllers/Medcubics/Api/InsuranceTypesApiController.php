<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Insurancetype;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Response;
use Request;
use Validator;
use Input;
use Lang;
use Config;
use DB;
use App\Models\Medcubics\Practice as Practice;

class InsuranceTypesApiController extends Controller 
{
	/*** Start to listing the Insurance Types  ***/
	public function getIndexApi($export='')
	{
		$insurancetypes = InsuranceType::with('user','userupdate')->get();
		### Export the Insurance Type ###
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'Insurancetypes',
				'heading'		=>	'Insurance Types',
				'fields' 		=>	array(
				'type_name'		=>	'Type Name',
				'updated_at'	=>	'Updated On',
				'created_by'	=>	array('table'=>'user','column' => 'short_name','label' => 'Created by'),
				'updated_by'	=>	array('table'=>'userupdate','column' => 'short_name','label' => 'Updated by'),
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
			$request['created_by'] = Auth::user ()->id;
			$request['updated_by'] = Auth::user ()->id;			
			$data = InsuranceType::create($request);					
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
						$db_name = $db->getpracticedbname('rural_physicians_group_pannu_pllc');
						$query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
	          			$dbs = DB::select($query, [$db_name]);      
	          			echo "<br> Start processing :".$db_name."<br>";					       
		    		if(!empty($dbs)){
                        $db = new DBConnectionController();			 	
                        $db->configureConnectionByName($db_name);
                        $mysqldbconn = DB::Connection($db_name);
                        $current_date =  date('Y-m-d');

                        // Update the Insurance types
                         $list = $mysqldbconn->select("SELECT code,type_name,cms_type,id as type_id FROM `insurancetypes` WHERE `id` = '" . $data->id . "' ");
                        if(empty($list)){
	                        $catList = $mysqldbconn->insert("insert into `insurancetypes` (`code`, `type_name`, `cms_type`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['code'] . "', '" . $request['type_name'] . "', '" . $request['cms_type'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
		                       	if($catList){
		                       		 \Log::info("Insurance Type Inserted");	echo "Insurance Type Inserted In ".$db_name."<br>";
		                       	} else{
		                       		 \Log::info("Insurance Type NOT Inserted");	echo "Insurance Type Inserted NOT In ".$db_name."<br>";
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
			### Thilagavathy End 11 nov 2019
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->encId));
		}
	}
	/*** End to Store the Insurance Types	 ***/
	
	/*** Start to Edit the Insurance Types	 ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && InsuranceType::where('id', $id)->count())
		{
			$insurancetypes = InsuranceType::findOrFail($id);
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
		$val_InsuranceType_rules = array('type_name' => 'required|unique:insurancetypes,type_name,'.$id.',id,deleted_at,NULL');
		$validator = Validator::make($request, $val_InsuranceType_rules, InsuranceType::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{
			$insurancetypes = InsuranceType::findOrFail($id);
			$insurancetypes->update(Request::all());
			$user = Auth::user ()->id;
			$insurancetypes->updated_by = $user;
			$insurancetypes->save ();
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
                        $catList = $mysqldbconn->update("update insurancetypes SET code = '".$request['code']."', type_name = '".$request['type_name']."',cms_type = '".$request['cms_type']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);
	                       	if($catList){
	                       		 \Log::info("Insurance Type Inserted");	echo "Insurance Type Inserted In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("Insurance Type NOT Inserted");	echo "Insurance Type Inserted NOT In ".$db_name."<br>";
	                       	}              					

	                    } else {
	                        \Log::info("No practice found");	echo "No practice found<br>";
	                    } 
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));
				} else {
					return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>''));
				}*/
			### Thilagavathy End 11 nov 2019
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
		if(InsuranceType::where('id', $id )->count())
		{
			$insurancetypes = InsuranceType::with('user','userupdate')->where('id', $id)->first();
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