<?php namespace App\Http\Controllers\Patients\Api;

use Request;
use App\Http\Controllers\Controller;
use Response;
use App\Models\Patients\ClaimOtherDetail as ClaimOtherDetail;
use App\Models\Payments\ClaimInfoV1;

class ClaimOtherDetailApiController extends Controller {

	public function getStoreApi($request = '')
	{
		$request = Request::all();        
		if(!empty($request['prescription_date']))
       		$request['prescription_date'] = date('Y-m-d', strtotime($request['prescription_date']));
        if(!empty($request['consultations_dates']))
        	$request['consultations_dates'] = date('Y-m-d', strtotime($request['consultations_dates']));
        if(!empty($request['date_of_last_visit']))
        	$request['date_of_last_visit'] = date('Y-m-d', strtotime($request['date_of_last_visit']));
        if(!empty($request['date_of_manifestation']))
        	$request['date_of_manifestation'] = date('Y-m-d', strtotime($request['date_of_manifestation']));
        if(!empty($request['estimated_dob']))
        	$request['estimated_dob'] = date('Y-m-d', strtotime($request['estimated_dob']));
	    if(!empty($request['effective_start']))
	    	$request['effective_start'] = date('Y-m-d', strtotime($request['effective_start']));
	    if(!empty($request['effective_end']))
	    	$request['effective_end'] = date('Y-m-d', strtotime($request['effective_end']));
	    if(!empty($request['date_of_last_xray']))
	    	$request['date_of_last_xray'] = date('Y-m-d', strtotime($request['date_of_last_xray']));
		$result = ClaimOtherDetail::create($request);
		if($result)
		{
			if(!empty($request['claim_id']))
			ClaimInfoV1::where('id', $request['claim_id'])->update(['claim_other_detail_id' =>$result->id]);
            return Response::json(array('status' => 'success', 'message' => 'claim other details added successfully', 'data' => $result->id));
		}
		else
		{
			return Response::json(array('status' => 'error', 'message' => 'claim other details did not added successfully', 'data' => ''));
		}
	}

	public function getEditApi($id)
	{

		if(ClaimOtherDetail::where('id',$id)->count()>0)
		{
			$claimotherdetail = ClaimOtherDetail::findOrFail($id);
            return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('claimotherdetail')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message' => '', 'data' => ''));
		}
	}

	public function getUpdateApi($id ,$request)
	{
		$request = Request::all();
		$claimotherdetail = ClaimOtherDetail::findOrFail($id);
		if(!empty($request['prescription_date']))
       		$request['prescription_date'] = date('Y-m-d', strtotime($request['prescription_date']));
        if(!empty($request['consultations_dates']))
        	$request['consultations_dates'] = date('Y-m-d', strtotime($request['consultations_dates']));
        if(!empty($request['date_of_last_visit']))
        	$request['date_of_last_visit'] = date('Y-m-d', strtotime($request['date_of_last_visit']));
        if(!empty($request['date_of_manifestation']))
        	$request['date_of_manifestation'] = date('Y-m-d', strtotime($request['date_of_manifestation']));
        if(!empty($request['estimated_dob']))
        	$request['estimated_dob'] = date('Y-m-d', strtotime($request['estimated_dob']));
	    if(!empty($request['effective_start']))
	    	$request['effective_start'] = date('Y-m-d', strtotime($request['effective_start']));
	    if(!empty($request['effective_end']))
	    	$request['effective_end'] = date('Y-m-d', strtotime($request['effective_end']));
	    if(!empty($request['date_of_last_xray']))
	    	$request['date_of_last_xray'] = date('Y-m-d', strtotime($request['date_of_last_xray']));
		if($claimotherdetail->update($request))
		{
			return Response::json(array('status' => 'success', 'message' => 'Additional claim details added successfully.', 'data' => $claimotherdetail->id));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message' => 'claim detail did not added successfully', 'data' => ''));
	    }
	}
}
