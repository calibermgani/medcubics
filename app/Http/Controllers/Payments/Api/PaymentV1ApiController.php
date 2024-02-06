<?php

namespace App\Http\Controllers\Payments\Api;

use App;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Patients\Api\ArManagementApiController;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Code as Code;
use App\Models\Document;
use App\Models\Patients\DocumentFollowupList;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientBudget;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Payments\PMTClaimCPTTXV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTWalletV1;
use App\Traits\ClaimUtil;
use Auth;
use DB;
use Image;
use Input;
use Lang;
use Response;

class PaymentV1ApiController extends Controller
{

    use ClaimUtil;

    public function createPayment($request, $claim_id)
    {
        try {
            if (!empty($claim_id)) {
                $claim_id = explode(',', $claim_id)[0];
                $create_claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
                $claim_lists = $this->getClaimDetails($create_claim_id);
                if (!empty($claim_lists)) {
                    $claims_dosDetails = $claim_lists->dosdetails; // $claim_lists[0]->dosdetails;
                    $dosDatas = $this->getClaimsliCpt_Tx_fin_Datas($claim_lists, $claims_dosDetails, $create_claim_id);
                    $billed = $claim_lists->total_charge;
                    $total = $this->getClaimsFinDetails($create_claim_id, $billed);
                    $claim_lists = array_merge($dosDatas, $total); //with all claim and cpts totals and txDesc Details
                    $claim_tx_list = $this->getClaimTxnDesc($create_claim_id);
                    $cpt_tx_list = $this->getClaimCptTxnDesc($create_claim_id);
                    $attachment_detail = $this->getClaimAttachemts($create_claim_id);
                }
                return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_lists', 'total', 'claim_tx_list', 'cpt_tx_list', 'attachment_detail')));
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createPayment", $e);
        }
    }

    public function getClaimsliCpt_Tx_fin_Datas($claimsInfoDetails, $claims_dosDetails, $claim_id)
    {
        try {
            $dosDatas = array();
            if ($claims_dosDetails) {
                $claimListDatas = array();
                $active_count = 0;
                for ($i = 0; $i < count($claims_dosDetails); $i++) {
                    $temp = array(
                        'dos' => ($claims_dosDetails[$i]->dos_from && $claims_dosDetails[$i]->dos_from != '0000-00-00') ? date('m/d/Y', strtotime($claims_dosDetails[$i]->dos_from)) : '',
                        'cpt_code' => $claims_dosDetails[$i]->cpt_code,
                        'charge' => $claims_dosDetails[$i]->charge,
                        'id' => $claims_dosDetails[$i]->id,
                        'is_active' => $claims_dosDetails[$i]->is_active,
                    );
                    $active_count = ($claims_dosDetails[$i]->is_active) ? $active_count + 1 : $active_count;
                    $claimscptFinData = $claims_dosDetails[$i]->claimCptFinDetails;

                    if ($claimscptFinData) {
                        //formala insurancebalance = totalCharge - (ins_bal+ins_adj+withHeld);
                        $insurance_paid = $claimscptFinData->insurance_paid + $claimscptFinData->insurance_adjusted + $claimscptFinData->with_held;
                        $ins_Balance = $claims_dosDetails[$i]->charge - $insurance_paid;
                        $toalPaid = $claimscptFinData->patient_paid + $claimscptFinData->insurance_paid;

                        $toalAdjustment = $claimscptFinData->patient_adjusted + $claimscptFinData->insurance_adjusted;
                        $allowedAmount = $claims_dosDetails[$i]->charge - $claimscptFinData->insurance_adjusted;
                        $temp1 = array(
                            'patient_paid' => $claimscptFinData->patient_paid,
                            'patient_adjusted' => $claimscptFinData->patient_adjusted,
                            'patient_balance' => $claimscptFinData->patient_balance,
                            'insurance_paid' => $claimscptFinData->insurance_paid,
                            'insurance_balance' => $this->numberFormater($ins_Balance),
                            'cpt_allowed_amt' => $this->numberFormater($allowedAmount),
                            'with_held' => $claimscptFinData->with_held,
                            'paid_amt' => $this->numberFormater($toalPaid),
                            'adjustment' => $this->numberFormater($toalAdjustment),
                            'balance' => $this->calculateCptFinBalance($claims_dosDetails[$i]->charge, $claimscptFinData)['balance']
                        );
                        array_push($claimListDatas, array_merge($temp, $temp1));
                    } else {
                        $temp2 = array(
                            'patient_paid' => '0.00',
                            'patient_adjusted' => '0.00',
                            'patient_balance' => '0.00',
                            'insurance_paid' => '0.00',
                            'insurance_balance' => '0.00',
                            'cpt_allowed_amt' => '0.00',
                            'with_held' => '0.00',
                            'balance' => $claims_dosDetails[$i]->charge
                        );
                        array_push($claimListDatas, array_merge($temp, $temp2));
                    }
                }

                $dosDatas = array(
                    'dosdetails' => $claimListDatas,
                    'total_lineitem_count' => count($claims_dosDetails),
                    'active_lineitem_count' => $active_count,
                    "is_insurncePaymentHistory" => $this->checkIns_PaymentClx_Tx($claim_id),
                );
            }

            if ($claimsInfoDetails) {
                $claimsInfoDetails = array(
                    'submited_date' => $this->dateformater($claimsInfoDetails->submited_date),
                    'last_submited_date' => $this->dateformater($claimsInfoDetails->last_submited_date),
                    'date_of_service' => $this->dateformater($claimsInfoDetails->date_of_service),
                    'id' => $claim_id,
                    'claim_number' => $claimsInfoDetails->claim_number,
                    'is_send_paid_amount' => $claimsInfoDetails->is_send_paid_amount,
                    'insurance_category' => $claimsInfoDetails->insurance_category,
                    'self_pay' => $claimsInfoDetails->self_pay,
                    'insurance_details' => $claimsInfoDetails->insurance_details,
                    'rendering_provider' => $claimsInfoDetails->rendering_provider,
                    'billing_provider' => $claimsInfoDetails->billing_provider,
                    'facility_detail' => $claimsInfoDetails->facility_detail,
                    "insurance_id" => (!empty($claimsInfoDetails->insurance_details->id)) ? $claimsInfoDetails->insurance_details->id : 0,
                    'patient' => $claimsInfoDetails->patient,
                    'created_at' => $this->dateformater($claimsInfoDetails->created_at), //date('m/d/Y', strtotime($claims_dosDetails[$i]->dos_from))
                    'status' => $claimsInfoDetails->status
                );
                $dosDatas = array_merge($dosDatas, $claimsInfoDetails);
            } else {
                $claimCptTxDatas = array(
                    'id' => 0,
                    'claim_number' => 0
                );
                $dosDatas = array_merge($dosDatas, $claimCptTxDatas);
            }
            return $dosDatas;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("getClaimsli_Tx_fin_Datas", $e);
        }
    }

    public function checkIns_PaymentClx_Tx($claim_id)
    {
        $txCount = PMTClaimTXV1::select(['id', 'claim_id', 'insurance_id'])
            ->where('pmt_method', 'Insurance')
            ->where(function ($query) {
                $query->where('pmt_type', 'Payment')
                    ->orwhere('pmt_type', 'Adjustment');
            })
            ->where('payer_insurance_id', '!=', 0)
            ->where('claim_id', $claim_id)
            ->count();
        return ($txCount >= 1) ? true : false;
    }

    public function getClaimsFinDetails($claimId, $totalCharge)
    {
        $financialDatas = ClaimInfoV1::getClaimFinDetails($claimId, $totalCharge);
        return $financialDatas;
    }

    public function calculateCptFinBalance($totalCharge, $claimscptFinData)
    {
        $financialDatas = ClaimInfoV1::getClaimCptFinBalance($totalCharge, $claimscptFinData);
        return $financialDatas;
        /*
        if (!empty($claimscptFinData)) {
            $patientAdjusted  =  isset($claimscptFinData['patient_adj'])?$claimscptFinData['patient_adj']:$claimscptFinData['patient_adjusted'];
            $insurance_adjusted  =  isset($claimscptFinData['insurance_adj'])?$claimscptFinData['insurance_adj']:$claimscptFinData['insurance_adjusted'];
            $withheld = isset($claimscptFinData->withheld) ? $claimscptFinData->withheld : $claimscptFinData->with_held;
            $totalPaid = $claimscptFinData->patient_paid + $claimscptFinData->insurance_paid;
            $totalAdjustment =$patientAdjusted+ $insurance_adjusted;
            $balance = $totalCharge - ($totalPaid + $totalAdjustment + $withheld);
            $temp = array();
            $temp['totalPaid'] = $totalPaid;
            $temp['totalAdjustment'] = $totalAdjustment;
            //balance also known as ArBalance
            $temp['balance'] =$this->numberFormater($balance);
            return $temp;
        } else {
            $temp = array();
            $temp['totalPaid'] = '0.00';
            $temp['totalAdjustment'] = '0.00';
            $temp['balance'] = '0.00';
            return $temp;
        }
        */
    }

    public function patientPaymentProcessHandler($request)
    {
        $paymentType = $request['payment_type'];
        $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
        if (!empty($paymentType)) {
            $txn_for = 'Patient_' . $paymentType;
            $response = '';
            
            switch ($paymentType) {
                case 'Payment':
                    $response = $this->storePatientsPayments($request);
                    break;

                case 'Refund':
                    $response = $this->doPatientReFundProcess($request);
                    break;

                case 'Adjustment':
                    $response = $this->doAdjustmentProcess($request);
                    break;

                case 'Credit Balance':
                    $response = $this->storeCreditBalancePayment($request);
                    break;
            }
            ClaimInfoV1::updateClaimStatus($claim_id, $txn_for);
            return $response;
        }
    }

    public function storePatientsPayments($request)
    {
        try {
            
            $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            $chargeV1ApiController = new ChargeV1ApiController();

            if ($request['payment_mode'] == 'Check' && $request['payment_detail_id'] == '') {
                $checkNoStatus = PMTInfoV1::findCheckExistsOrNot($request['check_no'], 'Patient', $request['payment_mode'], 'patientPayment', $patient_id);
                if ($checkNoStatus) {
                    return Response::json(array('status' => 'error', 'message' => "Check number alredy exist", 'data' => $request['patient_id']));
                }
            }
            if (!empty($request['claim_id'])) {
                DB::beginTransaction();
                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
                $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
                $request['claimInfoData'] = ClaimInfoV1::getClaimInsuranceDetails($claim_id);
                $claim_number_data = $request['claimInfoData']->claim_number;
				
				/* Updating Patient budget plan    */
				$patientBudgetInfo = PatientBudget::where('status','Active')->where('patient_id',$patient_id)->whereNull('deleted_at');
				$budgetCount = $patientBudgetInfo->count();
				$patientBudgetDetails = $patientBudgetInfo->get()->first();
				if($budgetCount != 0){
					$budget_balance = $patientBudgetDetails->budget_balance - $request['payment_amt'];
					if($budget_balance < 0)
						$budget_balance = 0;
					$patientBudgetInfo->update(['budget_balance'=>$budget_balance]);
				}
				
                //store the payment and get the id
                $paymentModeData = array(
                    'pmt_amt' => $request['payment_amt'],
                    'pmt_mode' => $request['payment_mode'],
                    'reference' => $request['reference'],
                    'pmt_type' => 'Payment',
                    'source' => 'posting',
                    'pmt_method' => 'Patient',
                    'insurance_id' => $insuranceId
                );

                $claimCptInfoIds = array();
                $patPaid = 0;
                if (isset($request['ids'])) {
                    for ($j = 0; $j < count($request['ids']); $j++) {
                        $patPaid +=$request['patient_paid'][$j];
                        $temp = array(
                            "claimCptId" => $request['ids'][$j],
                            "patientPaid" => $request['patient_paid'][$j]
                        );
                        array_push($claimCptInfoIds, $temp);
                    }
                }
                if ($request['payment_detail_id'] == '') {
                    $docInsertType = '';
                    $paymentId = $chargeV1ApiController->createPaymentInformation($paymentModeData, $patient_id, $claim_id, $request);
                } else {
                    $docInsertType = 'New Claim';
                    $paymentId = Helpers::getEncodeAndDecodeOfId($request['payment_detail_id'], 'decode');
                }

                if ((isset($request['eob_id']) && $request['eob_id']) || $docInsertType == 'New Claim') {
                    $this->paymentPostingDocumentUpload($request,$docInsertType,$patient_id,$paymentId,$claim_id);
                }

                $paymentClaimTx_DesIds = $chargeV1ApiController->createPaymentClaimTxDetails($paymentId, $claim_id, $patient_id, $paymentModeData, $request);

                $chargeV1ApiController->createPaymentClaimCptTxDetails($paymentId, $claim_id, $claimCptInfoIds, 'CREATE', $paymentClaimTx_DesIds, $paymentModeData, $request);
                // If statement is hold choosen the we need to update at patient table that should reflect on demographics
                if (isset($request['is_hold_statement'])) {

                    $update_arr['statements'] = "Hold";
                    if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
                        $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
                    } else {
                        $update_arr['hold_release_date'] = "0000-00-00";
                    }

                    if (isset($request['hold_reason']) && $request['hold_reason'] != ""){
                        $update_arr['hold_reason'] = $request['hold_reason'];
                    } else {
                        $update_arr['hold_reason'] = 0;
                    }
                    Patient::where('id', $patient_id)->update($update_arr);

                } else {
                    $update_arr['statements'] = "Yes";
                    $update_arr['hold_release_date'] = "0000-00-00";
                    $update_arr['hold_reason'] = 0;
                    Patient::where('id', $patient_id)->where('statements', 'Hold')->update($update_arr);
                }
                //In the Current-Check Unapplied amount all goes to current patient_Wallet                
                if ($request['unapplied_amt'] > 0 && $request['next'] == '') {
                    $walletData = array(
                        'patient_id' => $patient_id,
                        'pmt_info_id' => $paymentId,
                        'tx_type' => 'Credit',
                        'amt_pop' => ($request['unapplied_amt'] != $request['payment_amt']) ? $request['unapplied_amt'] : ($request['payment_amt'] - $patPaid),
                        'wallet_ref_id' => $paymentId,
                        'claimId' => $claim_id
                    );
                    $this->storeWalletData($walletData, false, false);
                }
                
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.payment_post_msg"), 'data' => $patient_id, 'payment_id' => $paymentId));
                //dd($paymentId);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $resp = $this->showErrorResponse("storePaymentspaymentsV1ApiController", $e);
        }
    }

    public function storeCreditBalancePayment($request)
    {
        try {
            $chargeV1ApiController = new ChargeV1ApiController();
            if (!empty($request['claim_id'])) {
                $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
				// Have to check patient balance available for txn
                $patient_pmt = isset($request['patient_paid'][0]) ? $request['patient_paid'][0] : 0;
                $walletAmt = PMTWalletV1::getPatientWalletData($patient_id);
                // Check patient credit balance exist
                if($walletAmt < $patient_pmt) {
                    return Response::json(array('status' => 'error', 'message' => "Wallet Balance Unavailable", 'data' => $request['patient_id']));
                }

                DB::beginTransaction();
                //  store the payment and get the id
                if (isset($request['ids'])) {
                    $allcptWalletTxDesIds = array();
                    $pmtId = 0;
                    //create claimTx Desc
                    $newclaimDes = array(
                        "claim_info_id" => $claim_id,
                        "pmt_id" => '0',
                        'resp' => $this->getClaimResponsibility($claim_id),
                        'txn_id' => '',
                    );
                    $txnFor = 'Patient Credit Balance';
                    $claimTxDesId = $this->storeClaimTxnDesc($txnFor, $newclaimDes);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$claimTxDesId)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $allPmtClaimTxId = array();
                    for ($j = 0; $j < count($request['ids']); $j++) {
                        $claimCptInfoIds = array();
                        $paymentId = 0;
                        $temp = array(
                            "claimId" => $claim_id,
                            "claimCptId" => $request['ids'][$j],
                            "patientPaid" => $request['patient_paid'][$j],
                            "paymentId" => $paymentId,
                            "wallettransactionIds" => $this->findWalletTransactionId($patient_id, $request['patient_paid'][$j], $paymentId)
                        );
                        if ($temp['wallettransactionIds']['status']) {
                            array_push($claimCptInfoIds, $temp);
                        }
                        $cptWalletTxDesIds = $this->createCreditBalanceClaimCptTxDetails($claim_id, $claimCptInfoIds, 'CREATE', $claimTxDesId, $patient_id);
                        //\Log::info("CPT Wallet DESC IDs"); \Log::info($cptWalletTxDesIds);
                        if(!empty($cptWalletTxDesIds)) {
                            foreach ($cptWalletTxDesIds['claimTxIds'] as $clamTxId) {
                                array_push($allPmtClaimTxId, $clamTxId);
                            }
                            foreach ($cptWalletTxDesIds['walletTxIds'] as $cptWalletTxIds) {
                                //\Log::info("Payment ID ".@$cptWalletTxIds['pmt_info_id']);
                                if(isset($cptWalletTxIds['pmt_info_id']) && $cptWalletTxIds['pmt_info_id'] != 0){
                                    //\Log::info("Collecting payment ID ".$cptWalletTxIds['pmt_info_id']);
                                    $pmtId = $cptWalletTxIds['pmt_info_id'];
                                }
                                array_push($allcptWalletTxDesIds, $cptWalletTxIds);
                            }
                        }
                    }
                    // Update payment id for zero txn also with payment id, to handle. Issue #MR-1276 - Start
                    if($pmtId != 0 && !empty($allPmtClaimTxId)) {
                        $zeroTxn = PMTClaimTXV1::whereIn('id',$allPmtClaimTxId)->where('payment_id', 0)->get();
                        if(!empty($zeroTxn)){
                            foreach($zeroTxn as $tTxn){
                                PMTClaimTXV1::where('id', $tTxn->id)->update(['payment_id' => $pmtId]);
                                PMTClaimCPTTXV1::where('pmt_claim_tx_id', $tTxn->id)->update(['payment_id' => $pmtId]);
                            }
                        }
                    }
                    // Update payment id for zero txn also with payment id, to handle. Issue #MR-1276 - End
                    ClaimTXDESCV1::where('id', $claimTxDesId)
                        ->update(["value_2" => implode(",", $allPmtClaimTxId), "value_1" => json_encode($allcptWalletTxDesIds)]);
                }

                if (isset($request['is_hold_statement'])) {
                    $update_arr['statements'] = "Hold";
                    if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
                        $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
                    } else {
                        $update_arr['hold_release_date'] = "0000-00-00";
                    }

                    if (isset($request['hold_reason']) && $request['hold_reason'] != ""){
                        $update_arr['hold_reason'] = $request['hold_reason'];
                    } else {
                        $update_arr['hold_reason'] = 0;
                    }
                    Patient::where('id', $patient_id)->update($update_arr);
                } else {
                    $update_arr['statements'] = "Yes";
                    $update_arr['hold_release_date'] = "0000-00-00";
                    $update_arr['hold_reason'] = 0;
                    Patient::where('id', $patient_id)->where('statements', 'Hold')->update($update_arr);                    
                }
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.credit_post_msg"), 'data' => $patient_id, 'payment_id' => $paymentId));
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on storeCreditBalancePayment. Error msg " . $e->getMessage());
            DB::rollBack();
            $resp = $this->showErrorResponse("storeCreditBalancePayment", $e);
        }
    }

