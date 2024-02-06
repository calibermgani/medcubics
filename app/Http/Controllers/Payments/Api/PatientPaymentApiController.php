<?php

namespace App\Http\Controllers\Payments\Api;

use App\Http\Controllers\Api\CommonExportApiController;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payments\Api\PaymentApiController as PaymentApiController;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Document;
use App\Models\Insurance as Insurance;
use App\Models\Patients\Patient as Patient;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimCPTTXV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTWalletV1;
use App\Models\Provider as Provider;
use App\Traits\ClaimUtil;
use App\Traits\CommonUtil;
use App\Http\Controllers\Claims\ClaimControllerV1 as ClaimControllerV1;
use Auth;
use Config;
use DB;
use Input;
use Lang;
use Request;
use Response;
use Session;

// should include to make claim related Traits
class PatientPaymentApiController extends Controller
{

    static $amt;
    static $cpt_paid_amt;
    static $claim_ids;
    static $remaining;
    static $wallet_refunded;
    static $balance_avail_amt = '';

    use ClaimUtil; // should include to make claim related Traits

    // /static $remaning_amt;
    public function getIndexApi($patient_id, $tab = null, $claim_id = null, $export = '')
    {
		
        // For eob attachment session data delete value starts here
        if (Session::has('eob_attachment')) {
            Session::forget('eob_attachment');
        }
        if ($claim_id == 'export')
            $claim_id = '';
        // For eob attachment session data delete value ends here       
        if ($patient_id != '')
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');
        $claims_list = [];
        $billing_providers = [];
        $claim_id = Helpers::getEncodeAndDecodeOfId(@$claim_id, 'decode');
        // dd($patient_id, $tab=null, $claim_id = null,$export='');
        if (Patient::where('id', $patient_id)->count()) {
            if (ClaimInfoV1::where('patient_id', $patient_id)->count() > 0) {
                if ($tab == "insurance") {
                    $claims_list = ClaimInfoV1::with('rendering_provider', 'refering_provider', 'facility_detail', 'insurance_details', 'billing_provider')
                        ->where('patient_id', $patient_id)->whereNotIn('status', ['E-bill'])->orderBy('date_of_service', 'DESC');
                } else {
                    $claims_list = ClaimInfoV1::
                    with('rendering_provider', 'refering_provider', 'facility_detail', 'insurance_details', 'billing_provider')
                        ->where('patient_id', $patient_id)
                        ->whereNotIn('status', ['E-bill'])
                        ->orderBy('date_of_service', 'DESC');
                }
                if (!empty($claim_id)) {
                    $claims_list = $claims_list->where('id', $claim_id)->get();
                } else {
                    $claims_list = $claims_list->get();
                }
                foreach ($claims_list as $claim) {
                    $claim_id = $claim->id;
                    $total_charge = $claim->total_charge;
                    $paymentV1ApiController = new PaymentV1ApiController();
                    $resultData = $paymentV1ApiController->getClaimsFinDetails($claim_id, $total_charge);
                    unset($resultData['id']); //no need
                    $claim['total_paid'] = $resultData['total_paid'];
                    $claim['totalAdjustment'] = $resultData['totalAdjustment'];
                    $claim['withheld'] = $resultData['withheld'];
                    $claim['patient_paid'] = $resultData['patient_paid'];
                    $claim['patient_due'] = $resultData['patient_due'];
                    $claim['balance_amt'] = $resultData['patient_due'];
                    $claim['insurance_due'] = $resultData['insurance_due'];
                    $claim['balance_amt'] = $resultData['balance_amt'];
                }
                $billing_providers = Provider::getBillingAndRenderingProvider('no', [Config::get('siteconfigs.providertype.Billing')]);
                $this->getPatientInsuranceApi($patient_id);
                if ($export != "") {
                    $exportparam = array(
                        'filename' => 'payment',
                        'heading' => 'Patient Payments',
                        'fields' => array(
                            'date_of_service' => 'DOS',
                            'claim_number' => 'Claim No',
                            'rendering_provider_id' => array(
                                'table' => 'rendering_provider', 'column' => 'short_name', 'label' => 'Rendering'
                            ),
                            'billing_provider_id' => array(
                                'table' => 'billing_provider', 'column' => 'short_name', 'label' => 'Billing'
                            ),
                            'facility_id' => array(
                                'table' => 'facility_detail', 'column' => 'short_name', 'label' => 'Facility'
                            ),
                            'Insurance Name' => array(
                                'table' => '', 'column' => 'insurance_id', 'use_function' => ['App\Models\Payments\ClaimInfoV1', 'GetInsuranceName'], 'label' => 'Billed To'),
                            'total_charge' => 'Billed',
                            'total_paid' => 'Paid',
                            'total_adjusted' => 'Adjustment',
                            'patient_due' => 'Pat Bal',
                            'insurance_due' => 'Ins Bal',
                            'balance_amt' => 'AR Bal',
                            'status' => 'Status',
                        )
                    );
                    $callexport = new CommonExportApiController();
                    return $callexport->generatemultipleExports($exportparam, $claims_list, $export);
                }
            }
            // dd($claims_list);
			$ClaimController  = new ClaimControllerV1("payment");   
			$search_fields_data = $ClaimController->generateSearchPageLoad('patients_payment_listing');
			 $searchUserData = $search_fields_data['searchUserData'];
		     $search_fields = $search_fields_data['search_fields'];
            return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claims_list', 'billing_providers', 'search_fields', 'searchUserData')));
        } else {
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.empty_record_msg"), 'data' => ''));
        }
    }

