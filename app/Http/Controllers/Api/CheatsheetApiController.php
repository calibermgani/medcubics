<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cheatsheet as Cheatsheet;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use App\Models\Resources as Resources;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use Lang;

class CheatsheetApiController extends Controller {
	
	public function getIndexApi($export='')
	{
		$cheatsheet = Cheatsheet::with('facility','provider','resources')->get();
		
		
		 if($export != "")
		{
			$exportparam 	= 	array(
					'filename'		=>	'Cheat Sheet',
					'heading'		=>	'Cheat Sheet',
					'fields' 		=>	array(
							'resource_id'		=>  array('table' =>'resources' , 'column' => 'resource_name' , 'label' => 'Resource'),
							'facility_id'		=>	array('table'=>'facility' ,	'column' => 'facility_name' , 'label' => 'Facility'),
							'provider_id'		=>	array('table'=>'provider' ,	'column' => ['first_name','last_name'] , 'label' => 'Provider'),
							'visit_type_id'		=>	'Visit Type',
							'cpt'				=>	'CPT',
							'icd'				=>	'ICD',
							'claimstatus'		=>	'Claim status',
							'feeschedules'		=>	'Feeschedules',
					));
			$export 		= 	new CommonExportApiController();
			return $export->generateExports($exportparam, $cheatsheet);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cheatsheet')));
	}

	public function getCreateApi()
	{			
		$cheatsheet = Cheatsheet::all();

		$facilities = Facility::where('status', '=', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name', 'id')->all();
		$facility_id=''; 

		$providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"),'id')->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
		$provider_id='';

		$resources = Resources::orderBy('resource_name','ASC')->pluck('resource_name', 'id')->all();
		$resource_id='';
			
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cheatsheet','facilities','facility_id','providers','provider_id','resources','resource_id')));
	}
	
	public function getStoreApi($request='')
	{
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, Cheatsheet::$rules, Cheatsheet::$messages);
		if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				$cheatsheet = Cheatsheet::create(Request::all());
				$id = $cheatsheet->id;
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>compact('id')));		
			}
	}
	
	public function getShowApi($id)
	{
			$cheatsheet = Cheatsheet::with('facility','provider','resources')->where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('cheatsheet')));	
	}
	
	public function getEditApi($id)
	{
			$cheatsheet = Cheatsheet::find($id);

			$facilities = Facility::where('status', '=', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name', 'id')->all();
			$facility_id = $cheatsheet->resource_location_id;

			$providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"),'id')->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
			$provider_id = $cheatsheet->default_provider_id;

			$resources = Resources::orderBy('resource_name','ASC')->pluck('resource_name', 'id')->all();
			$resource_id = $cheatsheet->resource_id;

			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('cheatsheet','facilities','facility_id','providers','provider_id','resources','resource_id')));
	}
	
	public function getUpdateApi($type, $id, $request='')
	{
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, Cheatsheet::$rules, Cheatsheet::$messages );
		
		if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				$cheatsheet = Cheatsheet::findOrFail($id);
				$cheatsheet->update(Request::all());
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
	}
	
	public function getDeleteApi($id)
	{
		Cheatsheet::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	
	function __destruct() 
	{
    }
	
}
