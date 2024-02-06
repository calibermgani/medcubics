<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Request;
use Input;
use Validator;
use Redirect;
use Auth;
use Session;
use View;
use DB;
use Config;
use App\Models\CommunicationInfo as CommunicationInfo;
use Carbon\Carbon;

class CommunicationHistoryController extends Api\CommunicationHistoryApiController {

    public function __construct() {
        View::share('heading', 'Practice');
        View::share('selected_tab', 'comhistory');
        View::share('heading_icon', Config::get('cssconfigs.Practicesmaster.practice'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $api_response = $this->getIndexApi();
        $api_response_data = $api_response->getData();
        $communicationhistory = $api_response_data->data->communicationhistory;
        return view('practice/communicationhistory/communicationhistorylist', compact('communicationhistory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

    public function callInitiated($patient_id, $from, $to) {
        try {
            $callInitiated = new CommunicationInfo;
            $callInitiated->patient_id = $patient_id;
            $callInitiated->claim_id = "0";
            $callInitiated->from = $from;
            $callInitiated->to = $to;
            $callInitiated->com_type = "Phone";
            $start_time = Carbon::now();
            $callInitiated->start_time = $start_time;

            $result = $callInitiated->save();

            if ($callInitiated) {
                return $result->id;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function callCompleted($id, $sid, $com_provider, $status, $duration, $cost) {
        try {
            $callCompleted = CommunicationInfo::findOrFail($id);
            $callCompleted->sid = $sid;
            $callCompleted->com_provider = $com_provider;
            $callCompleted->direction = "Outgoing";
            $callCompleted->status = $status;
//            $start_time = strtotime($callCompleted->start_time);
//            $end_time = strtotime(Carbon::now());
//            $duration = round(abs($end_time - $start_time) / 60,2);
            $callCompleted->duration = $duration;
            $callCompleted->cost = $cost;

            $callCompleted->save();

            if ($callCompleted) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function smsInititated($patient_id, $from, $to) {
        try {
            $smsInititated = new CommunicationInfo;
            $smsInititated->patient_id = $patient_id;
            $smsInititated->claim_id = "0";
            $smsInititated->from = $from;
            $smsInititated->to = $to;
            $smsInititated->com_type = "Sms";
            $start_time = Carbon::now();
            $smsInititated->start_time = $start_time;

            $result = $smsInititated->save();

            if ($smsInititated) {
                return $result->id;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function smsCompleted($id, $sid, $com_provider, $status) {
        try {
            $smsCompleted = CommunicationInfo::findOrFail($id);
            $smsCompleted->sid = $sid;
            $smsCompleted->com_provider = $com_provider;
            $smsCompleted->direction = "Outgoing";
            $smsCompleted->status = $status;

            $smsCompleted->save();

            if ($smsCompleted) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

}
