<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Medcubics\Pos as Pos;
use App\Models\Medcubics\IdQualifier as IdQualifier;
use App\Models\Document as Document;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Medcubics\Modifier as Modifier;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Lang;
use App\Models\Medcubics\Practice as Practice;

class CptApiController extends Controller 
{
	/*** lists page Starts ***/
	public function getIndexApi($export = "")
	{		
		$cpt_list = "";
		if($export != "") 
		{
			if($export == 'pdf' or $export == 'xlsx' or $export == 'csv') 
			{
				$table      = "cpts";
				$columns    = array('cpt_hcpcs','short_description','billed_amount','allowed_amount','pos_id','type_of_service');
				$filename   = "cpts";
				$columnheading = array('CPT/HCPCS','Short Description','Billed Amount','Allowed Amount','POS','Type of service');
			}
			$callexport = new CommonExportApiController();
			return $callexport->generatebulkexport($table,$columns, $filename,$columnheading,$export);
		}     
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cpt_list')));
	}
	/*** lists page Ends ***/ 

	/*** Create page Starts ***/
	public function getCreateApi()
	{			
		$pos = Pos::pluck('code','id')->all();      
		$qualifier = IdQualifier::pluck('id_qualifier_name','id')->all();      
		$modifier = Modifier::where("status","Active")->pluck("code","id")->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('modifier','pos','qualifier')));
	}
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
			$validate_cpt_rules = Cpt::$rules+array('cpt_hcpcs' => 'required|unique:cpts,cpt_hcpcs,NULL,id,deleted_at,NULL');
        $validator = Validator::make($request, $validate_cpt_rules, Cpt::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$request['cpt_hcpcs'] = strtoupper($request['cpt_hcpcs']);	
			$request['modifier_id'] = (isset($request['modifier_id'])) ? implode(",",$request['modifier_id']) : '';	
			if($request['effectivedate']!='')
				$request['effectivedate']= date("Y-m-d",strtotime($request['effectivedate']));
			if($request['terminationdate']!='')
				$request['terminationdate']= date("Y-m-d",strtotime($request['terminationdate']));
			$request['clia_id']=($request['required_clia_id']=="Yes")? $request["clia_id"]:''; 
			$cpt = Cpt::create($request);
			$user= Auth::user ()->id;
			$cpt->created_by = $user;
			$cpt_id = $cpt->id;
			if(isset($request['temp_doc_id']))
			{
				if($request['temp_doc_id']!="") 
					Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $cpt_id,'temp_type_id' => '']);
			}
			$cpt->save ();
			$cpt_id = Helpers::getEncodeAndDecodeOfId($cpt->id,'encode');

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
                         $list = $mysqldbconn->select("SELECT `cpt_hcpcs`,`pos_id`,`code_type`,`referring_provider` FROM `cpts` WHERE `id` = '" . $cpt->id . "' ");
                        if(empty($list)){
                        	$request['applicable_sex'] = (isset($request['applicable_sex'])) ? $request['applicable_sex'] : '';
	                        $catList = $mysqldbconn->insert("insert into `cpts` (`cpt_hcpcs`, `pos_id`, `short_description`,  `long_description`, `medium_description`, `applicable_sex`, `referring_provider`, `age_limit`, `effectivedate`, `terminationdate`, `allowed_amount`, `billed_amount`, `required_clia_id`, `work_rvu`, `facility_practice_rvu`, `nonfacility_practice_rvu`, `pli_rvu`, `total_facility_rvu`, `total_nonfacility_rvu`, `status`, `modifier_id`, `revenue_code`, `drug_name`, `ndc_number`, `min_units`, `max_units`, `service_id_qualifier`, `created_at`, `updated_at`, `created_by`, `updated_by`) values ('" . $request['cpt_hcpcs'] . "', '" . $request['pos_id'] . "', '" . $request['short_description'] . "', '" . $request['long_description'] . "', '" . $request['medium_description'] . "', '" . $request['applicable_sex'] . "', '" . $request['referring_provider'] . "', '" . $request['age_limit'] . "', '" . $request['effectivedate'] . "', '" . $request['terminationdate'] . "', '" . $request['allowed_amount'] . "', '" . $request['billed_amount'] . "', '" . $request['required_clia_id'] . "', '" . $request['work_rvu'] . "', '" . $request['facility_practice_rvu'] . "', '" . $request['nonfacility_practice_rvu'] . "', '" . $request['pli_rvu'] . "', '" . $request['total_facility_rvu'] . "', '" . $request['total_nonfacility_rvu'] . "', '" . $request['status'] . "', '" . $request['modifier_id'] . "', '" . $request['revenue_code'] . "', '" . $request['drug_name'] . "', '" . $request['ndc_number'] . "', '" . $request['min_units'] . "', '" . $request['max_units'] . "', '" . $request['service_id_qualifier'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "', '" . Auth::user ()->id . "', '" . Auth::user ()->id . "')");

	                        	if(isset($request['temp_doc_id']))
								{
									if($request['temp_doc_id']!="") 									
									$temp_type_id = '';
								    $catList = $mysqldbconn->update("update documents SET type_id = '".$cpt_id."',temp_type_id = '".$temp_type_id."' where temp_type_id=".$request['temp_doc_id']);	
								}
		                       	if($catList){
		                       		 \Log::info("CPT Inserted");	echo "CPT Inserted In ".$db_name."<br>";
		                       	} else{
		                       		 \Log::info("CPT NOT Inserted");	echo "CPT Inserted NOT In ".$db_name."<br>";
		                       	}
	                       	}            					

	                    } else {
	                        \Log::info("No practice found");	echo "No practice found<br>";
	                    } 
					}
					return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$cpt_id));
				} else {
					return Response::json(array('status'=>'success', 'message'=>"Practice Not found",'data'=>$cpt_id));
				}
				### Thilagavathy End 21 nov 2019

			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$cpt_id));					
		}
	}
	/*** Store Function Ends ***/
	
	/*** Show page Starts ***/ 
	public function getShowApi($id)
	{				
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Cpt::where('id', $id )->count()>0 && is_numeric($id))
		{
			$cpt = Cpt::with('pos','user','userupdate','qualifier')->where('id',$id)->first();	
			$cpt->modifier_id = ($cpt->modifier_id !='') ? implode(",",Modifier::whereIn("id",explode(",",$cpt->modifier_id))->pluck("code")->all()): '';
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('cpt')));	
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
		if(Cpt::where('id', $id )->count()>0 && is_numeric($id))
		{
			$cpt = Cpt::findOrFail($id);
			$cpt->modifier_id = ($cpt->modifier_id !='') ? explode(",",$cpt->modifier_id): '';
			$pos = Pos::pluck('pos','id')->all();
			$qualifier = IdQualifier::pluck('id_qualifier_name','id')->all();
			$modifier = Modifier::where("status","Active")->pluck("code","id")->all();
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cpt','modifier','pos','qualifier')));
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
		if(Cpt::where('id', $id )->count()>0 && is_numeric($id))
		{
			$request = Request::all();
			$validate_cpt_rules = Cpt::$rules+array('cpt_hcpcs' => 'required|unique:cpts,cpt_hcpcs,'.$id.',id,deleted_at,NULL');
			$validator = Validator::make($request, $validate_cpt_rules, Cpt::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$request['modifier_id'] = (isset($request['modifier_id'])) ? implode(",",$request['modifier_id']) : '';	
				$request['cpt_hcpcs'] = strtoupper($request['cpt_hcpcs']);	
				if($request['effectivedate']!="")	$request['effectivedate']= date("Y-m-d",strtotime($request['effectivedate']));
				if($request['terminationdate']!="")	$request['terminationdate']= date("Y-m-d",strtotime($request['terminationdate']));	
				$request['clia_id']=($request['required_clia_id']=="Yes")? $request["clia_id"]:''; 
				$cpt = Cpt::findOrFail($id);
				$cpt->update($request);
				$user = Auth::user ()->id;
				$cpt->updated_by = $user;
				$cpt->save ();
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

                        // Update the Insurance types                        
                        $catList = $mysqldbconn->update("update cpts SET cpt_hcpcs = '".$request['cpt_hcpcs']."', pos_id = '".$request['pos_id']."',referring_provider = '".$request['referring_provider']."',short_description = '".$request['short_description']."',long_description = '".$request['long_description']."',medium_description = '".$request['medium_description']."',applicable_sex = '".$request['applicable_sex']."',age_limit = '".$request['age_limit']."',allowed_amount = '".$request['allowed_amount']."',billed_amount = '".$request['billed_amount']."',effectivedate = '".$request['effectivedate']."',terminationdate = '".$request['terminationdate']."',required_clia_id = '".$request['required_clia_id']."', work_rvu = '".$request['work_rvu']."', facility_practice_rvu = '".$request['facility_practice_rvu']."', nonfacility_practice_rvu = '".$request['nonfacility_practice_rvu']."', pli_rvu = '".$request['pli_rvu']."', total_facility_rvu = '".$request['total_facility_rvu']."', status = '".$request['status']."', modifier_id = '".$request['modifier_id']."', revenue_code = '".$request['revenue_code']."', drug_name = '".$request['drug_name']."', ndc_number = '".$request['ndc_number']."', min_units = '".$request['min_units']."', max_units = '".$request['max_units']."', service_id_qualifier = '".$request['service_id_qualifier']."', updated_at = '".date('Y-m-d H:i:s')."' where id=".$id);	                 
	                       	if($catList){
	                       		 \Log::info("CPT Inserted");	echo "CPT Update In ".$db_name."<br>";
	                       	} else{
	                       		 \Log::info("CPT NOT Inserted");	echo "CPT Update NOT In ".$db_name."<br>";
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
		if(Cpt::where('id',$id)->count()>0 && is_numeric($id))
		{
			Cpt::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}		
	}
	/*** Delete Function Ends ***/
	
	/*** Cpt values Function Starts ***/
	public function getcptvaluesAdmin()
	{		
		$request 	= Request::all();				
		$start 		= $request['start'];
		$len 		= $request['length'];
        $cloum 		= intval($request["order"][0]["column"]);
		$order 		= $request['columns'][$cloum]['data'];
		$cpt_arr_details = [];
			
		$order_decs = $request["order"][0]["dir"];
		$search = '';
        if(!empty($request['search']['value']))
		{
			$search	= $request['search']['value'];
		}
        $cpt = 	Cpt::with('pos')
				->where(function($query) use($search){ return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
				->orWhere('short_description', 'LIKE', '%'.$search.'%')
				->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('pos_id', 'LIKE', '%'.$search.'%')
				->orWhere('type_of_service', 'LIKE', '%'.$search.'%');})->where('deleted_at',NULL)->orderBy($order,$order_decs)->skip($start)->take($len)->get()->toArray();										
		$total_rec_count 	= Cpt::with('pos')
				->where(function($query) use($search){ return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
				->orWhere('short_description', 'LIKE', '%'.$search.'%')
				->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('pos_id', 'LIKE', '%'.$search.'%')
				->orWhere('type_of_service', 'LIKE', '%'.$search.'%');})->where('deleted_at',NULL)->count();
		foreach($cpt as $cpt)
		{
			$cpt_details = $cpt;
			$cpt_details['id'] = Helpers::getEncodeAndDecodeOfId($cpt['id']);
			
			$cpt_arr_details[] = $cpt_details;
		}      
		$data['data'] = $cpt_arr_details;		
		$data 				= array_merge($data,$request);
		$data['recordsTotal'] 		= $total_rec_count;
		$data['recordsFiltered'] 	= $total_rec_count;		
		return Response::json($data);
	}
	/*** Cpt values Function Ends ***/
	
	function __destruct() 
	{
    }
}
