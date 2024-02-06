<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Staticpage as Staticpage;
use Input;
use Lang;
use App\Http\Helpers\Helpers as Helpers;
class StaticPageApiController extends Controller 
{
	/*** Start to listing the Helps  ***/
	public function getIndexApi($export='')
	{
		//Help list
		$staticpages = Staticpage::all();
		//Export the format PDF, Excel and CSV
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'title',
				'heading'		=>	'',
				'fields' 		=>	array(
					'title'		=>	'title',
					'slug'		=>	'slug',
					'content'	=>	'content',
					'status'	=>	'status',
			));
			$export 		= 	new CommonExportApiController();
			return $export->generateExports($exportparam, $staticpages);
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('staticpages')));
	}
	/*** End to listing the Helps  ***/
	
	/*** Start to Create the Helps	 ***/
	public function getCreateApi()
	{			
		$staticpages = Staticpage::orderBy('title','ASC')->pluck('title', 'id')->all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('staticpages')));
	}
	/*** End to Create the Helps	 ***/
	
	/*** Start to Store the Helps	 ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
		//Back end Validation check 
		$rules = Staticpage::$rules+array('type' => 'required|unique:staticpages,type,NULL,id,deleted_at,NULL');
		$validator = Validator::make($request,$rules , Staticpage::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$slug = Input::get('type');
			
			$slug = ucfirst(str_replace('_',' ',$slug));
			$request['slug'] = $slug;
			$request['created_by'] = Auth::user ()->id;
			$data = Staticpage::create($request);
			$data->created_at = date('Y-m-d h:i:s');
			$data->save();
			$id =Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			return Response::json(array('status'=>'success','message'=>Lang::get("common.validation.create_msg"),'data'=>$id));					
		}
	}
	/*** End to Store the Helps	 ***/
	
	/*** Start to Show the Helps	 ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//View the detail
		if(Staticpage::where('id',$id)->count()>0 && is_numeric($id))
		{	
			$staticpages = Staticpage::with('user','updateuser')->find($id);
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('staticpages')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Show the Helps	 ***/
	
	/*** Start to Edit the Helps	 ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//If any changes in in the Help
		if(Staticpage::where('id',$id)->count()>0 && is_numeric($id))
		{
			$staticpages = Staticpage::find($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('staticpages')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Helps ***/
	
	/*** Start to Update the Helps	 ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(Staticpage::where('id',$id)->count()>0 && is_numeric($id))
		{
			if($request == '')
				$request = Request::all();
			$rule = Staticpage::$rules+array('type' => 'required|unique:staticpages,type,'.$id.',id,deleted_at,NULL');
			$validator = Validator::make($request,$rule, Staticpage::messages());
			//Validation check		
			if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
			else
			{	
				$slug = Input::get('type');
				$slug = ucwords(str_replace('_',' ',$slug));
				$request['slug'] = $slug;
				$staticpages = Staticpage::findOrFail($id);
				$staticpages->update($request);
				$user = Auth::user ()->id;
				$staticpages->updated_by = $user;
				$staticpages->updated_at = date('Y-m-d h:i:s');
				$staticpages->save ();
				//dd($staticpages);
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
			}
		}
		else
		{
		   return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Update the Helps	 ***/
	
	/*** Start to Destory Helps ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Delete the detail
		if(Staticpage::where('id',$id)->count()>0 && is_numeric($id))
		{
			Staticpage::Where('id',$id)->delete();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.empty_record_msg"),'data'=>null));
		}
	}
	/*** End to Destory Helps	 ***/
	
	public function getHelpContentApi($type)
	{
		$staticpage = Staticpage::where('type',$type)->where('status','Active')->first();
		if($staticpage)
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('staticpage')));
		else
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>''));	
	}
	
	function __destruct() 
	{
    }
}
