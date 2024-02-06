<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Icd as Icd;
use App\Models\Medcubics\Icd as AdminIcd;
use App\Models\Practice as Practice;

use Auth;
use Response;
use Request;
use Validator;
use DB;
use Lang;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class IcdApiController extends Controller 
{
	/*** Icd lists page Starts ***/
	public function getIndexApi($export = "")
	{
		$icd_arr = [];
		if($export != "") 
		{
			if($export == 'pdf' or $export == 'xlsx' or $export == 'csv') 
			{
				$table     		= "icd_10";
				$columns    	= array('icd_code','short_description','sex','effectivedate','inactivedate');
				$filename   	= "icd";
				$columnheading 	= array('Code','Short Description','Gender','Effective Date','Inactive Date');
			}
			$callexport = new CommonExportApiController();
			return $callexport->generatebulkexport($table,$columns, $filename,$columnheading,$export,$with_table='',$con_response='yes',$pcon='yes');
		}
		$icd_arr['count'] = Icd::where('id','<>', 0)->count();//$icd_arr = Icd::where('id','<>', 0)->get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd_arr')));
	}
	/*** Icd lists page Ends ***/
    
    public function getIcdListApi($export = "") {
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $icd_arr = "";
        $icd_arr = Icd::where('id', '<>', 0)->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('icd_arr')));
    }

    /*** Store Function Starts ***/
	public function getStoreApi($request='')
	{
	    if($request == '')
		$request = Request::all();              
        $practice_db_name = config('siteconfigs.connection_database');
		$validate_icd_rules = Icd::$rules+array('icd_code' => 'required|max:8|unique:icd_10,icd_code,NULL,id,deleted_at,NULL', 'medium_description' => 'required');
        $validator = Validator::make($request, $validate_icd_rules, Icd::messages());
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
			if($request['short_description'] != '')
				$request['short_description'] = $request['short_description'];
			else
				$request['short_description'] = substr($request['medium_description'],0,28);
			$request['created_by'] = Auth::user ()->id;
			$icd 	= Icd::create($request); //AdminIcd::create($request);	

			$icd_id = Helpers::getEncodeAndDecodeOfId($icd->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.icd_create_msg"),'data'=> $icd_id));
	   }
	}
	/*** Store Function Ends ***/
        
        /*** Icd details show page Starts ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$database_name	= 'responsive';
		
		//if(Icd::on($database_name)->where('id', $id)->count()>0 && is_numeric($id)) 
		if(Icd::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			//$icd = Icd::on($database_name)->where('id',$id)->select('id','icd_code','order','icdid','short_description','effectivedate','inactivedate','long_description','medium_description','icd_type','header')->first()->toArray();
			$icd = Icd::where('id',$id)->select('id','icd_code','order','icdid','short_description','effectivedate','inactivedate','long_description','medium_description','icd_type','header')->first()->toArray();
			if(Icd::where('icd_code',$icd['icd_code'])->count()>0)
			{
				$icd_curr = Icd::where('icd_code',$icd['icd_code'])->select('sex','effectivedate','inactivedate','age_limit_lower','age_limit_upper',
				'map_to_icd9','statement_description')->first()->toarray();
				$icd = array_merge($icd, $icd_curr);
			}
			else
			{
				$icd['age_limit_upper']= $icd['age_limit_lower']=$icd['map_to_icd9'] = $icd['statement_description'] = $icd['sex'] = ""	;
			}
			$icd = (object) $icd;
			$icd->id = Helpers::getEncodeAndDecodeOfId($icd->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('icd')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}		
	}
	/*** Icd details show page Ends ***/
	
	/*** Icd details edit page Starts ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$database_name	= 'responsive';
		//if(Icd::on($database_name)->where('id', $id)->count()>0 && is_numeric($id)) 
		if(Icd::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			//$icd = Icd::on($database_name)->where('id',$id)->select('id','icd_code','order','icdid','short_description','effectivedate','inactivedate','long_description','medium_description','icd_type','header')->first()->toArray();
			$icd = Icd::where('id',$id)->select('id','icd_code','order','icdid','short_description','effectivedate','inactivedate','long_description','medium_description','icd_type','header')->first()->toArray();
			if(Icd::where('icd_code',$icd['icd_code'])->count()>0)
			{
				$icd_curr = Icd::where('icd_code',$icd['icd_code'])->select('sex','effectivedate','inactivedate','age_limit_lower','age_limit_upper','map_to_icd9','statement_description')->first()->toarray();
				$icd = array_merge($icd, $icd_curr);
			}
			else
			{
				
				$icd['age_limit_lower'] = $icd['age_limit_upper']= $icd['sex'] = $icd['map_to_icd9']= $icd['statement_description'] = "";
			}
			$icd = (object) $icd;
			$icd->id = Helpers::getEncodeAndDecodeOfId($icd->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('icd')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Icd details edit page Ends ***/
	
	/*** Icd details update Starts ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$database_name	= 'responsive';
		
		//if(Icd::on($database_name)->where('id', $id)->count()>0 && is_numeric($id)) 
		if(Icd::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			$request = Request::all();
			
			$validator = Validator::make($request, Icd::$rules, Icd::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{					
				//$icd = Icd::on($database_name)->where('id',$id)->select('icd_code')->first()->toArray();
				$icd = Icd::where('id',$id)->select('icd_code')->first()->toArray();
				$insert_arr		= array();
					
				foreach($request as $request_key=>$request_val)
				{
					if($request_key!='_method' && $request_key!='_token' && $request_key!='temp_doc_id' && $request_key!='sample' && $request_key!='effectivedate' && $request_key!='inactivedate')
					{
						$insert_arr[$request_key] = $request_val;
						
					}
					if($request_key == 'effectivedate' || $request_key == 'inactivedate')
					{ 
						if($request[$request_key] != '')
							$insert_arr[$request_key]= date("Y-m-d",strtotime($request_val));
						else
							$insert_arr[$request_key] = $request_val;
					}
				}
				$icd_details = Icd::Where('icd_code',$icd['icd_code']);	
				if($icd_details->count()){
					$icd_data = $icd_details->first();
					if($insert_arr['short_description'] == '')
						$insert_arr['short_description'] = substr($icd_data->medium_description,0,28);
					$insert_arr_value = Icd::where('icd_code', $icd['icd_code'])->update($insert_arr);
				}else
				{
					$insert_arr['icd_code'] = $icd['icd_code'];
					Icd::create($insert_arr);
				}
				$user = Auth::user ()->id;
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.icd_update_msg"),'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Icd details update Ends ***/
	
	/*** Icd details delete Starts ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(is_numeric($id) && Icd::where('id',$id)->count()>0 )
		{
			Icd::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Icd details delete Ends ***/
	
	/*** Icd details search page Starts ***/
	public function geticdtablevalues($export = "")
	{		
		$request = Request::all();				
		$start = $request['start'];
		$len = $request['length'];			
        $cloum = intval($request["order"][0]["column"]);
		$order = $request['columns'][$cloum]['data'];
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
		$database_name	= 'responsive';
					
		$icd_array = Icd::where(function($query) use($search){ 		//Icd::on($database_name)					
						if($search != "") {
							return $query->where('icd_code', 'LIKE', '%'.$search.'%')
								->orWhere('short_description', 'LIKE', '%'.$search.'%');				
						}						
					})->where('deleted_at',NULL)->orderBy($order,$order_decs)->skip($start)->take($len)->get()->toArray();

		$total_rec_count = Icd::where(function($query) use($search){	// ::on($database_name)					 
						if($search != ""){
							return $query->where('icd_code', 'LIKE', '%'.$search.'%')
							->orWhere('short_description', 'LIKE', '%'.$search.'%');			
						}	
					})->where('deleted_at',NULL)->count();	
					
		foreach($icd_array as $icd)
		{
			$icd_details = $icd;
			$icd_details['id'] = Helpers::getEncodeAndDecodeOfId($icd['id']);
			
			// Format the date
			if($icd_details['effectivedate']!='0000-00-00' || $icd_details['effectivedate']!='')
				$icd_details['effectivedate'] = Helpers::dateFormat($icd_details['effectivedate']);
			
			if($icd_details['inactivedate']!='0000-00-00' || $icd_details['inactivedate']!='')
				$icd_details['inactivedate'] = Helpers::dateFormat($icd_details['inactivedate']);
			
			/* @ No need to check master and practice tables since we imported master to practice.
			if(Icd::where('icd_code',$icd['icd_code'])->count()>0)
			{				
				$icd_details[$icd['icd_code']] =  Icd::where('icd_code',$icd['icd_code'])->select('sex','effectivedate','inactivedate')->first()->toArray();
				
				// Format the date
				if($icd_details[$icd['icd_code']]['effectivedate']!='0000-00-00' || $icd_details[$icd['icd_code']]['effectivedate']!='')
					$icd_details[$icd['icd_code']]['effectivedate'] = Helpers::dateFormat($icd_details[$icd['icd_code']]['effectivedate']);
				
				if($icd_details[$icd['icd_code']]['inactivedate']!='0000-00-00' || $icd_details[$icd['icd_code']]['inactivedate']!='')
					$icd_details[$icd['icd_code']]['inactivedate'] = Helpers::dateFormat($icd_details[$icd['icd_code']]['inactivedate']);
			} 
			*/
			$icd_arr[] = $icd_details;
		}
		if(count($icd_array)==0){
			$icd_arr = array();
		}
		$data['data'] = $icd_arr;
		$data = array_merge($data,$request);
		$data['recordsTotal'] = $total_rec_count;
		$data['recordsFiltered'] = $total_rec_count;		
		return Response::json($data);	
	}
	/*** Icd details search page Ends ***/
	
	/*** Mass import icd starts ***/
	public function massImportIcd(){
		$adminDB=env('DB_DATABASE', config('siteconfigs.connection_database'));
		// Getting current practice database details
		$practice_details = Practice::getPracticeDetails();
		$dbconnection = new DBConnectionController();
		$tenantDB = $dbconnection->getpracticedbname($practice_details['practice_name']);
		
        try {
            $table_name = 'icd_10';        
            // Truncate table - @todo if necessary trucate table.
            DB::statement("TRUNCATE TABLE $table_name");
			
            //$insert = "INSERT INTO $tenantDB.$table_name SELECT * FROM $adminDB.$table_name"; 
			
			//Short desc empty means getting form medium desc first 15 characters
			
			$insert = "INSERT INTO $tenantDB.$table_name (icd_type,`order`,icd_code,icdid,header,short_description,medium_description,long_description,statement_description,sex,age_limit_lower,age_limit_upper,effectivedate,inactivedate,cpt_check,map_to_icd9,created_by,updated_by) SELECT icd_type,`order`,icd_code,icdid,header,CASE short_description WHEN '' THEN SUBSTR(medium_description, 1, 28) ELSE short_description END ,medium_description,long_description,statement_description,sex,age_limit_lower,age_limit_upper,effectivedate,inactivedate,cpt_check,map_to_icd9,created_by,updated_by FROM $adminDB.$table_name where icd_code !=''";
			\Log::info("Exception on massImportIcd. Error: " . $insert);
            DB::insert($insert);    
            return Response::json(array('status' => 'success', 'message' => 'ICD imported from master'));            
        } catch (Exception $e) {            
            \Log::info("Exception on massImportIcd. Error: " . $e->getMessage());
            return Response::json(array('status'=>'error', 'message'=> "Unable to import ICD. Please contact admin"));	
        }		
	}
	/*** Mass import icd ends ***/

	function __destruct() 
	{
            
    }
}