    public function doPatientReFundProcess($request)
    {
        try {
            $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
            $chargeV1ApiController = new ChargeV1ApiController();
            if ($request['payment_mode'] == 'Check' && $request['payment_detail_id'] == '') {
                $checkNoStatus = PMTInfoV1::findCheckExistsOrNot($request['check_no'], 'Patient', $request['payment_mode'], 'patientPayment', $patient_id);
                if ($checkNoStatus) {
                    return Response::json(array('status' => 'error', 'message' => "Check number alredy exist", 'data' => $request['patient_id']));
                }
            }
            if (!empty($request['claim_id'])) {
                DB::beginTransaction();
                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
                //store the payment and get the id
                $paymentModeData = array(
                    'pmt_amt' => $request['payment_amt'],
                    'pmt_mode' => $request['payment_mode'],
                    'reference' => $request['reference'],
                    'pmt_type' => 'Refund',
                    'source' => 'posting',
                    'pmt_method' => 'Patient',
                    'insurance_id' => $insuranceId
                );

                $claimCptInfoIds = array();
                if (isset($request['ids'])) {
                    for ($j = 0; $j < count($request['ids']); $j++) {
                        $patientPaid = $request['patient_paid'][$j];
                        /*@commented  becase of desc need all cpt wheter is not filling
                         * if ($patientPaid == "0.00") {
                            continue;
                        }*/
                        $temp = array(
                            "claimCptId" => $request['ids'][$j],
                            "patientPaid" => -1 * abs($patientPaid)
                        );
                        array_push($claimCptInfoIds, $temp);
                    }
                }
                if ($request['payment_detail_id'] == '') {
                    $docInsertType = '';
                    $paymentId = $chargeV1ApiController->createPaymentInformation($paymentModeData, $patient_id, $claim_id, $request);
                } else {
                    $docInsertType = 'New Claim';
                    $paymentId = Helpers::getEncodeAndDecodeOfId($request['payment_detail_id'], 'decode');
                }

                if ((isset($request['eob_id']) && $request['eob_id']) || $docInsertType == 'New Claim') {
                    //Document::where('id', '=', $request['eob_id'])->update(['payment_id' => $paymentId, 'main_type_id' => $paymentId]);
                    $this->paymentPostingDocumentUpload($request,$docInsertType,$patient_id,$paymentId,$claim_id);
                }

                $paymentClaimTx_DesIds = $chargeV1ApiController->createPaymentClaimTxDetails($paymentId, $claim_id, $patient_id, $paymentModeData, $request);
                $chargeV1ApiController->createPaymentClaimCptTxDetails($paymentId, $claim_id, $claimCptInfoIds, 'CREATE', $paymentClaimTx_DesIds, $paymentModeData, $request);
                //if wallet_refund_amount is given then

                if (!empty($request['wallet_refund']) && $request['wallet_refund'] > '0.00' && $request['next'] == '') {
                    $paymentModeDataRefundFromWallet = array(
                        'pmt_amt' => -1 * abs($request['wallet_refund']),
                        'pmt_mode' => '',
                        'reference' => 'Refund from Wallet',
                        'pmt_type' => 'Refund',
                        'source' => 'refundwallet',
                        'pmt_method' => 'Patient',
                        'insurance_id' => $insuranceId
                    );
                    $walletRefId = $chargeV1ApiController->createPaymentInformation($paymentModeDataRefundFromWallet, $patient_id, $claim_id, $request);

                    $newwalletData = array(
                        'patient_id' => $patient_id,
                        'claimId' => $claim_id,
                        'pmt_info_id' => $paymentId,
                        'tx_type' => 'Debit',
                        'amt_pop' => -1 * abs($request['wallet_refund']),
                        'wallet_ref_id' => $walletRefId,
                        'used_amount' => $request['wallet_refund']
                    );
                    $resultStatus = $this->storeWalletData($newwalletData, false);
                }
                if (isset($request['is_hold_statement'])) {
                    //Patient::where('id', $patient_id)->update(['statements' => 'Hold']);
                    $update_arr['statements'] = "Hold";
                    if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
                        $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
                    } else {
                        $update_arr['hold_release_date'] = "0000-00-00";
                    }

                    if (isset($request['hold_reason']) && $request['hold_reason'] != ""){
                        $update_arr['hold_reason'] = $request['hold_reason'];
                    } else {
                        $update_arr['hold_reason'] = 0;
                    }
                    Patient::where('id', $patient_id)->update($update_arr);

                } else {
                    $update_arr['statements'] = "Yes";
                    $update_arr['hold_release_date'] = "0000-00-00";
                    $update_arr['hold_reason'] = 0;
                    Patient::where('id', $patient_id)->where('statements', 'Hold')->update($update_arr);
                }
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.refund_claim_msg"), 'data' => $patient_id, 'payment_id' => $paymentId));
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on doPatientReFundProcess. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("doPatientReFundProcess", $e);            
        }
    }

    public function doAdjustmentProcess($request)
    {
        try {
            $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
            $chargeV1ApiController = new ChargeV1ApiController();
            if (!empty($request['claim_id'])) {
                //dd($request);
                DB::beginTransaction();

                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');

                //store the payment and get the id
                $paymentModeData = array(
                    'pmt_amt' => isset($request['payment_amt']) ? $request['payment_amt'] : 0,
                    'pmt_type' => 'Adjustment',
                    'pmt_mode' => 'Adjustment',
                    'reference' => $request['reference'],
                    'source' => 'posting',
                    'pmt_method' => 'Patient',
                    'insurance_id' => $insuranceId
                );

                $claimCptInfoIds = array();
                if (isset($request['ids'])) {
                    for ($j = 0; $j < count($request['ids']); $j++) {
                        $temp = array(
                            "claimCptId" => $request['ids'][$j],
                            "writeoff" => $request['patient_paid'][$j],
                        );
                        array_push($claimCptInfoIds, $temp);
                    }
                }

                $paymentId = $chargeV1ApiController->createPaymentInformation($paymentModeData, $patient_id, $claim_id, $request);
                $paymentClaimTx_DesIds = $chargeV1ApiController->createPaymentClaimTxDetails($paymentId, $claim_id, $patient_id, $paymentModeData, $request);
                //$chargeV1ApiController->createPaymentClaimCptTxDetails($paymentId, $claim_id, $claimCptInfoIds, 'CREATE',$pmt_claim_txID);
                $chargeV1ApiController->createPaymentClaimCptTxDetails($paymentId, $claim_id, $claimCptInfoIds, 'CREATE', $paymentClaimTx_DesIds, $paymentModeData, $request);
                if (isset($request['is_hold_statement'])) {
                    //Patient::where('id', $patient_id)->update(['statements' => 'Hold']);
                    $update_arr['statements'] = "Hold";
                    if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
                        $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
                    } else {
                        $update_arr['hold_release_date'] = "0000-00-00";
                    }

                    if (isset($request['hold_reason']) && $request['hold_reason'] != ""){
                        $update_arr['hold_reason'] = $request['hold_reason'];
                    } else {
                        $update_arr['hold_reason'] = 0;
                    }
                    Patient::where('id', $patient_id)->update($update_arr);

                } else {
                    $update_arr['statements'] = "Yes";
                    $update_arr['hold_release_date'] = "0000-00-00";
                    $update_arr['hold_reason'] = 0;
                    Patient::where('id', $patient_id)->where('statements', 'Hold')->update($update_arr);
                }
                DB::commit();

                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.adjustment_post_msg"), 'data' => $patient_id, 'payment_id' => $paymentId));
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("doAdjustmentProcess", $e);
        }
    }

    public function doRefundFromWallet($request)
    {
        try {
            $chargeV1ApiController = new ChargeV1ApiController();
            $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            $curentPatientWalletAmount = PMTWalletV1::getPatientWalletData($patient_id);
            if ($curentPatientWalletAmount < $request['payamt']) {
                return Response::json(array('status' => 'error', 'message' => Lang::get("practice/patients/payments.validation.amountnotavailable"), 'data' => ""));
            } else {
                $paymentModeDataRefundFromWallet = array(
                    // 'pmt_amt' => -1 * abs($request['payamt']),
                    'pmt_amt' =>  abs($request['payamt']),
                    'reference' => 'Refund from Wallet',
                    'pmt_type' => 'Refund',
                    'source' => 'refundwallet',
                    'pmt_mode' => 'Check',
                    'pmt_method' => 'Patient',
                    'insurance_id' => ''
                );
                $paymentId = $chargeV1ApiController->createPaymentInformation($paymentModeDataRefundFromWallet, $patient_id, ' ', $request);
                $walletTnxIds = $this->findWalletTransactionId($patient_id, $request['payamt'], $paymentId);
                if ($walletTnxIds['status']) {
                    $curentPatientWalletAmount = PMTWalletV1::getPatientWalletData($patient_id);
                } else {
                    $curentPatientWalletAmount = 0.00;
                }
                if ($walletTnxIds['status']) {
                    return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.refund_post_msg"), 'data' => $curentPatientWalletAmount, 'payment_id' => $paymentId));
                }
            }

        } catch (Exception $e) {
            $resp = $this->showErrorResponse("doRefundFromWallet", $e);
        }
    }

