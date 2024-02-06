<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunicationInfo as CommunicationInfo;
use Input;
use Auth;
use Response;
use Request;
use Validator;
use DB;
use Config;

class CommunicationHistoryApiController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndexApi()
	{	
        $communicationhistory = CommunicationInfo::get();
        return Response::json(array('status'=>'success', 'message'=>null,'data'=>compact('communicationhistory')));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getCreateApi()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function getStoreApi()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getShowApi($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getEditApi($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getUpdateApi($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getDeleteApi($id)
	{
		//
	}

}
