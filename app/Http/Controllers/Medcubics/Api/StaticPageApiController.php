<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Validator;
use App\Models\Staticpage as Staticpage;
use Input;
use Lang;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Http\Helpers\Helpers as Helpers;

class StaticPageApiController extends Controller 
{
	/*** Start to listing the Helps  ***/
	public function getIndexApi($export='')
	{
		//Listing the Help module
		$staticpages = Staticpage::all();
		//Export the PDF, Excel and CSV format
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
	public function getCreateApi()
	{
		$staticpages = Staticpage::orderBy('title','ASC')->pluck('title','id')->all();
		return Response::json(array('status'=>'sucess', 'message'=>null,'data'=>compact('staticpages')));	
	}
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();
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
			$request['created_at'] = date('Y-m-d h:i:s');
			$data = Staticpage::create($request);
			$data->save();
			$id = Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			return Response::json(array('status'=>'success','message'=>Lang::get("common.validation.create_msg"),'data'=>$id));					
		}
	}
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Help detail view
		if(Staticpage::where('id',$id)->count())
		{
			$staticpages = Staticpage::with('user','updateuser')->find($id);
			$staticpages->id = Helpers::getEncodeAndDecodeOfId($staticpages->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('staticpages')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/*** End to Show the Helps	 ***/
	
	/*** Start to Edit the Helps	 ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Edit the changes 
		if(Staticpage::where('id',$id)->count())
		{
			$staticpages = Staticpage::find($id);
			$staticpages->id = Helpers::getEncodeAndDecodeOfId($staticpages->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('staticpages')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/*** End to Edit the Helps ***/
	
	/*** Start to Update the Helps	 ***/
	public function getUpdateApi($type, $id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
			$request = Request::all();
		//Validation check the request format is okay or not	
		$validator = Validator::make(
			Input::all(),
			['title' => 'required',
			'type' => 'required|unique:staticpages,type,'. $id,
			'content' => 'required',
			'status' => 'required',
			]
		);	
		$rule = Staticpage::$rules+array('type' => 'required|unique:staticpages,type,'.$id.',id,deleted_at,NULL');
			$validator = Validator::make($request,$rule, Staticpage::messages());
			
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
			$staticpages = Staticpage::with('user')->findOrFail($id);
			$staticpages->update($request);
			$user = Auth::user ()->id;
			$staticpages->updated_by = $user;
			$staticpages->updated_at = date('Y-m-d h:i:s');
			$staticpages->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
	/*** End to Update the Helps	 ***/
	
	/*** Start to Destory Helps ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		//Delete the current Help detail 
		Staticpage::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	/*** End to Destory Helps	 ***/
	
	public function getHelpContentApi($type)
	{
		$staticpage = Staticpage::where('slug',$type)->first();
		if($staticpage)
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('staticpage')));
		else
			return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>''));	
	}
	
	function __destruct() 
	{
    }
}
