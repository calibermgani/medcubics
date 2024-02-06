<?php namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use Request;
use Response;
use App\Models\Patients\ClaimAmbulanceBilling as ClaimAmbulanceBilling;
use App\Models\Payments\ClaimInfoV1;

class ClaimAmbulanceBillingApiController extends Controller {

	public function getStoreApi($request = '')
	{
		$request = Request::all();
		$result = ClaimAmbulanceBilling::create($request);
		if($result)
		{
			if(!empty($request['claim_id']))
			ClaimInfoV1::where('id', $request['claim_id'])->update(['ambulance_billing_id' =>$result->id]);
			return Response::json(array('status' => 'success', 'message' => 'claim biiling added successfuly', 'data' => $result->id));
		}
		else
		{
			return Response::json(array('status' => 'error', 'message' => 'claim biiling did not added successfuly', 'data' => ''));

		}
		
	}
	public function getEditApi($id)
	{
		if(ClaimAmbulanceBilling::where('id',$id)->count()>0)
		{
			$claimambulancedetail = ClaimAmbulanceBilling::findOrFail($id);			
            return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('claimambulancedetail')));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message' => '', 'data' => ''));
		}
	}
	public function getUpdateApi($id ,$request)
	{
		$request = Request::all();
		$claimambulancedetail = ClaimAmbulanceBilling::findOrFail($id);
		if($claimambulancedetail->update($request))
		{
			return Response::json(array('status' => 'success', 'message' => 'Additional claim details added successfully.', 'data' => $claimambulancedetail->id));
		}
		else
		{
			return Response::json(array('status'=>'error', 'message' => 'claim detail did not added successfully', 'data' => ''));
	    }
	}

}
