<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feeschedule as Feeschedule;
use App\Models\Favouritecpts;
use Auth;
use Response;
use Request;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Imports\FeescheduleImport;
use Excel;
use App\Models\Cpt as Cpt;
use App\Models\MultiFeeschedule as MultiFeeschedule; 
use App\Models\Modifier as Modifier;
use Schema;
use DB;
use App;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Config;
use Lang;
use Log;

class FeescheduleApiController extends Controller 
{
	/*** Feeschedule listing start ***/
	public function getIndexApi($export = "")
	{
		$practice_timezone = Helpers::getPracticeTimeZone(); 
		$feeschedules = MultiFeeschedule::with(['user','userupdate','feeSchedule' => function($query1) use($practice_timezone) {
		                            $query1->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'));
		                        },'insuranceInfo'])->groupBy('fee_schedule_id')->orderBy('created_at','DESC')->get();
		// dd($feeschedules);
		if($export != "")
		{
			$fee_r = $fee_list = array();
			foreach($feeschedules as $key=>$value)
			{
				if($value->percentage != ''){ 
					if($value->conversion_factor == 'decimal') $conv_factor_dis = "Decimal";
					elseif($value->conversion_factor == 'round_off') $conv_factor_dis = "Round off";
				}
				else 
					$conv_factor_dis = ""; 
									
				$file_name_arr		 		= explode(".",@$value->feeSchedule->file_name);
				$fee_r['file_name']			= str_limit(@$file_name_arr[0], 25, '...');
				$fee_r['choose_year']		= @$value->feeSchedule->choose_year;
				$fee_r['insurance']			= (isset($value->insuranceInfo->short_name) && !empty($value->insuranceInfo->short_name)) ? $value->insuranceInfo->short_name : "Default";
				$fee_r['percentage']		= @$value->feeSchedule->percentage;
				$fee_r['short_name']		= $value->user->short_name;
				$fee_r['created_at']		= Helpers::dateFormat($value->created_at);
				$fee_list[$key] 			= $fee_r;
				unset($fee_r);
			}
			$get_fee_list = json_decode(json_encode($fee_list));
			$exportparam = array(
							'filename'	=>	'Fee Schedule',
							'heading'	=>	'Fee Schedule',
							'fields' 	=>	array(
											'file_name'			=>	'File Name',
											'choose_year'		=>	'Year',
											'insurance'			=>	'Insurance',
											'percentage'		=>	'Percentage',
											'short_name'		=>	'Created By',
											'created_at'		=>	'Uploaded On'
											)
						);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $get_fee_list, $export);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('feeschedules')));
	}
	/*** Feeschedule listing end ***/
	
	/*** Feeschedule create details start ***/
	public function getCreateApi()
	{
		$feeschedules 		= Feeschedule::all();
		$fav_count = Cpt::has('favourite')->count();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('feeschedules','fav_count')));
	}
	/*** Feeschedule create details end ***/
	
	/*** Feeschedule store details start ***/
	public function getStoreApi()
	{
		$file 		= Request::file('upload_file');
		$request 	= Request::all();
		$user_id 		= Auth::user()->id;
		$org_filename	= $file->getClientOriginalName();
		$new_filename	= 'Favourite_Cpts_'.date('Y-m-d_H:i:s').".".$file->getClientOriginalExtension();
		$chk_env_site   = getenv('APP_ENV');
		
		if($chk_env_site==  Config::get('siteconfigs.production.defult_production'))
		{
			$storage_disk = "s3_production";
		}
		else
		{
 			$storage_disk = "s3";
		}
		
		$validationError	= 0;
		$new_cpt_codes = '';
		$database_name	= 'responsive';
		$tempId = rand();
		global $updateValidation;
		DB::beginTransaction();
		try{
			$rows = Excel::toArray(new FeescheduleImport(),$file);
			if(!empty($rows))	
			foreach($rows[0] as $row){		            	 	
				if($row[0]!="CPT / HCPCS"){		            
		            $validator = Validator::make(
		                        array('cpt_hcpcs'=>$row[0],
		                            'billed_amount' => $row[1], 
		                            'allowed_amount' => $row[2]),
		                            ['cpt_hcpcs' => 'required|min:5|alpha_num',
		                            'billed_amount' => 'required|numeric',
		                            'allowed_amount' => 'required|numeric',
		                            'modifier_1' => 'min:2',
		                            'modifier_2' => 'min:2',
		                            'modifier_3' => 'min:2',
		                            'modifier_4' => 'min:2'
		                            ]);

		            if ($validator->fails()) {
		            	$validationError	= 0;
		                DB::rollback();
		                $errors = $validator->errors()->all();
		                return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		            } else{
		                // Variable used for multi fee schedule
		                $year = $request['choose_year'];
		                $insurance = $request['insurance_id'];
		                $percentage         = $request['percentage'];
		                
		                // Practice Cpt Details 
		                $cptInfo        = Cpt::Where('cpt_hcpcs',$row[0]);
		                $cptCount   =   $cptInfo->count();
		                $cptDetails =   $cptInfo->get()->first();
		                // Practice Cpt Details 
		                if(isset($cptDetails)){
		                    // Practice Favourite Cpt details 
		                    $favCptInfo = Favouritecpts::Where('cpt_id',$cptDetails->id);
		                    $favCptCount = $favCptInfo->count();
		                    $favCptCount = $favCptInfo->get()->first();
		                    
		                    // Multi Fee Schedule Cpt Details 
		                    $multiCptInfo       =    MultiFeeschedule::where('cpt_id',$cptDetails->id)->where('year',$year)->where('insurance_id',$insurance);
		                    $multiCptCount      =    $multiCptInfo->count();
		                    $multiCptDetails    =    $multiCptInfo->get()->first();
		                }
		                
		                $modifier = '';
		                if(isset($row[3]) && !empty($row[3]) && $row[3] != Null){
		                    $modifierInfo = Modifier::where('code',$row[3])->get()->first();
		                    $modifier .= (isset($modifierInfo->id)) ? $modifierInfo->id."," : '';
		                }
		                if(isset($row[4]) && !empty($row[4]) && $row[4] != Null){
		                    $modifierInfo = Modifier::where('code',$row[4])->get()->first();
		                    $modifier .= (isset($modifierInfo->id)) ? $modifierInfo->id."," : '';
		                }
		                if(isset($row[5]) && !empty($row[5]) && $row[5] != Null){
		                    $modifierInfo = Modifier::where('code',$row[5])->get()->first();
		                    $modifier .= (isset($modifierInfo->id)) ? $modifierInfo->id."," : '';
		                }
		                if(isset($row[6]) && !empty($row[6]) && $row[6] != Null){
		                    $modifierInfo = Modifier::where('code',$row[6])->get()->first();
		                    $modifier .= (isset($modifierInfo->id)) ? $modifierInfo->id."," : '';
		                }
		                if($cptCount > 0)
		                {
		                    if($percentage!='')
		                    {
		                        if($row[2] == 0)
		                            $row[1] = 0;
		                        else
		                        {
		                            $percentage_val = ($row[2]/100)*$percentage;
		                            $row[1] = $row[2] + $percentage_val;
		                            $row[1] = floatval($row[1]);
		                        }
		                    }
		                    else
		                    {
		                        if($row[1] == 0 || $row[1] == '' )
		                            $row[1] = 0;
		                    }
							
							
							
		                    if(isset($cptDetails) && $multiCptCount == 0)
		                    {
		                        $multiInsertArr = array('cpt_id'=>$cptDetails->id, 'created_by'=>$user_id, 'fee_schedule_id'=>$tempId,'year'=>$year,'insurance_id'=>$insurance,'billed_amount'=>$row[1],'allowed_amount'=>$row[2],'modifier_id'=>$modifier);
		                        MultiFeeschedule::create($multiInsertArr);
		                    }
		                    else{
		                        if (array_key_exists("updateSuccess", $request)) {
		                            $multiUpdateArr = array('updated_by'=>$user_id, 'fee_schedule_id'=>$tempId,'billed_amount'=>$row[1],'allowed_amount'=>$row[2],'modifier_id'=>$modifier);
		                            MultiFeeschedule::where('cpt_id',$cptDetails->id)->where('year',$year)->where('insurance_id',$insurance)->update($multiUpdateArr);
		                            $new_cpt_codes .= $row[0].',';
		                        }
		                        else {
		                            $updateValidation = 2;
		                        }
		                       /*  $insert_arr     = array('cpt_hcpcs'=>$row[0],'allowed_amount'=>$row[2],'billed_amount'=>$row[1]);
		                        Cpt::create($insert_arr);  */
		                    }
		                    // Favourite if not available means create that cpt to favourite  
		                    if(Favouritecpts::Where('cpt_id',$cptDetails->id)->count() == 0)
		                    { 
		                        Favouritecpts::create(['cpt_id'=>$cptDetails->id,'user_id'=>$user_id, 'created_by'=>$user_id, 'updated_by'=>$user_id]);
		                    }
		                }else{
		                    if(isset($row[0])){
		                        $insert_arr     = array('cpt_hcpcs'=>$row[0],'allowed_amount'=>$row[2],'billed_amount'=>$row[1]);
		                        $cptCreatedDetails = Cpt::create($insert_arr);
		                        
		                        // Favourite if not available means create that cpt to favourite  
		                        Favouritecpts::create(['cpt_id'=>$cptCreatedDetails->id,'user_id'=>$user_id, 'created_by'=>$user_id, 'updated_by'=>$user_id]);
		                        $multiInsertArr = array('cpt_id'=>$cptCreatedDetails->id, 'created_by'=>$user_id, 'fee_schedule_id'=>$tempId,'year'=>$year,'insurance_id'=>$insurance,'billed_amount'=>$row[1],'allowed_amount'=>$row[2],'modifier_id'=>$modifier);
		                        MultiFeeschedule::create($multiInsertArr);
		                    }
		                }
		            $validationError = 1;
		            }
		        } 
			}
		
		}catch(Exception $e){
			$validationError	= 0;
			DB::rollback();
		}
		if($updateValidation == 2) {
			$errors	= array('upload_file'=>array('uploadError'));
			return Response::json(array('status'=>'error', 'message'=>'uploadError','data'=>''));				
		}elseif($validationError == 1){
			Storage::disk($storage_disk)->put("fee_schedule_files/".$new_filename,  File::get($file));
			$choose_year = (@$request['choose_year']=='') ? date('Y') :@$request['choose_year'];
			$percentage = !empty($request['percentage'])?$request['percentage']:'';
			$fee_sch_request = array('file_name'=>$org_filename,'choose_year'=>$choose_year,'conversion_factor'=>$request['conversion_factor'],'percentage'=>$percentage,'saved_file_name'=>$new_filename);
			$result = Feeschedule::create($fee_sch_request);
			$user 	= Auth::user ()->id;
			$result->created_by = $user;
			$result->updated_by = $user;
			$result->save ();
			multiFeeschedule::where('fee_schedule_id',$tempId)->update(['fee_schedule_id'=>$result->id]);
            DB::commit();         
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$new_cpt_codes));
			
		}
	}
	/*** Feeschedule store details end ***/
	
	/********************** Start feeschedule deleted process ***********************************/
	public function getdestroyApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Feeschedule::where('id', $id)->count()>0 && is_numeric($id)) 
		{
			Feeschedule::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		} 
		else 
		{
			return Response::json(array('status' => 'failure', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
		}
	}
	/********************** End feeschedule deleted process ***********************************/
	
	/*** Get Feeschedule sample upload file start ***/
	public function api_get_feeschedule_file($type)
	{
		if($type=="sample")
		{
			$data = Cpt::has('favourite')->select('cpt_hcpcs','billed_amount','allowed_amount','modifier_id')->where('cpt_hcpcs','<>','')->whereRaw('LENGTH(cpt_hcpcs) >= 5')->get();
			$columnheading 	= array('CPT / HCPCS','Billed Amount ($)','Allowed Amount ($)','Modifier 1','Modifier 2','Modifier 3','Modifier 4');
					$collect_array = array();
					$array = json_decode(json_encode($data), true);
					foreach($array as $key => $list){
						if(isset($list['modifier_id']) && !empty($list['modifier_id'])){
							$temp = explode(',',$list['modifier_id']);
							$array[$key]['modifier_1'] = (isset($temp[0]) ? Modifier::where('id',$temp[0])->value('code') : '');
							$array[$key]['modifier_2'] = (isset($temp[1]) ? Modifier::where('id',$temp[1])->value('code') : '');
							$array[$key]['modifier_3'] = (isset($temp[2]) ? Modifier::where('id',$temp[2])->value('code') : '');
							$array[$key]['modifier_4'] = (isset($temp[3]) ? Modifier::where('id',$temp[3])->value('code') : '');
						}
						unset($array[$key]['modifier_id']);
					}
					$collect_array = array_merge($collect_array,$array);
			/*$file_path 	= Helpers::amazon_server_get_file('fee_schedule_files/','fee_schedule_sample.xls');
			return $file_path;*/
		}
		elseif($type=="cptcode")
		{
			$data = Cpt::has('favourite')->select('cpt_hcpcs','billed_amount','allowed_amount')->get();
			$columnheading 	= array('cpt_hcpcs','allowed_amount','billed_amount');
			$collect_array = json_decode(json_encode($data), true);
		}
		else
		{
			$file_path 	= Helpers::amazon_server_get_file('fee_schedule_files/',$type);
			return $file_path;
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('collect_array','columnheading')));
	}
	/*** Get Feeschedule sample upload file End ***/

	public function feescheduleUpdateValiation() {

	}
	
	function __destruct() 
	{
    }
}
