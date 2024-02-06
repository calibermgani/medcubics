<?php

namespace App\Http\Controllers\Patients\Api;

use App\Http\Controllers\Controller;
use App\Models\Patients\ClaimDetail as ClaimDetail;
use App\Models\Patients\Patient as Patient;
use App\Models\Patients\PatientContact as PatientContact;
use App\Models\Payments\ClaimAddDetailsV1;
use App\Models\Facility as Facility;
use App\Models\Provider as Provider;
use App\Models \State as State;
use App\Http\Helpers\Helpers as Helpers;
use Request;
use Response;
use DB;
use Validator;

class ClaimDetailApiController extends Controller {

    public function getCreateApi($patient_id) {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        if (Patient::where('id', $patient_id)->count() > 0) {
            $claimdetail = [];            
            $patient_lists = Patient::where('id', $patient_id)->select('id', 'gender')->first();            
            $patient_attorney = PatientContact::where('category', 'Attorney')->where('patient_id', $patient_id)->pluck('attorney_adjuster_name', 'id')->all();
            $facilities = Facility::orderBy('facility_name', 'asc')->pluck('facility_name', 'id')->all();
            $providers = Provider::select(DB::raw("CONCAT(provider_name) AS provider_name"), 'id')->where('status', '=', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            $state = State::pluck('code', 'code')->all();
            return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('claimdetail', 'patient_lists', 'patient_attorney', 'facilities', 'providers', 'state')));
        } else {
            return Response::json(array('status' => 'error', 'message' => '', 'data' => ''));
        }
    }

    public function getStoreApi($request = '') {
        if (empty($request))
            $request = Request::all();
        //dd($request);
        $request['other_date'] = isset($request['other_date']) ? date('Y-m-d', strtotime($request['other_date'])) : '';
        $request['illness_box14'] = isset($request['illness_box14']) ? date('Y-m-d', strtotime($request['illness_box14'])) : '';
        $request['unable_to_work_from'] = isset($request['unable_to_work_from']) ? date('Y-m-d', strtotime($request['unable_to_work_from'])) : '';
        $request['unable_to_work_to'] = isset($request['unable_to_work_to']) ? date('Y-m-d', strtotime($request['unable_to_work_to'])) : '';
        //$result = ClaimDetail::create($request);
        $result = ClaimAddDetailsV1::create($request);
        if ($result) {
            if (!empty($request['claim_id']) && !isset($request['type'])) {  //Type was added default entry from charge entry proces
                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
                ClaimAddDetailsV1::where('id', $result->id)->update(['claim_id' => $claim_id]);
            }
            return Response::json(array('status' => 'success', 'message' => 'Additional claim details added successfully.', 'data' => $result->id));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'claim detail did not added successfully', 'data' => ''));
        }
    }

    public function getEditApi($id) {
        //dd($id);
        if (ClaimAddDetailsV1::where('id', $id)->count() > 0) {
            $claimdetail = ClaimAddDetailsV1::where('id', $id)->with(['provider_details', 'facility_detail', 'claim_info' => function($query) 
                {$query->select('patient_id', 'id');}])->first();
            $patient_id = ClaimAddDetailsV1::where('id', $id)->pluck('patient_id')->first();
            $patient_lists = Patient::where('id', @$claimdetail->claim_info->patient_id)->select('id', 'gender')->first();             
            $patient_attorney = PatientContact::where('category', 'Attorney')->where('patient_id', $patient_id)->pluck('employer_name', 'id')->all();
            $facilities = Facility::orderBy('facility_name', 'asc')->pluck('facility_name', 'id')->all();
            $state = State::pluck('code', 'code')->all();
            $providers = Provider::select(DB::raw("CONCAT(provider_name) AS provider_name"), 'id')->where('status', '=', 'Active')->orderBy('provider_name', 'ASC')->pluck('provider_name', 'id')->all();
            return Response::json(array('status' => 'success', 'message' => '', 'data' => compact('claimdetail', 'patient_lists', 'patient_attorney', 'facilities', 'providers', 'state')));
        } else {
            return Response::json(array('status' => 'error', 'message' => '', 'data' => ''));
        }
    }

    public function getUpdateApi($id, $request) {
        $request = Request::all();
        //dd($request);
        if (!empty($request['other_date']))
            $request['other_date'] = date('Y-m-d', strtotime($request['other_date']));
        if (!empty($request['illness_box14']))
            $request['illness_box14'] = date('Y-m-d', strtotime($request['illness_box14']));
        if (!empty($request['unable_to_work_from']))
            $request['unable_to_work_from'] = date('Y-m-d', strtotime($request['unable_to_work_from']));
        if (!empty($request['unable_to_work_to']))
            $request['unable_to_work_to'] = date('Y-m-d', strtotime($request['unable_to_work_to']));
        if (!empty($request['unable_to_work_to']))
            $request['unable_to_work_to'] = date('Y-m-d', strtotime($request['unable_to_work_to']));
        if (isset($request['is_autoaccident']) && $request['is_autoaccident'] == "No")
            $request['autoaccident_state'] = "";
        //$claimdetail = ClaimDetail::findOrFail($id);
        $claimdetail = ClaimAddDetailsV1::findOrFail($id);
        $validator = Validator::make($request, array('lab_charge'=> 'numeric|between:0.00,999999999.99'), array('lab_charge' => 'Enter valid lab charge!') );
        if ($validator->fails()) {
            return Response::json(array('status' => 'error', 'message' => 'Invalid lab charge', 'data' => $claimdetail->id));
        }
        //dd($claimdetail);
        if ($claimdetail->update($request)) {
            return Response::json(array('status' => 'success', 'message' => 'Additional claim details added successfully.', 'data' => $claimdetail->id));
        } else {
            return Response::json(array('status' => 'error', 'message' => 'claim detail did not added successfully', 'data' => $claimdetail->id));
        }
    }

}
