<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Medcubics\Cpt as AdminCpt;
use App\Models\Cpt as Cpt;
use App\Models\Favouritecpts as Favouritecpts;
use App\Models\SuperbillTemplate as SuperbillTemplate;
use Response;
use Request;
use Validator;
use Input;
use DB;
use Auth;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Lang;

class SuperbillsApiController extends Controller 
{
	/**** Start Display a listing of the superbill ***/
	public function getIndexApi($export='') {
		$superbill_template = SuperbillTemplate::with('provider','provider.degrees','provider.provider_types','creator','modifier')->orderBy('template_name',"ASC")->get();
		if($export != "") {
			$exportparam 	= 	array(
					'filename'		=>	'Superbill',
					'heading'		=>	'Superbills Report',
					'fields' 		=>	array(
							'template_name'	=>	'Template Name',
							'Provider Name'	=>	array('table'=>'','column' => 'provider_id', 'use_function' => ['App\Models\Provider','getProviderNamewithDegree'], 'label' => 'Provider Name'),
							'status'			=>	'Status',
							'created_at'		=>	'Created On',
							'created_by'		=>	array('table'=>'creator','column' => 'short_name','label' => 'Created by'),
			));
                        
            $callexport = new CommonExportApiController();
            return $callexport->generatemultipleExports($exportparam, $superbill_template, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('superbill_template')));
	}
	/*** End Display a listing of the superbill ***/
	
