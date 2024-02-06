<?php namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\Templatetype as Templatetype;
use App\Models\Templatepairs as Templatepairs;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Patients\PatientCorrespondence;
use Illuminate\Support\Collection;
use Response;
use Request;
use Validator;
use Lang;
use DB;

class TemplatesApiController extends Controller 
{
	/*** Start to listing the Templates  ***/
	public function getIndexApi($type,$export='')
	{
		$practice_timezone = Helpers::getPracticeTimeZone();
		if($export != "")
		{
			$templates_query = Template::with('templatetype','creator')->where('template_type_id', '!=', '0');
			if($type =="App")
				$templates = $templates_query->whereHas('templatetype', function($q){ $q->where('templatetypes', 'like',  'App');})->orderBy('id','ASC')->get();
			else
				$templates = $templates_query->whereHas('templatetype', function($q){ $q->where('templatetypes', 'not like',  'App');})->orderBy('id','ASC')->get();
			$exportparam 	= 	array(
			'filename'		=>	'templates',
			'heading'		=>	'Templates Report',
			'fields' 		=>	($type =="App") ? array(
							'name'			=>	'Name',
							'status'		=>	'Status',
							'created_by'	=>	array('table'=>'creator','column' => 'short_name','label' => 'Created by'),
							'created_at'	=>	 "Created On",
			) : array(
							'name'			=>	'Name',
							'templatetypes'	=>array('table'=>'templatetype','column' => 'templatetypes','label' => 'Category'),
							'status'		=>	'Status',
							'created_by'	=>	array('table'=>'creator','column' => 'short_name','label' => 'Created by'),
							'created_at'	=>	 "Created On",
			));
			$callexport = new CommonExportApiController();
			return $callexport->generatemultipleExports($exportparam, $templates, $export); 
		}
		$templates_query = Templatetype::with(['template' => function($query2) use ($practice_timezone) {
					$query2->select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'));
				},'template.creator'])->whereHas('template', function($q) { 
			$q->where('template_type_id', '!=', '0');
		});
		if($type =="App")
			$templates = $templates_query->where('templatetypes', 'like', 'App')->orderBy('templatetypes','ASC')->first();
		else
			$templates = $templates_query->where('templatetypes', 'not like', 'App')->orderBy('templatetypes','ASC')->get();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('templates')));
	}
	/*** 	End to listing the Templates   ***/
	
	/*** 	Start to Create the Templates   ***/
	public function getCreateApi()
	{
		$templatestype = Templatetype::where("templatetypes",'not like', '%App%')->pluck('templatetypes','id')->all();
                /** hided for 1st version - Benefit Verifications **/
                unset($templatestype[1]);
		$templatepairs = Templatepairs::pluck('value','label')->all();
		$templates_type_id ='';
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('templatestype','templates_type_id','templatepairs')));
	}
	/***	End to Create the Templates   ***/
	
	/***	Start to Store the Templates   ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		$rules = Template::$rules + array('name' =>'required|unique:templates,name,NULL,id,template_type_id,'.$request['template_type_id'].',deleted_at,NULL');
		$validator = Validator::make($request, $rules, Template::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
		}
		else
		{
			$request['created_by'] = Auth::user()->id;
			$data = Template::create($request);
			// dd($data);
			// $data->updated_at ="0000-00-00 00:00:00";
			$data->save();
			//Encode ID for data
			$temp = new Collection($data);
			$temp_id = $temp['id'];
			$temp->pull('id');
			$temp_encode_id = Helpers::getEncodeAndDecodeOfId($temp_id, 'encode');
			$temp->prepend($temp_encode_id, 'id');
			$data = $temp->all();
			$data = json_decode(json_encode($data), FALSE);
			//Encode ID for data
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$data->id));
		}
	}
	/*** 	End to Store the Templates	 ***/
	
	/*** 	Start to Show the Templates	***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if(Template::where('id', $id)->count())
		{
				$templates = Template::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('templatetype','creator','modifier')->find ( $id );
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('templates')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** 	End to Show the Templates	***/
	
	/*** 	Start to Edit the Templates	***/
	public function getEditApi($id)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$practice_timezone = Helpers::getPracticeTimeZone();
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Template::where('id', $id)->count())
		{
			$patient_correspondence = PatientCorrespondence::where('template_id',$id)->count();
			$templates = Template::select('*',DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'),DB::raw('CONVERT_TZ(updated_at,"UTC","'.$practice_timezone.'") as updated_at'))->with('templatetype','creator','modifier')->findOrFail($id);
			$templatepairs = Templatepairs::pluck('value','label')->all();
			$templatestype = Templatetype::where("templatetypes",'not like', '%App%')->pluck('templatetypes','id')->all();
			$templates_type_id =$templates->template_type_id;
	
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('templates','templatestype','templates_type_id','templatepairs','patient_correspondence')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** 	End to Edit the Templates 	***/
	
	/*** 	Start to Update the Templates	 ***/
	public function getUpdateApi($id, $request)
	{	
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Template::where('id', $id)->count())
		{
			$request = Request::all();
			$rules = Template::$rules + array('name' =>'required|unique:templates,name,'.$id.',id,template_type_id,'.$request['template_type_id'].',deleted_at,NULL');
			$validator = Validator::make($request, $rules, Template::messages());
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));
			}
			else
			{
				$templates = Template::findOrFail($id);
				$request['updated_by'] = Auth::user()->id;
				$templates->update($request);
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.template_update_msg"),'data'=>''));
			}
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** 	End to Update the Templates	 ***/
	
	/*** 	Start to Destory the Templates	 ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if((isset($id) && is_numeric($id))  && (isset($id) && is_numeric($id)) && Template::where('id', $id)->count())
		{
			Template::where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.template_delete_msg"),'data'=>''));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	
	/*** 	End to Destory the Templates	 ***/
	function __destruct() 
	{
    }
}