    public function getPaymentpopupApi($payment_detail_id = null)
    {
        $claims_lists = $payment_details = [];
        if (!empty($payment_detail_id)) {
            $payment_detail_id = Helpers::getEncodeAndDecodeOfId(@$payment_detail_id, 'decode');
            $payment_detailS = PMTInfoV1::with(['payment_claim_detail' => function ($q) {
                $q->groupBy('claim_id');
            }, 'insurancedetail', 'attachment_detail'])
                ->whereHas('payment_claim_detail', function ($q) use ($payment_detail_id) {
                    $q->where('payment_id', $payment_detail_id);
                })->first();
            $payment_detail = PMTInfoV1::getPaymentInfoDetailsById($payment_detail_id);
            $payment_detail->payment_claim_detail = (object)$payment_detailS['payment_claim_detail'];
            $payment_detail->insurancedetail = (object)$payment_detailS['insurancedetail'];
            $payment_details = $payment_detail;
            if (!empty($payment_details) && $payment_details->pmt_method == "Patient") {
                $patient_id = $payment_details->patient_id;
                $claims_lists = ClaimInfoV1::
                with('rendering_provider', 'refering_provider', 'facility_detail', 'insurance_details', 'billing_provider')
                    ->where('patient_id', $patient_id)
                    //->whereNotIn('status', ['E-bill', 'Hold'])
                    ->orderBy('id', 'DESC')->get();
                foreach ($claims_lists as $claim) {
                    $claim_id = $claim->id;
                    $total_charge = $claim->total_charge;
                    $paymentV1ApiController = new PaymentV1ApiController();
                    $resultData = $paymentV1ApiController->getClaimsFinDetails($claim_id, $total_charge);
                    unset($resultData['id']); //no need
                    $claim['total_paid'] = $resultData['total_paid'];
                    $claim['totalAdjustment'] = $resultData['totalAdjustment'];
                    $claim['withheld'] = $resultData['withheld'];
                    $claim['patient_paid'] = $resultData['patient_paid'];
                    $claim['patient_due'] = $resultData['patient_due'];
                    $claim['balance_amt'] = $resultData['patient_due'];
                    $claim['insurance_due'] = $resultData['insurance_due'];
                    $claim['balance_amt'] = $resultData['balance_amt'];
                }
            }
        }        
        $insurance_list = Insurance::where('status', 'Active')->pluck('insurance_name', 'id')->all();
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('insurance_list', 'payment_details', 'claims_lists')));
    }

    // Create payment starts here
    public function getCreateApi($claim_id = null)
    {
        $request = Request::all();
        $claim_id_list = [];
        $file = Request::file('filefield_eob');
        if (isset($request['payment_mode']) && $request['payment_mode'] == 'Check' && ($request['payment_type'] == "Payment" || $request['payment_type'] == "Refund") && !isset($request['takeback']) && empty($request['payment_detail_id'])) { //For adjustmnet it was empty	
            $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            $check_exists = PMTInfoV1::findCheckExistsOrNot($request['check_no'], "Patient", "Check", "patientPayment", $patient_id);
            if ($check_exists) {
                return Response::json(array('status' => 'error', 'message' => "Check number alredy exist", 'data' => $request['patient_id']));
            }
        }
        if ($claim_id != '') {
            $claim_ids = explode(',', $claim_id)[0];
            $paymentV1ApiController = new PaymentV1ApiController();
            $responseData = $paymentV1ApiController->createPayment('', $claim_ids);
            $response_Data = $responseData->getData();
            $claim_lists = $response_Data->data->claim_lists;
            $total = $response_Data->data->total;
        }

        // To get EOB attachment
        if (!empty($request['temp_type_id'])) {
            Session::put('eob_attachment', $request['temp_type_id']);
        }
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_lists', 'total', 'claim_id_list')));
    }

    // Create payment ends here
    // Store payments here starts
    public function getStoreApi($request = '')
    {
        $paymentV1Controller = new PaymentV1ApiController();
        $response = $paymentV1Controller->patientPaymentProcessHandler($request);
        // dd($response);
        $responseData = $response->getData();
        $patientId = $responseData->data;
        if ($responseData->status == 'success') {
            $pmtId = $responseData->payment_id;
            $patientId = Helpers::getEncodeAndDecodeOfId($patientId, 'encode');
            return Response::json(array('status' => 'success', 'message' => $responseData->message." - Claim No. : ".last($request['claim_number']), 'data' => $patientId, 'payment_id' => $pmtId));
        } else {
            return Response::json(array('status' => 'error', 'message' => $responseData->message, 'data' => $patientId, 'payment_id' => ''));
        }
    }

    // Store payments here ends
    // Save data to line items that we have choosen to each claims starts here
    public function saveClaimdata($data, $payment_id)
    {
        $claim_ids = '';
        $save_data = [];
        //dd($data)		;
        if (!empty($data['patient_paid'])) {
            $claim_id = Helpers::getEncodeAndDecodeOfId($data['claim_id'], 'decode');
            $data_need['claim_id'] = $claim_id;
            $calculted_data = $this->getPatientPaidCalculation($data);
            $patient_balance = $calculted_data['patient_balance'];
            $data_need['balance'] = $calculted_data['balance_amt'];
            $data_need['patient_paid'] = $calculted_data['patient_paid_amt'];
            $save_data['payment_id'] = $payment_id;
            $save_data['claim_id'] = $claim_id;
            $save_data['patient_id'] = $data['patient_id'];
            $save_data['paid_amt'] = isset($data['original_paid']) ? $data['original_paid'] : $calculted_data['paid_amt']; // from copay original amount added
            if ($data['payment_type'] == "Adjustment") { // To save adjustment paid as 0  we change the patient paid as 0
                $save_data['total_adjusted'] = $calculted_data['patient_paid_amt'];
                $save_data['patient_paid_amt'] = 0;
                $save_data['paid_amt'] = 0;
            } else {
                $save_data['patient_paid_amt'] = $calculted_data['patient_paid_amt'];
            }
            $save_data['balance_amt'] = $calculted_data['balance_amt'];
            $save_data['patient_due'] = $calculted_data['patient_due'];
            $save_data['insurance_due'] = $calculted_data['insurance_due'];
            // to pass data not to save
            $save_data['posting_type'] = $data['payment_type'];
            $save_data['payment_type'] = "Patient";
            //$save_data['status'] = $calculted_data['status'];
            $save_data['insurance_id'] = $calculted_data['insurance_id'];
            //$save_data['insurance_id'] = 0; // Changed statically due to requirement change;
            $save_data['reference'] = $data['reference'];
            $save_data['created_by'] = Auth::user()->id;
            //$save_data['description'] = "Payment has been done";	
            //dd($save_data['patient_paid_amt']);
            //dd($calculted_data);	
            //dd($save_data)		;
            if ($save_data['patient_paid_amt'] == 0 && $data['payment_type'] == "Payment") {
                // @todo - check and replace new pmt flow
                // PaymentClaimDetail::create($save_data);
                //$this->moveExcessamountToWallet($payment_id, $save_data['patient_id'], $claim_id,$data['payment_amt'], $save_data['insurance_id'], $save_data['insurance_due'], $save_data['patient_due']);
            } else {
                // @todo - check and replace new pmt flow
                // $result = PaymentClaimDetail::create($save_data);
                $save_data['insurance_id'] = $calculted_data['insurance_id'];
                $save_data['patient_paid_amt'] = $calculted_data['patient_paid_amt']; // In other places we subtract the equivelance amount fromm balance so we take it.
                //$patient_bal_val = array_sum($data['patient_balance']);
                $patient_bal_val = $save_data['balance_amt'];
                $insurance_due = (!empty($data['insurance_due'])) ? array_sum($data['insurance_due']) : 0;
                $total_balance = ($insurance_due < 0) ? $patient_bal_val - $insurance_due : $patient_bal_val;
                //dd($total_balance);				
                if ($patient_balance < 0 && $data['payment_type'] == "Payment" || $calculted_data['patient_due_arr_val'] < 0) {
                    //dd("sadsadsa");
                    //$move_amt = (!empty($calculted_data['patient_due_arr_val']) && $calculted_data['patient_due_arr_val']<0)?$calculted_data['patient_due_arr_val']:abs($patient_balance);	
                    //dd($move_amt);
                    $move_amt = abs($calculted_data['patient_due_arr_val']);
                    if ($move_amt > 0) {
                        $this->moveExcessamountToWallet($payment_id, $save_data['patient_id'], $claim_id, $move_amt, $save_data['insurance_id'], $save_data['insurance_due'], $save_data['patient_due']);
                    }
                }
                //dd("sadsafsdfdsdsa");
                $data_value = $this->saveClaimCptdata($save_data, $result->id, $data);  // Save CPT wise amount paid
                $this->updatewalletinfo($save_data['patient_paid_amt'], $payment_id, $data['payment_type']); //update wallet with the final used amount				
                if ($data['payment_type'] == "Adjustment" && !empty($data_value)) { // To save adjustment paid as 0  we change the patient paid as 0
                    // @todo check and replace new pmt flow
                    // PaymentClaimDetail::where('id', $result->id)->update(['patient_due' => $data_value['patient_balance'], 'insurance_due' => $data_value['insurance_balance']]);
                    ClaimInfoV1::where("id", $claim_id)->update(['patient_due' => $data_value['patient_balance'], 'insurance_due' => $data_value['insurance_balance']]);
                }
            }
        }
    }

    public function getPatientPaidCalculation($calculation_data)
    {
        $patient_due_array = [];
        $claim_id = Helpers::getEncodeAndDecodeOfId($calculation_data['claim_id'], 'decode');
        $claim_data = ClaimInfoV1::where('id', $claim_id)->select('insurance_id', 'self_pay', 'insurance_due', 'status', 'patient_due')->first();
        $paid_array = $calculation_data['patient_paid'];
        $balance_array = $calculation_data['balance'];

        $patient_due_array = @$calculation_data['patient_due'];
        $original_paid_amt = array_sum($paid_array);
        if ($calculation_data['payment_type'] != 'Adjustment') {
            /*             * Checking conditions with each line item of patient balance and balance and * */
            $amount_paid = array_map(function ($paid_array, $balance_array, $patient_due_array) {
                if ($patient_due_array == 0 && $balance_array > 0) {
                    return ($paid_array > $balance_array) ? $balance_array : $paid_array;
                } else {
                    return ($paid_array > $patient_due_array) ? $patient_due_array : $paid_array;
                }
            }, $paid_array, $balance_array, $patient_due_array); // Check with balance if paid grater take the balance as paid
            $paid_amt = array_sum($amount_paid);
        } else {
            $paid_amt = array_sum($paid_array);
        }

        $sum_val = 0;
        $sum_val = array_sum(array_map(function ($value, $paid) {
            return ($value < 0 && $paid > 0) ? $value : 0;   // Some times insurance balances in negative then can't able to make payment
        }, $calculation_data['patient_balance'], $paid_array));
        $pat_bal = array_sum($calculation_data['patient_balance']);

        if ($sum_val <= 0 && $pat_bal > 0 || $pat_bal < 0) {
            $pat_bal = $sum_val;                // One line item excesss paid and one line item not paid issues fixed
        }
        $patient_due = $claim_data->patient_due;
        $insurance_due = isset($calculation_data['insurance_due']) ? array_sum($calculation_data['insurance_due']) : 0;
        //dd($calculation_data)	;
        if ($claim_data->self_pay == "Yes" && $claim_data->insurance_id == 0) {
            $balance_amt = array_sum($calculation_data['patient_due']);
        } else {
            $balance_arr_val = array_map(function ($value) {
                return ($value > 0) ? $value : 0;
            }, $calculation_data['balance']);
            //$balance_amt  = array_sum($calculation_data['balance']); // When  balance was in negative but paid the line item balance it makes reduces the paid amount
            $balance_amt = array_sum($balance_arr_val);
        }

        $balance_amt = ($balance_amt <= 0 && $patient_due > 0) ? $patient_due : $balance_amt;

        $patient_balance = ($balance_amt > 0 || $paid_amt < 0) ? $balance_amt - $paid_amt : (($calculation_data['payment_type'] != "Adjustment") ? 0 - $paid_amt : 0); // when insurance payment doen as excess and we do patient payment the excess also added insot patient wallet   
        //dd($patient_balance);
        $patient_bal = ($patient_balance > 0) ? $patient_balance : 0;
        $patient_paid = (($patient_balance > 0) ? $paid_amt : ($balance_amt > 0 ? $balance_amt : 0));
        //$patient_paid = $paid_amt;			
        //$return_data['status'] = $claim_data->status;		
        $return_data['paid_amt'] = $original_paid_amt;
        //dd($calculation_data);
        if ($calculation_data['payment_type'] == 'Adjustment' || $calculation_data['payment_type'] == 'Refund') {
            $adjustment_val_remaining = array_map(function ($balance, $pat_paid, $pat_bal) {
                $pat_bal_return['pat_bal'] = ($pat_bal > 0) ? $pat_bal : 0;
                $pat_bal_return['ins_bal'] = ($pat_bal < 0) ? $pat_bal : 0;
                return $pat_bal_return;
                // Some times insurance balances in negative then can't able to make payment
            }, $calculation_data['balance'], $calculation_data['patient_paid'], $calculation_data['patient_balance']);
            $patient_bal = $patient_balance = array_sum(array_column($adjustment_val_remaining, 'pat_bal'));
            $insurance_due = array_sum(array_column($adjustment_val_remaining, 'ins_bal'));
        }
        if ($claim_data->self_pay == "Yes" && $claim_data->insurance_id == 0) {
            $return_data['patient_due'] = $patient_bal;
            //$return_data['insurance_due'] =  ($claim_data->insurance_due <0)?$claim_data->insurance_due:0;
            $return_data['insurance_due'] = ($insurance_due < 0) ? $insurance_due : 0;
            $return_data['balance_amt'] = $patient_bal;
            //$return_data['status'] = ($patient_bal>0)?"Patient":"Paid";	
        } else {
            $return_data['patient_due'] = (array_sum($balance_array) <= 0 && $patient_due > 0) ? $patient_bal : 0;
            //if()			//$return_data['insurance_due'] = ($claim_data->insurance_due == 0 && $patient_bal>0)?$patient_bal:$claim_data->insurance_due;
            $return_data['insurance_due'] = ($insurance_due == 0 && $patient_bal > 0) ? $patient_bal : $insurance_due;
            $return_data['balance_amt'] = $patient_bal;
            //dd($patient_bal);
            //$return_data['status'] = ($return_data['insurance_due']>0)?$claim_data->status:"Paid";	
        }
        //dd($return_data);
        $return_data['insurance_id'] = $claim_data->insurance_id;
        $return_data['patient_paid_amt'] = ($calculation_data['payment_type'] == "Adjustment") ? array_sum($paid_array) : $patient_paid;
        //$return_data['insurance_due'] = ($claim_data->insurance_due <=0 && $calculation_data['payment_type'] == "Adjustment" && $claim_data->self_pay != "Yes")?$claim_data->insurance_due-$return_data['patient_paid_amt']:$claim_data->insurance_due;
        //$return_data['insurance_due'] = ($claim_data->insurance_due <=0 && $calculation_data['payment_type'] == "Adjustment"  && $return_data['patient_paid_amt'] <0)?$insurance_due-$return_data['patient_paid_amt']:$insurance_due;
        $return_data['insurance_due'] = ($claim_data->insurance_due <= 0 && $calculation_data['payment_type'] == "Adjustment" && $return_data['patient_paid_amt'] < 0) ? $insurance_due : $insurance_due;
        //dd($return_data['patient_paid_amt']);
        $return_data['patient_balance'] = $patient_balance;
        $return_data['patient_due_arr_val'] = $pat_bal;
        //dd($return_data);
        return $return_data;
    }

    // Save data to line items that we have choosen to each claims ends here
    // Save the cpt amount for each claims if it was patient payment save the amount to first cpt starts here
    public function saveClaimCptdata($save_data, $payment_claim_detail_id, $data)
    {
        $cpt_paid_amt = $save_data['patient_paid_amt'];
        $payment_id = $save_data['payment_id'];
        $patient_id = $save_data['patient_id'];
        $i = 0;

        for ($i = 0; $i < count($data['cpt']); $i++) {
            $get_cpt_details = ClaimCPTInfoV1::where('id', $data['ids'][$i])->first();
            $patient_due = $data['patient_due'][$i];
            $patient_bal = $data['patient_balance'][$i];
            $balance = $data['balance'][$i];
            $patient_paid_amt = $data['patient_paid'][$i];
            // dd($patient_paid_amt);

            if ($patient_due == 0 && $balance > 0 || $data['payment_type'] == "Refund") {
                $patient_paid = ($patient_bal < 0) ? $balance : $patient_paid_amt;
            } else {
                $patient_paid = ($patient_bal < 0 && $patient_due == 0 && $data['payment_type'] != "Adjustment") ? $patient_due : (($patient_due < $patient_paid_amt) ? $patient_due : $patient_paid_amt); // when doing padjsutment , insurance overpayment and adjsutment takeback it makes the paid amount as 0
            }
            $balance_amt = ($patient_bal < 0 && $patient_due == 0) ? 0 : (($data['payment_type'] == "Refund") ? $balance + $patient_paid_amt : (($patient_due > 0 && $balance == 0) ? $patient_due - $patient_paid_amt : $balance - $patient_paid_amt));
            //dd($patient_paid);
            // $balance_amt  = ($balance_amt <= 0 && $patient_due>0)?$patient_due:$balance_amt;
            $return_data = [];
            if ($cpt_paid_amt != 0) {
                $dos_spt_details['payment_id'] = $payment_id;
                $dos_spt_details['claim_id'] = $save_data['claim_id'];
                $dos_spt_details['patient_id'] = $save_data['patient_id'];
                $dos_spt_details['balance_amt'] = $balance_amt;
                $dos_spt_details['paid_amt'] = $patient_paid_amt;
                $dos_spt_details['patient_paid'] = $patient_paid;
                $dos_spt_details['patient_balance'] = ($save_data['insurance_id'] != 0 && !empty($save_data['insurance_id'])) ? 0 : ($balance_amt < 0 ? 0 : $balance_amt);
                //dd($get_cpt_details->insurance_balance);
                //$dos_spt_details['insurance_balance'] = ($get_cpt_details->insurance_balance <=0 && $data['payment_type'] == "Adjustment" && $save_data['insurance_id'] != 0)?$get_cpt_details->insurance_balance-$patient_paid:$get_cpt_details->insurance_balance;	
                $dos_spt_details['insurance_balance'] = ($get_cpt_details->insurance_balance <= 0 && $data['payment_type'] == "Adjustment" && $patient_paid < 0) ? $get_cpt_details->insurance_balance - $patient_paid : $get_cpt_details->insurance_balance;
                $dos_spt_details['posting_type'] = "Patient";
                $dos_spt_details['payment_claim_detail_id'] = $payment_claim_detail_id;
                $dos_spt_details['claimdoscptdetail_id'] = $data['ids'][$i];
                $dos_spt_details['insurance_id'] = $save_data['insurance_id'];
                if ($save_data['posting_type'] == "Adjustment") {
                    $dos_spt_details['paid_amt'] = 0;
                    $dos_spt_details['adjustment'] = $patient_paid;
                    $dos_spt_details['patient_paid'] = 0;
                    $dos_spt_details['insurance_balance'] = ($save_data['insurance_id'] == 0 && $dos_spt_details['insurance_balance'] > 0) ? 0 : $dos_spt_details['insurance_balance'];
                } elseif ($save_data['posting_type'] == "Refund") {
                    $dos_spt_details['paid_amt'] = -1 * $patient_paid;
                    $dos_spt_details['patient_paid'] = -1 * $patient_paid;
                    $dos_spt_details['insurance_id'] = 0;
                }
                // @todo - check and replace new pmt flow
                //  $result = PaymentClaimCtpDetail::create($dos_spt_details);
                $dos_spt_details['paid_amt'] = $patient_paid;
                $patient_bal = $data['patient_balance'][$i];
                //dd($data['patient_balance'][$i]);
                //dd($dos_spt_details);
                $return_data = $this->getpatientInsuranceBalanceByCPT($save_data['claim_id']);
                if ($patient_bal < 0 && $patient_paid_amt > 0)
                    $this->moveExcessamountToCptWallet($dos_spt_details, $patient_bal);
                $this->updateClaimCptdata($dos_spt_details, $save_data['posting_type']);
                $this->saveTransactionhistory($dos_spt_details);
            }
        }
        //exit;	
        $this->updateclaims($save_data);
        return $return_data;
    }

    // Save the cpt amount for each claims if it was patient payment save the amount to first cpt ends here
    public function getpatientInsuranceBalanceByCPT($claim_id)
    {
        // @todo - check and replace new pmt flow
        /*
        $dos_cpt_details = Claimdoscptdetail::where('claim_id', $claim_id)->whereNotIn('cpt_code', ['Patient'])->pluck('id')->all();
        $detail = [];
        $data = [];
        foreach ($dos_cpt_details as $dos_cpt_detail) {
            $payment_claim_details = PaymentClaimCtpDetail::where('claimdoscptdetail_id', $dos_cpt_detail)->orderBy('id', 'desc')->select('id', 'patient_balance', 'insurance_balance')->first();
            $detail[$dos_cpt_detail]['patient_balance'] = $payment_claim_details->patient_balance;
            $detail[$dos_cpt_detail]['insurance_balance'] = $payment_claim_details->insurance_balance;
        }
        $data['patient_balance'] = array_sum(array_column($detail, 'patient_balance'));
        $data['insurance_balance'] = array_sum(array_column($detail, 'insurance_balance'));
        */
        $data['patient_balance'] = $data['insurance_balance'] = 0;
        return $data;
    }

    // Update or insert doscpt details table to get the patient paid amount for each claims starts here
    public function updateClaimCptdata($save_data, $type = null)
    {
        // @todo check and replace new pmt flow
        /*
        $get_cpt_details = Claimdoscptdetail::where('claim_id', $save_data['claim_id'])->where('cpt_code', 'Patient')->pluck('id');
        $get_cpt_detail_data = Claimdoscptdetail::where('id', $save_data['claimdoscptdetail_id'])->first();
        //dd($get_cpt_detail_data);
        $paid_amt = ($type == "Adjustment") ? $save_data['adjustment'] : $save_data['paid_amt'];
        $balance_amt = $save_data['patient_balance'];
        if ($type == "Adjustment") {
            $dos_spt_details['patient_adjusted'] = DB::raw("patient_adjusted +" . $paid_amt);
            $dos_spt_details['adjustment'] = DB::raw("adjustment +" . $paid_amt);
            $dos_spt_details['insurance_balance'] = $save_data['insurance_balance'];
            $dos_spt_details['patient_balance'] = (@$save_data['insurance_id'] == 0 || @$save_data['insurance_id'] == '' ) ? $save_data['balance_amt'] : 0;
        } else {
            $dos_spt_details['paid_amt'] = (!is_null($type) && $type == "Payment") ? DB::raw("paid_amt +" . $paid_amt) : DB::raw("paid_amt -" . $paid_amt);
            $dos_spt_details['patient_paid'] = (!is_null($type) && $type == "Payment") ? DB::raw("patient_paid +" . $paid_amt) : DB::raw("patient_paid -" . $paid_amt);
            $dos_spt_details['patient_balance'] = $balance_amt;
            //This is to save all the patient paid amount
            if (!empty($get_cpt_details)) {
                Claimdoscptdetail::where('id', $get_cpt_details)->update(['patient_paid' => DB::raw('patient_paid +' . $save_data['paid_amt'])]);
            }
            if (!$get_cpt_details) {
                //Insert a new row for patient paid amount
                $dos_detail['patient_id'] = $save_data['patient_id'];
                $dos_detail['claim_id'] = $save_data['claim_id'];
                $dos_detail['cpt_code'] = "Patient";
                $dos_detail['patient_paid'] = $paid_amt;
                Claimdoscptdetail::insert($dos_detail);
            }
        }
        $dos_spt_details['balance'] = (!is_null($type) && $type == "Refund") ? DB::raw("balance +" . $paid_amt) : DB::raw("balance -" . $paid_amt);
        //dd($dos_spt_details);
        //echo "<pre>"; print_r($dos_spt_details); exit;
        //exit;
        //dd($dos_spt_details);
        $result = Claimdoscptdetail::where('id', $save_data['claimdoscptdetail_id'])->update($dos_spt_details);
        */
    }

    //Refund process function starts here
    public function refundProcess($data, $payment_id)
    {
        $claim_ids = '';
        $wallet_refunded = 0;
        $patient_paid = array_sum($data['patient_paid']);
        $balance = array_sum($data['balance']);
        //$balance_amt = array_sum($data['patient_balance']);		
        $balance_amt = (($balance < 0) ? 0 : $balance) + $patient_paid;
        //dd($data);		
        if (!empty($data['patient_paid'])) {
            //dd($data);
            $save_data['claim_id'] = Helpers::getEncodeAndDecodeOfId($data['claim_id'], 'decode');
            //$original_paid = -1*$data['patient_paid'][$i];						
            $claim_data = ClaimInfoV1::where('id', $save_data['claim_id'])->select('insurance_id', 'self_pay', 'insurance_due', 'status')->first();
            $save_data['payment_id'] = $payment_id;
            $save_data['patient_id'] = $data['patient_id'];
            $save_data['posting_type'] = $data['payment_type'];
            $save_data['patient_paid_amt'] = -1 * $patient_paid;
            $save_data['paid_amt'] = -1 * $patient_paid;
            $save_data['balance_amt'] = $balance_amt;   // Balance at refund process
            $save_data['status'] = "Patient";
            //$save_data['insurance_id'] = $claim_data['insurance_id'];	
            $save_data['insurance_id'] = 0;
            // If insurance was availbale for the claim mode balance amount to insurance balance else move to patient balance
            if ($claim_data->self_pay == "Yes" && $claim_data->insurance_id == 0) {
                $save_data['patient_due'] = $save_data['balance_amt'];
                $save_data['insurance_due'] = ($claim_data->insurance_due < 0) ? $claim_data->insurance_due : 0;
                $save_data['status'] = ($save_data['balance_amt'] > 0) ? "Patient" : "Paid";
            } else {
                $save_data['insurance_due'] = @$claim_data->insurance_due;
                $save_data['patient_due'] = 0;
                $save_data['status'] = ($save_data['insurance_due'] > 0) ? $claim_data->status : $save_data['status'];
            }
            $save_data['description'] = "Amount Refunded to pateint";
            $save_data['created_by'] = Auth::user()->id;
            $save_data['payment_type'] = "Payment";
            $save_data['reference'] = $data['reference'];
            //dd($save_data);
            //@todo check and replace new pmt flow
            // $result = PaymentClaimDetail::create($save_data);
            $save_data['insurance_id'] = $claim_data->insurance_id;
            $save_data['patient_paid_amt'] = abs($save_data['patient_paid_amt']);
            // Do both process of wallet refund as well as claim refund  starts here	

            $wallet_refund_amt = 0;
            if (!empty($data['wallet_refund']))
                $wallet_refund_amt = $data['wallet_refund'];
            $this->updatewalletinfo($patient_paid, $payment_id, "Refund", $wallet_refund_amt, $save_data['patient_id']);
            // Do both process of wallet refund as well as claim refund  ends heres			
            $data_value = $this->saveClaimCptdata($save_data, $result->id, $data);
            if (!empty($data_value)) {
                // @todo check and replace new pmt flow
                //PaymentClaimDetail::where('id', $result->id)->update(['patient_due' => $data_value['patient_balance'], 'insurance_due' => $data_value['insurance_balance']]);
                ClaimInfoV1::where("id", $save_data['claim_id'])->update(['patient_due' => $data_value['patient_balance'], 'insurance_due' => $data_value['insurance_balance']]);
            }
        }
    }

    //Refund process function ends here
    // Credit balance concept starts here
    /* public function saveCreditBalanceClaim($data)
      {
      $wallet_id_s = '';
      for($i=0;$i<count($data['cpt']);$i++)
      {
      $claim_ids ='';
      $wallet_ids = $this->getcreditAmount($data['patient_id']);	// Get the wallet balance from here
      $remaining = '';
      foreach($wallet_ids as $key => $value){
      $wallet_id = $key;
      $save_data['claim_id'] = Helpers::getEncodeAndDecodeOfId($data['claim_id'],'decode');
      $claim_data = Claims::where('id', $save_data['claim_id'])->select('insurance_id', 'self_pay', 'insurance_due', 'status')->first();
      //$save_data['payment_id'] = $payment_id;
      $save_data['posting_type'] = $data['payment_type'];
      $save_data['status'] = "Paid";
      if($remaining > 0 && !empty($remaining))
      {
      $patient_paid = $remaining;
      $cpt_billed = $remaining;
      }else{
      $patient_paid = $data['patient_paid'][$i];

      $cpt_billed = $data['balance'][$i];
      }
      if($patient_paid > $cpt_billed && $value > $patient_paid)
      {
      $save_data['patient_paid_amt'] = $cpt_billed;
      $save_data['balance_amt'] = 0;
      } elseif($value < $patient_paid)
      {
      $save_data['patient_paid_amt'] = $value;
      $save_data['balance_amt'] = $cpt_billed - $value;
      $remaining = abs($value - $patient_paid);      // Find the remaning balance for claims to pay by another payment wallet
      }else
      {
      $save_data['patient_paid_amt'] = $patient_paid;
      $save_data['balance_amt'] =$cpt_billed - $patient_paid;
      }
      if($claim_data->self_pay == "Yes" && $claim_data->insurance_id == 0) {
      $save_data['patient_due'] = $save_data['balance_amt'];
      $save_data['insurance_due'] = 0; // Balance has been nill out but the due remains as in insurance
      $save_data['status'] = ($save_data['balance_amt']>0)?"Patient":"Paid";
      } else{
      $save_data['insurance_due'] = $claim_data->insurance_due;// eventhough the patient pays amount the insurance balance remains same
      $save_data['patient_due'] = 0;
      $save_data['status'] = $claim_data->status;
      }
      $save_data['reference'] = $data['reference'];
      $save_data['payment_id'] = $key;
      $save_data['created_by'] =  Auth::user()->id;
      $claim_ids.= $save_data['claim_id'].',';
      $save_data['patient_id'] = $data['patient_id'];

      $result = PaymentClaimDetail::create($save_data);
      $this->updatewalletinfo($save_data['patient_paid_amt'], $wallet_id, $save_data['posting_type']);
      $this->saveClaimCptdata($save_data, $result->id, $wallet_id);
      if($value > $save_data['patient_paid_amt']){
      break;
      }

      }
      //$i++;
      }

      } */
    public function saveCreditBalanceClaim($data)
    {
        $wallet_id_s = '';
        $claim_ids = '';
        $wallet_ids = $this->getcreditAmount($data['patient_id']); // Get the wallet balance from here	
        $remaining = '';
        $save_data['claim_id'] = Helpers::getEncodeAndDecodeOfId($data['claim_id'], 'decode');
        $claim_data = ClaimInfoV1::where('id', $save_data['claim_id'])->select('insurance_id', 'self_pay', 'insurance_due', 'status')->first();
        $patient_paid = array_sum($data['patient_paid']);
        $balance_amt = array_sum($data['balance']);

        $nopaidbalance = $balance_amt - $patient_paid;

        $patient_originally_paid = $patient_paid;
        //dd($wallet_ids);
        foreach ($wallet_ids as $key => $value) {
            $wallet_id = $key;
            $save_data['posting_type'] = $data['payment_type'];
            $save_data['status'] = "Paid";
            if ($patient_originally_paid > $value && !isset($remaining_pateint_balance)) {
                $patient_paid = $value;
                $remaining_pateint_balance = $patient_originally_paid - $value;
                $balance_amt = $remaining_pateint_balance;
            } elseif (isset($remaining_pateint_balance) && $remaining_pateint_balance > $value) {
                $patient_paid = $value;
                $remaining_pateint_balance = $remaining_pateint_balance - $value;
                $balance_amt = $remaining_pateint_balance;
            } elseif (isset($remaining_pateint_balance) && $remaining_pateint_balance < $value) {
                $patient_paid = $remaining_pateint_balance;
                $balance_amt = 0;
            } elseif ($patient_originally_paid <= $value) {
                $patient_paid = $patient_originally_paid;
                /* $remaining_pateint_balance  = 0;
                  $balance_amt = 0; */
                $nopaidbalance = 0;
                $remaining_pateint_balance = $balance_amt - $patient_paid;
                $balance_amt = $remaining_pateint_balance;
                if ($patient_originally_paid < $value)
                    $exit_status = 1;
            }
            $save_data['patient_paid_amt'] = $patient_paid;
            $save_data['balance_amt'] = $balance_amt + $nopaidbalance;
            if ($claim_data->self_pay == "Yes" && $claim_data->insurance_id == 0) {
                $save_data['patient_due'] = $save_data['balance_amt'];
                $save_data['insurance_due'] = 0; // Balance has been nill out but the due remains as in insurance
                $save_data['status'] = ($save_data['balance_amt'] > 0) ? "Patient" : "Paid";
            } else {
                $save_data['insurance_due'] = $claim_data->insurance_due; // eventhough the patient pays amount the insurance balance remains same
                $save_data['patient_due'] = 0;
                $save_data['status'] = $claim_data->status;
            }
            $patient_originally_paid = $patient_originally_paid - $save_data['patient_paid_amt'];
            $save_data['reference'] = $data['reference'];
            $save_data['insurance_id'] = $claim_data->insurance_id;
            $save_data['payment_id'] = $key;
            $save_data['created_by'] = Auth::user()->id;
            $claim_ids .= $save_data['claim_id'] . ',';
            $save_data['patient_id'] = $data['patient_id'];
            $save_data['payment_type'] = "Patient";
            //echo "<pre>";print_r($save_data);						     
            // @todo check and replace new pmt flow
            //$result = PaymentClaimDetail::create($save_data);

            //$save_data['insurance_id'] = $claim_data->insurance_id;									
            //$this->updatewalletinfo($save_data['patient_paid_amt'], $wallet_id, $save_data['posting_type']);
            $save_data['payment_id_claim'][$result->id] = $key;
            //echo "<pre>";print_r($save_data);
            if ($balance_amt == 0 || isset($exit_status) && $exit_status == 1) {
                break;
            }
            $nopaidbalance = 0;
        }

        $save_data['patient_paid_amt'] = array_sum($data['patient_paid']);
        $this->saveClaimCptdataCreditbalance($save_data, $result->id, $data);
        //$i++;			
    }

    public function saveClaimCptdataCreditbalance($save_data, $payment_claim_detail_id, $data)
    {
        //dd($save_data);
        $save_data['claim_id'] = Helpers::getEncodeAndDecodeOfId($data['claim_id'], 'decode');
        // @todo check and replace new pmt flow
        /*
        for ($i = 0; $i < count($data['cpt']); $i++) {
            $remaining_pateint_balance = 0;
            $balance_amt = $data['balance'][$i];
            $wallet_ids = $this->getcreditAmount($data['patient_id']); // Get the wallet balance from here	
            $claim_detail_id = $data['ids'][$i];
            $getcptdetails = Claimdoscptdetail::where('id', $claim_detail_id)->select('insurance_balance', 'balance')->first();
            $remaining = '';
            $balance_avail_amt = 0;
            //dd($wallet_ids)	
            $patient_paid = $data['patient_paid'][$i];
            $nopaidbalance = $balance_amt - $patient_paid;
            $patient_originally_paid = $patient_paid;
            foreach ($wallet_ids as $key => $value) {
                $wallet_id = $key;
                $exit_status = 0;
                if ($patient_originally_paid > $value && $remaining_pateint_balance == 0) {
                    $patient_paid = $value;
                    $remaining_pateint_balance = $patient_originally_paid - $value;
                    $balance_amt = $remaining_pateint_balance;
                } elseif (isset($remaining_pateint_balance) && $remaining_pateint_balance > $value && $remaining_pateint_balance != 0) {
                    $patient_paid = $value;
                    $remaining_pateint_balance = $remaining_pateint_balance - $value;
                    $balance_amt = $remaining_pateint_balance;
                } elseif (isset($remaining_pateint_balance) && $remaining_pateint_balance < $value && $remaining_pateint_balance != 0) {

                    $patient_paid = $remaining_pateint_balance;
                    $balance_amt = 0;
                } elseif (isset($remaining_pateint_balance) && $remaining_pateint_balance == $value && $remaining_pateint_balance != 0) {
                    $patient_paid = $value;
                    $remaining_pateint_balance = 0;  // patient specified amount paid here so we quite next
                    $exit_status = 1;
                    $balance_amt = $balance_amt - $patient_paid;
                } elseif ($patient_originally_paid <= $value) {
                    $patient_paid = $patient_originally_paid;                    
                    $nopaidbalance = 0;
                    $remaining_pateint_balance = $balance_amt - $patient_paid;
                    $balance_amt = $remaining_pateint_balance;
                    if ($patient_originally_paid < $value)
                        $exit_status = 1;
                }
                //$balance_amt = $remaining_pateint_balance = isset($balance_amt)?$balance_amt+$nopaidbalance:0;
                $balance_amt = isset($balance_amt) ? $balance_amt + $nopaidbalance : 0;
                $patient_paid = isset($patient_paid) ? $patient_paid : 0;
                $dos_spt_details['payment_id'] = $key;
                $dos_spt_details['claim_id'] = $save_data['claim_id'];
                $dos_spt_details['patient_id'] = $save_data['patient_id'];
                $dos_spt_details['balance_amt'] = $balance_amt;
                $dos_spt_details['paid_amt'] = $patient_paid;
                $dos_spt_details['patient_paid'] = $patient_paid;
                $dos_spt_details['patient_balance'] = ($save_data['insurance_id'] != 0 && !empty($save_data['insurance_id'])) ? 0 : $balance_amt;
                $dos_spt_details['insurance_balance'] = @$getcptdetails->insurance_balance;
                $dos_spt_details['posting_type'] = "Patient";
                $dos_spt_details['insurance_id'] = $save_data['insurance_id'];
                $dos_spt_details['payment_claim_detail_id'] = $payment_claim_detail_id;
                $dos_spt_details['claimdoscptdetail_id'] = $claim_detail_id;
                $result = PaymentClaimCtpDetail::create($dos_spt_details);
                $dos_spt_details['paid_amt'] = $patient_paid;
                $this->updateClaimCptdata($dos_spt_details, "Payment");
                $this->updatewalletinfo($patient_paid, $wallet_id, $save_data['posting_type']);
                $this->saveTransactionhistory($dos_spt_details);
                if ($balance_amt == 0 || isset($exit_status) && $exit_status == 1) {
                    break;
                }
                $nopaidbalance = 0;
            }
        }
        */
        //echo "<pre>"; print_r($save_data); exit;
        //exit;
        $this->updateclaims($save_data);
    }

    // Credit balance concept ends here.
    public function updateclaims($save_data)
    {
        $paid_amt = $save_data['patient_paid_amt'];
        $balance_amt = $save_data['balance_amt'];
        $posting_type = $save_data['posting_type'];
        $insurance_balance = $save_data['insurance_due'];
        $claim_data = ClaimInfoV1::where('id', $save_data['claim_id'])->select('self_pay', 'status', 'patient_due', 'balance_amt')->first();
        $claim_patient_due = $claim_data->patient_due;
        $claim_balance_amt = $claim_data->balance_amt;
        $patient_due = ($claim_patient_due > 0 && $claim_balance_amt <= 0) ? DB::raw("patient_due -" . $paid_amt) : $save_data['patient_due'];
        //dd($save_data);
        if ($balance_amt <= 0 && $claim_data->self_pay == "Yes") {
            $status = "Paid";
        } elseif ($balance_amt > 0 && $claim_data->self_pay == "Yes") {
            $status = "Patient";
        } else {
            $status = $claim_data->status;
        }
        if ($balance_amt < 0) {

        } else {
            // @todo - check and replace new pmt flow.
            if ($posting_type == "Adjustment") {
                // For adjustment update it into as adjusted
                // DB::table('claims')->where('id', $save_data['claim_id'])->update(['total_adjusted' => DB::raw("total_adjusted +" . $paid_amt), 'balance_amt' => DB::raw("balance_amt -" . $paid_amt), 'insurance_due' => $insurance_balance, 'patient_due' => $patient_due, 'status' => $status, 'patient_adjusted' => DB::raw("patient_adjusted +" . $paid_amt)]);
            } elseif ($posting_type == "Refund") {
                //DB::table('claims')->where('id', $save_data['claim_id'])->update(['total_paid' => DB::raw("total_paid -" . $paid_amt), 'balance_amt' => DB::raw("balance_amt +" . $paid_amt), 'patient_paid' => DB::raw("patient_paid -" . $paid_amt), 'insurance_due' => $insurance_balance, 'patient_due' => $patient_due, 'status' => $status]);
            } else {
                // DB::table('claims')->where('id', $save_data['claim_id'])->update(['total_paid' => DB::raw("total_paid +" . $paid_amt), 'balance_amt' => DB::raw("balance_amt -" . $paid_amt), 'patient_paid' => DB::raw("patient_paid +" . $paid_amt), 'insurance_due' => $insurance_balance, 'patient_due' => $patient_due, 'status' => $status]);
            }
        }
    }

    public function updatewalletinfo($amt_used, $wallet_id, $type, $wallet_refund_amt = null, $patient_id = null)
    {
        if (!is_null($wallet_refund_amt) && $wallet_refund_amt != 0) {
            $amt_used = $amt_used + $wallet_refund_amt;
            $transactiondata['posting_type'] = $type;
            $transactiondata['payment_id'] = $wallet_id;
            $transactiondata['patient_id'] = $patient_id;
            $transactiondata['amt_used'] = -1 * $wallet_refund_amt;
            $transactiondata['type_id'] = $wallet_id;  // To save negative transaction at payments table and to get understans negative transaction.
            $transactiondata['description'] = "Excess amount moved to wallet" . $wallet_refund_amt;
            $this->getUpdateRefundamount($transactiondata, $patient_id, $wallet_refund_amt, "refund");
            //$this->saveTransactionhistory($transactiondata, 'refund');				
        }
        $amt_used = ($type == "Refund" && $amt_used < 0) ? abs($amt_used) : $amt_used;
        //dd($amt_used)		;
        PMTInfoV1::where('id', $wallet_id)->update(['amt_used' => DB::raw("amt_used +" . $amt_used), 'balance' => DB::raw("balance -" . $amt_used)]);
    }

    public function getApiPopuppaymentdata($claim_id)
    {
        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        $attachment_detail = [];
        /* $claim_detail = Claims::with(['billing_provider', 'insurance_details', 'cpttransactiondetails','facility_detail', 'rendering_provider'])
          ->where('id', $claim_id)->first(); */

        $claim_detail = ClaimInfoV1::with('dosdetails', 'patient', 'insurance_details', 'rendering_provider', 'billing_provider', 'facility_detail')
            ->where('id', $claim_id)->first();
        // @todo check and replace new pmt flow    
        $claim_list = [];//PaymentClaimDetail::with(['payment', 'paymentcptdetail', 'insurance_detail'])->where('claim_id', $claim_id)->get();

        // New payment flow for desc start 
        $claim_tx_list = $this->getClaimTxnDesc($claim_id);
        $cpt_tx_list = $this->getClaimCptTxnDesc($claim_id);
        //dd($claim_list);
        // New payment flow for desc end     
        // @todo - check and replace new pmt flow
        $payment_claim_ids = PMTClaimTXV1::where('claim_id', $claim_id)->pluck('claim_id', 'payment_id')->all();

        $attachment_detail = PMTInfoV1::has('attachment_detail')
            ->with('attachment_detail')
            ->whereIn('id', array_keys($payment_claim_ids))->get();
        //dd($attachment_detail);
        return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_list', 'claim_detail', 'attachment_detail', 'claim_tx_list', 'cpt_tx_list')));
    }

    public function getcreditAmount($patient_id)
    {
        $wallet_balance = PMTInfoV1::where('patient_id', $patient_id)->where('pmt_method', 'Patient')->where('pmt_type', 'Payment')->where('balance', '>', 0)->where('void_check', NULL)->pluck('balance', 'id')->all();
        return $wallet_balance;
    }

    public function listWalletTransactionHistories($patient_id)
    {
        $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');

        $transaction_histories = PMTInfoV1::with('payment')->where('patient_id', $patient_id)->get();
    }

    public function listClaimTransactionHistories($claim_id)
    {
        // @todo check and repalce new pmt flow
        /*
        $claim_ctp_transactions = PaymentClaimDetail::with('claim', 'payment')->where('claim_id', $claim_id)->get();
        foreach ($claim_ctp_transactions as $claim_ctp_transaction) {
            $type = $claim_ctp_transaction->payment->type;
            $payment_mode = $claim_ctp_transaction->payment->payment_mode;
            $payment_amt = $claim_ctp_transaction->payment->payment_amt;
        }
        */
    }

    public function listClaimcptTransactionHistories($claim_id)
    {
        // 
        // $claim_transaction = PaymentTransactionHistory::where('claim_id', $claim_id)->orderBy('id', 'desc')->get();
        $claim_transaction = [];
    }

    public function log($data)
    {
        $file = fopen(app_path() . '/log.txt', 'a');
        fwrite($file, "\n" . $data . "\n");
        fclose($file);
    }

    // Get Patient insurance with category starts here
    public function getPatientInsuranceApi($patient_id)
    {
        $insurance_lists = Patient::getPatientInsuranceWithCategory($patient_id);
        return $insurance_lists;
    }

    // Get Patient insurance with category ends here
    //Add amount to patient wallet starts here
    public function addAmountToWalletApi($request, $type = null)
    {

        /** @todo check and remove this function - since not going to use this anymore.
         * $request['payment_amt'] = $request['payment_amt_pop'];
         * $pay_amt = $request['payment_amt'];
         * $request['payment_method'] = 'Patient';
         * $request['payment_type'] = "Payment";
         * $patient_id = $request['patient_id'];
         * $file = Request::file('filefield_eob');
         * if (isset($type)) {
         * $request['payment_type'] = "Refund";
         * $type = "refundwallet";
         * $request['payment_mode'] = "Check";
         * $request['balance'] = -1 * $request['payment_amt'];
         * }
         * $type = is_null($type) ? 'addwallet' : $type; // APyment or refund
         * $check_exists = 0;
         *
         * if ($request['payment_mode'] == "Check"){
         * // @todo - check and remove
         * //$check_exists = PMTInfoV1::whereRaw('check_no = ? and pmt_method = ? and pmt_mode = ? and void_check is null', array($request['check_no'], "Patient", "Check"))->count();
         * $check_exists = PMTInfoV1::findCheckExistsOrNot($request['check_no'], "Patient", "Check", "patientPayment", $patient_id);
         * }
         * $wallet_balance = PMTInfoV1::where('patient_id', $patient_id)->where('pmt_method', 'Patient')->where('pmt_type', 'Payment')->whereNotIn('source', ['refundwallet'])->sum('balance');
         * $wallet_balance = ($wallet_balance < 0) ? 0 : $wallet_balance;
         * // echo $wallet_balance; exit;
         * // To check whether wallet having the required amount or not starts here
         * if ($pay_amt > $wallet_balance && $type != 'addwallet' && $request['payment_type'] == "Payment") {
         * return Response::json(array('status' => 'error', 'message' => Lang::get("practice/patients/payments.validation.amountnotavailable"), 'data' => ""));
         * }
         * // To check whether wallet having the required amount or not ends here
         * if ($check_exists) {
         * return Response::json(array('status' => 'error', 'message' => Lang::get("practice/patients/payments.validation.checkexist"), 'data' => $request['patient_id']));
         * } else {
         *
         * DB::beginTransaction();
         * try {
         *
         * //dd($request);
         * if (!empty($file)) {
         * $payment = new PaymentApiController();
         * $attachment_id = $payment->movePaymentAttachment($request);
         * $request['eob_id'] = $attachment_id;
         * }
         * // dd($request)    ;
         * // @todo check  and replace new pmt flow
         * $payment_id = PMTInfoV1::savePaymentDetail($request, $type);
         *
         * if ($payment_id) {
         * $transactiondata['posting_type'] = $request['payment_type'];
         * $transactiondata['patient_id'] = $patient_id;
         * $transactiondata['payment_id'] = $payment_id;
         * $transactiondata['description'] = "Amount " . $request['payment_amt'] . ' ' . $request['payment_type'] . ' to user wallet';
         * if (isset($type) && $type == "refundwallet") { // CAll refund method and update the amount used fieilds
         * $this->getUpdateRefundamount($transactiondata, $patient_id, $request['payment_amt']);
         * }
         * $wallet_amt = PMTInfoV1::where('patient_id', $patient_id)->where('pmt_method', 'Patient')->whereNotIn('source', ['refundwallet'])->where('pmt_type', 'Payment')->where('void_check', NULL)->sum('balance');
         * $wallet_amt = number_format($wallet_amt, 2);
         * DB::commit();
         * $message = ($type == "refundwallet") ? Lang::get("common.validation.refund_msg") : Lang::get("common.validation.create_msg");
         * return Response::json(array('status' => 'success', 'message' => $message, 'data' => $wallet_amt));
         * } else {
         * return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => ''));
         * }
         * } catch (Exception $e) {
         * DB::rollBack();
         * throw $e;
         * }
         * }
         */
    }

    public function getUpdateRefundamount($transaction, $patient_id, $refunded_amount, $type = null)
    {
        $patient_paid_details = PMTInfoV1::where('patient_id', $patient_id)->whereNotIn('source', ['refundwallet'])->where('pmt_method', 'Patient')->where('pmt_type', '!=', 'Refund')->where('void_check', NULL)->where('balance', '>', 0)->get();
        // dd($patient_paid_details)        ;
        $remaining_amount = $refunded_amount;
        // echo  "type".$type;
        $transaction['description'] = "Amount refunded to patient";
        foreach ($patient_paid_details as $patient_paid_detail) {
            $balance = $patient_paid_detail->balance;
            if ($remaining_amount > $balance) {
                $remaining_amount = $remaining_amount - $balance;
                $payment_amount = $balance;
                $payment_id = $patient_paid_detail->id;
                PMTInfoV1::where('id', $payment_id)->update(['amt_used' => DB::raw("amt_used +" . $payment_amount), 'balance' => DB::raw("balance -" . $payment_amount)]);
                $transaction['source_payment_id'] = $payment_id;
                $transaction['refund_amt'] = $payment_amount;
            } elseif ($remaining_amount <= $balance) {
                $payment_amount = $remaining_amount;
                $payment_id = $patient_paid_detail->id;
                PMTInfoV1::where('id', $payment_id)->update(['amt_used' => DB::raw("amt_used +" . $payment_amount), 'balance' => DB::raw("balance -" . $payment_amount)]);
                $transaction['source_payment_id'] = $payment_id;
                $transaction['refund_amt'] = $payment_amount;
                $this->saveTransactionhistory($transaction, $type);
                break;
            }
            $this->saveTransactionhistory($transaction, $type);
        }
        //exit;       
    }

    // Save transaction history starts here	
    // @todo check and replace new pmt flow
    public function saveTransactionhistory($transactiondata, $type = null)
    {
        /* @todo check and remove this
         * $posting_type = $transactiondata['posting_type'];
         * $trans_data['claim_id'] = isset($transactiondata['claim_id']) ? $transactiondata['claim_id'] : '';
         * $trans_data['payment_id'] = $transactiondata['payment_id'];
         * $trans_data['patient_id'] = isset($transactiondata['patient_id']) ? $transactiondata['patient_id'] : '';
         * $trans_data['payment_claim_detail_id'] = isset($transactiondata['payment_claim_detail_id']) ? $transactiondata['payment_claim_detail_id'] : '';
         * $trans_data['paymentcpt_detail_id'] = isset($transactiondata['paymentcpt_detail_id']) ? $transactiondata['paymentcpt_detail_id'] : '';
         * $trans_data['posting_type'] = $posting_type;
         * $trans_data['type'] = isset($transactiondata['type']) ? $transactiondata['type'] : '';
         * $trans_data['type_id'] = isset($transactiondata['type_id']) ? $transactiondata['type_id'] : '';
         * $trans_data['description'] = isset($transactiondata['description']) ? $transactiondata['description'] : "";
         * $trans_data['created_by'] = Auth::user()->id;
         * $trans_data['source_payment_id'] = @$transactiondata['source_payment_id'];
         * $trans_data['refund_amt'] = @$transactiondata['refund_amt'];
         * //echo "sdfds";print_r($type);
         * // @todo - check and replace new pmt flow
         * // PaymentTransactionHistory::create($trans_data);
         * if ($type == "refund" && $transactiondata['amt_used'] != 0) {
         * $trans_data['payment_amt'] = 0;
         * $trans_data['balance'] = $transactiondata['amt_used'];
         * $trans_data['payment_method'] = 'Patient';
         * $trans_data['payment_type'] = 'Payment';
         * $payment_id = PMTInfoV1::savePaymentDetail($trans_data, 'refundwallet', $trans_data['type_id']); // Refund from wallet negative transaction entry
         * //dd($trans_data)    ;
         * }
         */
    }

    // Save transaction history ends here
    //Add amount to patient wallet ends here
    // When we do payment eventhough we have claim balance as 0 we will move the corresponding amount patient's wallet starts here
    public function moveExcessamountToWallet($payment_id, $patient_id, $claim_id, $amount, $ins_id, $ins_due = 0, $pat_due = 0)
    {
        $saveData['claim_id'] = $claim_id;
        $saveData['patient_id'] = $patient_id;
        $saveData['created_by'] = Auth::user()->id;
        $saveData['payment_type'] = "Addwallet";
        $saveData['payment_id'] = $payment_id;
        $saveData['insurance_id'] = $ins_id;
        $saveData['transaction_type'] = "addwallet";
        $saveData['description'] = "Excess paid amount moved to wallet" . abs($amount);
        $saveData['patient_paid_amt'] = -1 * $amount; // deducting amount 
        $saveData['patient_due'] = $pat_due; // deducting amount         
        $saveData['insurance_due'] = $ins_due;
        // @todo check and replace new pmt flow
        // $payment_claim_detail = PaymentClaimDetail::create($saveData);
    }

    public function moveExcessamountToCptWallet($data_val, $amount)
    {
        // @todo - check and replace new pmt flow
        /*
        $saveData['claim_id'] = $data_val['claim_id'];
        $saveData['patient_id'] = $data_val['patient_id'];
        $saveData['created_by'] = Auth::user()->id;
        $saveData['payment_id'] = $data_val['payment_id'];
        $saveData['insurance_id'] = $data_val['insurance_id'];
        $saveData['transaction_type'] = "addwallet";
        $saveData['patient_paid'] = $amount; // deducting amount 
        $saveData['paid_amt'] = $amount;
        $saveData['patient_balance'] = $data_val['patient_balance'];
        $saveData['insurance_balance'] = $data_val['insurance_balance'];
        $saveData['claimdoscptdetail_id'] = $data_val['claimdoscptdetail_id'];
        $saveData['description'] = "Excess paid amount moved to wallet" . abs($amount);        
        PaymentClaimCtpDetail::create($saveData);
        */
    }

    // When we do payment eventhough we have claim balance as 0 we will move the corresponding amount patient's wallet ends here
    public function listPatientInsuranceApi($patient_id)
    {
        $insurance_lists = Patient::getPatientUniqueInsuranceDetails($patient_id);
        return Response::json(array('data' => $insurance_lists));
    }

    /*  @Mk
     *  void check is only applied if Check is Patient
     *  add a the all the transaction amount  -
     *  delete  document's
     *  add the wallet Tx
     */

    public function voidPaymentcheckdataApi($payment_id)
    {
        try {
            $paymentData = PMTInfoV1::where('id', $payment_id)->first();
            if ($paymentData['pmt_method'] == 'Patient') {
                $claimTxData = PMTClaimTXV1::select('id','payment_id','claim_id' , 'pmt_method', 'pmt_type', DB::raw('sum(total_paid) as total_paid'), DB::raw('group_concat(id) as txn_ids'))
                              ->where('payment_id', $payment_id)
                              ->groupBy("payment_id", "claim_id");
                $claimTxDetails = $claimTxData->get()->toArray();
            }
            //\Log::info("voidPaymentcheckdataApi");      \Log::info($claimTxDetails);
            DB::beginTransaction();
            foreach($claimTxDetails AS $claimTxDatas){
                if ($claimTxDatas['total_paid'] > '0') {                    
                    $claimTxIDS = $claimTxDatas['id']; //$claimTxData->pluck('id')->all();
                    $txnFor = $claimTxDatas['pmt_method'] . ' ' . $claimTxDatas['pmt_type'];
                   
                    /* Claim related handle start */
                    if (!empty($claimTxDatas['claim_id'])) {
                        
                        //$claimCptTx = PMTClaimCPTTXV1::select('*', 'paid') 
                        //Change by baskar -06/12/18
                        $claimCptTx = PMTClaimCPTTXV1::select('*', DB::raw('sum(paid) as paid'))
                                    //->where('pmt_claim_tx_id', $claimTxIDS)
                                    ->where('payment_id', $payment_id)
                                    ->where('claim_id', $claimTxDatas['claim_id']) // Included condition for
                                    ->groupBy("payment_id", "claim_cpt_info_id") // Group by pmt id and cpt id used to handle mulitple cpt.
                                    ->get();
                        
                        $claimCptTx1 = PMTClaimCPTTXV1::select('*', DB::raw('sum(paid) as paid'))
                                    ->where('pmt_claim_tx_id', $claimTxIDS)
                                    ->groupBy("payment_id", "claim_cpt_info_id") // Group by pmt id and cpt id used to handle mulitple cpt.
                                    ->get();            

                        //\Log::info("CPT Tx");     \Log::info($claimCptTx);            
                        $finDatas = PMTClaimFINV1::where('claim_id', $claimTxDatas['claim_id'])->first();
                        $patientId = $finDatas['patient_id'];
                        $newPmtClaimTx = array(
                            "payment_id" => $payment_id,
                            "claim_id" => $claimTxDatas['claim_id'],
                            "pmt_method" => 'Patient',
                            "pmt_type" => 'Payment',
                            "patient_id" => $patientId,
                            "total_paid"=> -1 *($claimTxDatas['total_paid']),
                            "posting_date" => date("Y-m-d"),
                            "created_by" => Auth::user()->id
                        );                     
                        $pmtClaimTxId = PMTClaimTXV1::create($newPmtClaimTx)->id;
                        $desArr = array(
                            'pmt_id' => $payment_id,
                            'claim_info_id' => $claimTxDatas['claim_id'],
                            'txn_id' => $pmtClaimTxId,
                            'resp' => $this->getClaimResponsibility($claimTxDatas['claim_id']),
                            'check_amount' => -1 * ($claimTxDatas['total_paid'])
                        );

                        $claimTxDesId = $this->storeClaimTxnDesc('Void Check', $desArr);
                        $chargeV1 = new  ChargeV1ApiController();
                        
                        foreach ($claimCptTx as $claimCpt) {
                            $pmtClaimTxDetails = PMTClaimTXV1::select(['id','pmt_method','pmt_type'])
                                                 ->where('id',$claimCpt['pmt_claim_tx_id'])
                                                 ->first();
                            $paidAmount = $claimCpt['paid'];
                            $claimCpt['paid'] = -1 * $paidAmount;
                            $claimCpt = $claimCpt->toArray();
                            $claimCpt['resp'] = $this->getClaimResponsibility($claimCpt['claim_id']);
                            $claimCpt['pmt_claim_tx_id'] = $pmtClaimTxId;
                            $resultSet = PMTClaimCPTTXV1::create($claimCpt); //\Log::info($claimCpt);
                            $desArr = array(
                                'pmt_id' => $claimCpt['payment_id'],
                                'txn_id' => $resultSet->id,
                                'claim_tx_desc_id' => $claimTxDesId,
                                'claim_info_id' => $claimCpt['claim_id'],
                                'claim_cpt_info_id' => $claimCpt['claim_cpt_info_id'],
                                'check_amount' => $claimCpt['paid'],
                                'resp' => $this->getClaimResponsibility($claimCpt['claim_id'])
                            );
                            $txnFor = $pmtClaimTxDetails['pmt_method'] . ' ' . $pmtClaimTxDetails['pmt_type'];
                            $usedAmount = ($paidAmount < 0) ? $paidAmount : $claimCpt['paid'];
                            $cptTxDesId = $this->storeClaimCptTxnDesc('Void Check', $desArr);//\Log::info($desArr);
                            PMTInfoV1::updatePaymettAmoutUsed($payment_id, $usedAmount);
                            //$chargeV1->updateClaimCptTxData($claimCpt, $txnFor);
                            //\Log::info("Update claim fin called");
                            $currentFinBalance = $chargeV1->updateClaimCptFindData($claimCpt, $txnFor);
                            $chargeV1->updateBalanceClaimCPTTXDesc($cptTxDesId, $currentFinBalance['cpt_patient_balance'], $currentFinBalance['cpt_insurance_balance']);
                        }

                        $currentClaimFinBalance = $chargeV1->findClaimLevelPatientandInsuranceBal(['claim_id' => $claimTxDatas['claim_id']], $claimTxDesId);
                        $chargeV1->updateBalanceClaimTXDesc($claimTxDesId, $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                        ClaimInfoV1::updateClaimStatus($claimTxDatas['claim_id'], $txnFor);
                    }
                }/*else{
                        return Response::json(array('status' => 'failed', 'message' => 'failed', 'data' => 'failed'));
                }*/
            }

            /* Claim related handle end */
            $walletDatas = PMTWalletV1::where('pmt_info_id', $payment_id)->get();
            if (!empty($claimTxDatas)) {
                foreach ($walletDatas as $walletData) {
                    $walletAmount = $walletData['amount'];
                    $walletData['amount'] = -1 * $walletData['amount'];
                    $walletData['tx_type'] = 'Debit';
                    $walletData = $walletData->toArray();
                    PMTWalletV1::create($walletData);
                    $walletData = ($walletAmount < 0) ? abs($walletAmount) : $walletData['amount'];
                    PMTInfoV1::updatePaymettAmoutUsed($payment_id, $walletData);
                }
            }

            //Document::where('main_type_id', $payment_id)->delete();
            PMTInfoV1::where('id', $payment_id)->update(['void_check' => 1]);
            DB::commit();            
            return Response::json(array('status' => 'success', 'message' => 'success', 'data' => 'success'));
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->showErrorResponse("updatePmt_infoAmt_usedColumn", $e);
            return Response::json(array('status' => 'failed', 'message' => 'failed', 'data' => 'failed'));
            
        }
    }

    // Refund void payment
    public function voidRefundPaymentcheckdataApi($payment_id)
    {
        try {
            $paymentData = PMTInfoV1::where('id', $payment_id)->first();
            if ($paymentData['pmt_method'] == 'Patient') {
                $claimTxData = PMTClaimTXV1::select('id','payment_id','total_paid','claim_id' , 'pmt_method', 'pmt_type', 'total_paid')
                              ->where('payment_id', $payment_id)
                              ->groupBy("payment_id", "claim_id");
                $claimTxDetails = $claimTxData->get()->toArray();
            }
           // \Log::info("Void refund payment");
            //\Log::info($claimTxDetails);
            DB::beginTransaction();
            foreach($claimTxDetails AS $claimTxDatas){
                if ($claimTxDatas['total_paid'] < '0') {                    
                    $claimTxIDS = $claimTxDatas['id']; 
                    $txnFor = $claimTxDatas['pmt_method'] . ' ' . $claimTxDatas['pmt_type'];
                   
                    /* Claim related handle start */
                    if (!empty($claimTxDatas['claim_id'])) {
                        $claimCptTx = PMTClaimCPTTXV1::select('*', DB::raw('sum(paid) as paid'))
                                    ->where('pmt_claim_tx_id', $claimTxIDS)
                                    ->groupBy("payment_id", "claim_cpt_info_id") // Group by pmt id and cpt id used to handle mulitple cpt.
                                    ->get();              

                        $finDatas = PMTClaimFINV1::where('claim_id', $claimTxDatas['claim_id'])->first();
                        $patientId = $finDatas['patient_id'];
                        $newPmtClaimTx = array(
                            "payment_id" => $payment_id,
                            "claim_id" => $claimTxDatas['claim_id'],
                            "pmt_method" => 'Patient',
                            "pmt_type" => 'Refund',
                            "patient_id" => $patientId,
                            "total_paid"=> -1 *($claimTxDatas['total_paid']),
                            "posting_date" => date("Y-m-d"),
                            "created_by" => Auth::user()->id
                        );                        
                        $pmtClaimTxId = PMTClaimTXV1::create($newPmtClaimTx)->id;
                        $desArr = array(
                            'pmt_id' => $payment_id,
                            'claim_info_id' => $claimTxDatas['claim_id'],
                            'txn_id' => $pmtClaimTxId,
                            'resp' => $this->getClaimResponsibility($claimTxDatas['claim_id']),
                            'check_amount' => -1 * ($claimTxDatas['total_paid'])
                        );

                        $claimTxDesId = $this->storeClaimTxnDesc('Void Check', $desArr);

                        $chargeV1 = new  ChargeV1ApiController();
                        foreach ($claimCptTx as $claimCpt) {
                            $pmtClaimTxDetails = PMTClaimTXV1::select(['id','pmt_method','pmt_type'])
                                                 ->where('id',$claimCpt['pmt_claim_tx_id'])
                                                 ->first();
                            $paidAmount = $claimCpt['paid'];
                            $claimCpt['paid'] = -1 * $paidAmount;
                            $claimCpt = $claimCpt->toArray();
                            $claimCpt['resp'] = $this->getClaimResponsibility($claimCpt['claim_id']);
                            $claimCpt['pmt_claim_tx_id'] = $pmtClaimTxId;
                            $resultSet = PMTClaimCPTTXV1::create($claimCpt);
                            $desArr = array(
                                'pmt_id' => $claimCpt['payment_id'],
                                'txn_id' => $resultSet->id,
                                'claim_tx_desc_id' => $claimTxDesId,
                                'claim_info_id' => $claimCpt['claim_id'],
                                'claim_cpt_info_id' => $claimCpt['claim_cpt_info_id'],
                                'check_amount' => $claimCpt['paid'],
                                'resp' => $this->getClaimResponsibility($claimCpt['claim_id'])
                            );
                            $txnFor = $pmtClaimTxDetails['pmt_method'] . ' ' . $pmtClaimTxDetails['pmt_type'];
                            $usedAmount = ($paidAmount < 0) ? $paidAmount : $claimCpt['paid'];
                            $cptTxDesId = $this->storeClaimCptTxnDesc('Void Check', $desArr);
                            PMTInfoV1::updatePaymettAmoutUsed($payment_id, $usedAmount);
                            //$chargeV1->updateClaimCptTxData($claimCpt, $txnFor);
                            $currentFinBalance = $chargeV1->updateClaimCptFindData($claimCpt, $txnFor);
                            $chargeV1->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                $currentFinBalance['cpt_patient_balance'], $currentFinBalance['cpt_insurance_balance']);
                        }
                        $currentClaimFinBalance = $chargeV1->findClaimLevelPatientandInsuranceBal(['claim_id' => $claimTxDatas['claim_id'] ], $claimTxDesId);
                        $chargeV1->updateBalanceClaimTXDesc($claimTxDesId,
                            $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                        ClaimInfoV1::updateClaimStatus($claimTxDatas['claim_id'], $txnFor);
                    }
                }/*else{
                        return Response::json(array('status' => 'failed', 'message' => 'failed', 'data' => 'failed'));
                }*/
            }

            /* Claim related handle end */
            if(!empty($paymentData) && $paymentData->source=="refundwallet"){
                $walletDatas = PMTWalletV1::where('wallet_Ref_Id', $payment_id)->get();
                foreach ($walletDatas as $walletData) {
                    PMTWalletV1::where('id',$walletData->id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
                }
            }
            else
                $walletDatas = PMTWalletV1::where('pmt_info_id', $payment_id)->get();
            if (!empty($claimTxDatas)) {
                foreach ($walletDatas as $walletData) {
                    $walletAmount = $walletData['amount'];
                    $walletData['amount'] = -1 * $walletData['amount'];
                    $walletData['tx_type'] = 'Debit';
                    $walletData = $walletData->toArray();
                    PMTWalletV1::create($walletData);
                    $walletData = ($walletAmount < 0) ? abs($walletAmount) : $walletData['amount'];
                    PMTInfoV1::updatePaymettAmoutUsed($payment_id, $walletData);
                }
            }

            //Document::where('main_type_id', $payment_id)->delete();
            PMTInfoV1::where('id', $payment_id)->update(['void_check' => 1]);
            DB::commit();            
            return Response::json(array('status' => 'success', 'message' => 'success', 'data' => 'success'));
            
        } catch (Exception $e) {
            DB::rollBack();
            $this->showErrorResponse("updatePmt_infoAmt_usedColumn", $e);
            return Response::json(array('status' => 'failed', 'message' => 'failed', 'data' => 'failed'));
            
        }
    }

    public function updateClaimOnPatientPaidTransaction($data, $payment_id)
    {
        // @todo - check and replace ne pmt flow
        /*
        $saveData['claim_id'] = $data->claim_id;
        $saveData['payment_id'] = $payment_id;
        $saveData['created_by'] = Auth::user()->id;
        $saveData['payment_type'] = "Patient";
        //$saveData['payment_id'] = $payment_id;
        $saveData['description'] = "Amount reverted";
        $saveData['patient_paid_amt'] = -1 * $data->patient_paid;    // Deducting amount
        $claim_data = Claims::where("id", $saveData['claim_id'])->select("id", "insurance_due", "patient_due")->first();
        $saveData['patient_due'] = $claim_data->patient_due;
        $saveData['insurance_due'] = $claim_data->insurance_due;
        PaymentClaimDetail::create($saveData);
        */
    }

    public function updateClaimCptOnPatientPaidTransaction($data, $amount, $patient_balance_cpt, $insurance_balance_cpt)
    {
        // @todo check and replace new pmt flow
        /*
        $saveData['claim_id'] = $data->claim_id;
        $saveData['patient_id'] = $data->patient_id;
        $saveData['created_by'] = Auth::user()->id;
        $saveData['payment_type'] = "Patient";
        //$saveData['payment_id'] = $payment_id;
        $saveData['claimdoscptdetail_id'] = $data->id;
        $saveData['description'] = "Amount reverted";
        $saveData['patient_paid'] = -1 * $amount; // deducting amount  
        $saveData['paid_amt'] = -1 * $amount; // deducting amount  
        $saveData['patient_balance'] = $patient_balance_cpt;
        $saveData['insurance_balance'] = $insurance_balance_cpt;
        //echo "<pre>";print_r($saveData); exit;
        //dd($saveData);
        ($amount != 0) ? PaymentClaimCtpDetail::create($saveData) : "";
        */
    }

    public function getTransactionForRefund($id)
    {
        //  @todo - check and replace new pmt flow
        /*
        $data = PaymentTransactionHistory::Has('payment_data')->groupBy('source_payment_id')->where('payment_id', $id)->selectRaw('source_payment_id, sum(refund_amt) as refund_amt')->pluck('refund_amt', 'source_payment_id')->all();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                Payment::where('id', $key)->update(['amt_used' => DB::raw("amt_used -" . $value), 'balance' => DB::raw("balance +" . $value)]);
            }
        }       */

    }    

}