    public function findWalletTransactionId($patientId, $paidAmount, $payment_Wallet_ref_Id)
    {
        try {
            if ($paidAmount > 0) {
                $balanceAmount = DB::raw('SUM(amount) as amount');
                $walletData = PMTWalletV1::select('*', $balanceAmount)
                    ->where('patient_id', $patientId)
                    ->having('amount', '>', 0)->groupBy("pmt_info_id")
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->toArray();
                $statusArr = array();
                if (!empty($walletData)) {
                    $incomingPmtAmount = $paidAmount;
                    $remainingPmtAmount = $paidAmount;
                    $transactionArr = array();
                    if (!empty($paidAmount) && $paidAmount != '0.00') {
                        for ($i = 0; $i < count($walletData); $i++) {
                            $walletAmount = $walletData[$i]['amount'];
                            $transactionId = $walletData[$i]['id'];
                            $patient_id = $walletData[$i]['patient_id'];
                            $pmt_info_id = $walletData[$i]['pmt_info_id'];
                            /* if ($walletAmount == '0.00') {
                                 continue;
                             }*/
                            if ($incomingPmtAmount >= $walletAmount) {
                                $remainingPmtAmount = $remainingPmtAmount - $walletAmount;
                                $incomingPmtAmount = $incomingPmtAmount - $walletAmount;
                                $temp = array();
                                $temp ['amountApplied'] = $walletAmount;
                                $temp ['pmt_info_id'] = $pmt_info_id;
                                $temp ['patient_id'] = $patient_id;
                                $temp ['wallet_Ref_Id'] = $payment_Wallet_ref_Id;
                                $temp ['walletid'] = $transactionId;
                                $temp ['transactionId'] = $this->updateAppliedAmount($temp);
                                array_push($transactionArr, $temp);
                            } else if ($incomingPmtAmount < $walletAmount) {
                                $remainingPmtAmount = $remainingPmtAmount - $remainingPmtAmount;
                                $incomingPmtAmount = $incomingPmtAmount - $remainingPmtAmount;
                                if ($i == 0) {
                                    $incomingPmtAmount = $paidAmount;
                                }
                                $temp = array();
                                $temp ['walletid'] = $transactionId;
                                $temp ['amountApplied'] = $incomingPmtAmount;
                                $temp ['pmt_info_id'] = $pmt_info_id;
                                $temp ['wallet_Ref_Id'] = $payment_Wallet_ref_Id;
                                $temp ['patient_id'] = $patient_id;
                                $temp ['transactionId'] = $this->updateAppliedAmount($temp);
                                array_push($transactionArr, $temp);
                            }
                            if ($remainingPmtAmount == 0) {
                                break;
                            }
                        }
                        if (!empty($transactionArr)) {
                            $statusArr['status'] = True;
                            $statusArr['data'] = $transactionArr;
                        } else {
                            $statusArr['status'] = false;
                            $statusArr['data'] = [];
                        }
                        return $statusArr;
                    } else {
                        $statusArr['status'] = false;
                        $statusArr['data'] = [];
                        return $statusArr;
                    }
                }
            } else {
                $statusArr['status'] = true;
                $statusArr['data'] = [];
                return $statusArr;
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("findWalletTransactionId", $e);
        }
    }

    public function updateAppliedAmount($data)
    {
        try {
            global $status;
            $status = 0;
            $newWalData['patient_id'] = $data['patient_id'];
            $newWalData['pmt_info_id'] = $data['pmt_info_id'];
            $newWalData['wallet_Ref_Id'] = $data['wallet_Ref_Id'];
            $newWalData['amount'] = $data['amountApplied'] * -1;
            $newWalData['tx_type'] = 'Debit';
            $resultSet = PMTWalletV1::create($newWalData);
            if ($resultSet) {
                PMTInfoV1::updatePaymettAmoutUsed($newWalData['pmt_info_id'], $data['amountApplied']);
                $status = $resultSet->id;
            } else {
                $status = 0;
            }
            return $status;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateAppliedAmount", $e);
        }
    }

    public function insurancePaymentProcessHandler($request)
    {   //\Log::info("Insurance Pmt Process Handler");    \Log::info($request);
        $paymentType = $request['payment_type'];
        $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
        $txn_for = 'Insurance_' . $paymentType;
        if (!empty($paymentType)) {
            switch ($paymentType) {
                case 'Payment':
                    $response = $this->storeInsurancePayments($request);
                    break;

                case 'Refund':
                    $response = $this->doInsuranceReFundProcess($request);
                    break;

                case 'Adjustment':
                    $response = $this->doInsuranceAdjustmentProcess($request);
                    break;
            }
            ClaimInfoV1::updateClaimStatus($claim_id, $txn_for);
            //change the claimStatus
            if (!empty($request['status'])) {
                ClaimInfoV1::where('id', $claim_id)
                    ->update(['status' => $request['status']]);
            }
			if(isset($request['is_send_paid_amount']) && trim($request['is_send_paid_amount']) == 'on'){
				$request['is_send_paid_amount'] = 'Yes';
				ClaimInfoV1::where('id', $claim_id)
                    ->update(['is_send_paid_amount' => $request['is_send_paid_amount']]);
			}else{
				$request['is_send_paid_amount'] = 'No';
				ClaimInfoV1::where('id', $claim_id)
                    ->update(['is_send_paid_amount' => $request['is_send_paid_amount']]);
			}
			
           $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
           $paymentHoldReson = $request['payment_hold_reason']; 
            if (!empty($paymentHoldReson)) {
                ClaimInfoV1::where('id', $claim_id)
                    ->update(['payment_hold_reason' => $paymentHoldReson]);
               if($paymentHoldReson == 'patient'){    
                   $update_arr['statements'] = 'Hold';
                   if (isset($request['hold_release_date']) && ($request['hold_release_date'] != "0000-00-00") && ($request['hold_release_date'] != "")){
                        $update_arr['hold_release_date'] = date('Y-m-d', strtotime($request['hold_release_date']));
                    } else {
                        $update_arr['hold_release_date'] = "0000-00-00";
                    }

                    if (isset($request['hold_reason']) && $request['hold_reason'] != ""){
                        $update_arr['hold_reason'] = $request['hold_reason'];
                    } else {
                        $update_arr['hold_reason'] = 0;
                    }
                   Patient::where('id', $patient_id)->update($update_arr);
               }  
            } else {                
                // While posting if unchecked have to release patient as well claim.
                ClaimInfoV1::where('id', $claim_id)->update(['payment_hold_reason' => '']);
                Patient::where('id', $patient_id)->update(['statements' => 'Yes', 'hold_release_date' => '0000-00-00','hold_reason' => 0]);                
            }
            return $response;
        }
    }

    public function storeInsurancePayments($request)
    {
        try {
            //\Log::info("\n Store Insurance pmt called \n"); \Log::info($request);
            $chargeV1ApiController = new ChargeV1ApiController();
            $check_val = (is_numeric($request['check_no']) && $request['check_no'] / 1 == 0) ? 0 : 1;
            if (empty($request['payment_detail_id'])) {
                $checkNoStatus = PMTInfoV1::findCheckExistsOrNot($request['check_no'], 'Insurance', $request['payment_mode'], 'insurancePayment', '');
                if ($check_val == 0) {
                    $checkNoStatus = false;
                }
                if ($checkNoStatus) {
                    return Response::json(array('status' => 'error', 'message' => "Check number alredy exist", 'data' => $request['patient_id']));
                }
            }
            if (!empty($request['claim_id'])) {
                $patientId = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
                $request['claimInfoData'] = ClaimInfoV1::getClaimInsuranceDetails($claim_id);
                $claim_number_data = $request['claimInfoData']->claim_number;
                DB::beginTransaction();
                $paymentModeData = array(
                    'pmt_amt' => $request['payment_amt'],
                    'pmt_mode' => $request['payment_mode'],
                    'reference' => $request['reference'],
                    'pmt_type' => 'Payment',
                    'source' => 'posting',
                    'pmt_method' => 'Insurance',
                    'insurance_id' => $request['insurance_id']
                );
                if (!empty($request['payment_detail_id'])) {
                    $docInsertType = 'New Claim';
                    $paymentId = Helpers::getEncodeAndDecodeOfId($request['payment_detail_id'], 'decode');
                } else {
                    $docInsertType = '';
                    $paymentId = $chargeV1ApiController->createPaymentInformation($paymentModeData, $patientId, $claim_id, $request);
                    //\Log::info("Create pmt info called ".$paymentId);
                }
                // if we change the insurance from popup we change first then do transaction
                if ((isset($request['eob_id']) && $request['eob_id']) || $docInsertType == 'New Claim') { 
                    $this->paymentPostingDocumentUpload($request,$docInsertType,$patientId,$paymentId,$claim_id);
                }

                $pmtTxDatas = array(
                    'pmt_id' => $paymentId,
                    'txn_id' => ''
                );

                $respchage1 = array();
                if (!empty($request['change_insurance_category']) && (!empty($request['changed_insurance_id']))) {
                    $ccrresult = $this->changeTheClaimResponsibility($claim_id, $request['change_insurance_category'], $request['changed_insurance_id'], 'No', $pmtTxDatas);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$ccrresult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $temp['id'] = $ccrresult;
                    $temp['from'] = 'popUp';
                    array_push($respchage1, $temp);
                } else {
                    $temp['id'] = '';
                    $temp['from'] = 'popupNone';
                    array_push($respchage1, $temp);
                }

                $paymentClaimTx_DesIds = $this->createInsurancePaymentClaimTxDetails($paymentId, $claim_id, $patientId, $paymentModeData, $request);

                $pmtTxDatas = array_merge($pmtTxDatas, $paymentClaimTx_DesIds);

                $responsibilityDesArr = $this->changeResponsibilityHandler($request, $patientId, $claim_id, $pmtTxDatas);
                $responsibilityDesArr = array_merge($respchage1, $responsibilityDesArr);
                $this->createInsurancePamentClaimCptTxDetails($paymentId, $claim_id, $request, 'CREATE', $pmtTxDatas, $responsibilityDesArr);
                //add patientNotes
                if (!empty($request['content'])) {
                    $chargeV1ApiController->savePatientNotes($request['content'], $claim_id, $patientId);
                }
                //update ClaimSubmitted Count only in Insurance Payment
                $submit_count =ClaimInfoV1::where('id', $claim_id)->first();
                if($submit_count->claim_submit_count==0) {
                    // Last Submission Date must not change on Insurance Transaction
                    // Rev-1 Ref.MR-2890 - Ravi - 19-09-2019
                    ClaimInfoV1::where('id', $claim_id)->update(['claim_submit_count' => 1, 'submited_date' => date("Y-m-d H:i:s"), 'last_submited_date' => date("Y-m-d  H:i:s")]);
                }               
                DB::commit();
                $chargeV1ApiController->finalClaimBalanceUpdate(['claim_id' => $claim_id]);
                return Response::json(array('status' => 'success', 'message' => 'Payment posted successfully. - Claim No. : '.$claim_number_data, 'data' => $request['patient_id'], 'payment_id' => $paymentId));
            }
        } catch (Exception $e) {
            DB::rollBack();
            $resp = $this->showErrorResponse("storeInsurancePayments", $e);
            \Log::info("Exception catched on storeInsurancePayments. Error msg " . $e->getMessage());
        }
    }

    public function doInsuranceReFundProcess($request)
    {
        try {
            $chargeV1ApiController = new ChargeV1ApiController();
            $check_val = (is_numeric($request['check_no']) && $request['check_no'] / 1 == 0) ? 0 : 1;
            if (empty($request['payment_detail_id'])) {
                $checkNoStatus = PMTInfoV1::findCheckExistsOrNot($request['check_no'], 'Insurance', $request['payment_mode'], 'insurancePayment', '');
                if ($check_val == 0) {
                    $checkNoStatus = false;
                }
                if ($checkNoStatus) {
                    return Response::json(array('status' => 'error', 'message' => "Check number alredy exist", 'data' => $request['patient_id']));
                }
            }
            if (!empty($request['claim_id'])) {
                $patientId = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
                $request['claimInfoData'] = ClaimInfoV1::getClaimInsuranceDetails($claim_id);
                DB::beginTransaction();
                /*
                 * Payment Mode is allways Check
                 * */
                $paymentModeData = array(
                    'pmt_amt' => $request['payment_amt'],
                    'pmt_mode' => 'Check',
                    'reference' => $request['reference'],
                    'pmt_type' => 'Refund',
                    'source' => 'posting',
                    'pmt_method' => 'Insurance',
                    'insurance_id' => $request['insurance_id']
                );
                if (!empty($request['payment_detail_id'])) {
                    $docInsertType = 'New Claim';
                    $paymentId = Helpers::getEncodeAndDecodeOfId($request['payment_detail_id'], 'decode');
                } else {
                    $docInsertType = '';
                    $paymentId = $chargeV1ApiController->createPaymentInformation($paymentModeData, $patientId, $claim_id, $request);
                }

                if ((isset($request['eob_id']) && $request['eob_id']) || $docInsertType == 'New Claim') {
                    //Document::where('id', '=', $request['eob_id'])->update(['payment_id' => $paymentId, 'main_type_id' => $paymentId]);
                    $this->paymentPostingDocumentUpload($request,$docInsertType,$patientId,$paymentId,$claim_id);
                }
                $pmtTxDatas = array(
                    'pmt_id' => $paymentId,
                    'txn_id' => ''
                );

                $respchage1 = array();
                if (!empty($request['change_insurance_category']) && (!empty($request['changed_insurance_id']))) {
                    $ccrresult = $this->changeTheClaimResponsibility($claim_id, $request['change_insurance_category'], $request['changed_insurance_id'], 'No', $pmtTxDatas);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$ccrresult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $temp['id'] = $ccrresult;
                    $temp['from'] = 'popUp';
                    array_push($respchage1, $temp);
                } else {
                    $temp['id'] = '';
                    $temp['from'] = 'popupNone';
                    array_push($respchage1, $temp);
                }


                $paymentClaimTx_DesIds = $this->createInsurancePaymentClaimTxDetails($paymentId, $claim_id, $patientId, $paymentModeData, $request);
                $pmtTxDatas = array_merge($pmtTxDatas, $paymentClaimTx_DesIds);

                $responsibilityDesArr = $this->changeResponsibilityHandler($request, $patientId, $claim_id, $pmtTxDatas);
                $responsibilityDesArr = array_merge($respchage1, $responsibilityDesArr);
                $this->createInsurancePamentClaimCptTxDetails($paymentId, $claim_id, $request, 'CREATE', $pmtTxDatas, $responsibilityDesArr);
                //add patientNotes
                if (!empty($request['content'])) {
                    $chargeV1ApiController->savePatientNotes($request['content'], $claim_id, $patientId);
                }
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => 'Refund from claim initiated successfully', 'data' => $request['patient_id'], 'payment_id' => $paymentId));
            }
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info("Exception catched on doInsuranceReFundProcess. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("doInsuranceReFundProcess", $e);
        }
    }

