<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Patients\Api\PatientApiController as PatientApiController;
use View;
use App;
use Request;
use Redirect;
use App\Models\Patient;
use App\Http\Helpers\Helpers as Helpers;
use Session;
use App\Models\Eras;
use App\Models\Insurance;
use App\Models\Code;
use App\Models\Payments\PMTInfoV1;
use App\Models\Patients\PatientNote;
use Validator;
class PatientPaymentController extends Api\PatientPaymentApiController {

    public function __construct() {
        View::share('heading', 'Payment');
        View::share('selected_tab', 'payments');
        View::share('heading_icon', 'fa-money');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getPaymentpopup($tab) {
        
        $api_response = $this->getPaymentpopupApi();            
        $api_response_data = $api_response->getData();
        $patient_ins = $api_response_data->data->insurance_list;
        $patient_ins = (array) $patient_ins;
        $claims_lists = [];       
        if ($tab == "insurance") {
            $view = 'patients/payments/insurance_addpop';
        } elseif ($tab == "patient") {
            $view = 'patients/payments/patientaddpopup';
        }
        return view($view, compact('patient_ins', 'claims_lists'));
    }

    public function geterapopup($id = '', $cheque = '') {

        $filename = Eras::where('id', $id)->pluck('pdf_name')->first();
        if (App::environment() == "production")
            $path_medcubic = $_SERVER['DOCUMENT_ROOT'] . '/medcubic/';
        else
            $path_medcubic = public_path() . '/';

        $local_path = $path_medcubic . 'media/era_files/' . Session::get('practice_dbid') . '/';
        $check_count = 0;
        /**
         * Declaration part array's and variables
         */
        $glossary = $basic_info = $insert_data = array();

        foreach (glob($local_path . $filename) as $list) {

            /**
             * Getting file content using file function
             * Convert the file content into array using (~)
             */
            $file_content = file($list);
            $file_full_content = explode('~', $file_content[0]);

            /**
             * Using file content to find separator
             */
            $symb_check = implode('', $file_full_content);
            $first_segment = $file_full_content[0];
            if (count(explode('|', $symb_check)) > 1) {
                $separate = "|";
            } elseif (count(explode('*', $symb_check)) > 1) {
                $separate = "*";
            }
            $spl_symb = explode($separate, $first_segment);
            $spl_separate = $spl_symb[16];

            /**
             * Separating the segment and getting data in the segment
             */
            foreach ($file_full_content as $key => $segment) {

                if (substr($segment, 0, 3) == 'ST' . $separate) {
                    $check_count++;
                    $basic_count = 0;
                    $claim_count = 0;
                    $claim_cpt_count = 0;
                }
                if (substr($segment, 0, 4) == 'TRN' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['check_details']['check_no'] = $temp[2];
                }
                if (substr($segment, 0, 4) == 'CLP' . $separate) {
                    $claim_count++;
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_id'] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_insurance_type'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_total_amount'] = $temp[3];
                    if (!empty($temp[4]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_paid_amount'] = $temp[4];
                    if (!empty($temp[5]))
                        $basic_info[$check_count]['claim'][$claim_count]['claim_coins_amount'] = $temp[5];
                    if (!empty($temp[7]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_icn'] = $temp[7];
                }
                if (substr($segment, 0, 4) == 'CAS' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['claims_adj'] = $temp[1];
                }
                if (substr($segment, 0, 7) == 'NM1' . $separate . 'QC' . $separate) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_lastname'] = $temp[3];
                    if (!empty($temp[4]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_firstname'] = $temp[4];
                    if (!empty($temp[5]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_Suffix'] = $temp[5];
                    if (!empty($temp[9]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_hic'] = $temp[9];
                }
                if (substr($segment, 0, 4) == 'MOA' . $separate) {
                    $temp = explode($separate, $segment);
                    $basic_info[$check_count]['claim'][$claim_count]['patient_moa'] = $temp[3];
                }
                if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['patient_statement_date'] = date('d/m/Y', strtotime($temp[2]));
                }
                if (substr($segment, 0, 4) == 'SVC' . $separate) {
                    $claim_cpt_count ++;
                    $temp = explode($separate, $segment);
                    $temp_proc = explode($spl_separate, $temp[1]);
                    if (!empty($temp_proc[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['proc'] = $temp_proc[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['billed_amount'] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['insurance_paid_amount'] = $temp[3];
                    if (!empty($temp[5]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['units'] = $temp[5];
                    elseif (!empty($temp[7]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['units'] = $temp[7];
                }
                if (substr($segment, 0, 4) == 'DTM' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['service_date'] = $temp[2];
                }
                if (substr($segment, 0, 4) == 'CAS' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[1]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['type_' . $temp[1]] = $temp[1];
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['type_coins_' . $temp[1]] = $temp[2];
                    if (!empty($temp[3]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['type_' . $temp[1]] = $temp[3];
                    if ($temp[1] == 'CO' || $temp[1] == 'OA' || $temp[1] == 'PI')
                        $glossary[] = $temp[2];
                }
                if (substr($segment, 0, 7) == 'REF' . $separate . 'LU' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['pos'] = $temp[2];
                }
                if (substr($segment, 0, 7) == 'AMT' . $separate . 'B6' . $separate && $claim_cpt_count != 0) {
                    $temp = explode($separate, $segment);
                    if (!empty($temp[2]))
                        $basic_info[$check_count]['claim'][$claim_count]['cpt_details'][$claim_cpt_count]['allowed'] = $temp[2];
                }
            }
        }

        return view('payments/payments/erashowpopup', compact('basic_info', 'cheque'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request) {
        $post_val = Request::all();
        $method = $request::method();
        if (empty($post_val) && Session::has('post_val')) {
            $post_val = Session::get('post_val');
            $payment_id = Helpers::getEncodeAndDecodeOfId($post_val['payment_detail_id'], 'decode');
            if (!empty($payment_id)) {
                $getval = PMTInfoV1::getPaymentDadetailData($payment_id);
                //$getval = $getval['attributes'];
                $post_val['payment_amt'] = $getval['balance'];
                $post_val['unapplied'] = !is_null($getval['balance']) ? $getval['balance'] : $post_val['unapplied'];
                $post_val['payment_amt_calc'] = !is_null($getval['balance']) ? $getval['balance'] : $post_val['unapplied'];
            }
        } elseif ($method == "GET" && !Session::has('post_val')) {
            return Redirect::to('/payments');
        }
        if (!empty($post_val))
            $api_response = $this->getCreateApi($post_val['claim_ids']);
        // dd($api_response);
        $api_response_data = $api_response->getData();
        $claims_lists = [];
        if ($api_response_data->status == 'success') {
            $claims_lists = $api_response_data->data->claim_lists;
            $total_list = $api_response_data->data->total;
            $claim_id_list = $api_response_data->data->claim_id_list;
        } elseif ($api_response_data->status == 'error') {
            return Redirect::to('payments')->with('error', $api_response_data->message);
        }
        $view = "payments/payments/patient/patient_payment_create";
        if (Request::ajax())
            $view = 'payments/payments/patient/apend_ajax_patientpayment';
        unset($post_val['filefield_eob']);
        $patID = Helpers::getEncodeAndDecodeOfId(@$post_val['patient_id'], 'decode');        
        $patient_alert_note = PatientNote::where('notes_type_id', $patID)->where('notes_type', 'patient')->where('status', 'Active')->where('patient_notes_type', 'alert_notes')->select("created_by", "content")->first();
        return view($view, compact('claims_lists', 'post_val', 'total_list', 'claim_id_list','patient_alert_note'));
    }

    public function create1($claim_id) {
        return view('patients/payments/edit01');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $request = $request::all();
		
        /* $validate = Validator::make($request,[
                                'patient_id'=>'required',
                                'payment_type'=>'required|alpha',
                                'claim_id'=>'required',
                                'payment_method'=>'required|alpha',
                                'payment_mode'=>'required|alpha',
                                'payment_amt'=>'required|numeric',
                                'check_no'=>'required|alpha_num',
                                'check_date'=>'required|date',
                                'tot_billed_amt'=>'required|numeric',
                                'tot_paid_amt'=>'required|numeric',
                                'tot_balance_amt'=>'required|numeric',
                                'unapplied_amt'=>'required|numeric',
                                'dos'=>'required',
                                'cpt'=>'required|max:6',
                                'cpt_billed_amt'=>'required|between:0,99.99',
                                'cpt_allowed_amt'=>'required|between:0,99.99',
                                'balance'=>'required|between:0,99.99',
                                'paid_amt'=>'required|between:0,99.99',
                                'ids'=>'required'
                                ]); */
        /* if($validate->fails()){
            return Redirect::to('/payments')->with('error', implode('<br>', (array_unique($validate->errors()->all()))));
        }else{ */
            $api_response = $this->getStoreApi($request);
			
            $api_response_data = $api_response->getData();
            $patient_id = $api_response_data->data;
            $payment_id = Helpers::getEncodeAndDecodeOfId(@$api_response_data->payment_id, 'encode');
            if ($api_response_data->status == 'success' && $request['next'] != 1) {
                return Redirect::to('payments')->with('success', $api_response_data->message);
            } elseif ($api_response_data->status == 'success' && $request['next'] == 1) {
                Session::put('post_val.payment_detail_id', $payment_id);
                return Redirect::to('/payments/create');
            } else {
                return Redirect::to('payments')->with('error', "The Check number number already exists");
            }
        //}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show() {
        return view('patients/billing/show');
    }

    public function getpopuppaymentdata($claim_id) {
        $api_response = $this->getApiPopuppaymentdata($claim_id);
        $api_response_data = $api_response->getData();
        $claim_detail = $api_response_data->data->claim_detail;
        $claim_transaction = $api_response_data->data->claim_list;
        return view('patients/payments/paymentpopup', compact('claim_detail', 'claim_transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $api_response = $this->getCreateApi();
        $api_response_data = $api_response->getData();
        $facilities = $api_response_data->data->facilities;
        $providers = $api_response_data->data->providers;
        $rendering_providers = $api_response_data->data->rendering_providers;
        $referring_providers = $api_response_data->data->referring_providers;
        $billing_providers = $api_response_data->data->billing_providers;
        $insurances = $api_response_data->data->insurances;
        $facility_id = '';
        $provider_id = '';
        $rendering_provider_id = '';
        $referring_provider_id = '';
        $billing_provider_id = '';
        $insurance_id = '';
        return view('patients/payments/edit', compact('facilities', 'facility_id', 'providers', 'rendering_providers', 'referring_providers', 'billing_providers', 'provider_id', 'rendering_provider_id', 'referring_provider_id', 'billing_provider_id', 'insurances', 'insurance_id'));
    }

    public function listPatientInsurance($patient_id) {
        $api_response = $this->listPatientInsuranceApi($patient_id);
        $api_response_data = $api_response->getData();
        $insurance_lists = $api_response_data->data;
        return view('payments/payments/patient_insurance_list', compact('insurance_lists'));
    }

    public function getPaymentcheckdata($payment_id) {
        $payment = new PaymentApiController();
        $api_response = $payment->getPaymentcheckdataApi($payment_id);
        $api_response_data = $api_response->getData();
        $payment_details = $api_response_data->data->payment_detail;
        return view('payments/payments/patient/patient_payment_claim_detail', compact('payment_details'));
    }

    public function voidPaymentcheckdata($payment_id) {
        $payment_id = Helpers::getEncodeAndDecodeOfId($payment_id, 'decode');
        $paymentData = PMTInfoV1::where('id', $payment_id)->first();
        if($paymentData['pmt_method'] == 'Patient' && $paymentData['pmt_type'] == 'Refund')
            $api_response = $this->voidRefundPaymentcheckdataApi($payment_id);
        else
            $api_response = $this->voidPaymentcheckdataApi($payment_id);
        if($api_response){
            $api_response_data = $api_response->getData();
            return $api_response_data->data;
        } else {
            return '';
        }
    }

}
