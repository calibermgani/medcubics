<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cpt as Cpt;
use App\Models\Medcubics\Cpt as CptMaster;
use App\Models\Pos as Pos;
use App\Models\Favouritecpts;
use App\Models\Document as Document;
use App\Models\Medcubics\IdQualifier as IdQualifier;
use App\Models\MultiFeeschedule as MultiFeeschedule;
use App\Models\Modifier as Modifier;
use App\Models\Practice as Practice;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use App;
use Excel;
use App\Http\Helpers\Helpers as Helpers;
use Lang;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;

class CptApiController extends Controller 
{
	/*** Cpt lists page Starts ***/
	public function getIndexApi($export = "")
	{		
		$cpt_arr = "";
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
			return $callexport->generatebulkexport($table,$columns, $filename,$columnheading,$export,$with_table='',$con_response='yes',$pcon='yes');
		}
		//$cpt_arr['count'] = Cpt::where('id','<>', 0)->count();
		$cpt_arr = Cpt::where('id','<>', 0)->select(DB::Raw('count(*) as count'))->first(); 
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cpt_arr')));
	}
	/*** Cpt lists page Ends ***/
    public function getCptApi($export = "") {
        ini_set('memory_limit', '2G');
        ini_set('max_execution_time', 300);
        $cpt_arr = "";        
        $cpt_arr = Cpt::where('id', '<>', 0)->orderBy('cpt_hcpcs','ASC')->get();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('cpt_arr')));
    }

    /*** Create page Starts ***/
	public function getCreateApi()
	{			
		$pos = Pos::select(DB::Raw("CONCAT(code, '-', pos) as code"),'id')->pluck("code",'id')->all();      
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
            $practice_db_name = config('siteconfigs.connection_database');
			$validate_cpt_rules = Cpt::$rules+array('cpt_hcpcs' => 'required|unique:cpts|unique:cpts,cpt_hcpcs,NULL,id,deleted_at,NULL');
			if(is_null($request['revenue_code']) == true) { unset($validate_cpt_rules['revenue_code']); }
			if(is_null($request['ndc_number']) == true) { unset($validate_cpt_rules['ndc_number']); }
			if(is_null($request['clia_id']) == true) { unset($validate_cpt_rules['clia_id']); }
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
			if($request['short_description'] != '')
				$request['short_description'] = $request['short_description'];
			else
				$request['short_description'] = substr($request['medium_description'],0,28);
			$request['unit_code'] = $request['unit_code'];	
			$request['unit_cpt'] = $request['unit_cpt'];	
			$request['unit_ndc'] = $request['unit_ndc'];	
			$request['unit_value'] = $request['unit_value'];	
			$cpt = Cpt::create($request);
			$user= Auth::user ()->id;
			$cpt->created_by = $user;
			$cpt_id = $cpt->id;
			if(isset($request['temp_doc_id']) && $request['temp_doc_id']!="")
			{
				Document::where('temp_type_id', '=', $request['temp_doc_id'])->update(['type_id' => $cpt_id,'temp_type_id' => '']);
			}
			$cpt->save ();
            $temp['user_id'] = $user;
            $temp['cpt_id'] = $cpt_id;
            $temp['created_by'] = $user;
            Favouritecpts::create($temp);
			$cpt_id = Helpers::getEncodeAndDecodeOfId($cpt->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.cpt_create_msg"),'data'=>$cpt_id));					
		}
	}
	/*** Store Function Ends ***/
	
	/*** Cpt details show page Starts ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$database_name	= getenv('DB_DATABASE');
		//if(Cpt::on("responsive")->where('id',$id)->count()>0 && is_numeric($id))
		if(Cpt::where('id',$id)->count()>0 && is_numeric($id))
		{
			$cpt = Cpt::with('favourite','pro_category')->where('id',$id)->first();
			$cpt = (!empty($cpt)) ? $cpt->toArray() : [];
			if((!empty($cpt)) && Cpt::where('cpt_hcpcs',$cpt['cpt_hcpcs'])->count()>0)
			{
				$cpt_curr = Cpt::with('pos','modifier_name')->where('cpt_hcpcs',$cpt['cpt_hcpcs'])->select('code_type','icd','effectivedate','terminationdate','type_of_service','pos_id','applicable_sex','referring_provider','age_limit','revenue_code','drug_name','ndc_number','min_units','max_units','anesthesia_unit','service_id_qualifier','allowed_amount','billed_amount','required_clia_id','clia_id','short_description','medium_description','long_description','modifier_id','referring_provider','work_rvu','facility_practice_rvu','nonfacility_practice_rvu','pli_rvu','total_facility_rvu','total_nonfacility_rvu','created_at','updated_at','created_by','updated_by','procedure_category')->first()->toarray();
				$cpt = array_merge($cpt, $cpt_curr);
			}
			else
			{
				$cpt['code_type'] = $cpt['icd'] = $cpt['effectivedate']	= $cpt['terminationdate'] = $cpt['type_of_service']	= $cpt['pos_id'] = $cpt['applicable_sex'] = $cpt['age_limit'] = $cpt['revenue_code'] = $cpt['drug_name'] = $cpt['ndc_number'] = $cpt['min_units'] = $cpt['max_units'] = $cpt['anesthesia_unit'] =  $cpt['service_id_qualifier'] = $cpt['allowed_amount'] = $cpt['billed_amount'] = $cpt['required_clia_id'] = $cpt['clia_id'] = $cpt['pos'] = "";
			}
			$cpt = (object) $cpt;
			$cpt->modifier_id = (isset($cpt->modifier_id) && $cpt->modifier_id !='') ? implode(",",Modifier::whereIn("id",explode(",",$cpt->modifier_id))->pluck("code")->all()): '';
			$qualifier = IdQualifier::where('id',$cpt->service_id_qualifier)->first(); 
			$cpt->service_id_qualifier = ($qualifier =='' || $qualifier ==null) ? '' :$qualifier->id_qualifier_name;
			$cpt->id = (isset($cpt->id)) ? Helpers::getEncodeAndDecodeOfId($cpt->id,'encode') : 0;
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('cpt', 'qualifier')));		
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Cpt details show page Ends ***/
	
	/*** Cpt details edit page Starts ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$database_name	= getenv('DB_DATABASE');
		
		//if(Cpt::on("responsive")->where('id',$id)->count()>0 && is_numeric($id))
		if(Cpt::where('id',$id)->count()>0 && is_numeric($id))
		{
			//$cpt = Cpt::on("responsive")->with('pos', 'favourite')->where('id',$id)->select('id','cpt_hcpcs','medium_description','medicare_global_period','deleted_at','modifier_id','effectivedate','terminationdate')->first()->toArray();
			$cpt = Cpt::with('favourite','pro_category')->where('id',$id)->first()->toArray();
			// $cpt = Cpt::with('pos', 'favourite')->join('procedure_categories','procedure_categories.id','=','cpts.procedure_category')->where('cpts.id',$id)->select('procedure_categories.procedure_category as proc','cpts.id','cpts.cpt_hcpcs','cpts.medium_description','cpts.medicare_global_period','cpts.deleted_at','cpts.modifier_id','cpts.effectivedate','cpts.terminationdate')->first()->toArray();
			if(Cpt::where('cpt_hcpcs',$cpt['cpt_hcpcs'])->count()>0)
			{
				$cpt_curr = Cpt::where('cpt_hcpcs',$cpt['cpt_hcpcs'])->select('code_type','icd','effectivedate','terminationdate','type_of_service','pos_id','applicable_sex','referring_provider','age_limit','revenue_code','drug_name','ndc_number','min_units','max_units','anesthesia_unit','service_id_qualifier','allowed_amount','modifier_id','billed_amount','required_clia_id','clia_id','short_description','long_description','modifier_id','work_rvu','facility_practice_rvu','nonfacility_practice_rvu','referring_provider','pli_rvu','total_facility_rvu','total_nonfacility_rvu','created_at','updated_at','created_by','updated_by','procedure_category','unit_code','unit_cpt','unit_ndc','unit_value')->first()->toarray();
				$cpt = array_merge($cpt, $cpt_curr);
				// dd($cpt);
			}
			else
			{
				$cpt['code_type'] = $cpt['icd'] = $cpt['effectivedate']	= $cpt['terminationdate'] = $cpt['type_of_service']	= $cpt['pos_id'] = $cpt['applicable_sex'] = $cpt['age_limit'] = $cpt['revenue_code'] = $cpt['drug_name'] = $cpt['ndc_number'] = $cpt['min_units'] = $cpt['max_units'] = $cpt['anesthesia_unit'] = $cpt['service_id_qualifier'] = $cpt['allowed_amount'] = $cpt['billed_amount'] = $cpt['required_clia_id'] = $cpt['clia_id'] = $cpt['pos'] = "";
			}
			$cpt = (object) $cpt;
			$cpt->modifier_id = ($cpt->modifier_id !='') ? explode(",",$cpt->modifier_id): '';
			$modifier = Modifier::where("status","Active")->pluck("code","id")->all();
			$pos = Pos::select(DB::Raw("CONCAT(code, '-', pos) as code"),'id')->pluck("code",'id')->all(); 
			$pos_id = $cpt->pos_id;
			$qualifier = IdQualifier::pluck('id_qualifier_name','id')->all();
			$cpt->id = Helpers::getEncodeAndDecodeOfId($cpt->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cpt','pos','pos_id', 'qualifier', 'modifier')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Cpt details edit page Ends ***/
	
	/*** Cpt details update Starts ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$database_name	= "responsive";
		
		//if(Cpt::on($database_name)->where('id',$id)->count()>0 && is_numeric($id))
		if(Cpt::where('id',$id)->count()>0 && is_numeric($id))
		{
			$request = Request::all();
			
			$request['clia_id']=(isset($request['required_clia_id']) && $request['required_clia_id']=="Yes")? $request["clia_id"]:''; 
			$request['allowed_amount']=($request['allowed_amount']!="")? str_replace(",","",$request["allowed_amount"]):''; 
			$request['billed_amount']=($request['billed_amount']!="")? str_replace(",","",$request["billed_amount"]):''; 
			$request['effectivedate']=($request['effectivedate']!="")? date("Y-m-d",strtotime($request['effectivedate'])):''; 
			$request['terminationdate']=($request['terminationdate']!="")? date("Y-m-d",strtotime($request['terminationdate'])):''; 
			$validator = Validator::make($request, Cpt::$rules, Cpt::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{
				$cpt_hcpcs = Cpt::where('id',$id)->pluck('cpt_hcpcs')->all();
				$request['cpt_hcpcs'] = $cpt_hcpcs[0];
				$request['modifier_id'] = (isset($request['modifier_id'])) ? implode(",",$request['modifier_id']) : '';
				unset($request['_method'],$request['_token'],$request['temp_doc_id'],$request['sample']);
				$user = Auth::user()->id;
				$request['updated_by'] = $user;	
				$cpts_details = Cpt::Where('cpt_hcpcs',$request['cpt_hcpcs']);
				if($cpts_details->count())
				{
					$user = Auth::user()->id;
					$cpt_data = $cpts_details->first();
						//$update_query = Cpt::findOrFail($id);
					$request['work_rvu'] = $request['work_rvu'];
					$request['facility_practice_rvu'] = $request['facility_practice_rvu'];
					$request['nonfacility_practice_rvu'] = $request['nonfacility_practice_rvu'];
					$request['pli_rvu'] = $request['pli_rvu'];
					if($request['short_description'] != '')
						$request['short_description'] = $request['short_description'];
					else
						$request['short_description'] = substr($cpt_data->medium_description,0,28);
					$request['long_description'] = $request['long_description'];
					$request['total_facility_rvu'] = $request['total_facility_rvu'];
					$request['total_nonfacility_rvu'] = $request['total_nonfacility_rvu'];	
					$request['unit_code'] = $request['unit_code'];	
					$request['unit_cpt'] = $request['unit_cpt'];	
					$request['unit_ndc'] = $request['unit_ndc'];	
					$request['unit_value'] = $request['unit_value'];	
					$request['updated_by'] = $user;
					$request['updated_at'] = date('Y-m-d h:i:s');
					
					if((isset($request['year']) && !empty($request['year'])) || (isset($request['insurance']) && !empty($request['year']))){
						unset($request['year']);
						unset($request['insurance']);
						unset($request['allowed_amount']);
						unset($request['billed_amount']);
						unset($request['multiFeeScheduleCptID']);
						unset($request['popup_procedure_category']);
					}else{
						unset($request['multiFeeScheduleCptID']);
						unset($request['year']);
						unset($request['insurance']);
						unset($request['popup_procedure_category']);
					}
					$update_query = Cpt::where('cpt_hcpcs' ,$request['cpt_hcpcs'])->update($request);
				}
				else
				{
					$request['created_by'] = $user;
					$create_query = Cpt::create($request);
				}
				$message = (Favouritecpts::where('cpt_id', $id)->count()) ? Lang::get("common.validation.cpt_update_msg") : Lang::get("common.validation.add_fav");
				return Response::json(array('status'=>'success', 'message'=> $message,'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Cpt details update Ends ***/
	
	/*** Cpt details delete Starts ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(is_numeric($id) && Cpt::where('id',$id)->count()>0 )
		{
			Cpt::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}	
	}
	/*** Cpt details delete Ends ***/
	
	/*** Cpt favoritelist Starts ***/
	public function getListFavouritesApi($export = "")
	{		
		$request = Request::all();	
		$header = [];
		$insurance_id = isset($request['insurance'])?$request['insurance']:'insurance_id';
		if(isset($request['insurance'])){
			$header['Insurance'] = $request['insurance'];
		}
		$favourites_ids = Favouritecpts::pluck("cpt_id")->all();
		if(isset($request['year']) && !empty($request['year'])){
			$header['Year'] = $request['year'];
			if(isset($request['insurance']))
				$multiCpt = MultiFeeschedule::where('year',$request['year'])->where('insurance_id',$insurance_id)->where('status','Active')->pluck('cpt_id')->all();
			else
				$multiCpt = MultiFeeschedule::where('year',$request['year'])->where('status','Active')->pluck('cpt_id')->all();
			$favourites_ids = array_intersect($multiCpt,$favourites_ids);
		}
		$favourites = [];
		$total_rec_count = 0;
		if(count($favourites_ids)>0)
		{
			$cpt_values =Cpt::whereIn('id',$favourites_ids)->where('status',"Active")->orderBy('cpt_hcpcs','ASC')->get()->toArray();	
			foreach($cpt_values as $key=>$value)
			{
				if($value["cpt_hcpcs"] !="")
				{
					$cpt_value['cpt'] = (object) $value;
					$cpt_det = Cpt::where('cpt_hcpcs',$value["cpt_hcpcs"])->select('billed_amount', 'allowed_amount', 'pos_id', 'modifier_id', 'type_of_service' )->first()->toArray();
					if(isset($request['year']) && !empty($request['year'])){
						if(isset($request['insurance']))
						$collect_values = MultiFeeschedule::where('year',$request['year'])->where('insurance_id',$insurance_id)->where('cpt_id',$value["id"])->select('billed_amount', 'allowed_amount', 'modifier_id')->first()->toArray();
						else
						$collect_values = MultiFeeschedule::where('year',$request['year'])->where('cpt_id',$value["id"])->select('billed_amount', 'allowed_amount', 'modifier_id')->first()->toArray();
					}
					if(count($cpt_det) > 0)
					{
						$cpt_value['cpt']->billed_amount = (isset($collect_values['billed_amount'])) ? $collect_values['billed_amount'] : $cpt_det['billed_amount'];
						$cpt_value['cpt']->allowed_amount = (isset($collect_values['allowed_amount'])) ? $collect_values['allowed_amount'] : $cpt_det['allowed_amount'];
						$cpt_value['cpt']->pos_id = $cpt_det['pos_id'];
						if(isset($collect_values['modifier_id']) && $collect_values['modifier_id']!=''){
							$modifiers = DB::table('modifiers')->selectRaw('GROUP_CONCAT(code SEPARATOR ", ") as modifiers')->whereIn('id',explode(',', $collect_values['modifier_id']))->get();
							$modifiers = $modifiers[0]->modifiers;
						}else if(isset($cpt_det['modifier_id']) && $cpt_det['modifier_id']!=''){ 
							$modifiers = DB::table('modifiers')->selectRaw('GROUP_CONCAT(code SEPARATOR ", ") as modifiers')->whereIn('id',explode(',', $cpt_det['modifier_id']))->get();
							$modifiers = $modifiers[0]->modifiers;
						}else{
							$modifiers = '';
						}
						$cpt_value['cpt']->modifier_id =  $modifiers;
						$cpt_value['cpt']->type_of_service = $cpt_det['type_of_service']; 
					}
					$favourites[] = (object) $cpt_value;
				}
			}
		}
		$favourites = ($favourites !='') ? (object) $favourites:[];
		
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'			=>	'Cpt',
				'heading'			=>	'Cpt',
				'fields'			=>	array(
				'cpt_hcpcs'			=> 	array('table'=>'cpt' ,'column' => 'cpt_hcpcs' ,'label' => 'CPT / HCPCS'),
				'short_description' => 	array('table'=>'cpt' ,'column' => 'short_description','label' => 'Short Description'),
				'billed_amount'		=> 	array('table'=>'cpt' ,'column' => 'billed_amount' ,'label' => 'Billed Amount'),
				'allowed_amount' 	=> 	array('table'=>'cpt' ,'column' => 'allowed_amount','label' => 'Allowed Amount'),
				'pos_id' 			=> 	array('table'=>'cpt' ,'column' => 'pos_id','label' => 'POS'),
				'modifier_id' 			=> 	array('table'=>'cpt' ,'column' => 'modifier_id','label' => 'Modifier'),
				'type_of_service' =>	array('table'=>'cpt' ,	'column' => 'type_of_service' ,'label' => 'Type of service'),
				
			));
			$callexport = new CommonExportApiController();
			
			return $callexport->generatemultipleExports($exportparam, $favourites, $export);
		}
		
		return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('favourites','header')));
	}
	/*** Cpt favoritelist Ends ***/

		/*** Cpt favoritelist Starts ***/
	public function getCptUpdateApi($export = "")
	{		
		$favourites_ids = Favouritecpts::pluck("cpt_id")->all();
		$favourites = '';
		if(count($favourites_ids)>0)
		{
			$cpt_values =Cpt::whereIn('id',$favourites_ids)->where('status',"Active")->get()->toArray();	
			foreach($cpt_values as $key=>$value)
			{
				if($value["cpt_hcpcs"] !="")
				{
					$cpt_value['cpt'] = (object) $value;
					if(Cpt::where('cpt_hcpcs',$value['cpt_hcpcs'])->count()>0)
					{
						$collect_values =  Cpt::where('cpt_hcpcs',$value["cpt_hcpcs"])->first()->toArray();

						$cpt_value['cpt']->billed_amount = $collect_values['billed_amount'];
						$cpt_value['cpt']->allowed_amount = $collect_values['allowed_amount'];
						$cpt_value['cpt']->pos_id = $collect_values['pos_id'];
						$cpt_value['cpt']->type_of_service = $collect_values['type_of_service']; 
					}
					$favourites[] = (object) $cpt_value;
				}
			}
		}
		$favourites = ($favourites !='') ? (object) $favourites:[];
		
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'			=>	'Cpt',
				'heading'			=>	'Cpt',
				'fields'			=>	array(
				'cpt_hcpcs'			=> 	array('table'=>'cpt' ,'column' => 'cpt_hcpcs' ,'label' => 'CPT / HCPCS'),
				'short_description' => 	array('table'=>'cpt' ,'column' => 'short_description','label' => 'Short Description'),
				'billed_amount'		=> 	array('table'=>'cpt' ,'column' => 'billed_amount' ,'label' => 'Billed Amount'),
				'allowed_amount' 	=> 	array('table'=>'cpt' ,'column' => 'allowed_amount','label' => 'Allowed Amount'),
				'pos_id' 			=> 	array('table'=>'cpt' ,'column' => 'pos_id','label' => 'POS'),
				'type_of_service' =>	array('table'=>'cpt' ,	'column' => 'type_of_service' ,'label' => 'Type of service'),
				
			));
			$callexport = new CommonExportApiController();
			
			return $callexport->generatemultipleExports($exportparam, $favourites, $export);
		}
		
		return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('favourites')));
	}
	/*** Cpt favoritelist Ends ***/
	
	/*** Cpt favoritelist popup Starts ***/
	public function getToggleFavouritesApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$requestInfo = Request::all();
		$database_name	= "responsive";
		
		// Getting current practice database details
		$practice_details = Practice::getPracticeDetails();
		$dbconnection = new DBConnectionController();
		$tenantDB = $dbconnection->getpracticedbname($practice_details['practice_name']);
		
		// Getting Admin database details
		$adminDB = config('siteconfigs.connection_database');
		// Table name setting
		$table_name = 'cpts';		
		$master_cpts = Cpt::where('id',$id)->get()->first();
		$favCount = Favouritecpts::where('cpt_id', $id)->count();
			
		if(empty($master_cpts['procedure_category']))
			$returnData['validation'] = "Yes";
		else
			$returnData['validation'] = "No";
		if($favCount != 0)
			$returnData['validation'] = "No";
		if(!isset($requestInfo['check'])){
			if(isset($requestInfo['procedure_category']))
				Cpt::where('id',$id)->update(['procedure_category'=>$requestInfo['procedure_category']]);
			if($favCount == 0)
			{
				$user_id = Auth::user()->id;
				$user = Auth::user()->id;
				
				if(Cpt::Where('cpt_hcpcs',$master_cpts['cpt_hcpcs'])->count())
				{
					$request['updated_by'] = $user;
					$request['updated_at'] = date('Y-m-d h:i:s');
					$update_query = Cpt::where('cpt_hcpcs' ,$master_cpts['cpt_hcpcs'])->update($request);
				}
				else
				{				
					// Inserting admin table to current database table
					$insert = "INSERT INTO $tenantDB.$table_name (short_description,medium_description,long_description,print_statedesc,cpt_hcpcs,code_type,type_of_service,pos_id,applicable_sex,referring_provider,age_limit,allowed_amount,billed_amount,modifier_id,revenue_code,drug_name,ndc_number,min_units,max_units,anesthesia_unit,service_id_qualifier,medicare_global_period,required_clia_id,clia_id,icd,work_rvu,facility_practice_rvu,nonfacility_practice_rvu,pli_rvu,total_facility_rvu,total_nonfacility_rvu,effectivedate,terminationdate,status) SELECT short_description,medium_description,long_description,print_statedesc,cpt_hcpcs,code_type,type_of_service,pos_id,applicable_sex,referring_provider,age_limit,allowed_amount,billed_amount,modifier_id,revenue_code,drug_name,ndc_number,min_units,max_units,anesthesia_unit,service_id_qualifier,medicare_global_period,required_clia_id,clia_id,icd,work_rvu,facility_practice_rvu,nonfacility_practice_rvu,pli_rvu,total_facility_rvu,total_nonfacility_rvu,effectivedate,terminationdate,status FROM $adminDB.$table_name where id = $id";
					DB::insert($insert);
					$id = DB::getPdo()->lastInsertId();
				}
				$data = ['cpt_id'=>$id,'user_id'=>$user_id, 'created_by'=>$user_id, 'updated_by'=>$user_id];
				Favouritecpts::create($data);
				$returnData['success'] = 1;
				return $returnData;
			}
			else
			{
				Favouritecpts::where('cpt_id', $id)->delete();
				$returnData['success'] = 0;
				return $returnData;
			}
		}else{
			return $returnData;
		}
	}
	/*** Cpt favoritelist popup Ends ***/
	
	/*** Cpt details search page Starts ***/
	public function getcpttablevalues()
	{		
		$request = Request::all();			
		$start = $request['start'];
		$len = $request['length'];		
		$cpt_arr_details = [];
		$cloum = intval($request["order"][0]["column"]);
		$order = $request['columns'][$cloum]['data'];
		if($request['columns'][$cloum]['data'] == 'favourite'){
			$order = 'id';
		}		
		$order_decs = $request["order"][0]["dir"];
		$search = '';
        if(!empty($request['search']['value'])) {
			$search= $request['search']['value'];
		}
		//$cpt 	= Cpt::on("responsive")->with('pos','user', 'favourite','userupdate')
		$cpt 	= Cpt::with('pos','user', 'favourite','userupdate')
				->where(function($query) use($search){ 
					if(trim($search) != '') 
						return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
							->orWhere('short_description', 'LIKE', '%'.$search.'%')
							->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
							->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
							->orWhere('pos_id', 'LIKE', '%'.$search.'%')
							->orWhere('type_of_service', 'LIKE', '%'.$search.'%');
				})
				->where('deleted_at',NULL)
				->where('status',"Active")
				->skip($start)->take($len)	
				->orderBy($order,$order_decs)
				->get()->toArray();	

		//$total_rec_count = Cpt::on("responsive")->with('pos', 'favourite')
		$total_rec_count = Cpt::with('pos', 'favourite')		
							->where(function($query)use($search) { 
								if(trim($search) != '') 
								return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
								->orWhere('short_description', 'LIKE', '%'.$search.'%')
								->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
								->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
								->orWhere('pos_id', 'LIKE', '%'.$search.'%')
								->orWhere('type_of_service', 'LIKE', '%'.$search.'%');
							})
							->where('status',"Active")
							->where('deleted_at',NULL)
							->orderBy($order,$order_decs)
							->where('deleted_at',NULL)
							->count();	

		foreach($cpt as $cpt)
		{
			/*  
			### Old file changes	
				$cpt_details = $cpt;
				$cpt_details['id'] = Helpers::getEncodeAndDecodeOfId($cpt['id']);
			
				if(Cpt::where('cpt_hcpcs',$cpt['cpt_hcpcs'])->count()>0)
				{
					$cpt_details[$cpt['cpt_hcpcs']] =  Cpt::where('cpt_hcpcs',$cpt['cpt_hcpcs'])->first()->toArray();
				}
				$cpt_arr_details[] = $cpt_details;

			*/
			//CPT in Practice master table check 
			//if((is_null($cpt["cpt_hcpcs"])) == true ) {
			if($cpt["cpt_hcpcs"] !="") {	
				$cpt_value = (object) $cpt;					
				$cpt_value->id = Helpers::getEncodeAndDecodeOfId($cpt['id']);
				/*
				if(Cpt::where('cpt_hcpcs',$cpt['cpt_hcpcs'])->count()>0)
				{
					$collect_values =  Cpt::where('cpt_hcpcs',$cpt["cpt_hcpcs"])->first()->toArray();						
					$cpt_value->billed_amount = $collect_values['billed_amount'];
					$cpt_value->allowed_amount = $collect_values['allowed_amount'];
					$cpt_value->short_description = $collect_values['short_description'];						
					$cpt_value->pos_id = $collect_values['pos_id'];
					$cpt_value->type_of_service = $collect_values['type_of_service']; 
				}
				*/
				$cpt_arr_details[] = (array) $cpt_value;
			}
		}		
		if(count($cpt)==0){
			$cpt_arr_details = array();
		}
		$data['data'] = $cpt_arr_details;	
		$data = array_merge($data,$request);
		$data['recordsTotal'] = $total_rec_count;
		$data['recordsFiltered'] = $total_rec_count;
		return Response::json($data);
	}
	/*** Cpt details search page Ends ***/
	
	
	/*** FavouritesCpt details search page Starts ***/
	public function getFavouritescpttablevalues($year = '',$insurance = '')
	{
	    $request = Request::all();
		if($year == 'undefined')
			$year = '';
		if($insurance == 'undefined')
			$insurance = 0;
		
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
		$favourites_ids = Favouritecpts::pluck("cpt_id")->all();
		if(!empty($year)){
			$multiCpt = MultiFeeschedule::where('year',$year)->where('insurance_id',$insurance)->where('status','Active')->pluck('cpt_id')->all();
			$favourites_ids = array_intersect($multiCpt,$favourites_ids);
		}
		$favourites = [];
		$total_rec_count = 0;
		if(count($favourites_ids)>0)
		{
			if(!empty($year)){
			$cpt_values =Cpt::with(array('pos','favourite','multifeeSchedule'=>function($query)use($year,$insurance){
								$query->where('year',$year)->where('insurance_id',$insurance)->where('status','Active');
						}))->whereIn('id',$favourites_ids)->where(function($query) use($search){ return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
				->orWhere('short_description', 'LIKE', '%'.$search.'%')
				->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('pos_id', 'LIKE', '%'.$search.'%')
				->orWhere('type_of_service', 'LIKE', '%'.$search.'%');})->where('deleted_at',NULL)->orderBy($order,$order_decs)->skip($start)->take($len)->where('status',"Active")->get()->toArray();
				
			$total_rec_count =Cpt::with(array('pos','favourite','multifeeSchedule'=>function($query)use($year,$insurance){
								$query->where('year',$year)->where('insurance_id',$insurance)->where('status','Active');
						}))->whereIn('id',$favourites_ids)->where(function($query) use($search){ return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
				->orWhere('short_description', 'LIKE', '%'.$search.'%')
				->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('pos_id', 'LIKE', '%'.$search.'%')
				->orWhere('type_of_service', 'LIKE', '%'.$search.'%');})->where('deleted_at',NULL)->orderBy($order,$order_decs)
				//->skip($start)->take($len)
				->where('status',"Active")->count();
			}else{
				$cpt_values =Cpt::with('pos','favourite')->whereIn('id',$favourites_ids)->where(function($query) use($search){ return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
				->orWhere('short_description', 'LIKE', '%'.$search.'%')
				->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('pos_id', 'LIKE', '%'.$search.'%')
				->orWhere('type_of_service', 'LIKE', '%'.$search.'%');})->where('deleted_at',NULL)->orderBy($order,$order_decs)->skip($start)->take($len)->where('status',"Active")->get()->toArray();
				
			$total_rec_count =Cpt::with('pos','favourite')->whereIn('id',$favourites_ids)->where(function($query) use($search){ return $query->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')
				->orWhere('short_description', 'LIKE', '%'.$search.'%')
				->orWhere('allowed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('billed_amount', 'LIKE', '%'.$search.'%')
				->orWhere('pos_id', 'LIKE', '%'.$search.'%')
				->orWhere('type_of_service', 'LIKE', '%'.$search.'%');})->where('deleted_at',NULL)->orderBy($order,$order_decs)
				//->skip($start)->take($len)
				->where('status',"Active")->count();
			}
			foreach($cpt_values as $key=>$value)
			{
				if($value["cpt_hcpcs"] !="")
				{
					$cpt_value = (object) $value;					
					$cpt_value->id = Helpers::getEncodeAndDecodeOfId($value['id']);
					$cptId = Helpers::getEncodeAndDecodeOfId($cpt_value->id,'decode');
					if(Cpt::where('cpt_hcpcs',$value['cpt_hcpcs'])->count()>0)	{
						if(!empty($year)){
							$multiCount = MultiFeeschedule::where('year',$year)->where('insurance_id',$insurance)->where('cpt_id',$cptId)->count();
							if($multiCount == 0)
								$tempInsurance = 0;
							else
								$tempInsurance = $insurance;
							$collect_values =  Cpt::with(array('pos','multifeeSchedule'=>function($query)use($year,$tempInsurance){
									$query->where('year',$year)->where('insurance_id',$tempInsurance)->where('status','Active');
							}))->where('cpt_hcpcs',$value["cpt_hcpcs"])->first()->toArray();
						}else{
							$collect_values =  Cpt::with('pos')->where('cpt_hcpcs',$value["cpt_hcpcs"])->first()->toArray();
						}
						if(isset($collect_values['multifee_schedule']['modifier_id'])){
							$ids = explode(',',$collect_values['multifee_schedule']['modifier_id']);
							$modifier = Modifier::whereIn('id',$ids)->where("status","Active")->pluck("code")->all();
							$collect_values['modifier_id'] = implode($modifier,',');
						}else{
							$ids = explode(',',$collect_values['modifier_id']);
							$modifier = Modifier::whereIn('id',$ids)->where("status","Active")->pluck("code")->all();
							$collect_values['modifier_id'] = implode($modifier,',');
						}
						$cpt_value->billed_amount = (isset($collect_values['multifee_schedule']['billed_amount'])) ? $collect_values['multifee_schedule']['billed_amount'] : $collect_values['billed_amount'];
						$cpt_value->allowed_amount = (isset($collect_values['multifee_schedule']['allowed_amount'])) ? $collect_values['multifee_schedule']['allowed_amount'] : $collect_values['allowed_amount'];
						$cpt_value->short_description = $collect_values['short_description'];						
						$cpt_value->pos_id = ($collect_values['pos']['code'] == 0) ? 'Nil' : $collect_values['pos']['code'];
						$cpt_value->modifier_id = ($collect_values['modifier_id'] == '') ? 'Nil' : $collect_values['modifier_id'];
						$cpt_value->type_of_service = $collect_values['type_of_service']; 
					}
					$favourites[] = (array) $cpt_value;
				}
			}
		}		
		$data['data'] = $favourites;	
	    $data = array_merge($data,$request);
		$data['recordsTotal'] = $total_rec_count;
		$data['recordsFiltered'] = $total_rec_count;	
		return Response::json($data);
	}
	/*** Favourites Cpt details search page Ends ***/
	
	/*** Mass import cpt starts ***/
	public function massImportCpt(){
		
		$adminDB=env('DB_DATABASE', config('siteconfigs.connection_database')); 
		// Getting current practice database details
		$practice_details = Practice::getPracticeDetails();
		$dbconnection = new DBConnectionController();
		$tenantDB = $dbconnection->getpracticedbname($practice_details['practice_name']);
		
        try {
            $table_name = 'cpts';        
            // Truncate table - @todo if necessary trucate table.
            DB::statement("TRUNCATE TABLE $table_name");
            
			//$insert = "INSERT INTO $tenantDB.$table_name SELECT * FROM $adminDB.$table_name"; 
			
			// Short desc empty means getting form medium desc first 28 characters
			
			$insert = "INSERT INTO $tenantDB.$table_name (short_description,medium_description,long_description,print_statedesc,cpt_hcpcs,code_type,type_of_service,pos_id,applicable_sex,referring_provider,age_limit,allowed_amount,billed_amount,modifier_id,revenue_code,drug_name,ndc_number,min_units,max_units,anesthesia_unit,service_id_qualifier,medicare_global_period,required_clia_id,clia_id,icd,work_rvu,facility_practice_rvu,nonfacility_practice_rvu,pli_rvu,total_facility_rvu,total_nonfacility_rvu,effectivedate,terminationdate,status) SELECT CASE short_description WHEN '' THEN SUBSTR(medium_description, 1, 28) ELSE short_description END ,medium_description,long_description,print_statedesc,cpt_hcpcs,code_type,type_of_service,pos_id,applicable_sex,referring_provider,age_limit,allowed_amount,billed_amount,modifier_id,revenue_code,drug_name,ndc_number,min_units,max_units,anesthesia_unit,service_id_qualifier,medicare_global_period,required_clia_id,clia_id,icd,work_rvu,facility_practice_rvu,nonfacility_practice_rvu,pli_rvu,total_facility_rvu,total_nonfacility_rvu,effectivedate,terminationdate,status FROM $adminDB.$table_name where cpt_hcpcs != ''";
            DB::insert($insert);    
            return Response::json(array('status' => 'success', 'message' => 'CPT imported from master'));            
        } catch (Exception $e) {
            \Log::info("Exception on massImportCpt. Error: " . $e->getMessage());
            return Response::json(array('status'=>'error', 'message'=> "Unable to import CPT. Please contact admin"));	
        }		
	}
	/*** Mass import cpt ends ***/

	function __destruct() 
	{
    }
	
	public function multiFeeScheduleDataApi(){
		$request = Request::all();
		$cptInfo = MultiFeeschedule::where('year',$request['year'])->where('insurance_id',$request['insurance_id'])->where('status','Active')->where('cpt_id',Helpers::getEncodeAndDecodeOfId($request['cpt_id'],'decode'))->get()->first();
		$data['billed_amount'] = @$cptInfo->billed_amount;
		$data['allowed_amount'] = @$cptInfo->allowed_amount;
		$data['Modifier'] = @$cptInfo->modifier_id;
		$cptDetails = Cpt::where('id',Helpers::getEncodeAndDecodeOfId($request['cpt_id'],'decode'))->first();
		$data['default_billed_amount'] = $cptDetails->billed_amount;
		$data['default_allowed_amount'] = $cptDetails->allowed_amount;
		$data['default_modifier_id'] = $cptDetails->modifier_id;
		return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('data')));
	}

	public function api_get_SampleCpt_file($type)
	{
		if($type=="sample")
		{
			$columnheading 	= array('cpt_hcpcs','short_description','work_rvu');
			$excel 			= App::make('excel');
			// $database_name	= 'responsive';
			// dd($database_name);
			Excel::create("FavouriteCpts", function($excel) use($columnheading) {
				$data = Favouritecpts::join("cpts","cpts.id","=","favouritecpts.cpt_id")
				->select('cpts.cpt_hcpcs','cpts.short_description','cpts.work_rvu')
				->get();
				// dd($data);
				$excel->sheet('Sheet1', function($sheet) use($data,$columnheading) {
					$collect_array = '';
					$heading_array[] = $columnheading;
					$array = json_decode(json_encode($data), true);
					$collect_array = array_merge($heading_array,$array);
					$sheet->fromArray($collect_array, null, 'A1', false, false);
				});
			})->export("xls");
		}
		else
		{
			$file_path 	= Helpers::amazon_server_get_file('fee_schedule_files/',$type);
			return $file_path;
		}
	}
	
}