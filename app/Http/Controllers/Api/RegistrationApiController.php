<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;
use Response;
use Request;
use Lang;
use App\Models\Registration as Registration;

class RegistrationApiController extends Controller 
{
	/*** lists page Starts ***/
	public function getIndexApi($id='') 
	{
		if(Registration::first())
		{
			$registration = Registration::first();
			return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('registration')));
		}
		else
		{
			return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data'=>''));
		}
    }
	/*** Lists Function Ends ***/
	
	/*** Store Function Starts ***/
	public function getStoreApi($request = '') 
	{
        $request = Request::all();
		$user = Auth::user ()->id;
		if($request)
		{
			$Registration=Registration::truncate();
			$Registration = Registration::create($request);
			$Registration->created_by = $user;
			$Registration->updated_by = $user;
			$Registration->save();
			return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.update_msg"), 'data' => ''));
		}
	}
	/*** Store Function Ends ***/
	
	function __destruct() 
	{
    }
}
