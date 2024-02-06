<?php namespace App\Http\Controllers\Medcubics\Api;

use App\Http\Controllers\Controller;
use App\Models\Medcubics\Feeschedule as Feeschedule;
use Auth;
use Response;
use Request;
use Validator;
use Input;
use Lang;

class FeescheduleApiController extends Controller 
{
	/*** Listing the feescheduler start ***/
	public function getIndexApi($export = "")
	{
		$feeschedules = Feeschedule::with('user','userupdate')->get();
		if($export != "")
		{
			$exportparam 	= 	array(
				'filename'=>	'Fee Schedule Medcubics',
				'heading'=>	'Fee Schedule Medcubics',
				'fields' =>	array(
								'file_name'		=> 'Name',
								'fees_type'		=> 'Type',
								'template' 		=> 'Template',
								'choose_year'	=> 'Year',
                                                                'created_by' =>	array('table'=>'user' ,	'column'	 => 'name' ,	'label' => 'Created By'),
                                                                'updated_by' =>	array('table'=>'userupdate' ,	'column' => 'name' ,	'label' => 'Updated By'),
				));
			$callexport = new CommonExportApiController();
                        return $callexport->generatemultipleExports($exportparam, $feeschedules, $export); 
		}
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('feeschedules')));
	}
	/*** List the feescheduler end***/
	
	/*** Create new feescheduler start ***/
	public function getCreateApi()
	{
		$feeschedules = Feeschedule::all();
		return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('feeschedules')));
	}
	/*** Create new feescheduler end ***/
	
	/*** Store the new feescheduler start ***/
	public function getStoreApi($request='')
	{
		if($request == '')
			$request = Request::all();

		$validator = Validator::make($request, Feeschedule::$rules, Feeschedule::$messages);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$feeschedules = Feeschedule::create($request);
			$user = Auth::user ()->id;
			$feeschedules->created_by = $user;
			$feeschedules->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.create_msg"),'data'=>''));					
		}
	}
	/*** Store the new feescheduler end ****/
	
	/**** View the feescheduler start ***/
	public function getShowApi($id)
	{
		if(Feeschedule::with('user','userupdate')->where('id', $id )->count())
		{
			$feeschedules = Feeschedule::with('user','userupdate')->where('id', $id )->first();
			return Response::json(array('status'=>'success', 'message'=>'','data'=>compact('feeschedules')));	
		}
		else
		{
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
		}
	}
	/*** View the feescheduler end ***/
	
	/*** Edit the feeschedler values start ***/
	public function getEditApi($id)
	{
		if(Feeschedule::with('user','userupdate')->where('id', $id )->count())
        {
			$feeschedules = Feeschedule::findOrFail($id);
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('feeschedules')));
        }
        else
        {
			return Response::json(array('status'=>'failure', 'message'=>Lang::get("common.validation.not_found_msg"),'data'=>'null'));
		}
	}
	/*** Edit the feeschedler values end***/
	
	/*** Stroe the feescheduler values start ***/
	public function getUpdateApi($id, $request='')
	{
		if($request == '')
		$request = Request::all();
		$validator = Validator::make(
		Input::all(),
		[
		'file_name' 		=> 'required|unique:feeschedules,file_name,'.$id,
		'fees_type' 		=> 'required',
		'template' 			=> 'required',
		'choose_year' 		=> 'required|digits:4',
		'conversion_factor' => 'required',
		'percentage' 		=> 'required',
        ], 
		
		[ 
		'file_name.required' 		=> 'Please, Enter your File Name!',
		'file_name.unique' 			=> 'File name must be unique!',
		'fees_type.required' 		=> 'Please, Select your Fees Type!',
		'template.required' 		=> 'Please, Select your Template!',
		'choose_year.required' 		=> 'Please, Choose year!',
		'choose_year.digits' 		=> 'Year should be 4 digits',
		'conversion_factor.required'=> 'Please, Conversion Factor!',
		'percentage.required' 		=> 'Please, Enter your Percentage!',
		]
		);
		if ($validator->fails())
		{
			$errors = $validator->errors();
			return Response::json(array('status'=>'error', 'message'=>$errors,'data'=>''));	
		}
		else
		{		
			$feeschedule = Feeschedule::findOrFail($id);
			$feeschedule->update($request);
			$user = Auth::user ()->id;
			$feeschedule->updated_by = $user;
			$feeschedule->save ();
			return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.update_msg"),'data'=>''));					
		}
	}
	/*** Store the feescheduler values end ***/
	
	/*** Delete the feescheduler values start ***/
	public function getDestroyApi($id)
	{
		Feeschedule::find($id)->delete();
		return Response::json(array('status'=>'success', 'message'=>Lang::get("common.validation.delete_msg"),'data'=>''));	
	}
	/*** Delete the feescheduler values end ***/
	
	function __destruct() 
	{
    }
}
