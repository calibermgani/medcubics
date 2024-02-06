<?php namespace App\Http\Controllers\Api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\EmailTemplate as EmailTemplate;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use File;
use Lang;

class EmailTemplateApiController extends Controller 
{
	/*** start to list the page ***/
	public function getIndexApi($export='')
	{
		$emailtemplate 		  =	Emailtemplate::get()->toArray();
		$email_template_count = Emailtemplate::count();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('emailtemplate','email_template_count')));
	}
	/*** end to list the page ***/

	/*** start to update the email template content ***/
	public function getUpdateApi()
	{
		$request 	= Request::all();
		$rules = $messages = [];
		foreach($request['subject'] as $key => $value)
		{
			$rules['subject.'.$key] = 'required';
			$rules['content.'.$key] = 'required';
			$messages['subject.' . $key.'.required'] = Lang::get("practice/patients/correspondence.validation.subject");
			$messages['content.' . $key.'.required'] = Lang::get("common.validation.content");
		}
		$validator 	= Validator::make($request,$rules,$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{	
			$user = Auth::user()->id;
			$Emailtemplate =array();
			unset($request['_token']);
			foreach($request as $key_name=>$value_arr)
			{
				foreach($value_arr as $key=>$value)
				{
					$res = Emailtemplate::findOrFail($key);
					$res->$key_name = $value;
					$res->updated_by = $user;
					$res->save ();
				}
			}
			/** Ends - General address flag update ***/
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));	
		}
	}
	/*** End to update the email template content ***/
}