    public function doInsuranceAdjustmentProcess($request)
    {
        try {
            if (!empty($request['claim_id'])) {
                DB::beginTransaction();
                $claim_id = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
                $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
                $chargeV1ApiController = new ChargeV1ApiController();
                $request['claimInfoData'] = ClaimInfoV1::getClaimInsuranceDetails($claim_id);
                //store the payment and get the id
                $paymentModeData = array(
                    'pmt_amt' => isset($request['payment_amt']) ? $request['payment_amt'] : 0,
                    'pmt_type' => 'Adjustment',
                    'pmt_mode' => 'Adjustment',
                    'reference' => $request['reference'],
                    'source' => 'posting',
                    'pmt_method' => 'Insurance',
                    'insurance_id' => $request['insurance_id']
                );
                $claimCptInfoIds = array();
                if (isset($request['ids'])) {
                    for ($j = 0; $j < count($request['ids']); $j++) {
                        $temp = array(
                            "claimCptId" => Helpers::getEncodeAndDecodeOfId($request['ids'][$j], 'decode'),
                            "writeoff" => $request['adjustment'][$j]
                        );
                        array_push($claimCptInfoIds, $temp);
                    }
                }
                if (!empty($request['payment_detail_id'])) {
                    $docInsertType = 'New Claim';
                    $paymentId = Helpers::getEncodeAndDecodeOfId($request['payment_detail_id'], 'decode');
                } else {
                    $docInsertType = '';
                    $paymentId = $chargeV1ApiController->createPaymentInformation($paymentModeData, $patient_id, $claim_id, $request);
                }


                $pmtTxDatas = array(
                    'pmt_id' => $paymentId,
                    'txn_id' => ''
                );
                if ((isset($request['eob_id']) && $request['eob_id'])  || $docInsertType == 'New Claim') {
                    //Document::where('id', '=', $request['eob_id'])->update(['payment_id' => $paymentId, 'main_type_id' => $paymentId]);
                    $this->paymentPostingDocumentUpload($request,$docInsertType,$patient_id,$paymentId,$claim_id);
                }

                $respchage1 = array();
                if (!empty($request['change_insurance_category']) && (!empty($request['changed_insurance_id']))) {
                    $ccrresult = $this->changeTheClaimResponsibility($claim_id, $request['change_insurance_category'], $request['changed_insurance_id'], 'No', $pmtTxDatas);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$ccrresult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $temp['id'] = $ccrresult;
                    $temp['from'] = 'popUp';
                    array_push($respchage1, $temp);
                } else {
                    $temp['id'] = '';
                    $temp['from'] = 'popupNone';
                    array_push($respchage1, $temp);
                }
                $paymentClaimTx_DesIds = $this->createInsurancePaymentClaimTxDetails($paymentId, $claim_id, $patient_id, $paymentModeData, $request);
                $pmtTxDatas = array_merge($pmtTxDatas, $paymentClaimTx_DesIds);

                $responsibilityDesArr = $this->changeResponsibilityHandler($request, $patient_id, $claim_id, $pmtTxDatas);
                $responsibilityDesArr = array_merge($respchage1, $responsibilityDesArr);
                $this->createInsurancePamentClaimCptTxDetails($paymentId, $claim_id, $request, 'CREATE', $pmtTxDatas, $responsibilityDesArr);
                //add patientNotes
                if (!empty($request['content'])) {
                    $chargeV1ApiController->savePatientNotes($request['content'], $claim_id, $patient_id);
                }

                //update ClaimSubmitted Count only in Insurance Adjustment 
                $submit_count =ClaimInfoV1::where('id', $claim_id)->first();
                if($submit_count->claim_submit_count == 0) {
                    // Last Submission Date must not change on Insurance Transaction
                    // Rev-1 Ref.MR-2890 - Ravi - 19-09-2019
                    ClaimInfoV1::where('id', $claim_id)->update(['claim_submit_count' => 1, 'submited_date' =>date("Y-m-d H:i:s"), 'last_submited_date' => date("Y-m-d  H:i:s")]);
                }               
                // Update submit count process end

                DB::commit();
                $chargeV1ApiController->finalClaimBalanceUpdate(['claim_id' => $claim_id]);
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.adjustment_post_msg"), 'data' => $request['patient_id'], 'payment_id' => $paymentId));
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on doInsuranceAdjustmentProcess. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("doInsuranceAdjustmentProcess", $e);
        }
    }

    public function createInsurancePaymentClaimTxDetails($paymentId, $claim_id, $patientId, $datas, $request)
    {   //\Log::info("Create Ins PmtClaim Tx Details PMT #".$paymentId." Claim #".$claim_id." Pat #".$patientId);
        //\Log::info($datas); \Log::info($request);
        try {
            if ($datas) {
                $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
                //$claimInfoData = $request['claimInfoData'];
                if(isset($request['pmt_post_ins_cat']) && $request['pmt_post_ins_cat'] != '')
                    $ins_category = $request['pmt_post_ins_cat'];
                elseif(isset($request['other_pmt_ins_cat']) && $request['other_pmt_ins_cat'] != '')
                    $ins_category = $request['other_pmt_ins_cat'];
                else 
                    $ins_category = $this->getClaimInsuranceCategory($request['claim_insurance_id'], $insuranceId, $patientId, @$request['change_insurance_category']);

                $newPmtClaimTx = array(
                    "payment_id" => $paymentId,
                    "claim_id" => $claim_id,
                    "pmt_method" => $datas['pmt_method'],
                    "pmt_type" => $datas['pmt_type'],
                    "patient_id" => $patientId,
                    "payer_insurance_id" => $insuranceId,
                    "ins_category" => $ins_category, //$this->getClaimInsuranceCategory($request['claim_insurance_id'], $insuranceId, $patientId, @$request['change_insurance_category']),
                    "claim_insurance_id" => $request['claim_insurance_id'],
                    "posting_date" => date("Y-m-d"),
                    "created_by" => Auth::user()->id
                );

                $resultSet = PMTClaimTXV1::create($newPmtClaimTx);
                //create Description
                $newclaimDesrecption = array(
                    "claim_info_id" => $claim_id,
                    "pmt_id" => $paymentId,
                    'resp' => $this->getClaimResponsibility($claim_id),
                    'denials_code' => implode(",", array_values(array_filter($request['remarkcode']))),
                    'txn_id' => $resultSet->id,
                    'value_1' => $insuranceId // which insurance is give the amount
                );

                $txnFor = $datas['pmt_method'] . ' ' . $datas['pmt_type'];
                $claimDesCreateStatus = $this->storeClaimTxnDesc($txnFor, $newclaimDesrecption);
                if(isset($request['backDate'])){
                    ClaimTXDESCV1::where('id',$claimDesCreateStatus)->update(['created_at' => $this->dateformater($request['backDate'])]);
                }

                /* if ($request['status'] == 'Denied') {
                     //do denial_Desc
                     $newDenials = array(
                         "claim_info_id" => $claim_id,
                         "pmt_id" => $paymentId,
                         'txn_id' => $resultSet->id,
                         'denials_code' => implode(",", array_values(array_filter($request['remarkcode']))),
                         'resp' => $this->getClaimResponsibility($claim_id)
                     );
                     $this->storeClaimTxnDesc('Denials', $newDenials);
                 }*/
                return ['txn_id' => $resultSet->id, "claimTxnDescId" => $claimDesCreateStatus];
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on createInsurancePaymentClaimTxDetails. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("createInsurancePaymentClaimTxDetails", $e);
        }
    }

    public function createInsurancePamentClaimCptTxDetails($paymentId, $claim_id, $datas, $flag, $paymentClaimTx_DesIds, $claimLevelResChangArr)
    {
        try {
            //dd($datas);
            $newPmtCptTx = array();
            if ($datas) {
                $ids = $datas['ids'];
                $idCount = count($ids);
                $pmt_clmTxId = $paymentClaimTx_DesIds['txn_id'];
                $lastRecord = $idCount - 1;
                for ($i = 0; $i < $idCount; $i++) {
                    $paymentType = $datas['payment_type'];
                    $claim_cpt_info_id = Helpers::getEncodeAndDecodeOfId($ids[$i], 'decode');
                    if ($flag == "CREATE" && (!empty($paymentType))) {
                        //need to check when we create a new cpt_tx becase empty tx we no need......
                        $activeLineItem = $datas['active_lineitem'];
                        if (!array_key_exists($i, $activeLineItem)) {
                            ClaimCPTInfoV1::where('id', $claim_cpt_info_id)->update(['is_active' => 0]);
                        } else {
                            ClaimCPTInfoV1::where('id', $claim_cpt_info_id)->update(['is_active' => 1]);
                        }

                        if ($paymentType == 'Payment') {
                            $newPmtCptTx = array(
                                "payment_id" => $paymentId,
                                "claim_id" => $claim_id,
                                "pmt_claim_tx_id" => $pmt_clmTxId,
                                "claim_cpt_info_id" => $claim_cpt_info_id,
                                "allowed" => $datas['cpt_allowed_amt'][$i],
                                "deduction" => $datas['deductable'][$i],
                                "copay" => $datas['co_pay'][$i],
                                "coins" => $datas['co_ins'][$i],
                                "withheld" => $datas['with_held'][$i],
                                "writeoff" => $datas['adjustment'][$i], //nedd to chage the name on the blade
                                "paid" => $datas['paid_amt'][$i],
                                "denial_code" => $datas['remarkcode'][$i], //nedd to chage the name on the blade
                                "created_by" => Auth::user()->id
                            );
                            $newPmtCptTx['paid'] = (!empty($newPmtCptTx['paid'])) ? $newPmtCptTx['paid'] : 0;
                            PMTInfoV1::where('id', $paymentId)->update(['amt_used' => DB::raw('amt_used +' . $newPmtCptTx['paid'] . '')]);
                            // Payments: Adjustment amount applied for multiple line items if applied for single line items which is incorrect
                            // Rev-1  Ref: MR-2805 - Ravi - 09-09-2019
                            $datas['adjustment_popup_Data'] = [];
                            // Adjustment details not proiveded then dont pass
                            if(isset($datas['with_held'][$i]) && $datas['with_held'][$i] != '0.00')  {
                                $datas['adjustment_popup_Data'] = array(
                                    'adj_reson'=>  $datas['adj_reason'][$i],
                                    'adj_reson_amount'=>  $datas['adj_reson_amount'][$i],
                                );
                            }

                        } elseif ($paymentType == 'Refund') {
                            $newPmtCptTx = array(
                                "payment_id" => $paymentId,
                                "claim_id" => $claim_id,
                                "pmt_claim_tx_id" => $pmt_clmTxId,
                                "claim_cpt_info_id" => $claim_cpt_info_id,
                                //"allowed" => $datas['cpt_allowed_amt'][$i],
                                "deduction" => $datas['deductable'][$i],
                                "copay" => $datas['co_pay'][$i],
                                "coins" => $datas['co_ins'][$i],
                                "withheld" => $datas['with_held'][$i],
                                "writeoff" => $datas['adjustment'][$i], //nedd to chage the name on the blade
                                "paid" => -1 * abs($datas['paid_amt'][$i]), //it's only allowed to enter the blade
                                "denial_code" => $datas['remarkcode'][$i], //nedd to chage the name on the blade
                                "created_by" => Auth::user()->id
                            );
                            //update amountUsed
                            $newPmtCptTx['paid'] = (!empty($newPmtCptTx['paid'])) ? $newPmtCptTx['paid'] : 0;
                            PMTInfoV1::where('id', $paymentId)->update(['amt_used' => DB::raw('amt_used +' . abs($newPmtCptTx['paid']) . '')]);
                        } elseif ($paymentType == 'Adjustment') {
                            $newPmtCptTx = array(
                                "payment_id" => $paymentId,
                                "claim_id" => $claim_id,
                                "pmt_claim_tx_id" => $pmt_clmTxId,
                                "claim_cpt_info_id" => $claim_cpt_info_id,
                                //"allowed" => $datas['cpt_allowed_amt'][$i],
                                "deduction" => $datas['deductable'][$i],
                                "copay" => $datas['co_pay'][$i],
                                "coins" => $datas['co_ins'][$i],
                                "withheld" => $datas['with_held'][$i],
                                //"writeoff" => -1 * abs($datas['adjustment'][$i]),//nedd to chage the name on the blade
                                "writeoff" => $datas['adjustment'][$i], //nedd to chage the name on the blade
                                // "paid" => -1 * abs($datas['paid_amt'][$i]), //it's only allowed to enter the blade
                                "denial_code" => $datas['remarkcode'][$i], //nedd to chage the name on the blade
                                "created_by" => Auth::user()->id
                            );
                            //amountUsed
                            $newPmtCptTx['writeoff'] = (!empty($newPmtCptTx['writeoff'])) ? $newPmtCptTx['writeoff'] : 0;
                            PMTInfoV1::where('id', $paymentId)->update(['amt_used' => DB::raw('amt_used +' . $newPmtCptTx['writeoff'] . '')]);
                            // Payments: Adjustment amount applied for multiple line items if applied for single line items which is incorrect
                            // Rev-1  Ref: MR-2805 - Ravi - 09-09-2019
                            $datas['adjustment_popup_Data'] = [];
                            // Adjustment details not proiveded then dont pass
                            if(isset($datas['with_held'][$i]) && $datas['with_held'][$i] != '0.00')  {
                                $datas['adjustment_popup_Data'] = array(
                                    'adj_reson'=>  $datas['adj_reason'][$i],
                                    'adj_reson_amount'=>  $datas['adj_reson_amount'][$i],
                                );
                            }
                        }
                        $this->createCptTx_CptDes($newPmtCptTx, $claimLevelResChangArr, $paymentType, $paymentClaimTx_DesIds, $lastRecord, $i, $datas);


                    } else if ($flag == "UPDATE") {
                        /*$claim_cpt_info_id = $datas[$i]['claimCptId'];
                        $dd = PMTClaimCPTTXV1::where('claim_cpt_info_id', '=', $claim_cpt_info_id)
                                ->update(["paid" => $datas[$i]['patientPaid']]);*/

                    }

                }
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on createInsurancePamentClaimCptTxDetails. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("createInsurancePamentClaimCptTxDetails", $e);
        }
    }

    public function changeResponsibilityHandler($request, $patientId, $claim_id, $pmtTxDatas)
    {   //\Log::info("Change Responsibility handler. Claim ".$claim_id); \Log::info($request); \Log::info($pmtTxDatas);
        try {
            //change the Responsibility from the popup
            //change the Responsibility from the drop down on paymentPage
            $responsibilityDesArr = array(); //have all claimLevelDes(ResponsibilityChanges) tbl Id's
            if (!empty($request['next_responsibility'])) {
                if (!empty($request['next_responsibility']) && $request['next_responsibility'] != 'patient') {
                    $responsibility = explode('-', $request['next_responsibility']);
                    $ccrresult = $this->changeTheClaimResponsibility($claim_id, $responsibility[0], $responsibility[1], 'No', $pmtTxDatas);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$ccrresult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $temp['id'] = $ccrresult;
                    $temp['from'] = 'dropDown';
                    array_push($responsibilityDesArr, $temp);
                } else if (!empty($request['next_responsibility']) && $request['next_responsibility'] == 'patient') {
                    $ccrresult = $this->changeTheClaimResponsibility($claim_id, '', 0, 'Yes', $pmtTxDatas);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$ccrresult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $temp['id'] = $ccrresult;
                    $temp['from'] = 'dropDown';
                    array_push($responsibilityDesArr, $temp);
                }
            } else {
                //find the patient next responsibility and change it ...
                if ($request['status'] != 'Denied' && $request['status'] != 'pending') {
                    $ccrresult = $this->doAutoChangeClaimResponsibility($patientId, $claim_id, $pmtTxDatas, $request);
                    $temp['id'] = $ccrresult;
                    $temp['from'] = 'autoChange';
                    array_push($responsibilityDesArr, $temp);
                } else if ($request['status'] == 'Denied' || $request['status'] == 'pending') {
                    if ($request['changed_insurance_id'] == '') {
                        $claimInfoData = $request['claimInfoData'];
                        $ccrresult = $this->changeTheClaimResponsibility($claim_id, $claimInfoData['insurance_category'], $claimInfoData['insurance_id'], $claimInfoData['self_pay'], $pmtTxDatas);
                        if(isset($request['backDate'])){
                            ClaimTXDESCV1::where('id',$ccrresult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                        }
                        $temp['id'] = $ccrresult;
                        $temp['from'] = 'autoChange';
                        array_push($responsibilityDesArr, $temp);
                    } else {
                        $claimInfoData = ClaimInfoV1::getClaimInsuranceDetails($claim_id);
                        $ccrresult = $this->changeTheClaimResponsibility($claim_id, $claimInfoData['insurance_category'], $claimInfoData['insurance_id'], $claimInfoData['self_pay'], $pmtTxDatas);
                        if(isset($request['backDate'])){
                            ClaimTXDESCV1::where('id',$ccrresult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                        }
                        $temp['id'] = $ccrresult;
                        $temp['from'] = 'autoChange';
                        array_push($responsibilityDesArr, $temp);
                    }

                }

            }
            return $responsibilityDesArr;
        } catch (Exception $e) {
            \Log::info("Exception catched on changeResponsibilityHandler. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("changeResponsibilityHandler", $e);
        }
    }

    public function checkClaimNextResponsibility($patientId, $ins_category) {   
        $ins_id = 0;
        if ($ins_category == 'Primary') {
            $statusArr = $this->checkNextResponsibilityExistOrNot($patientId, 'Secondary');
            $ins_id = ($statusArr['status']) ? @$statusArr['ins_id'] : 0;
        } elseif (($ins_category == 'Secondary')) {
            $secRes = $this->checkNextResponsibilityExistOrNot($patientId, 'Tertiary');
            $ins_id = ($secRes['status']) ? @$secRes['ins_id'] : 0;                
        } elseif ($ins_category == 'Tertiary') {            
            $ins_id = 0;
        }    
        return $ins_id;
    }

    public function doAutoChangeClaimResponsibility($patientId, $claimId, $pmtTxDatas, $request)
    {
        try {
            $oldClaimInfodetails = ClaimInfoV1::select(['id', 'insurance_id', 'self_pay', 'insurance_category'])
                ->where('id', $claimId)->first()->toArray();

            if (!empty($oldClaimInfodetails['insurance_id'])) {
                $currentInsDetails = PatientInsurance::
                where('insurance_id', $oldClaimInfodetails['insurance_id'])->
                where('category', $oldClaimInfodetails['insurance_category'])
                    ->where('patient_id', $patientId)->first();
                if (!empty($currentInsDetails)) {
                    $currentInsDetails = $currentInsDetails->toArray();
                    $desResult = $secDesRes = $terDesRes = '';
                    if ($currentInsDetails['category'] == 'Primary') {
                        $statusArr = $this->checkNextResponsibilityExistOrNot($patientId, 'Secondary');
                        if ($statusArr['status']) {
                            $desResult = $this->changeTheClaimResponsibility($claimId, $statusArr['ins_cat'], $statusArr['ins_id'], 'No', $pmtTxDatas);
                            if(isset($request['backDate'])){
                                ClaimTXDESCV1::where('id',$desResult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                            }
                            return $desResult;
                        } else {
                            $TerResult = $this->checkNextResponsibilityExistOrNot($patientId, 'Tertiary');
                            if ($TerResult['status']) {
                                $desResult = $this->changeTheClaimResponsibility($claimId, $TerResult['ins_cat'], $TerResult['ins_id'], 'No', $pmtTxDatas);
                                if(isset($request['backDate'])){
                                    ClaimTXDESCV1::where('id',$desResult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                                }
                                return $desResult;
                            } else {
                                $terDesRes = $this->changeTheClaimResponsibility($claimId, '', '0', 'Yes', $pmtTxDatas);
                                if(isset($request['backDate'])){
                                    ClaimTXDESCV1::where('id',$terDesRes)->update(['created_at' => $this->dateformater($request['backDate'])]);
                                }
                                return $terDesRes;
                            }
                        }
                    } else if (($currentInsDetails['category'] == 'Secondary')) {
                        $secRes = $this->checkNextResponsibilityExistOrNot($patientId, 'Tertiary');
                        if ($secRes['status']) {
                            $secDesRes = $this->changeTheClaimResponsibility($claimId, $secRes['ins_cat'], $secRes['ins_id'], 'No', $pmtTxDatas);
                            if(isset($request['backDate'])){
                                ClaimTXDESCV1::where('id',$secDesRes)->update(['created_at' => $this->dateformater($request['backDate'])]);
                            }
                        } else {
                            $secDesRes = $this->changeTheClaimResponsibility($claimId, '', '0', 'Yes', $pmtTxDatas);
                            if(isset($request['backDate'])){
                                ClaimTXDESCV1::where('id',$secDesRes)->update(['created_at' => $this->dateformater($request['backDate'])]);
                            }
                        }
                        return $secDesRes;
                    } else if ($currentInsDetails['category'] == 'Tertiary') {
                        $terDesRes = $this->changeTheClaimResponsibility($claimId, '', '0', 'Yes', $pmtTxDatas);
                        if(isset($request['backDate'])){
                            ClaimTXDESCV1::where('id',$terDesRes)->update(['created_at' => $this->dateformater($request['backDate'])]);
                        }
                        return $terDesRes;
                    }
                } else {
                    $statusArr = $this->checkNextResponsibilityExistOrNot($patientId, 'Primary');
                    if ($statusArr['status']) {
                        $desResult = $this->changeTheClaimResponsibility($claimId, $statusArr['ins_cat'], $statusArr['ins_id'], 'No', $pmtTxDatas);
                        if(isset($request['backDate'])){
                            ClaimTXDESCV1::where('id',$desResult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                        }
                        return $desResult;
                    } else {
                        $statusArr = $this->checkNextResponsibilityExistOrNot($patientId, 'Secondary');
                        if ($statusArr['status']) {
                            $desResult = $this->changeTheClaimResponsibility($claimId, $statusArr['ins_cat'], $statusArr['ins_id'], 'No', $pmtTxDatas);
                            if(isset($request['backDate'])){
                                ClaimTXDESCV1::where('id',$desResult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                            }
                            return $desResult;
                        } else {
                            $TerResult = $this->checkNextResponsibilityExistOrNot($patientId, 'Tertiary');
                            if ($TerResult['status']) {
                                $desResult = $this->changeTheClaimResponsibility($claimId, $TerResult['ins_cat'], $TerResult['ins_id'], 'No', $pmtTxDatas);
                                if(isset($request['backDate'])){
                                    ClaimTXDESCV1::where('id',$desResult)->update(['created_at' => $this->dateformater($request['backDate'])]);
                                }
                                return $desResult;
                            } else {
                                $terDesRes = $this->changeTheClaimResponsibility($claimId, '', '0', 'Yes', $pmtTxDatas);
                                if(isset($request['backDate'])){
                                    ClaimTXDESCV1::where('id',$terDesRes)->update(['created_at' => $this->dateformater($request['backDate'])]);
                                }
                                return $terDesRes;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on doAutoChangeClaimResponsibility. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("doAutoChangeClaimResponsibility", $e);
        }
    }

    public function checkNextResponsibilityExistOrNot($patientId, $type)
    {
        $currentInsDetails = PatientInsurance::where('category', $type)
            ->where('patient_id', $patientId)
            ->first();
        if (!empty($currentInsDetails)) {
            $currentInsDetails = $currentInsDetails->toArray();
            $temp['status'] = true;
            //heare we give InsuranceTable Id
            $temp['ins_id'] = $currentInsDetails['insurance_id'];
            $temp['ins_cat'] = $currentInsDetails['category'];
            return $temp;
        } else {
            $temp['status'] = false;
            $temp['ins_id'] = 0;
            $temp['ins_cat'] = 0;
            return $temp;
        }
    }

    public function changeTheClaimResponsibility($claim_id, $in_Category, $in_Id, $selfPay, $pmtTxDatas)
    {   //\Log::info("Change claim responsibility called. claim_id".$claim_id." Ins Cat ".$in_Category); \Log::info($pmtTxDatas);
        try {
            $oldClaimInfodetails = ClaimInfoV1::select(['patient_id', 'id', 'insurance_id', 'self_pay', 'status', 'insurance_category'])
                ->where('id', $claim_id)->first()->toArray();
            //\Log::info($oldClaimInfodetails); \Log::info($oldClaimInfodetails);
            //\Log::info("Claim ID #".$claim_id."Ins Cat#".$in_Category." in ID#".$in_Id. "SELF PAY#".$selfPay);
            //\Log::info($pmtTxDatas);

            // If same then no need to update it.
            if($oldClaimInfodetails['insurance_id'] == $in_Id && $oldClaimInfodetails['insurance_category'] == '$in_Category'){
                return '';
            }

            //if Resp is Insurance  then status is Ready else Patient
            //$status = ($in_Id > 0) ? 'Ready' : 'Patient';
            
            if ($in_Id > 0) {
                $status = 'Ready';
                $chargeV1 = new ChargeV1ApiController();
                $patientInsuranceId = $chargeV1->findPatientInsuranceDetails(['patientId' => $oldClaimInfodetails['patient_id'], 'insuranceId' => $in_Id, 'insuranceCategory' => $in_Category], 'GET');
            } else {
                $status = 'Patient';
                $patientInsuranceId = '0';
            }
            
            ClaimInfoV1::where('id', $claim_id)
                ->update([
                    'insurance_category' => $in_Category,
                    'insurance_id' => $in_Id,
                    'self_pay' => $selfPay,
                    'status' => $status,
                    'patient_insurance_id' => $patientInsuranceId
                ]);
            $old_ins = ($oldClaimInfodetails['insurance_id'] > 0) ? $oldClaimInfodetails['insurance_id'] : 0;
            //create Description
            $newclaimDesrecption = array(
                "claim_info_id" => $claim_id,
                "old_insurance_id" => $old_ins,
                "new_insurance_id" => $in_Id,
                "pmt_id" => $pmtTxDatas['pmt_id'],
                "txn_id" => $pmtTxDatas['txn_id'],
            );
            $txn_for = 'Responsibility';
            $claimCreateStatus = $this->storeClaimTxnDesc($txn_for, $newclaimDesrecption);
            ClaimInfoV1::updateClaimStatus($claim_id, $txn_for);
            return $claimCreateStatus;
        } catch (Exception $e) {
            \Log::info("Exception catched on changeTheClaimResponsibility. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("changeTheClaimResponsibility", $e);
        }
    }

    public function createWalletData($request)
    {
        try {           
            $patientId = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
            $source = (array_key_exists('source', $request)) ? $request['source'] : (($request['payment_method'] == "Insurance")? "posting" : (($request['payment_type'] == "Payment" && $request['payment_method'] != "Insurance")?'addwallet':""));
            if ($request['payment_mode'] == 'Check') {
                $paymentType = strtolower($request['payment_method']) . '' . $request['payment_type'];
                $checkNoStatus = PMTInfoV1::findCheckExistsOrNot($request['check_no'], $request['payment_method'], $request['payment_mode'], $paymentType, $patientId);
                if ($checkNoStatus) {
                    return Response::json(array('status' => 'error', 'message' => "Check number alredy exist", 'data' => $request['patient_id']));
                }
            }
            DB::beginTransaction();
            $paymentData = array(
                'pmt_amt' => $request['payment_amt_pop'],
                'pmt_mode' => $request['payment_mode'],
                'reference' => $request['reference'],
                'pmt_method' => $request['payment_method'],
                'pmt_type' => ($request['payment_method'] == "Patient" && $request['payment_type'] == "Payment")?'Credit Balance':$request['payment_type'],
                'source' => $source,
                'insurance_id' => $insuranceId
            );
            $chargeV1ApiController = new ChargeV1ApiController();
            $message = Lang::get("common.validation.wallet_create_msg");
            $paymentId = $chargeV1ApiController->createPaymentInformation($paymentData, $patientId, '', $request);
            
            if(isset($request['eob_id']) && !empty($request['eob_id'])){ 
                $document_details = Document::where('temp_type_id', '=', $request['eob_id']);
                $document_count = $document_details->count();
                $documentDetailsInfo = $document_details->get()->first();
                if($document_count > 0 ){   
                    $document_details->update(['payment_id' => $paymentId, 'main_type_id' => $paymentId,'type_id'=>$patientId,'checkno'=>$request['check_no'],'checkamt'=>$request['payment_amt_pop'],'checkdate'=>date('Y-m-d',strtotime($request['check_date'])),'payer'=>@$request['insurance_id']]);
                    DocumentFollowupList::where('document_id',$documentDetailsInfo->id)->update(['patient_id'=>$patientId]);
                }
            }
            
            // For insurance payment dont need to add it to wallet.
            if($request['payment_method'] != "Insurance") {
                $walletData = array(
                    'patient_id' => $patientId,
                    'pmt_info_id' => $paymentId,
                    'tx_type' => 'Credit',
                    'amt_pop' => $request['payment_amt_pop'],
                    'wallet_ref_id' => $paymentId,
                    'claimId' => ''
                );

                $walletDataId = $this->storeWalletData($walletData, false, false)['walltId'];
                if (!empty($walletDataId)) {
                    $resultSet = PMTWalletV1::select(
                        DB::raw('sum(amount) as amount')
                    )->where('patient_id', $patientId)->first();
                    $totalWalletAmount = $resultSet->amount;
                } else {
                    $totalWalletAmount = 0.00;
                }
            }
            else
            {
                $totalWalletAmount = 0.00;
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => $message, 'data' => $totalWalletAmount));
            }

            if ($walletDataId > 0) {
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => $message, 'data' => $totalWalletAmount));
            } else {
                DB::rollBack();
                return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => ''));
            }
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info("Exception catched on createWalletData. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("createWalletData", $e);
        }
    }

    public function storeWalletData($datas, $descStatus, $updateAmountUsed = true)
    {
        try {
            $walletData = array(
                'patient_id' => $datas['patient_id'],
                'pmt_info_id' => $datas['pmt_info_id'],
                'tx_type' => $datas['tx_type'],
                'amount' => $datas['amt_pop'],
                'wallet_Ref_Id' => $datas['wallet_ref_id'],
                'applied' => 0,
                'created_by' => Auth::user()->id
            );
            $usedAmount = (isset($datas['used_amount'])) ? $datas['used_amount'] : $walletData['amount'];
            //update the amount used
            if ($updateAmountUsed) {
                PMTInfoV1::updatePaymettAmoutUsed($walletData['pmt_info_id'], $usedAmount);
            }

            $claimTxDesId = 0;
            if ($descStatus) {
                $newDesrecption = array(
                    "claim_info_id" => $datas['claimId'],
                    "pmt_id" => $datas['pmt_info_id'],
                    'amount' => $datas['amt_pop'],
                    'txn_id' => '',
                    'resp' => $this->getClaimResponsibility($datas['claimId'])
                );
                $claimTxDesId = $this->storeClaimTxnDesc('Wallet', $newDesrecption);
                $walletId = 0;
            } else {
                $resultSet = PMTWalletV1::create($walletData);
                $walletId = $resultSet->id;
            }
            return ['walltId' => $walletId, 'claimTxID' => $claimTxDesId];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info("Exception catched on storeWalletData. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("storeWalletData", $e);
        }
    }

    public function createCreditBalanceClaimCptTxDetails($claim_id, $datas, $flag, $claimTxnDescId, $patient_id)
    {
        //\Log::info("Create credit balance claim cpt tx"); \Log::info($datas);
        try {
            if ($datas) {
                $allwalletTnxIds = array();
                $allPmtClaimTxIds = array();
                $lastRecord = count($datas) - 1;
                if ($flag == "CREATE") {
                    for ($i = 0; $i < count($datas); $i++) {
                        $walletTxDatas = $datas[$i]['wallettransactionIds']['data'];
                        $cptTxIds = array();
                        if (!empty($walletTxDatas)) {
                            foreach ($walletTxDatas as $walletTxData) {
                                $paymentId = $walletTxData['pmt_info_id'];
                                //create claim Tx
                                $newPmtClaimTx = array(
                                    "payment_id" => $paymentId,
                                    "claim_id" => $claim_id,
                                    "pmt_method" => 'Patient',
                                    "pmt_type" => 'Credit Balance',
                                    "patient_id" => $patient_id,
                                    "posting_date" => date("Y-m-d"),
                                    "created_by" => Auth::user()->id
                                );
                                $pmt_claim_txID = PMTClaimTXV1::create($newPmtClaimTx)->id;

                                //create cptTx
                                $newPmtCptTx = array(
                                    "payment_id" => $paymentId,
                                    "claim_id" => $claim_id,
                                    "pmt_claim_tx_id" => $pmt_claim_txID,
                                    "claim_cpt_info_id" => $datas[$i]['claimCptId'],
                                    "paid" => $walletTxData['amountApplied'],
                                    "created_by" => Auth::user()->id,
                                    'resp' => $this->getClaimResponsibility($claim_id)
                                );
                                $this->paymentPostingDocumentUpload('','','',$paymentId,$claim_id);
                                $txnFor = 'Patient Credit Balance';
                                $cptTxId = PMTClaimCPTTXV1::create($newPmtCptTx)->id;
                                $chargeV1Api = new ChargeV1ApiController();
                                $chargeV1Api->updateClaimCptTxData($newPmtCptTx, $txnFor);
                                $currentFinBalance = $chargeV1Api->updateClaimCptFindData($newPmtCptTx, $txnFor);
                                array_push($cptTxIds, $cptTxId);
                                array_push($allPmtClaimTxIds, $pmt_claim_txID);
                                array_push($allwalletTnxIds, $walletTxData);
                            }
                            //create cptTxDescrecption
                            $newclaimCptDesrecption = array(
                                "claim_info_id" => $claim_id,
                                "pmt_id" => '0',
                                'claim_tx_desc_id' => $claimTxnDescId,
                                'claim_cpt_info_id' => $datas[$i]['claimCptId'],
                                'txn_id' => '0',
                                'resp' => $this->getClaimResponsibility($claim_id),
                                'value1' => json_encode($walletTxDatas),
                                'value2' => implode(",", $cptTxIds)//$datas[$i]['patientPaid']
                            );

                            $cptTxDesId = $this->storeClaimCptTxnDesc($txnFor, $newclaimCptDesrecption);
                            $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                $currentFinBalance['cpt_patient_balance'], $currentFinBalance['cpt_insurance_balance']);
                        } else {
                            //if line item is empty we create a empty claimTx and cptTx
                            //create claim Tx
                            $paymentId = '0';
                            $newPmtClaimTx = array(
                                "payment_id" => $paymentId,
                                "claim_id" => $claim_id,
                                "pmt_method" => 'Patient',
                                "pmt_type" => 'Credit Balance',
                                "patient_id" => $patient_id,
                                "posting_date" => date("Y-m-d"),
                                "created_by" => Auth::user()->id
                            );
                            $pmt_claim_txID = PMTClaimTXV1::create($newPmtClaimTx)->id;
                            $newPmtCptTx = array(
                                "payment_id" => $paymentId,
                                "claim_id" => $claim_id,
                                "pmt_claim_tx_id" => $pmt_claim_txID,
                                "claim_cpt_info_id" => $datas[$i]['claimCptId'],
                                "paid" => $datas[$i]['patientPaid'],
                                "created_by" => Auth::user()->id,
                                'resp' => $this->getClaimResponsibility($claim_id)
                            );
                            $txnFor = 'Patient Credit Balance';
                            $resultSet = PMTClaimCPTTXV1::create($newPmtCptTx);
                            //add and update the applied amount in charge screen
                            $chargeV1Api = new ChargeV1ApiController();
                            $chargeV1Api->updateClaimCptTxData($newPmtCptTx, $txnFor);
                            $currentFinBalance = $chargeV1Api->updateClaimCptFindData($newPmtCptTx, $txnFor);
                            array_push($allPmtClaimTxIds, $pmt_claim_txID);
                            $newclaimCptDesrecption = array(
                                "claim_info_id" => $claim_id,
                                "pmt_id" => $paymentId,
                                'claim_tx_desc_id' => $claimTxnDescId,
                                'claim_cpt_info_id' => $datas[$i]['claimCptId'],
                                'txn_id' => $resultSet->id,
                                'resp' => $this->getClaimResponsibility($claim_id),
                                'value1' => '',
                                'value2' => ''
                            );
                            $cptTxDesId = $this->storeClaimCptTxnDesc($txnFor, $newclaimCptDesrecption);
                            $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                $currentFinBalance['cpt_patient_balance'], $currentFinBalance['cpt_insurance_balance']);
                        }
                        if ($i == $lastRecord) {
                            $datasForClaim = array(
                                'claim_id' => $claim_id,
                                'pmt_claim_tx_id' => $pmt_claim_txID,
                                'tnxFor' => $txnFor,
                                'resp' => $this->getClaimResponsibility($claim_id),
                                'transaction_type' => $txnFor
                            );
                            $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($datasForClaim, $claimTxnDescId);
                            $chargeV1Api->updateBalanceClaimTXDesc($claimTxnDescId,
                                $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                        }
                    }
                    $temp['walletTxIds'] = $allwalletTnxIds;
                    $temp['claimTxIds'] = $allPmtClaimTxIds;
                    return $temp;
                }
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on createCreditBalanceClaimCptTxDetails. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("createCreditBalanceClaimCptTxDetails", $e);
        }
    }

    public function createCptTx_CptDes($newPmtCptTx, $claimLevelResChangArr, $paymentType, $paymentClaimTx_DesIds, $lastRecord, $i, $request)
    {
        try {
            $claim_id = $newPmtCptTx['claim_id'];
            $paymentId = $newPmtCptTx['payment_id'];
            $claim_cpt_info_id = $newPmtCptTx['claim_cpt_info_id'];
            $responsibility = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';  //pmtCheckInsurance Id
            if (!empty($claimLevelResChangArr)) {
                $chargeV1Api = new ChargeV1ApiController();
                foreach ($claimLevelResChangArr as $key => $value) {
                    $arrIndex = $claimLevelResChangArr[$key];
                    if (isset($arrIndex['id']) && $arrIndex['id'] != '') {
                        //Construct New Description table with previous record
                        $decData = ClaimTXDESCV1::where('id', $arrIndex['id'])->first()->toArray();
                        if (!empty($decData)) {
                            $newCptDesc = array(
                                "claim_info_id" => $claim_id,
                                "pmt_id" => $paymentId,
                                'resp' => $decData['value_2'],
                                'claim_tx_desc_id' => $arrIndex['id'],
                                'claim_cpt_info_id' => $claim_cpt_info_id,
                                'old_insurance_id' => $decData['value_1'],
                                'new_insurance_id' => $decData['value_2']
                            );

                            // Responsibility change from popup
                            if ($arrIndex['from'] == 'popUp') {
                                //***** Start: Creating Responsibility Change Actions ****//
                                $cptTxDesId = $this->storeClaimCptTxnDesc('Responsibility', $newCptDesc);
                                $newPmtCptTx['resp'] = $responsibility;
                                $newPmtCptTx['tnxFor'] = 'Responsibility';
                                $newPmtCptTx['claim_id'] = $claim_id;
                                $newPmtCptTx['transaction_type'] = 'Responsibility';

                                //dd($newPmtCptTx);
                                //Update CPT Financial Table - Insurance and Patient Balance
                                $currentFinBalance = $chargeV1Api->findClaimCptLevelPatientandInsuranceBal($newPmtCptTx);
                                $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                    $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);


                                //If Last line item found - Update Claim Level Fin Table
                                if ($i == $lastRecord) {
                                    $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $arrIndex['id']);
                                    $chargeV1Api->updateBalanceClaimTXDesc($arrIndex['id'],
                                        $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                }
                                $walletTxDesId = $chargeV1Api->checkandSendTheBalanceAmountToWallet($newPmtCptTx);
                                if ($walletTxDesId != 0) {
                                    $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $walletTxDesId);
                                    $chargeV1Api->updateBalanceClaimTXDesc($walletTxDesId,
                                        $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                }

                                //***** Stop: Creating Responsibility Change Actions ****//

                                //***** Start: Insurance - Payment, Refund, Adj Actions ****//
                                //CPT: Create Responsibility Change Description
                                $newClaimCptDesrecption = array(
                                    "claim_info_id" => $claim_id,
                                    "pmt_id" => $paymentId,
                                    'claim_cpt_info_id' => $claim_cpt_info_id,
                                    'claim_tx_desc_id' => $paymentClaimTx_DesIds['claimTxnDescId'],
                                    'denials_code' => '',
                                    'resp' => $newCptDesc['new_insurance_id'],
                                    'value_1' => $responsibility
                                );
                                //CPT: Create new payment record
                                $txnFor = 'Insurance ' . $paymentType;
                                $newPmtCptTx['tnxFor'] = $txnFor;
                                $claimCptTxId = PMTClaimCPTTXV1::create($newPmtCptTx)->id;
                                //add adjustment splitUP to represent Table
                                if(isset($request['adjustment_popup_Data'])) {
                                    $this->createAdjustmentPopInfo($claim_id, $claim_cpt_info_id, $claimCptTxId, $request['adjustment_popup_Data']);
                                }

                                //CPT: Get ID of created record
                                $newClaimCptDesrecption['txn_id'] = $claimCptTxId;
                                //CPT: Check for denials
                                if ($newPmtCptTx['denial_code'] != '') {
                                    $newClaimCptDesrecption['denials_code'] = $newPmtCptTx['denial_code'];
                                    $cptTxDesId = $this->storeClaimCptTxnDesc('Denials', $newClaimCptDesrecption);
                                    $chargeV1Api->updateClaimCptTxData($newPmtCptTx, $txnFor);
                                    $currentFinBalance = $chargeV1Api->updateClaimCptFindData($newPmtCptTx, $txnFor);
                                    $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                        $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                                    /*-- store ArClaimDenail Notes  start --*/
                                    $denialCodes  = explode(',',$newPmtCptTx['denial_code']);
                                    $claimDenailsData = array(
                                        'denial_date' => $request['check_date'],
                                        'check_no' => $request['payment_mode'] . '/' . $request['check_no'],
                                        'denial_insurance' => $responsibility,
                                        'denial_codes' => $this->findDenailCodesTbl_Ids($denialCodes),
                                        'claim_id' => $claim_id,
                                        'reference' => ''
                                    );
                                    $arMangementApi = new ArmanagementApiController();
                                    $arMangementApi->storeARClaimDenailNotes($claimDenailsData);
                                    /*-- store ArClaimDenail Notes end --*/

                                    if ($i == $lastRecord) {
                                        $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $paymentClaimTx_DesIds['claimTxnDescId']);
                                        $chargeV1Api->updateBalanceClaimTXDesc($paymentClaimTx_DesIds['claimTxnDescId'], $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                    }
                                } else {
                                    //CPT: Description based on Transaction
                                    $cptTxDesId = $this->storeClaimCptTxnDesc($txnFor, $newClaimCptDesrecption);
                                    $newPmtCptTx['transaction_type'] = $txnFor;
                                    $newPmtCptTx['resp'] = $responsibility;
                                    $newPmtCptTx['tnxFor'] = $txnFor;

                                    //CPT: Update claim transaction paid based on line items
                                    $chargeV1Api->updateClaimCptTxData($newPmtCptTx, $txnFor);

                                    //CPT: Update CPT Fin table
                                    $currentFinBalance = $chargeV1Api->updateClaimCptFindData($newPmtCptTx, $txnFor);
                                    $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                        $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                                    //If Last line item found - Update Claim Level Fin Table
                                    if ($i == $lastRecord) {
                                        $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $paymentClaimTx_DesIds['claimTxnDescId']);
                                        $chargeV1Api->updateBalanceClaimTXDesc($paymentClaimTx_DesIds['claimTxnDescId'],
                                            $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                    }
                                }
                            } //Responsibility changed from dropdown - Perform only responsibility update and descriptions
                            else if ($arrIndex['from'] == 'dropDown') {
                                $cptTxDesId1 = $this->storeClaimCptTxnDesc('Responsibility', $newCptDesc);
                                $newPmtCptTx['resp'] = $this->getClaimResponsibility($claim_id);
                                $newPmtCptTx['tnxFor'] = 'Responsibility';
                                $newPmtCptTx['claim_id'] = $claim_id;
                                $newPmtCptTx['transaction_type'] = 'Responsibility';
                                //Update CPT Financial Table - Insurance and Patient Balance
                                $currentFinBalance = $chargeV1Api->findClaimCptLevelPatientandInsuranceBal($newPmtCptTx);
                                $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId1,
                                    $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                                //If Last line item found - Update Claim Level Fin Table

                                if ($i == $lastRecord) {
                                    $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $arrIndex['id']);
                                    $chargeV1Api->updateBalanceClaimTXDesc($arrIndex['id'],
                                        $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                }
                                $walletTxDesId = $chargeV1Api->checkandSendTheBalanceAmountToWallet($newPmtCptTx);
                                if ($walletTxDesId != 0) {
                                    $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $walletTxDesId);
                                    $chargeV1Api->updateBalanceClaimTXDesc($walletTxDesId,
                                        $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                }
                            } else if ($arrIndex['from'] == 'autoChange') { //Responsibility changed from
                                $newPmtCptTx['resp'] = $this->getClaimResponsibility($claim_id);
                                $newPmtCptTx['tnxFor'] = 'Responsibility';
                                $newPmtCptTx['claim_id'] = $claim_id;
                                $newPmtCptTx['transaction_type'] = 'Responsibility';
                                $currentFinBalance = $chargeV1Api->findClaimCptLevelPatientandInsuranceBal($newPmtCptTx);
                                $cptTxDesId = $this->storeClaimCptTxnDesc('Responsibility', $newCptDesc);
                                $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                    $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);

                                if ($i == $lastRecord) {
                                    $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $arrIndex['id']);
                                    $chargeV1Api->updateBalanceClaimTXDesc($arrIndex['id'],
                                        $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                }
                                $walletTxDesId = $chargeV1Api->checkandSendTheBalanceAmountToWallet($newPmtCptTx);
                                if ($walletTxDesId != 0) {
                                    $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $walletTxDesId);
                                    $chargeV1Api->updateBalanceClaimTXDesc($walletTxDesId,
                                        $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                                }
                            }
                        }
                    } else if ($arrIndex['id'] == '' && $arrIndex['from'] == 'popupNone') {
                        $txnFor = 'Insurance ' . $paymentType;
                        $claimCptTxId = PMTClaimCPTTXV1::create($newPmtCptTx)->id;
                        if(isset($request['adjustment_popup_Data'])) {
                            $this->createAdjustmentPopInfo($claim_id, $claim_cpt_info_id, $claimCptTxId, $request['adjustment_popup_Data']);
                        }
                        $newclaimDesrecption = array(
                            "claim_info_id" => $claim_id,
                            "pmt_id" => $paymentId,
                            'claim_tx_desc_id' => $paymentClaimTx_DesIds['claimTxnDescId'],
                            'claim_cpt_info_id' => $claim_cpt_info_id,
                            'txn_id' => $claimCptTxId,
                            'denials_code' => '',
                            'resp' => $responsibility,
                        );
                        $newPmtCptTx['resp'] = $responsibility;
                        $newPmtCptTx['transaction_type'] = $txnFor;
                        $newPmtCptTx['tnxFor'] = $txnFor;
                        $chargeV1Api->updateClaimCptTxData($newPmtCptTx, $txnFor);

                        if ($newPmtCptTx['denial_code'] != '') {
                            $newclaimDesrecption['denials_code'] = $newPmtCptTx['denial_code'];
                            $cptTxDesId = $this->storeClaimCptTxnDesc('Denials', $newclaimDesrecption);
                            $chargeV1Api->updateClaimCptTxData($newPmtCptTx, $txnFor);
                            $currentFinBalance = $chargeV1Api->updateClaimCptFindData($newPmtCptTx, $txnFor);
                            $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                            /* -- store ArClaimDenail Notes -*/
                            $denialCodes  = explode(',',$newPmtCptTx['denial_code']);
                            $claimDenailsData = array(
                                'denial_date' => $request['check_date'],
                                'check_no' => $request['payment_mode'] . '/' . $request['check_no'],
                                'denial_insurance' => $responsibility,
                                'denial_codes' => $this->findDenailCodesTbl_Ids($denialCodes),
                                'claim_id' => $claim_id,
                                'reference' => ''
                            );

                            $arMangementApi = new ArmanagementApiController();
                            $arMangementApi->storeARClaimDenailNotes($claimDenailsData);
                            /* -- store ArClaimDenail Notes -*/

                            if ($i == $lastRecord) {
                                $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $paymentClaimTx_DesIds['claimTxnDescId']);
                                $chargeV1Api->updateBalanceClaimTXDesc($paymentClaimTx_DesIds['claimTxnDescId'], $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                            }
                        } else {
                            $cptTxDesId = $this->storeClaimCptTxnDesc($txnFor, $newclaimDesrecption);
                            $currentFinBalance = $chargeV1Api->updateClaimCptFindData($newPmtCptTx, $txnFor);
                            $chargeV1Api->updateClaimCptTxData($newPmtCptTx, $txnFor);
                            $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                                $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                            if ($i == $lastRecord) {
                                $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $paymentClaimTx_DesIds['claimTxnDescId']);
                                $chargeV1Api->updateBalanceClaimTXDesc($paymentClaimTx_DesIds['claimTxnDescId'], $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {
            \Log::info("Exception catched on createCptTx_CptDes. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("createCptTx_CptDes", $e);
        }
    }

    public   function createAdjustmentPopInfo($claim_id,$cpt_id,$cptTx_id,$datas) {
        try {
            // Payments: Adjustment amount applied for multiple line items if applied for single line items which is incorrect
            // Rev-1  Ref: MR-2805 - Ravi - 09-09-2019
            if(!empty($datas['adj_reson'])){
                foreach ($datas['adj_reson'] as $key=>$value) {
                    if(!empty($datas['adj_reson_amount'][$key])) {
                        $newDatas = array(
                            'claim_id' => $claim_id,
                            'claim_cpt_id' => $cpt_id,
                            'claim_cpt_tx_id' => $cptTx_id,
                            'adjustment_id' => $datas['adj_reson'][$key],
                            'adjustment_amt' => $datas['adj_reson_amount'][$key]
                        );
                        App\Models\Payments\ClaimCPTOthersAdjustmentInfoV1::create($newDatas);
                    }
                }
            }
        } catch (Exception $e) {
            \Log::info("createAdjustmentPopInfo " . $e->getMessage());
            $resp = $this->showErrorResponse("createAdjustmentPopInfo", $e);
        }
    }

    public  function  findDenailCodesTbl_Ids($denialCodes){
        try{
            $remarkCode = array();
            foreach ($denialCodes as $denial_code_value) {
                if(!empty($denial_code_value)) {
					$dataArr = ['CO', 'PR', 'OA', 'PI'];
					$str2 = substr(trim($denial_code_value), 0, 2);
					if (in_array($str2, $dataArr)) {
						$transactionCode = Code::where('transactioncode_id', substr($denial_code_value, 2))->pluck('id')->first();
						array_push($remarkCode, $transactionCode);
					}else{
						$transactionCode = Code::where('transactioncode_id', $denial_code_value)->pluck('id')->first();
						array_push($remarkCode, $transactionCode);
					}
                }
            }
            return $remarkCode;
        }catch (Exception $e){
            \Log::info("findDenailTableIds" . $e->getMessage());
        }
    }

    /*
     * resubmt the Claim and change the Responsiblility
     *
     */
    public function reSubmitClaimFromArManagment($patientId, $claimId, $insuranceId)
    {
        try {
            $oldClaimInfodetails = ClaimInfoV1::select(['id', 'insurance_id', 'self_pay', 'status', 'insurance_category'])
                ->where('id', $claimId)->first()->toArray();
            $oldInsuranceId = $oldClaimInfodetails['insurance_id'];
            $oldInsurance_Category = $oldClaimInfodetails['insurance_category'];

            //if patient
            if ($insuranceId == '0') {
                $newInsuranceId = $insuranceId;
                $newInsuranceCategory = '';
            } else {
                //input format like category-insuranceId
                $insuranceData = explode('-', $insuranceId);
                $newInsuranceCategory = $insuranceData[0];
                $newInsuranceId = $insuranceData[1];
            }

            /*if ($oldInsuranceId == $newInsuranceId && $oldInsurance_Category == $newInsuranceCategory) {
                $message = 'Claim already filed with the selected insurance';
                return Response::json(array('status' => 'info', 'message' => $message, 'data' => $claimId));
            } else {*/
                $message = 'successfully update';
                if ($newInsuranceId != 0) {
                    $claimTxDesId = $this->changeTheClaimResponsibility($claimId, $newInsuranceCategory, $newInsuranceId, 'No', ['pmt_id' => '', 'txn_id' => '']);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$claimTxDesId)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                } else {
                    $claimTxDesId = $this->changeTheClaimResponsibility($claimId, '', '', 'Yes', ['pmt_id' => '', 'txn_id' => '']);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$claimTxDesId)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                }
                $status = $this->createClaimAndCptResTxDesc($claimId, $newInsuranceId, $oldInsuranceId, $claimTxDesId);
                if ($status) {                    
                    // Calling to update the claim status
                    // Rev.1 Ref: MEDV2-545 - Ravi - 10-12-2019
                    ClaimInfoV1::updateClaimStatus($claimId, 'Responsibility');
                    return Response::json(array('status' => 'success', 'message' => $message, 'data' => $claimId));
                } else {
                    return Response::json(array('status' => 'failed', 'message' => $message, 'data' => $claimId));
                }
            //}

        } catch (Exception $e) {
            $resp = $this->showErrorResponse("reSubmitClaimFromArManagment", $e);
            return Response::json(array('status' => 'error', 'message' => $e->getMessage()));
        }
    }

    public function createClaimAndCptResTxDesc($claimId, $newInsuranceId, $oldInsuranceId, $claimTxDesId)
    {
        try {
            $claimCptDatas = ClaimCPTInfoV1::where('claim_id', $claimId)->get();
            if (isset($claimCptDatas)) {
                $new_insurance_id = ($newInsuranceId > '0') ? $newInsuranceId : "0";
                $chargeV1Api = new ChargeV1ApiController();
                foreach ($claimCptDatas as $claimCpt) {
                    $newCptDes = array(
                        'claim_info_id' => $claimId,
                        'claim_cpt_info_id' => $claimCpt['id'],
                        "claim_tx_desc_id" => $claimTxDesId,
                        'resp' => $this->getClaimResponsibility($claimId),
                        'old_insurance_id' => $oldInsuranceId,
                        'new_insurance_id' => $new_insurance_id,
                        'pmt_id' => ''
                    );
                    $cptTxDesId = $this->storeClaimCptTxnDesc('Responsibility', $newCptDes);
                    $newCptDes['tnxFor'] = 'Responsibility';
                    $newCptDes['claim_id'] = $claimId;
                    $newCptDes['transaction_type'] = 'Responsibility';
                    $currentFinBalance = $chargeV1Api->findClaimCptLevelPatientandInsuranceBal($newCptDes);
                    $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                        $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                }
                $newClaimDes['tnxFor'] = 'Responsibility';
                $newClaimDes['claim_id'] = $claimId;
                $newClaimDes['transaction_type'] = 'Responsibility';
                $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newClaimDes, $claimTxDesId);
                $chargeV1Api->updateBalanceClaimTXDesc($claimTxDesId, $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createClaimAndCptResTxDesc", $e);
        }
    }


    public function addDenialFromArManagment($request)
    {
        try {// var_dump($request['next_responsibility']);die;
            $claim_id = $request['claim_id'];
            $request['check_date'] = $request['denial_date'];
            $denialInsuranceId = explode('-', $request['denial_insurance'])[1];
            $denialInsuranceCategory = explode('-', $request['denial_insurance'])[0];
            $request['insurance_id'] = $denialInsuranceId;
            $reference = $request['reference'];
            $patientId = $request['patient_id'];
            $denialCodes = $request['denial_codes'];
            $next_responsibility = $request['next_responsibility'];
            $claimDetails = $request['claim_details'];
            $request['claim_insurance_id'] = $claimDetails['insurance_id'];
            /* Responsponsibility block strt  */
            if ($claimDetails['insurance_id'] != $denialInsuranceId) {
                //input format like category-insuranceId
                if($request['next_responsibility'] != ''){
                    if ($next_responsibility == '0') { 
                        $newInsuranceId = 0;
                        $newInsuranceCategory = '';
                    } else {
                        $nextRespData = explode('-', $next_responsibility);
                        $newInsuranceCategory = $nextRespData[0];
                        $newInsuranceId = $nextRespData[1];
                    }
                    /* $insuranceData = explode('-', $request['next_responsibility']);
                    $newInsuranceCategory = isset($insuranceData[0]) ? $insuranceData[0] : 0;
                    $newInsuranceId = isset($insuranceData[1]) ? $insuranceData[1] : 0; */
                    if ($newInsuranceId != 0) {
                        $claimTxDesId = $this->changeTheClaimResponsibility($claim_id, $newInsuranceCategory, $newInsuranceId, 'No', ['pmt_id' => '', 'txn_id' => '']);
                        if(isset($request['backDate'])){
                            ClaimTXDESCV1::where('id',$claimTxDesId)->update(['created_at' => $this->dateformater($request['backDate'])]);
                        }
                        
                        $status = $this->createClaimAndCptResTxDesc($claim_id, $newInsuranceId, $claimDetails['insurance_id'], $claimTxDesId);
                    }
                }
            }
            /* Responsponsibility block end   */

            /* Denials block start   */
            $remarkCode = array();
            foreach ($denialCodes as $denial_code_value) {
                $transactionCode = Code::where('id', $denial_code_value)->pluck('transactioncode_id')->first();
                array_push($remarkCode, $transactionCode);
            }
            $request['remarkcode'] = $remarkCode;
            //createNewPyament
            DB::beginTransaction();
            $paymentModeData = array(
                'pmt_amt' => '0',
                'pmt_mode' => 'Check',
                'reference' => $reference,
                'pmt_type' => 'Payment',
                'source' => 'posting',
                'pmt_method' => 'Insurance',
                'insurance_id' => $request['insurance_id']
            );
            $chargeV1 = new ChargeV1ApiController();
            $paymentId = $chargeV1->createPaymentInformation($paymentModeData, $patientId, $claim_id, $request);
            $paymentClaimTx_DesIds = $this->createInsurancePaymentClaimTxDetails($paymentId, $claim_id, $patientId, $paymentModeData, $request);
            $claimTxDesId = $paymentClaimTx_DesIds['claimTxnDescId'];
            $claimCptDatas = ClaimCPTInfoV1::where('claim_id', $claim_id)->get();
            if (isset($claimCptDatas)) {
                //$new_insurance_id = ($newInsuranceId > '0') ? $newInsuranceId : "0" ;
                $chargeV1Api = new ChargeV1ApiController();
                $denailCodes = implode(",", array_values(array_filter($request['remarkcode'])));
                foreach ($claimCptDatas as $claimCpt) {
                    $newPmtCptTx = array(
                        "payment_id" => $paymentId,
                        "claim_id" => $claim_id,
                        "pmt_claim_tx_id" => $paymentClaimTx_DesIds['txn_id'],
                        "claim_cpt_info_id" => $claimCpt['id'],
                        "denial_code" => $denailCodes,
                        "created_by" => Auth::user()->id
                    );

                    $claimCptTxId = PMTClaimCPTTXV1::create($newPmtCptTx)->id;
                    $newCptDes = array(
                        'claim_info_id' => $claim_id,
                        'claim_cpt_info_id' => $claimCpt['id'],
                        "claim_tx_desc_id" => $claimTxDesId,
                        'resp' => $this->getClaimResponsibility($claim_id),
                        'old_insurance_id' => $claimDetails['insurance_id'],
                        //'new_insurance_id' => 0,
                        'pmt_id' => $paymentId,
                        'txn_id' => $claimCptTxId,
                        'denials_code' => $denailCodes
                    );
                    $cptTxDesId = $this->storeClaimCptTxnDesc('Denials', $newCptDes);
                    $newCptDes['tnxFor'] = 'Denials';
                    $newCptDes['claim_id'] = $claim_id;
                    $newCptDes['transaction_type'] = 'Denials';
                    $newCptDes['denials_code'] = implode(",", array_values(array_filter($request['remarkcode'])));
                    $currentFinBalance = $chargeV1Api->findClaimCptLevelPatientandInsuranceBal($newCptDes);
                    $chargeV1Api->updateBalanceClaimCPTTXDesc($cptTxDesId,
                        $currentFinBalance['patient_balance'], $currentFinBalance['insurance_balance']);
                }
                $newClaimDes['tnxFor'] = 'Denials';
                $newClaimDes['claim_id'] = $claim_id;
                $newClaimDes['transaction_type'] = 'Denials';
                $currentClaimFinBalance = $chargeV1Api->findClaimLevelPatientandInsuranceBal($newClaimDes, $claimTxDesId);
                $chargeV1Api->updateBalanceClaimTXDesc($claimTxDesId, $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
            }
            /* Denials block end   */

            /* next Responsibility block start   */
            if($next_responsibility != ''){
                if ($next_responsibility == '0') { 
                    $newInsuranceId = 0;
                    $newInsuranceCategory = '';
                } else {
                    $nextRespData = explode('-', $next_responsibility);
                    $newInsuranceCategory = $nextRespData[0];
                    $newInsuranceId = $nextRespData[1];
                }
                
                if ($newInsuranceId != 0 && $claimDetails['insurance_id'] != $denialInsuranceId) { 
                    $claimTxDesId = $this->changeTheClaimResponsibility($claim_id, $newInsuranceCategory, $newInsuranceId, 'No',
                        ['pmt_id' => '', 'txn_id' => '']);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$claimTxDesId)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $status = $this->createClaimAndCptResTxDesc($claim_id, $newInsuranceId, $claimDetails['insurance_id'], $claimTxDesId);
                }
                if ($denialInsuranceCategory == 'Others' || $newInsuranceId == 0) {
                    $claimTxDesId = $this->changeTheClaimResponsibility($claim_id, '', '', 'Yes', ['pmt_id' => '', 'txn_id' => '']);
                    if(isset($request['backDate'])){
                        ClaimTXDESCV1::where('id',$claimTxDesId)->update(['created_at' => $this->dateformater($request['backDate'])]);
                    }
                    $this->createClaimAndCptResTxDesc($claim_id, $newInsuranceId, $claimDetails['insurance_id'], $claimTxDesId);
                }
            }
            
            /* next Responsibility block End*/
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            \Log::info("addDenialFromArManagment" . $e->getMessage());
        }
    }

    public function getClaimAttachemts($create_claim_id)
    {
        try {
            $payment_claim_ids = PMTClaimTXV1::where('claim_id', $create_claim_id)
                ->pluck('claim_id', 'payment_id')->all();
            if (!empty($payment_claim_ids)) {
                $attachment_detail = PMTInfoV1::has('attachment_detail')
                    ->with('attachment_detail')
                    ->whereIn('id', array_keys($payment_claim_ids))->get();
                return $attachment_detail;
            }
        } catch (Exception $e) {
            \Log::info("Exception catched on getClaimAttachemts. Error msg " . $e->getMessage());
            $resp = $this->showErrorResponse("getClaimAttachemts", $e);
        }
    }
    
    public function paymentPostingDocumentUpload($request,$docInsertType,$patientId,$paymentId,$claim_id){
        $pmtInfo = PMTInfoV1::where('id',$paymentId)->get()->first();
        
        if(!empty($pmtInfo)){
            if($pmtInfo->pmt_mode == 'EFT' || $pmtInfo->pmt_mode == 'Check' || $pmtInfo->pmt_mode == 'Credit'){
                $claimInfo = ClaimInfoV1::where('id',$claim_id)->get()->first();
                if(empty($patientId))
                    $patientId = $claimInfo->patient_id;
                $claim_number_data = $claimInfo->claim_number;
                if(isset($request['eob_id']) && !empty($request['eob_id'])){
                    $document_details = Document::where('temp_type_id', '=', $request['eob_id']);
                    $document_count = $document_details->count();
                    $documentDetailsInfo = $document_details->get()->first();
                    if($document_count > 0 && $docInsertType == ''){    
                        $document_details->update(['payment_id' => $paymentId, 'main_type_id' => $paymentId, 'temp_type_id' => '','claim_id'=>$claim_id,'claim_number_data'=>$claim_number_data,'type_id'=>$patientId,'checkno'=>@$request['check_no'],'checkamt'=>@$request['payment_amt'],'checkdate'=>date('Y-m-d',strtotime(@$request['check_date'])),'payer'=>@$request['claim_insurance_id']]);
                        DocumentFollowupList::where('document_id',$documentDetailsInfo->id)->update(['patient_id'=>$patientId,'claim_id'=>$claim_id]);
                    }
                }
                $documentPaymentDetails = Document::where('payment_id', '=', $paymentId);
                $documentPaymentCount = $documentPaymentDetails->count();
                $documentPaymentInfo = $documentPaymentDetails->get()->first();
                if($docInsertType == 'New Claim' && $documentPaymentCount > 0){
                    $dataArr['practice_id'] = $documentPaymentInfo->practice_id;
                    $dataArr['type_id'] = $patientId;
                    $dataArr['document_type'] = $documentPaymentInfo->document_type;
                    $dataArr['main_type_id'] = $documentPaymentInfo->main_type_id;
                    $dataArr['claim_id'] = $documentPaymentInfo->$claim_id;
                    $dataArr['payment_id'] = $documentPaymentInfo->payment_id;
                    $dataArr['upload_type'] = $documentPaymentInfo->upload_type;
                    $dataArr['document_path'] = $documentPaymentInfo->document_path;
                    $dataArr['document_extension'] = $documentPaymentInfo->document_extension;
                    $dataArr['document_domain'] = $documentPaymentInfo->document_domain;
                    $dataArr['title'] = $documentPaymentInfo->title;
                    $dataArr['category'] = $documentPaymentInfo->category;
                    $dataArr['document_categories_id'] = $documentPaymentInfo->document_categories_id;
                    $dataArr['filename'] = $documentPaymentInfo->filename;
                    $dataArr['checkdate'] = $documentPaymentInfo->checkdate;
                    $dataArr['filesize'] = $documentPaymentInfo->filesize;
                    $dataArr['page'] = $documentPaymentInfo->page;
                    $dataArr['payer'] = $documentPaymentInfo->payer;
                    $dataArr['checkno'] = $documentPaymentInfo->checkno;
                    $dataArr['checkamt'] = $documentPaymentInfo->checkamt;
                    $dataArr['user_email'] = $documentPaymentInfo->user_email;
                    $dataArr['claim_number_data'] = $claim_number_data;
                    $dataArr['mime'] = $documentPaymentInfo->mime;
                    $dataArr['original_filename'] = $documentPaymentInfo->original_filename;
                    $dataArr['created_by'] = $documentPaymentInfo->created_by;
                    $newDocument = Document::create($dataArr);

                    $oldDocument = DocumentFollowupList::where('document_id',$documentPaymentInfo->id)->get()->first();
                    $assign_data['document_id'] = $newDocument->id;
                    $assign_data['assigned_user_id'] = $oldDocument->assigned_user_id;
                    $assign_data['patient_id'] = $oldDocument->patient_id;
                    $assign_data['claim_id'] = $oldDocument->claim_id;
                    $assign_data['notes'] = $oldDocument->notes;
                    $assign_data['priority'] = $oldDocument->priority;
                    $assign_data['followup_date'] = $oldDocument->followup_date;
                    $assign_data['status'] = $oldDocument->status;
                    $assign_data['created_by'] = Auth::user()->id;
                    $assigned_data = DocumentFollowupList::create($assign_data);
                    $assigned_data->save();
                }
            }
        }
    }  



	public function changeClaimRespobilityInClearingHouseUpdation($patientId, $claimId, $insuranceId)
    {
        try {
            $oldClaimInfodetails = ClaimInfoV1::select(['id', 'insurance_id', 'self_pay', 'status', 'insurance_category'])
                ->where('id', $claimId)->first()->toArray();
            $oldInsuranceId = $oldClaimInfodetails['insurance_id'];
            $oldInsurance_Category = $oldClaimInfodetails['insurance_category'];

           
			$insuranceData = explode('-', $insuranceId);
			$newInsuranceCategory = $insuranceData[0];
			$newInsuranceId = $insuranceData[1];
            
			if ($oldInsuranceId != $newInsuranceId && $oldInsurance_Category != $newInsuranceCategory) {
				if ($newInsuranceId != 0) {
					$claimTxDesId = $this->changeTheClaimResponsibility($claimId, $newInsuranceCategory, $newInsuranceId, 'No', ['pmt_id' => '', 'txn_id' => '']);
				} else {
					$claimTxDesId = $this->changeTheClaimResponsibility($claimId, '', '', 'Yes', ['pmt_id' => '', 'txn_id' => '']);
				}
				$status = $this->createClaimAndCptResTxDesc($claimId, $newInsuranceId, $oldInsuranceId, $claimTxDesId);
				if ($status) {                    
					ClaimInfoV1::updateClaimStatus($claimId, 'Responsibility');
				}
			}
           

        } catch (Exception $e) {
            $resp = $this->showErrorResponse("changeClaimRespobilityInClearingHouseUpdation", $e);
        }
    }

}