<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use App\Models\Resources as Resources;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use DB;
use Lang;

class ResourcesApiController extends Controller {
	
	public function getIndexApi($export='')
	{
		$resources = Resources::with('facility','provider')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
					'filename'		=>	'Resources',
					'heading'		=>	'',
					'fields' 		=>	array(
							'resource_name'				=>	'Resource Name',
							'resource_location_id'		=>	array('table'=>'facility' ,	'column' => 'facility_name' ,	'label' => 'Resource Location'),
							'resource_code'				=>	'Resource Code',
							'phone_number'				=>	'Phone Number',
							'default_provider_id'			=>	array('table'=>'provider' ,	'column' => 'provider_name',	'label' => 'Default Provider'),
					));
                        
                        $callexport = new CommonExportApiController();
                        return $callexport->generatemultipleExports($exportparam, $resources, $export); 
                
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('resources')));
	}
	
	public function getCreateApi()
	{			
		$resources = Resources::all();

		$facilities = Facility::where('status', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name', 'id')->all();
		$facility_id=''; 

		//$providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"),'id')->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
		$providers = Provider::select(DB::raw("provider_name"),'id')->where('status', '=', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
		$provider_id='';
			
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('resources','facilities','facility_id','providers','provider_id')));
	}
	
	public function getStoreApi($request='')
	{
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, Resources::$rules, Resources::$messages);
		if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				$resources = Resources::create(Request::all());
				$user = Auth::user ()->id;
				$resources->created_by = $user;
				$resources->save ();
				$id = $resources->id;
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>compact('id')));		
			}
	}
	
	public function getShowApi($id)
	{
			if(Resources::where('id',$id)->count()){
			$resources = Resources::with('facility','provider')->where('id',$id)->first();	
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('resources')));	
	}
			else{
			   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
	}
	
	public function getEditApi($id)
	{
			if(Resources::where('id',$id)->count()){
			$resources = Resources::find($id);

			$facilities = Facility::where('status', 'Active')->orderBy('facility_name','ASC')->pluck('facility_name', 'id')->all();
			$facility_id = $resources->resource_location_id;

				//$providers = Provider::select(DB::raw("CONCAT(last_name,' ',first_name) AS provider_name"),'id')->where('status', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
				$providers = Provider::select(DB::raw("provider_name"),'id')->where('status', 'Active')->orderBy('provider_name','ASC')->pluck('provider_name', 'id')->all();
			$provider_id = $resources->default_provider_id;

			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('resources','facilities','facility_id','providers','provider_id')));
			}
			else{
			   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
			}
	}
	
	public function getUpdateApi($type, $id, $request='')
	{
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, Resources::$rules, Resources::$messages );
		
		if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				$resources = Resources::findOrFail($id);
				$resources->update(Request::all());
				$user = Auth::user ()->id;
				$resources->updated_by = $user;
				$resources->save ();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
	}
	
	public function getDeleteApi($id)
	{
		Resources::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	
	function __destruct() 
	{
    }
}
