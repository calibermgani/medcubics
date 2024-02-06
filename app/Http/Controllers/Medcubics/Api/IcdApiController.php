<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Icd as Icd;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Lang;
use App\Models\Medcubics\Practice as Practice;

class IcdApiController extends Controller 
{	
	/*** lists page Starts ***/
	public function getIndexApi($export = "")
	{
		$icd_arr = "";
		if($export != "") 
		{
			if($export == 'pdf' or $export == 'xlsx' or $export == 'csv') 
			{
				$table      	= "icd_10";
				$columns    	= DB::raw('icd_code, short_description, sex , date_format(effectivedate, "%m/%d/%y") AS effdate,date_format(inactivedate, "%m/%d/%y") AS indate');
				$filename   	= "icd";
				$columnheading 	= array('Code','Short Description','Gender','Effective Date','Inactive Date');
			}
			
			$callexport = new CommonExportApiController();
			return $callexport->generatebulkexport($table,$columns, $filename,$columnheading,$export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd_arr')));
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function getCreateApi()
	{			
		$icd = Icd::all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd')));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		$validate_icd_rules = Icd::$rules+array('icd_code' => 'required|max:8|unique:icd_10,icd_code,NULL,id,deleted_at,NULL');
        $validator = Validator::make($request, $validate_icd_rules, Icd::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$request['icd_code'] = strtoupper($request['icd_code']);	
			if($request['effectivedate']!='')
				$request['effectivedate'] 		= date("Y-m-d",strtotime($request['effectivedate'])); 
			if($request['inactivedate']!='')
				$request['inactivedate'] 		= date("Y-m-d",strtotime($request['inactivedate'])); 	
			$icd 	= Icd::create($request);
			$user 	= Auth::user ()->id;
			$icd->created_by = $user;
			$icd->updated_at = date('Y-m-d H:i:s');
			$icd->save ();
			$icd->encId = Helpers::getEncodeAndDecodeOfId($icd->id,'encode');

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
                        $request['sex'] = (isset($request['sex'])) ? $request['sex'] :'';
                         $list = $mysqldbconn->select("SELECT `icd_type`,`order`,`icd_code`,`id` FROM `icd_10` WHERE `id` = '" . $icd->id . "' ");
                        if(empty($list)){
	                        $catList = $mysqldbconn->insert("insert into `icd_10` (`icd_type`, `header`, `icd_code`, `short_description`,  `long_description`, `medium_description`, `sex`, `age_limit_lower`, `age_limit_upper`, `effectivedate`, `inactivedate`, `map_to_icd9`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['icd_type'] . "', '" . $request['header'] . "', '" . $request['icd_code'] . "', '" . $request['short_description'] . "', '" . $request['long_description'] . "', '" . $request['medium_description'] . "', '" . $request['sex'] . "', '" . $request['age_limit_lower'] . "', '" . $request['age_limit_upper'] . "', '" . $request['effectivedate'] . "', '" . $request['inactivedate'] . "', '" . $request['map_to_icd9'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");
		                       	if($catList){
		                       		 \Log::info("ICD Inserted");	echo "ICD Inserted In ".$db_name."<br>";
		                       	} else{
		                       		 \Log::info("ICD NOT Inserted");	echo "ICD Inserted NOT In ".$db_name."<br>";
		                       	}
	                       	}            					

	                    } else {
	                        \Log::info("No practice found");	echo "No practice found<br>";
	                    } 
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$icd->encId));
				} else {
					return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$icd->encId));
				}
				### Thilagavathy End 21 nov 2019

				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$icd->encId));
		}
	}
	/*** Store Function Ends ***/
	
	/*** Show page Starts ***/ 
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Icd::where('id', $id )->count()>0 && is_numeric($id))
		{
			$icd = Icd::with('user','userupdate')->where('id',$id)->first();	
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('icd')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}		
	}
	/*** Show Function Ends ***/
	
	/*** Edit page Starts ***/ 
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Icd::where('id', $id )->count()>0 && is_numeric($id))
		{
			$icd = Icd::findOrFail($id);
			if($icd['effectivedate'] != '0000-00-00') $icd['effectivedate']	=	date('m/d/Y',strtotime($icd['effectivedate']));
			else $icd['effectivedate']	=	'';
			
			if($icd['inactivedate'] != '0000-00-00')  $icd['inactivedate'] 	=	date('m/d/Y',strtotime($icd['inactivedate']));
			else $icd['inactivedate']	=	'';
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}	
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$request = Request::all();
		if(Icd::where('id', $id )->count()>0 && is_numeric($id)) 
		{
			$validate_icd_rules = Icd::$rules+array('icd_code' => 'required|max:8|unique:icd_10,icd_code,'.$id.',id,deleted_at,NULL');
			$validator = Validator::make($request, $validate_icd_rules, Icd::$messages );
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{			
				$request['icd_code'] = strtoupper($request['icd_code']);
				if($request['effectivedate']!='')
					$request['effectivedate'] 	= date("Y-m-d",strtotime($request['effectivedate'])); 
				if($request['inactivedate']!='')
					$request['inactivedate'] 	= date("Y-m-d",strtotime($request['inactivedate'])); 
				$user 	= 	Auth::user ()->id;
				$icd 	= 	Icd::findOrFail($id);
				$icd->update($request);
				$icd->updated_by = $user;
				$icd->save ();

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
	                        $catList = $mysqldbconn->update("update icd_10 SET icd_type = '".$request['icd_type']."', header = '".$request['header']."',icd_code = '".$request['icd_code']."',short_description = '".$request['short_description']."',long_description = '".$request['long_description']."',medium_description = '".$request['medium_description']."',sex = '".$request['sex']."',age_limit_lower = '".$request['age_limit_lower']."',age_limit_upper = '".$request['age_limit_upper']."',effectivedate = '".$request['effectivedate']."',inactivedate = '".$request['inactivedate']."',map_to_icd9 = '".$request['map_to_icd9']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);	                 
	                       	if($catList){
	                       		 \Log::info("ICD Inserted");	echo "ICD Update In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("ICD NOT Inserted");	echo "ICD Update NOT In ".$db_name."<br>";
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
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Icd::where('id',$id)->count()>0 && is_numeric($id))
		{
			Icd::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Delete Function Ends ***/
	
	/*** Icd values starts ***/
	public function geticd10valuesAdmin()
	{		
		$request 	= Request::all();				
		$start 		= $request['start'];
		$len 		= $request['length'];
        $cloum 		= intval($request["order"][0]["column"]);
		$order 		= $request['columns'][$cloum]['data'];
		if($request['columns'][$cloum]['data'] == 'favourite')
		{
			$order = 'id';
		}	
		$order_decs = $request["order"][0]["dir"];
        $search = '';
		if(!empty($request['search']['value']))
		{
			$search= $request['search']['value'];
		}
		$icd_arr = 	Icd::with('user','userupdate')
				->where(function($query) use($search){ 
				return $query->where('icd_code', 'LIKE', '%'.$search.'%')
				->orWhere('short_description', 'LIKE', '%'.$search.'%')
				->orWhere('sex', 'LIKE', '%'.$search.'%')
				->orWhere('order', 'LIKE', '%'.$search.'%')	;			
	   })->where('deleted_at',NULL)->orderBy($order,$order_decs)->skip($start)->take($len)->get()->toArray();
		$total_rec_count 		= Icd::with('user','userupdate')
						->where(function($query) use($search){ 
						return $query->where('icd_code', 'LIKE', '%'.$search.'%')
						->orWhere('short_description', 'LIKE', '%'.$search.'%')
						->orWhere('sex', 'LIKE', '%'.$search.'%')
						->orWhere('order', 'LIKE', '%'.$search.'%')	;			
						})->where('deleted_at',NULL)->count();		 
		$admin_icd_arr =[];
		foreach($icd_arr as $icd)
		{
			$icd_details = $icd;
			$icd_details['id'] = Helpers::getEncodeAndDecodeOfId($icd['id']);
			
			// Format the date
			if($icd_details['effectivedate']!='0000-00-00' || $icd_details['effectivedate']!='')
				$icd_details['effectivedate'] = Helpers::dateFormat($icd_details['effectivedate']);
			
			if($icd_details['inactivedate']!='0000-00-00' || $icd_details['inactivedate']!='')
				$icd_details['inactivedate'] = Helpers::dateFormat($icd_details['inactivedate']);
			
			$admin_icd_arr[] = $icd_details;
		}		
		$data['data']		 	= $admin_icd_arr;
		$data 					= 	array_merge($data,$request);
		$data['recordsTotal'] 	= $total_rec_count;
		$data['recordsFiltered']= $total_rec_count;		
		return Response::json($data);	
	}
	/*** Icd values Ends ***/
	
	function __destruct() 
	{
    }
}
