<?php

namespace App\Http\Controllers\Patients;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use App\Http\Controllers\Payments\Api\PaymentApiController as PaymentApiController;   // Extended from main payment controller
use View;
use Redirect;
use Session;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Payments\PMTInfoV1 as PMTInfoV1;
use App\Models\STMTHoldReason as STMTHoldReason;
use App\Models\Patients\PatientNote;
use Route;
use Validator;
class InsurancePaymentController extends PaymentApiController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        View::share('heading', 'Payment');
        View::share('selected_tab', 'payments');
        View::share('heading_icon', 'fa-money');
    }

    public function index() {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $post_val = Request::all();
        $id = Route::current()->parameter('id');
        if (empty($post_val) && Session::has('post_val')) {
            $post_val = Session::get('post_val');
            if (empty($post_val['payment_detail_id']) && Session::has('post_val')) {

                //$post_val = $post_val;
                //$post_val['claim_id'] = Helpers::getEncodeAndDecodeOfId($post_val['claim_id'],'encode');
                // /dd($post_val);
            } elseif (!empty($post_val['payment_detail_id']) && Session::has('post_val')) {
                $payment_detail_id = Helpers::getEncodeAndDecodeOfId($post_val['payment_detail_id'], 'decode');
                $getval = PMTInfoV1::getPaymentDadetailData($payment_detail_id);
                //$getval = $getval['attributes'];
                $post_val['payment_amt'] = $getval['pmt_amt'];
                $post_val['unapplied'] = $getval['balance'];
            }
        }
        $api_response = $this->getCreateApi($post_val);
        $api_response_data = $api_response->getData();

        $patient_id = !(empty($id)) ? $id : Helpers::getEncodeAndDecodeOfId(@$post_val['patient_id'], 'encode');
        //dd($api_response);
        //dd($patient_id);

        if ($api_response_data->status != 'error') {
            $claims_list = $api_response_data->data->claims_lists;
            //  dd($claims_list);
            $remarkcode = $api_response_data->data->remarkcode;
            //dd($remarkcode);
            $insurance_lists = $api_response_data->data->insurance_lists;
            $insurance_list_total = $api_response_data->data->insurance_list_total;
            $check_box_count = $api_response_data->data->check_box_count;
            unset($post_val['filefield_eob']);
            $stmt_holdreason = STMTHoldReason::getStmtHoldReasonList();
            $patId = Helpers::getEncodeAndDecodeOfId(@$patient_id, 'decode');            
            $patient_alert_note = PatientNote::where('notes_type_id', @$patId)->where('notes_type', 'patient')->where('status', 'Active')->where('patient_notes_type', 'alert_notes')->select("created_by", "content")->first();
            return view('patients/payments/insurance_create', compact('claims_list', 'post_val', 'remarkcode', 'insurance_lists', 'insurance_list_total', 'check_box_count','stmt_holdreason', 'patient_alert_note'));
        } elseif ($api_response_data->status == 'error') {
            return Redirect::to('/patients/' . $patient_id . '/payments')->with('error', $api_response_data->message);
        } else {

            return Redirect::to('/patients/' . $patient_id . '/payments');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $request = Request::all();
        /* Removed validation
        $validate = Validator::make($request,[
                                //'change_insurance_category'=>'required',
                                'type'=>'required|alpha',
                                'patient_id'=>'required',
                                'payment_type'=>'required|alpha',
                                'claim_id'=>'required',
                                'payment_method'=>'required|alpha',
                                'payment_mode'=>'nullable|alpha',
                                // 'payment_amt'=>'nullable|numeric',
                                //'check_no'=>'nullable|alpha_num',
                                //'check_date'=>'nullable|date',
                                'deposite_date'=>'required|date',
                                'tot_billed_amt'=>'required|numeric',
                                'tot_paid_amt'=>'required|numeric',
                                'tot_balance_amt'=>'required|numeric',
                                'posting_date'=>'required|date',
                                'payment_unapplied_amt'=>'nullable|numeric',
                                'claim_balance'=>'required|numeric',
                                'dos_from'=>'required',
                                'cpt'=>'required|max:6',
                                'cpt_billed_amt'=>'required|between:0,99.99',
                                'cpt_allowed_amt'=>'required|between:0,99.99',
                                'balance'=>'required|between:0,99.99',
                                'co_pay'=>'required|between:0,99.99',
                                'co_ins'=>'required|between:0,99.99',
                                'with_held'=>'required|between:0,99.99',
                                'adjustment'=>'required|between:0,99.99',
                                'paid_amt'=>'required|between:0,99.99',
                                'ids'=>'required',
                                //'is_send_paid_amount'=>'required',
                                ]);
        if($validate->fails()){
            return Redirect::to('/payments')->with('error', implode('<br>', (array_unique($validate->errors()->all()))));
        }else{ */
            $api_response = $this->getStoreApi($request);
            $patient_id = 0;
            if(!empty($api_response)) {
                $api_response_data = $api_response->getData();
                $patient_id = $api_response_data->data;
                $payment_id = Helpers::getEncodeAndDecodeOfId(@$api_response_data->payment_id, 'encode');
            }
            if (isset($api_response_data->status) && $api_response_data->status == 'success' && empty($request['next'])) {
                return Redirect::to('/patients/' . $patient_id . '/payments')->with('success', $api_response_data->message);
            } elseif (isset($api_response_data->status) && $api_response_data->status == 'success' && $request['next'] == 1) {
                Session::put('post_val.payment_detail_id', $payment_id);
                return Redirect::to('/patients/' . $patient_id . '/payments/insurancecreate')->with('success', $api_response_data->message);
            } else {
                return Redirect::to('/patients/' . $patient_id . '/payments')->with('error', "The check number has already been taken.");
            }
        //}
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

}
