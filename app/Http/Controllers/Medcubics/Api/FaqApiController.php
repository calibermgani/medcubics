<?php namespace App\Http\Controllers\Medcubics\Api;

use Response;
use Validator;
use Request;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Medcubics\Faq as FAQ;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use DB;
use Lang;


class FaqApiController extends Controller {
	/**** FAQ List page start ***/
	public function getIndexApi($export='')
	{
		$faq = FAQ::get();
		
		 if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'faq',
				'heading'		=>	'',
				'fields' 		=>	array(
					'question'				=>	'Question',
					'answer'				=>	'Answer',
					'status'				=>	'Status'
					)
			);
                       
		$callexport = new CommonExportApiController();
		return $callexport->generatemultipleExports($exportparam, $faq, $export); 
                
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('faq')));
	}
	/**** FAQ List page end ***/
	
	/**** FAQ Create page start ***/
	public function getCreateApi()
	{
		$faq = FAQ::all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('faq')));
		
	}

	/**** FAQ create page end ***/
	
	/**** FAQ store page start ***/
	public function getStoreApi($request='')
	{
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, FAQ::$rules+array('question' => 'required|unique:faqs,question,NULL,id,deleted_at,NULL'), FAQ::$messages);
		if ($validator->fails())
			{
				$errors = $validator->errors();
				return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
			}
		else
			{	
				$faq = FAQ::create(Request::all());
				$user = Auth::user ()->id;
				$faq->created_by = $user;$id = $faq->id;
				$faq->save ();
				$id = Helpers::getEncodeAndDecodeOfId($faq->id,'encode');
				return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));		
			}
	}
	/**** FAQ store page end ***/
	/**** FAQ show page start ***/
	public function getShowApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		
		if($id != '')
		{
			$faq = FAQ::where('id',$id)->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('faq')));	
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>null));
		}
	}
	/**** FAQ show page end ***/
	/**** FAQ edit page start ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(FAQ::where('id',$id)->count())
		{
			$faq = Holdoption::find($id);
			return Response::json(array('status'=>'success', 'message'=>null, 'data'=>compact('faq')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"), 'data'=>null));
		}
	}
	/**** FAQ edit page end ***/
	/**** FAQ Update page start ***/
	public function getUpdateApi($id, $request='')
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
		$request = Request::all();
		$validator = Validator::make($request, FAQ::$rules+array('question' => 'required|unique:faqs,question,'.$id.',id,deleted_at,NULL'), FAQ::$messages);
		if ($validator->fails())
		{	$errors = $validator->errors();
			$id = Helpers::getEncodeAndDecodeOfId($id,'encode');
			return Response::json(array('status'=>'error', 'message'=>$errors, 'data'=>$id));	
		}
		else
		{	
			$data = FAQ::findOrFail($id);
			$data->update($request);
			$user = Auth::user()->id;
			$data->updated_by = $user;
			$data->save ();
			$id = Helpers::getEncodeAndDecodeOfId($data->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"), 'data'=>$id));					
		}
	}
	/**** FAQ update page end ***/
	/**** FAQ delete page start ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		FAQ::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"), 'data'=>''));
	}
	/**** FAQ delete page end ***/
}
