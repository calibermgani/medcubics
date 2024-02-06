<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provider as Provider;
use App\Models\Facility as Facility;
use App\Http\Controllers\Api\CommonExportApiController;
use App\Models\QuestionnariesTemplate as QuestionnariesTemplate;
use App\Models\Questionnaries as Questionnaries;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Lang;
use DB;

class QuestionnariesApiController extends Controller {

	public function getIndexApi($export='')
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$questionnaries	= Questionnaries::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->has("questionnaries_option")->with('questionnaries_option','provider','provider.degrees','provider.provider_types','facility','facility.facility_address','facility.speciality_details','facility.county','facility.pos_details','creator','modifier')->orderBy("created_at","DESC")->get();
		/*** Export option starts here ***/
		if($export != "")
		{   
			$exprt = array(
						'filename'	=>	'Questionnaries List',
						'heading'	=>	'',
						'fields' 	=>	array(
										'Provider Name'	=>	array('table'=>'','column' => 'provider_id', 'use_function' => ['App\Models\Provider','getProviderNamewithDegree'], 'label' => 'Provider Name'),
										'Facility Name'		=>	array('table'=>'facility','column' => 'facility_name','label'=> 'Facility Name'),
										'Questionnaires'	=>	array('table'=>'questionnaries_option','column' => 'title', 'label'=> 'Questionnaires Template'),
										'Created By'		=>	array('table'=>'creator','column' => 'short_name', 'label'=> 'Created By'),
										'Updated By'		=>	array('table'=>'modifier','column' => 'short_name','label'=> 'Updated By'),
										'updated_at'		=>	'Updated On')
						);
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exprt, $questionnaries, $export); 
		}
		/*** Export option ends here ***/
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('questionnaries')));
	}
	/*** Create page Starts ***/
	public function getCreateApi()
	{
		$facility_list	=  	Facility::getAllfacilities();//Getting all facilities detail
		$provider_list	=  	Provider::getRenderingAndBillingProvider('yes');//Getting all provider detail
		$questionnaires_list	=  	QuestionnariesTemplate::orderBy("created_at","ASC")->groupBy('template_id')->pluck('title','template_id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('facility_list','provider_list','questionnaires_list')));
	}	
	/*** Create page Ends ***/
	
	/*** Store Function Starts ***/
	public function getStoreApi($request='')
	{	
		if($request == '')
			$request 	= Request::all();
		
		Validator::extend('uniquequestionaries', function($attribute, $value, $parameters) use($request)
		{
			$facility_id = $request['facility_id'];
			$provider_id = $request['provider_id'];
			$questionnaires = $value;
			
			$getQuestionnaries = Questionnaries::where('facility_id', $facility_id)->where('provider_id',$provider_id)->where('template_id',$questionnaires)->get();
			
			if(count($getQuestionnaries)>0)
			{
				return false;	
			}
			else
			{
				return true;
			}
		});
		
		$validation_rules = Questionnaries::$rules+array('template_id' => 'required|uniquequestionaries');
		
		$validator 		= Validator::make($request, $validation_rules, Questionnaries::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$user= Auth::user()->id;
			$request['created_at'] = date('Y-m-d h:i:s');
			$data = Questionnaries::create($request);
			$data->created_by = $user;
			$data->save();
			$form_id  = Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$form_id));
		}
	}
	/*** Store Function Ends ***/
	
	/*** Show Function Starts ***/
	public function getShowApi($id)
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Questionnaries::where('id', $id)->count()>0 && is_numeric($id)==1)
		{	
			$questionaries = Questionnaries::select('*', DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'), DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('questionnaries_option','provider','provider.degrees','facility','creator','modifier')->where('id',$id)->first()->toArray();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>compact('questionaries')));
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
		if(Questionnaries::where('id', $id)->count()>0 && is_numeric($id)==1)
		{
			$facility_list	=  	Facility::getAllfacilities();//Getting all facilities detail
			$provider_list	=  	Provider::getBillingAndRenderingProvider('yes');//Getting all provider detail
			//$provider_list	=  	Provider::getAllprovider();//Getting all provider detail
			$questionnaires_list	=  	QuestionnariesTemplate::orderBy("created_at","ASC")->groupBy('template_id')->pluck('title','template_id')->all();
			$questionaries = Questionnaries::with('questionnaries_option','provider','facility')->where('id',$id)->first()->toArray();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('questionaries','facility_list','provider_list','questionnaires_list')));
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}
	/*** Edit page Ends ***/
	
	/*** Update Function Starts ***/
	public function getUpdateApi($id)
	{
		$form_id  = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Questionnaries::where('id', $form_id)->count()>0 && is_numeric($form_id)==1)
		{
			$request 	= Request::all();
			
			Validator::extend('uniquequestionaries', function($attribute, $value, $parameters) use($request,$form_id)
			{
				$facility_id = $request['facility_id'];
				$provider_id = $request['provider_id'];
				$questionnaires = $value;
				
				$getQuestionnaries = Questionnaries::where('facility_id', $facility_id)->where('provider_id',$provider_id)->where('template_id',$questionnaires)->where('id','!=',$form_id)->get();
				
				if(count($getQuestionnaries)>0)
				{
					return false;	
				}
				else
				{
					return true;
				}
			});
			
			$validation_rules = Questionnaries::$rules+array('template_id' => 'required|uniquequestionaries');
			
			$validator 		= Validator::make($request, $validation_rules, Questionnaries::$messages);
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$user= Auth::user()->id;
				$data = Questionnaries::findOrFail($form_id);
				$data ->update($request);
				$data->updated_by = $user;
				$data->updated_at = date('Y-m-d h:i:s');
				$data->save();
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>null));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>'null'));
		}
	}	
	/*** Update Function Ends ***/
	
	/*** Delete Function Starts ***/
	public function getDestroyApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Questionnaries::where('id',$id)->count())
		{
			Questionnaries::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>''));	
		}
	}
	/*** Delete Function Ends ***/
	function __destruct() 
	{
    }
}
