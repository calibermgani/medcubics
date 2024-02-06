<?php namespace App\Http\Controllers\Api;

use Response;
use Validator;
use Request;
use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\DocumentCategories as DocumentCategories;
use App\Http\Helpers\Helpers as Helpers;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use DB;
use Lang;


class ClinicalNotesCategoryApiController extends Controller {
	/**** Clinical categories List page start ***/
	public function getIndexApi($export='')
	{
		$clinicalcategories = DocumentCategories::with('creator')->where('module_name','clinicalnotes')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'		=>	'ClinicalCategories',
				'heading'		=>	'',
				'fields' 		=>	array(
					'category_value'=>	'Name',
					'created_by'	=>	array('table'=>'creator','column' => 'short_name','label' => 'Created By'),
					'created_at'	=>	 "Created On",
					)
			);
                       
		$callexport = new CommonExportApiController();
		return $callexport->generatemultipleExports($exportparam, $clinicalcategories, $export); 
                
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('clinicalcategories')));
	}
	/**** Clinical categories List page end ***/
	
		
	/**** clinicalnotescategory store page start ***/
	public function getStoreApi($request='')
	{
		if($request == '')
		$request = Request::all();
		$rules_doc = array('category_value' => 'required|unique:document_categories');
		$validator = Validator::make($request,$rules_doc , DocumentCategories::messages());
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{
			$clinicalcategories = DocumentCategories::create($request);
			$user = Auth::user ()->id;
			$clinicalcategories->module_name = "clinicalnotes";
			$clinicalcategories->created_by = $user;
			$id = $clinicalcategories->id;
			$clinicalcategories->save ();
			$id = Helpers::getEncodeAndDecodeOfId($clinicalcategories->id,'encode');
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>$id));		
		}
	}
	/**** clinicalnotescategory store page end ***/
	
	/**** clinicalnotescategory edit page start ***/
	public function getEditApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if(DocumentCategories::where('id',$id)->count())
		{
			$clinicalcategories = DocumentCategories::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null, 'data'=>compact('clinicalcategories')));
		}
		else
		{
		   return Response::json(array('status'=>'error', 'message'=>Lang::get("common.validation.not_found_msg"), 'data'=>null));
		}
	}
	/**** clinicalnotescategory edit page end ***/
	/**** clinicalnotescategory Update page start ***/
	public function getUpdateApi($id, $request='')
	{
		$original_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		if($request == '')
			$request = Request::all();
		$rules_validation = array('category_value' => 'required|unique:document_categories,category_value,'.$original_id);		
		$validator = Validator::make($request,$rules_validation , DocumentCategories::messages());
		if ($validator->fails())
		{	$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors, 'data'=>$id));	
		}
		else
		{	
			$data = DocumentCategories::findOrFail($original_id);
			$data->update($request);
			$data->updated_by = Auth::user()->id;
			$data->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"), 'data'=>$id));					
		}
	}
	/**** clinicalnotescategory update page end ***/
	
	/**** clinicalnotescategory delete page start ***/
	public function getDeleteApi($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		DocumentCategories::Where('id',$id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"), 'data'=>''));
	}
	/**** clinicalnotescategory delete page end ***/
}