	/*** Start Display create page of the superbill ***/
	public function getCreateApi() {
		$providers = Provider::typeBasedProviderlist('Rendering'); 
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('providers')));
	}
	/*** End Display create page of the superbill ***/
	
	/*** Start superbill Edit page display ***/
	public function getEditApi($id) {
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && SuperbillTemplate::where('id', $id)->count()) {
			$superbill_arr 		= SuperbillTemplate::with('provider','creator','modifier')->find($id)->toArray();
			$superbill_arr['header_list'] = explode(",",$superbill_arr['header_list']);
			$superbill_arr['get_list_order'] = explode(",",$superbill_arr['get_list_order']);
			$drop_down_val = explode(",",$superbill_arr['order_header']);
			if($superbill_arr["skin_procedures_units"] !='') 
				$superbill_arr["skin_procedures_units"] = explode(",",$superbill_arr["skin_procedures_units"]);
			if($superbill_arr["medications_units"] !='') 
				$superbill_arr["medications_units"] = explode(",",$superbill_arr["medications_units"]);
			
			foreach ($superbill_arr['get_list_order'] as $key=>$list_value) {
				$superbill_arr[$list_value] = explode(",",$superbill_arr[$list_value]);
				$DB = DB::connection()->getDatabaseName();
				$admin_db_name = 'responsive';//getenv('DB_DATABASE');
				if($DB == $admin_db_name) {
					$cpt_list	= Cpt::whereIn('cpt_hcpcs', $superbill_arr[$list_value])->orderBy('short_description','ASC')->pluck('short_description','cpt_hcpcs')->all();
				} else {
					$cpt_list	= Cpt::on("responsive")->whereIn('cpt_hcpcs', $superbill_arr[$list_value])->orderBy('short_description','ASC')->pluck('short_description','cpt_hcpcs')->all();
				}
				$superbill_arr[$list_value]=[];
				foreach ($cpt_list as $k=>$values) {
					$superbill_arr[$list_value][] = $k."::".$values;
				}
			}
			
			foreach ($superbill_arr['get_list_order'] as $key=>$list_value) {
				$attach[$list_value] = $drop_down_val[$key];
			}
			$superbill_arr["drop_down"]= $attach;
			
			$providers = Provider::typeBasedProviderlist('Rendering');
			$superbill_arr['id'] = Helpers::getEncodeAndDecodeOfId($superbill_arr['id'],'encode');
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('superbill_arr','providers')));
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End superbill Edit page display ***/
	
	/*** Store superbill template starts here [ajax] ***/
	public function getTemplatestoreApi($request="") {		
		$request 	= Request::all();
		$get_order = explode(",",$request['get_list_order'][0]);
		$request['header_style'] = str_replace(",", '/&/', $request['header_style'][0]);
		$request['get_list_order'] = $request['get_list_order'][0];
		$request['header_list'] = $request['header_list'][0];
		$request['order_header'] = $request['order_header'][0];
		foreach ($get_order as $k=>$value) 	{
			$request[$value] = implode(",",$request[$value]);
			if($value =="skin_procedures" || $value =="medications")
				$request[$value."_units"] = implode(",",$request[$value."_units"]);
		}
		$user = Auth::user ()->id;
		if(isset($request["template_id"])) {
			$id = Helpers::getEncodeAndDecodeOfId($request["template_id"],'decode');
			Validator::extend('chk_name_exists', function($attribute, $value, $parameters) use($request,$id) {
				$template_name = $value; 
				$provider_id	= $request['provider_id'];
				$count = SuperbillTemplate::where('template_name',$template_name)->where('provider_id',$provider_id)->where('id','!=',$id)->count();
				if($count > 0)	
					return false;
				else
					return true;
			});	
				
			$validation_rules =	[
				'template_name' => 'required|chk_name_exists',
				'provider_id' => 'required',
				'header_list' => 'required'
			];
			$validator = Validator::make($request, $validation_rules, SuperbillTemplate::messages());
			if ($validator->fails()) {
				$errors = $validator->errors()->getmessages();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			} else {
				$data = SuperbillTemplate::findOrFail($id);
				$data->update($request);
				$data->updated_by = $user;
				$data->save();
				$message = Lang::get("common.validation.update_msg");
			}
		} else {
			Validator::extend('chk_name_exists', function($attribute, $value, $parameters) use($request) {
				$template_name = $value; 
				$provider_id	= $request['provider_id'];
				$count = SuperbillTemplate::where('template_name',$template_name)->where('provider_id',$provider_id)->count();
				if($count > 0)	
					return false;
				else
					return true;					
			});
			$validation_rules =	[
				'template_name' => 'required|chk_name_exists',
				'provider_id' => 'required',
				'header_list' => 'required'
			];
			$validator = Validator::make($request, $validation_rules, SuperbillTemplate::messages());
			if ($validator->fails()) {
				$errors = $validator->errors()->getmessages();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			} else {
				$data = SuperbillTemplate::create($request);
				$data->created_by = $user;
				$data->save();
				$message = Lang::get("common.validation.create_msg");
			}
		}
		
		if($data->id) {
			$id = Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>$message,'data'=>$id));
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
		
	}
	/*** Search CPT Codes ends here ***/
	
	/*** Start superbill details page show ***/
	public function getShowApi($id) {
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && SuperbillTemplate::where('id', $id)->count()) {
			$superbill_arr = SuperbillTemplate::with('provider','provider.degrees','creator','modifier')->find($id)->toArray();
			$superbill_arr['header_list'] = explode(",",$superbill_arr['header_list']);
			$superbill_arr['get_list_order'] = explode(",",$superbill_arr['get_list_order']);
			
			if($superbill_arr["skin_procedures_units"] !='') 
				$superbill_arr["skin_procedures_units"] = explode(",",$superbill_arr["skin_procedures_units"]);
			if($superbill_arr["medications_units"] !='') 
				$superbill_arr["medications_units"] = explode(",",$superbill_arr["medications_units"]);
			
			foreach ($superbill_arr['header_list'] as $key=>$list_value) {
				$superbill_arr[$list_value] = explode(",",$superbill_arr[$list_value]);
				$DB = DB::connection()->getDatabaseName();
				$admin_db_name = 'responsive';//getenv('DB_DATABASE');
				if($DB == $admin_db_name) {
					$cpt_list	= Cpt::whereIn('cpt_hcpcs', $superbill_arr[$list_value])->orderBy('short_description','ASC')->pluck('short_description','cpt_hcpcs')->all();
				} else {
					$cpt_list	= Cpt::on("responsive")->whereIn('cpt_hcpcs', $superbill_arr[$list_value])->orderBy('short_description','ASC')->pluck('short_description','cpt_hcpcs')->all();
				}
				
				$superbill_arr[$list_value]=[];
				foreach ($cpt_list as $k=>$values) {
					$superbill_arr[$list_value][] = $k."::".$values;
				}
			}
			$superbill_arr['id'] = Helpers::getEncodeAndDecodeOfId($superbill_arr['id'],'encode');
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('superbill_arr')));
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End superbill details page show ***/
	
	/*** Search CPT codes starts here [ajax]  ****/
	public function getTemplatesearchApi($request="", $type= '')
	{
		
		if($type == "charge"){    // This would be doen from charge entry screen.
			$search = $request;
		} else{
			$request 	= Request::all();
			$search		= $request['search_keyword'];
		}		
		$DB = DB::connection()->getDatabaseName();
		$admin_db_name = 'responsive';//getenv('DB_DATABASE');
		if($DB == $admin_db_name) {
			/*$cpt_list	= Cpt::select('cpt_hcpcs','short_description','medium_description','long_description','pli_rvu','medicare_global_period','modifier','work_rvu','facility_practice_rvu','nonfacility_practice_rvu','total_facility_rvu','total_nonfacility_rvu','created_at','updated_at','created_by','updated_by')->where('cpt_hcpcs', 'LIKE', '%'.$search.'%')->orWhere('short_description', 'LIKE', '%'.$search.'%')
					->orWhere('long_description', 'LIKE', '%'.$search.'%')
					->orWhere('work_rvu', 'LIKE', '%'.$search.'%')
					->orWhere('total_facility_rvu', 'LIKE', '%'.$search.'%')
					->orWhere('total_nonfacility_rvu', 'LIKE', '%'.$search.'%')
					->take(60)
					->get();*/
					
					/* AdminCpt to Cpt changed */
					
			$cpt_ids = Favouritecpts::pluck('cpt_id')->all(); // Cpt will be listed only if it was added into favourites
			$cpt_list	= Cpt::select('cpt_hcpcs','short_description','medium_description','long_description','pli_rvu','medicare_global_period','modifier_id','work_rvu','facility_practice_rvu','nonfacility_practice_rvu','total_facility_rvu','total_nonfacility_rvu','created_at','updated_at','created_by','updated_by')
			        ->where(function($query) use ($cpt_ids){
			        	$query->whereIn('id',$cpt_ids);			        	
			        })
			        ->where(function($query) use($search){
			        	// MEDV2-864 - Separate Code Search using '@' symbol for ICD and CPT
			        	if (strpos($search, '@') !== false) {
	                    	$search = str_replace('@', '', $search);
	                    	$query->orwhere('cpt_hcpcs', 'LIKE', $search.'%');
	                    } else {
					        $query->orwhere('cpt_hcpcs', 'LIKE', '%'.$search.'%')
							        ->orWhere('short_description', 'LIKE', '%'.$search.'%')
									->orWhere('long_description', 'LIKE', '%'.$search.'%')
									->orWhere('work_rvu', 'LIKE', '%'.$search.'%')
									->orWhere('total_facility_rvu', 'LIKE', '%'.$search.'%')
									->orWhere('total_nonfacility_rvu', 'LIKE', '%'.$search.'%');	
						}
			        })->where('status', 'Active')			       				
					->take(60)
					->get();
		} else {
			$cpt_ids = Favouritecpts::pluck('cpt_id')->all();   // Cpt will be listed only if it was added into favourites
			/* AdminCpt to Cpt changed */
			$cpt_list	= Cpt::select('cpt_hcpcs','short_description','medium_description','long_description','pli_rvu','medicare_global_period','modifier_id','work_rvu','facility_practice_rvu','nonfacility_practice_rvu','total_facility_rvu','total_nonfacility_rvu','created_at','updated_at','created_by','updated_by')
					        ->where(function($query) use ($cpt_ids){
					        	$query->whereIn('id',$cpt_ids);			        	
					        })
					        ->where(function($query) use($search){
					        	if (strpos($search, '@') !== false) {
			                    	$search = str_replace('@', '', $search);
			                    	$query->orwhere('cpt_hcpcs', 'LIKE', $search.'%');
			                    } else {
							        $query->orwhere('cpt_hcpcs', 'LIKE', '%'.$search.'%')
									        ->orWhere('short_description', 'LIKE', '%'.$search.'%')
											->orWhere('long_description', 'LIKE', '%'.$search.'%')
											->orWhere('work_rvu', 'LIKE', '%'.$search.'%')
											->orWhere('total_facility_rvu', 'LIKE', '%'.$search.'%')
											->orWhere('total_nonfacility_rvu', 'LIKE', '%'.$search.'%');
								}	
					        })->where('status', 'Active')			       				
							->take(60)
							->get();		
            
		}
		if(count($cpt_list)>0) {
			return Response::json(array('status'=>'success', 'message'=>'','data'=>$cpt_list));
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End search superbill process ***/
	
	/*** Start superbill template model view show ***/
	public function getTemplateshowApi($request="")	{
		$request 	= Request::all();
		unset($request['_token']);
		if(isset($request['template_id'])) {
			$request['template_id'] = Helpers::getEncodeAndDecodeOfId($request['template_id'],'decode');
			$header_style	= explode("/&/",SuperbillTemplate::where('id',$request['template_id'])->value('header_style'));
			unset($request['template_id']);
			return Response::json(array('status'=>'update', 'message'=>'','data'=>compact('request','header_style')));
		} else {
			return Response::json(array('status'=>'create', 'message'=>'','data'=>compact('request')));
		}
	}
	/*** End superbill template model view show ***/
	
	/*** Start delete superbill process ***/
	public function getDeleteApi($id) {
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && SuperbillTemplate::where('id', $id)->count()){
			$result = SuperbillTemplate::find($id)->delete();
			if($result == 1) 
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
			else			 
				return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));
		} else {
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));
		}
	}
	/*** End delete superbill process ***/
	
	/*** Search CPT codes ends here [ajax] ***/
	function __destruct() {
    }
}