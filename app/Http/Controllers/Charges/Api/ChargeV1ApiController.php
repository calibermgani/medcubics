<?php

namespace App\Http\Controllers\Charges\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App\Http\Helpers\Helpers as Helpers;
use App\Models\Icd as Icd;
use App\Models\Insurance as Insurance;
use App\Models\Patients\Patient;
use App\Models\Patients\PatientAuthorization as PatientAuthorization;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use App\Models\Patients\PatientNote;
use App\Models\Payments\ClaimAddDetailsV1;
use App\Models\Payments\ClaimAnesthesiaV1;
use App\Models\Payments\ClaimCPTInfoV1;
use App\Models\Payments\ClaimCPTShadedInfoV1;
use App\Models\Payments\ClaimCPTTXDESCV1;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Payments\PMTADJInfoV1;
use App\Models\Payments\PMTCardInfoV1;
use App\Models\Payments\PMTCheckInfoV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\Payments\PMTClaimCPTTXV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTEFTInfoV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTWalletV1;
use App\Models\Cpt;
use App\Models\Provider as Provider;
use App\Models\Scheduler\PatientAppointment;
use App\Traits\ClaimUtil;
use Auth;
use DB;
use Exception;
use Image;
use Input;
use Lang;
use Log;
use Response;
use Request;

/**
  |--------------------------------------------------------------------------
  | ChargeCaptureAppApiController
  | @author Manikandan Duraisamy - CD019
  |--------------------------------------------------------------------------
  |
 */
class ChargeV1ApiController extends Controller {

    use ClaimUtil;

    /* createCharge @param $requst
     * Insert anesthesia Details into  CLAIM_ANESTHESIA GetId
     * then insert all charges details into claim_info GetId
     * then insert the all line items into CLAIM_CPT_INFO
     * then insert PMT_CLAIM_FIN
     */

    /**
     * ChargeV1ApiController constructor.
     */
    protected $paymentV1;

    public function __construct() {
        $this->paymentV1 = new PaymentV1ApiController();
    }

    public function createCharge($request) {
        try {
            $anesthesiaId = null;
            if ($request) {
                if (isset($request['anesthesia_start']) && $request['anesthesia_start'] != "") {
                    $anesthesiaId = $this->createAnesthesiaDetails($request['anesthesia_start'], $request['anesthesia_stop'], $request['anesthesia_minute'], '', 'CREATE');
                    $response = $this->createClaimInfoDetails($request, $anesthesiaId, '', 'CREATE');
                    return $response;
                } else {
                    $responseData = $this->createClaimInfoDetails($request, $anesthesiaId, '', 'CREATE');
                    return $responseData;
                }
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createCharge", $e);
        }
    }

    public function createAnesthesiaDetails($astart, $aStop, $aMinute, $anesthesiaID, $flag) {
        //anesthesia_start,anesthesia_stop,anesthesia_minute;
        try {
            $anesthesiaData = array(
                'anesthesia_start' => $astart,
                'anesthesia_stop' => $aStop,
                'anesthesia_minute' => $aMinute,
                "created_by" => Auth::user()->id
            );

            if (empty($anesthesiaID) && $flag == "CREATE") {
                $anesthesiaResultSet = ClaimAnesthesiaV1::create($anesthesiaData);
                return $anesthesiaResultSet->id;
            } elseif (isset($anesthesiaID) && !empty($anesthesiaID) && $flag == "UPDATE") {
				$claimAnesInfo = ClaimAnesthesiaV1::where('id', $anesthesiaID);
				$claimAnesCount = $claimAnesInfo->get()->count();
				if($claimAnesCount != 0)
					$claimAnesInfo->update($anesthesiaData);
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createAnesthesiaDetails", $e);
        }
    }

    public function createClaimInfoDetails($request, $anestehsiaId, $claimId, $flag) {
        try {
            $request = $this->reindexArray($request);
            $patientId = $patient_id = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
            $isHold = (array_key_exists('is_hold', $request)) ? $request['is_hold'] : '0';
            $holdReasonId = (array_key_exists('hold_reason_id', $request)) ? $request['hold_reason_id'] : '0';
            $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
            $is_send_paid_amount = (array_key_exists('is_send_paid_amount', $request)) ? $request['is_send_paid_amount'] : 'No';
            $is_send_paid_amount = ($is_send_paid_amount == 'on') ? 'Yes' : 'No';
            $insurance_category = (array_key_exists('insurance_category', $request)) ? $request['insurance_category'] : '';
            $selfPay = ($request['self'] == '1') ? "Yes" : "No";
            $resp = ($selfPay == 'Yes') ? '0' : $insuranceId;
            $claimDetailId = $request['claim_detail_id'];
            $copay = (array_key_exists('copay', $request)) ? $request['copay'] : '';
            $claim_reference = (array_key_exists('copay_detail', $request)) ? $request['copay_detail'] : '';
            $patientInsuranceId = (!empty($insurance_category)) ? $this->findPatientInsuranceDetails(['patientId' => $patientId, 'insuranceId' => $insuranceId, 'insuranceCategory' => $insurance_category], 'GET') : "0";
            $newClaimInfoDatas = array(
                'patient_id' => $patientId,
                "date_of_service" => $this->dateformater($request['dos_from'][0]),
                "charge_add_type" => 'billing',
                "icd_codes" => $this->getIcdIdsWithValue($request),
                "primary_cpt_code" => $this->valueValidation($request['cpt'][0]),
                "rendering_provider_id" => $request['rendering_provider_id'],
                "refering_provider_id" => $request['refering_provider_id'],
                "billing_provider_id" => $request['billing_provider_id'],
                "facility_id" => $request['facility_id'],
                "insurance_id" => $insuranceId,
                "pos_id" => $request['pos_id'],
                "self_pay" => $selfPay,
                "insurance_category" => $insurance_category,
                "patient_insurance_id" => $patientInsuranceId,
                "auth_no" => $request['auth_no'],
                "doi" => $this->dateformater($request['doi']),
                "admit_date" => $this->dateformater($request['admit_date']),
                "discharge_date" => $this->dateformater($request['discharge_date']),
                "anesthesia_id" => $this->valueValidation($anestehsiaId),
                "is_send_paid_amount" => $is_send_paid_amount,
                //"total_charge" => $this->valueValidation($request['total_charge']),
                "hold_reason_id" => $holdReasonId,
                "claim_type" => $this->getPayerIdbilledToInsurabce($insuranceId),
                "created_by" => (isset(Auth::user()->id)) ? Auth::user()->id : @$request['userId'],
                "total_charge" => $request['total_charge'],
                "claim_reference" => $claim_reference
            );

            $authData = array(
                'patient_id' => $patientId,
                'insurance_id' => $insuranceId,
                'pos_id' => $request['pos_id'],
                'auth_no' => $request['auth_no']
            );
            if(isset($request['cpt']) && !empty($request['cpt']))
                foreach($request['cpt'] as $cpt){
                    $cpts = Cpt::where('cpt_hcpcs',$cpt)->where('required_clia_id','Yes')->get();
                    if(count((array)$cpts)>0){
                        $clia_number = $request['facility_clai_no'];
                        break;
                    }else{
                        $clia_number = '';
                    }
                    
                }
            if (empty($claimId) && $flag == "CREATE") {
                DB::beginTransaction();

                if (isset($request['auth_no']) && !empty($request['auth_no'])) {
                    $this->storePatientAuth($authData);
                }

                $resultSet = ClaimInfoV1::create($newClaimInfoDatas);

                //update claim_count in payment Table
                $patientData = Patient::select('id', 'claim_count')->where('id', $patient_id)->first();
                if(!empty($patientData)){
                    $patientData =  $patientData->toArray() ;
                    $claim_count = $patientData['claim_count'] + 1;
                    Patient::where('id', $patientData['id'])->update(['claim_count' => $claim_count]);
                }                                

                $genClaimNo = $this->generatePaddedNumber($resultSet->id);
                if(\Session::get('practice_dbid')==75)
                    $genClaimNo = 'L'.$genClaimNo;
                if (isset($request['backDate'])) {
                    ## if back date is set should update with rtimezone '13:00:00' for getting correct date
                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                    $resultSet->update(['claim_number' => $genClaimNo, 'created_at' => $back_date]);
                } else {
                    $resultSet->update(['claim_number' => $genClaimNo]);
                }
                $claimInfoId = $resultSet->id;
                $newclaimDesrecption = array(
                    "claim_info_id" => $claimInfoId,
                    'resp' => $resp,
                    "created_by" => (isset(Auth::user()->id)) ? Auth::user()->id : @$request['userId'],
                    'charge_amt' => $request['total_charge']
                );
                $claimTxDescResult = $this->storeClaimTxnDesc('New Charge', $newclaimDesrecption);
                $request['newChargeClaimTxDescId'] = $claimTxDescResult;
                if (isset($request['backDate'])) {
                    ClaimTXDESCV1::where('id', $claimTxDescResult)->update(['created_at' => $back_date]);
                }
                //addFinData
                $this->createPaymentClaimFinData(['claim_id' => $claimInfoId, 'total_charge' => $request['total_charge'], 'patientId' => $patientId, 'claimTxDesId' => $claimTxDescResult], 'CREATE', $resp);
                if (isset($request['backDate'])) {
                    PMTClaimFINV1::where('claim_id', $claimInfoId)->update(['created_at' => $back_date]);
                }

                $claimCptInfoIds = $this->storeClaimCptInfo($patientId, $request, $claimInfoId);

                //add claimFinCptData
                //$this->createPaymentClaimCptFinData($claimCptInfoIds, $claimInfoId, 'CREATE', $resp);
                if(!isset($request['note_type']))
                    $this->savePatientNotes($request['note'], $resultSet->id, $patientId);
                if (!empty($claimDetailId)) {
                    ClaimAddDetailsV1::where('id', $claimDetailId)->update(['claim_id' => $claimInfoId]);
                } else {
                    $this->DefaultClaimDetailEntryProcess($claimInfoId,$clia_number);
                }
                // Update appointment if exist on the claim date then set status as completed for appointment
                $app_id = (isset($request['appointment_id']) && !empty($request['appointment_id'])) ? $request['appointment_id'] : '';
                PatientAppointment::updateAppointmentOnchargecreation($request['facility_id'], $request['rendering_provider_id'], $patient_id, $request['dos_from'][0], $app_id);

                // Update claim status                 
                if ($isHold == '1') {
                    ClaimInfoV1::updateClaimStatus($claimInfoId, 'hold');
                } else {
                    ClaimInfoV1::updateClaimStatus($claimInfoId, 'create_charge');
                }
                //  if copy NOT EMPTY
                //  do + => PMT_Info Pmt_claim_tx && Pmt_claim_cpt_tx Tables
                if (!empty($copay)) {
                    $paymentData = array(
                        'pmt_amt' => $request['copay_amt'],
                        'pmt_mode' => $request['copay'],
                        'reference' => '', // $request['copay_detail'], // @ While create charge reference consider only for charge reference not for payment reference
                        'pmt_type' => 'Payment',
                        'source' => 'charge',
                        'pmt_method' => 'Patient',
                        'insurance_id' => $insuranceId
                    );
                    $paymentId = $this->createPaymentInformation($paymentData, $patientId, $claimInfoId, $request);
                    $paymentClaimTx_DesIds = $this->createPaymentClaimTxDetails($paymentId, $claimInfoId, $patientId, $paymentData, $request);
                    $this->createPaymentClaimCptTxDetails($paymentId, $claimInfoId, $claimCptInfoIds, 'CREATE', $paymentClaimTx_DesIds, $paymentData, $request);
                    DB::commit();
                    // Update claim status if not hold.
                    if ($isHold != '1') {
                        // Update claim status after added copay if provided
                        ClaimInfoV1::updateClaimStatus($claimInfoId, 'patient_payment');
                    }
                    return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $claimInfoId));
                }
                if (!empty($claimTxDescResult)) {
                    DB::commit();
                    return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $claimInfoId));
                } else {
                    DB::rollBack();
                    return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => ''));
                }
            } elseif (!empty($claimId) && $flag == "UPDATE") {
                DB::beginTransaction();
                if (isset($request['auth_no']) && !empty($request['auth_no'])) {
                    $this->storePatientAuth($authData);
                }
                $claimInfoV1oldData = ClaimInfoV1::select('insurance_id', 'self_pay', 'total_charge')
                                ->where('id', $claimId)->get()->first();

                $request['oldclmInfo_Chr_Amt'] = $claimInfoV1oldData['total_charge'];
                $editDescription = array(
                    "claim_info_id" => $claimId,
                    "old_insurance_id" => ($claimInfoV1oldData['self_pay'] == 'Yes') ? '0' : $claimInfoV1oldData['insurance_id'],
                    "new_insurance_id" => $resp,
                    'resp' => $resp
                );
                if ($claimInfoV1oldData['insurance_id'] != $request['insurance_id']) {
                    $claimTxDesId = $this->storeClaimTxnDesc('Responsibility', $editDescription);
                    $request['respChangeClaimTxDescId'] = $claimTxDesId;
                    if (isset($request['backDate'])) {
                        $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                        ClaimTXDESCV1::where('id', $claimTxDesId)->update(['created_at' => $back_date]);
                    }
                    $request['resChanged'] = true;
                    $request['oldclmInfo_Res_Id'] = $editDescription['old_insurance_id'];

                    // update claim fin details
                    $this->createPaymentClaimFinData(['claim_id' => $claimId, 'total_charge' => $claimInfoV1oldData['total_charge'], 'claimTxDesId' => $claimTxDesId], 'UPDATE', $resp);
                    if (isset($request['backDate'])) {
                        $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                        PMTClaimFINV1::where('claim_id', $claimId)->update(['created_at' => $back_date]);
                    }
                }
                // Updated by 
                $newClaimInfoDatas["updated_by"] = (isset(Auth::user()->id)) ? Auth::user()->id : @$request['userId'];
                ClaimInfoV1::where('id', $claimId)->update($newClaimInfoDatas);

                $claimCptInfoIds = $this->storeClaimCptInfo($patientId, $request, $claimId);
                $this->savePatientNotes($request['note'], $claimId, $patientId);

                if (!empty($claimDetailId)) {
                    if(!empty($clia_number))
                        ClaimAddDetailsV1::where('id', $claimDetailId)->update(['claim_id' => $claimId, 'box23_type'=>'clia_no', 'box_23'=>$clia_number]);
                    else
                        ClaimAddDetailsV1::where('id', $claimDetailId)->update(['claim_id' => $claimId]);
                } else {
                    $this->DefaultClaimDetailEntryProcess($claimId,$clia_number);
                }
                //  if copy NOT EMPTY
                //  do + => PMT_Info Pmt_claim_tx && Pmt_claim_cpt_tx Tables
                ////dd($request);
                if (empty($request['copay_id']) && !empty($request['copay'])) {
                    $paymentData = array(
                        'pmt_amt' => $request['copay_amt'],
                        'pmt_mode' => $request['copay'],
                        'reference' => '', // $request['copay_detail'], // @ While create charge reference consider only for charge reference not for payment reference
                        'pmt_type' => 'Payment',
                        'source' => 'charge',
                        'pmt_method' => 'Patient',
                        'insurance_id' => $insuranceId
                    );
                    $paymentId = $this->createPaymentInformation($paymentData, $patientId, $claimId, $request); //no need becase we dont allowed to edit.
                    $paymentClaimTx_DesIds = $this->createPaymentClaimTxDetails($paymentId, $claimId, $patientId, $paymentData, $request);
                    //Log::info("payment on Update Cpts; Message: " .$paymentClaimTx_DesIds);
                    $this->createPaymentClaimCptTxDetails($paymentId, $claimId, $claimCptInfoIds, 'CREATE', $paymentClaimTx_DesIds, $paymentData, $request);
                } elseif (!empty($request['copay_id'])) {
                    $paymentData = array(
                        'pmt_type' => 'Payment',
                        'pmt_method' => 'Patient'
                    );
                    $copay_payment_Id = (array_key_exists('copay_id', $request)) ? $request['copay_id'] : '0';
                    $paymentClaimTx_DesIds = array('pmtClmTxId' => '', 'claimTxnDescId' => '');
                    $this->createPaymentClaimCptTxDetails($copay_payment_Id, $claimId, $claimCptInfoIds, 'UPDATE', $paymentClaimTx_DesIds, $paymentData, $request);
                }

                // Update claim status                 
                if ($isHold == '1') {
                    ClaimInfoV1::updateClaimStatus($claimId, 'hold');
                } else {
                    (isset($request['save_resumit']) && $request['save_resumit'] == 1) ? "" : ClaimInfoV1::updateClaimStatus($claimId, 'edit_charge');
                }
                DB::commit();
                return Response::json(array('status' => 'success', 'message' => Lang::get("common.validation.create_msg"), 'data' => $claimId));
            }
        } catch (Exception $e) {
            DB::rollBack();
            $this->showErrorResponse("createClaimInfoDetails", $e);
            return Response::json(array('status' => 'error', 'message' => Lang::get("common.validation.error_msg"), 'data' => ''));
        }
    }

    public function storeClaimCptInfo($patientId, $request, $claimId) {
        try {
            if (!empty($claimId) && $request) {
                $cptDatas = (array_key_exists('cpt', $request)) ? $request['cpt'] : [];
                $selfPay = (array_key_exists('self', $request)) ? $request['self'] : '0';
                $selfPay = ($selfPay == '1') ? "Yes" : "No";
                $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
                $newChargeClaimTxDescId = (array_key_exists('newChargeClaimTxDescId', $request)) ? $request['newChargeClaimTxDescId'] : '0';
                $respChangeClaimTxDescId = (array_key_exists('respChangeClaimTxDescId', $request)) ? $request['respChangeClaimTxDescId'] : '0';
                $resChanged = (array_key_exists('resChanged', $request)) ? $request['resChanged'] : false;
                $oldresp = (array_key_exists('oldclmInfo_Res_Id', $request)) ? $request['oldclmInfo_Res_Id'] : '0';
                $oldChrAmt = (array_key_exists('oldclmInfo_Chr_Amt', $request)) ? $request['oldclmInfo_Chr_Amt'] : '0';
                $activeLineItems = (array_key_exists('active_lineitem', $request)) ? $request['active_lineitem'] : [];
                $claimCptInfoIds = array();
                $oneTimeclmTxDes = $oneTimeclmTxDes1 = $oneTimeclmTxDes2 = $oneTimeclmTxDes3 = true;
                $editChargeDesId = $editChargeDesId1 = 0;
                $dataCount = count((array)$cptDatas);
                for ($i = 0; $i < $dataCount; $i++) {
                    if (!empty($request['cpt'][$i])) {
                        global $activeLineItem;
                        if (isset($activeLineItems) && !empty($activeLineItems)) {
                            $activeLineItem = isset($request['active_lineitem'][$i]) ? $request['active_lineitem'][$i] : 0;
                            $activeLineItem = (!empty($activeLineItem)) ? 1 : $activeLineItem;
                        } else {
                            $activeLineItem = 1;
                        }
                        $newClaimCptData = array(
                            "patient_id" => $patientId,
                            "claim_id" => $claimId,
                            "dos_from" => $this->dateformater($request['dos_from'][$i]),
                            "dos_to" => $this->dateformater($request['dos_to'][$i]),
                            "cpt_code" => $request['cpt'][$i],
                            "modifier1" => $request['modifier1'][$i],
                            "modifier2" => $request['modifier2'][$i],
                            "modifier3" => $request['modifier3'][$i],
                            "modifier4" => $request['modifier4'][$i],
                            "cpt_icd_code" => (isset($request["icd".($i+1)]) && $request["icd".($i+1)] != '') ? $request["icd".($i+1)] : $request['cpt_icd_map'][$i],
                            "cpt_icd_map_key" => $request['cpt_icd_map_key'][$i],
                            "unit" => $request['unit'][$i],
                            "charge" => $request['charge'][$i],
                            "is_active" => $activeLineItem,
                            "created_by" => (isset(Auth::user()->id)) ? Auth::user()->id : @$request['userId'],
                        );
                        $box_24_AToG = $request['box_24_AToG'][$i];
                        $claimCptInfoIdsoldData = array();
                        $rowId = (isset($request['ids'][$i]) && !empty($request['ids'][$i])) ? Helpers::getEncodeAndDecodeOfId($request['ids'][$i], 'decode') : 0;
                        if ($rowId > 0) {
                            $claimCptInfoIdsoldData = ClaimCPTInfoV1::select('id', 'charge')
                                            ->where('id', $rowId)->first()->toArray();
                        }
                        //remove updateOrCreate() and add create,update fun in 5.6 
                       /* if ($rowId <= 0) {
                             $resultSet = ClaimCPTInfoV1::Create($newClaimCptData);
                        }else{
                             $resultSet = ClaimCPTInfoV1::where('id' , $rowId)->update( $newClaimCptData);
                        } */  
                        $resultSet = ClaimCPTInfoV1::updateOrCreate(['id' => $rowId], $newClaimCptData);
                        $currentCptID = $resultSet->id;
                        if (isset($request['backDate'])) {
                            $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                            ClaimCPTInfoV1::where('id', $currentCptID)->update(['created_at' => $back_date]);
                        }

                        $resp = ($selfPay == 'Yes') ? '0' : $insuranceId;
                        $boxrowId = (isset($request['box_ids'][$i]) && !empty($request['box_ids'][$i])) ? Helpers::getEncodeAndDecodeOfId($request['box_ids'][$i], 'decode') : 0;
                        if (!empty($box_24_AToG)) {
                            $box_24Ato_Jdata['box_24_AToG'] = $box_24_AToG;
                            $box_24Ato_Jdata['claim_cpt_info_v1_id'] = $resultSet->id;
                            $box_24Ato_Jdata['created_by'] = Auth::user()->id;
                            ClaimCPTShadedInfoV1::updateOrCreate(['id' => $boxrowId], $box_24Ato_Jdata);
                        }
                        if ($rowId > 0) {
                            $newCptDes = array(
                                'claim_info_id' => $claimId,
                                'claim_cpt_info_id' => $claimCptInfoIdsoldData['id'],
                                'value1' => $claimCptInfoIdsoldData['charge'],
                                "claim_tx_desc_id" => $newChargeClaimTxDescId,
                                'value2' => $request['charge'][$i],
                                'resp' => $resp,
                                'old_insurance_id' => $oldresp,
                                'new_insurance_id' => $resp,
                                'pmt_id' => ''
                            );
                            //If charge_amount and billed_to(Insurance) changed then we need to create a claim and cpt Tx desc
                            if ($claimCptInfoIdsoldData['charge'] != $request['charge'][$i] && !$resChanged) {
                                // Charge amount modified, responsibility not modified
                                if ($oneTimeclmTxDes) {
                                    $editChargeDesId = $this->storeClaimTxnDesc('Edit Charge', ["claim_info_id" => $claimId, 'resp' => $resp, 'old_charge_amt' => $oldChrAmt, 'new_charge_amt' => $request['total_charge']]);
                                    if (isset($request['backDate'])) {
                                        $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                        ClaimTXDESCV1::where('id', $editChargeDesId)->update(['created_at' => $back_date]);
                                    }
                                    $this->createPaymentClaimFinData(['claim_id' => $claimId, 'total_charge' => $request['total_charge'], 'claimTxDesId' => $editChargeDesId], 'UPDATE', $resp);
                                    $oneTimeclmTxDes = false;
                                }
                                $newCptDes['claim_tx_desc_id'] = $editChargeDesId;
                                $newCptDes['old_charge_amt'] = $claimCptInfoIdsoldData['charge'];
                                $newCptDes['new_charge_amt'] = $newClaimCptData['charge'];

                                $cpttxDesId = $this->storeClaimCptTxnDesc('Edit Charge', $newCptDes);
                                if (isset($request['backDate'])) {
                                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                    ClaimCPTTXDESCV1::where('id', $cpttxDesId)->update(['created_at' => $back_date]);
                                }
                                $finData = array(array(
                                        'claimCptId' => $currentCptID,
                                        'charge' => $newClaimCptData['charge'],
                                        'cptTxDesId' => $cpttxDesId)
                                );
                                $this->createPaymentClaimCptFinData($finData, $claimId, 'UPDATE', $resp);
                            } elseif ($claimCptInfoIdsoldData['charge'] == $request['charge'][$i] && $resChanged) {
                                $newCptDes['claim_tx_desc_id'] = $respChangeClaimTxDescId;
                                $cpttxDesId = $this->storeClaimCptTxnDesc('Responsibility', $newCptDes);
                                if (isset($request['backDate'])) {
                                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                    ClaimCPTTXDESCV1::where('id', $cpttxDesId)->update(['created_at' => $back_date]);
                                }
                                $finData = array(array(
                                        'claimCptId' => $currentCptID,
                                        'charge' => $claimCptInfoIdsoldData['charge'],
                                        'cptTxDesId' => $cpttxDesId)
                                );
                                // Charge amount not modified, responsibility only changed
                                $this->createPaymentClaimCptFinData($finData, $claimId, 'UPDATE', $resp);
                            } elseif ($claimCptInfoIdsoldData['charge'] != $request['charge'][$i] && !empty($respChangeClaimTxDescId) && $resChanged) {
                                if ($oneTimeclmTxDes1) {
                                    // Charge amount modified and responsibility changed
                                    $editChargeDesId1 = $this->storeClaimTxnDesc('Edit Charge', ["claim_info_id" => $claimId, 'resp' => $resp, 'old_charge_amt' => $oldChrAmt, 'new_charge_amt' => $request['total_charge']]);
                                    if (isset($request['backDate'])) {
                                        $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                        ClaimTXDESCV1::where('id', $editChargeDesId1)->update(['created_at' => $back_date]);
                                    }
                                    $this->createPaymentClaimFinData(['claim_id' => $claimId, 'total_charge' => $request['total_charge'], 'claimTxDesId' => $editChargeDesId1], 'UPDATE', $resp);
                                    $oneTimeclmTxDes1 = false;
                                }

                                $newCptDes['claim_tx_desc_id'] = $respChangeClaimTxDescId;
                                $cpttxDesId = $this->storeClaimCptTxnDesc('Responsibility', $newCptDes);
                                if (isset($request['backDate'])) {
                                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                    ClaimCPTTXDESCV1::where('id', $cpttxDesId)->update(['created_at' => $back_date]);
                                }
                                $finData = array(array(
                                        'claimCptId' => $currentCptID,
                                        'charge' => $claimCptInfoIdsoldData['charge'],
                                        'cptTxDesId' => $cpttxDesId)
                                );
                                $this->createPaymentClaimCptFinData($finData, $claimId, 'UPDATE', $resp);

                                $newCptDes['claim_tx_desc_id'] = $editChargeDesId1;
                                $cpttxDesId = $this->storeClaimCptTxnDesc('Edit Charge', $newCptDes);
                                if (isset($request['backDate'])) {
                                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                    ClaimCPTTXDESCV1::where('id', $cpttxDesId)->update(['created_at' => $back_date]);
                                }
                                $finData = array(array(
                                        'claimCptId' => $currentCptID,
                                        'charge' => $newClaimCptData['charge'],
                                        'cptTxDesId' => $cpttxDesId)
                                );
                                $this->createPaymentClaimCptFinData($finData, $claimId, 'UPDATE', $resp);
                            }
                        } elseif ($rowId == 0) {
                            //if new line Item add on update Charge
                            if (($oneTimeclmTxDes2 && empty($editChargeDesId) && empty($editChargeDesId1) && empty($newChargeClaimTxDescId) && empty($respChangeClaimTxDescId))) {
                                $claim_tx_desc_id = $this->storeClaimTxnDesc('Edit Charge', ["claim_info_id" => $claimId, 'resp' => $resp, 'old_charge_amt' => '', 'new_charge_amt' => $request['total_charge']]);
                                if (isset($request['backDate'])) {
                                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                    ClaimTXDESCV1::where('id', $claim_tx_desc_id)->update(['created_at' => $back_date]);
                                }
                                $this->createPaymentClaimFinData(['claim_id' => $claimId, 'total_charge' => $request['total_charge'], 'patientId' => $patientId, 'claimTxDesId' => $claim_tx_desc_id], 'UPDATE', $resp);
                                $oneTimeclmTxDes2 = false;
                            } else if (($oneTimeclmTxDes3 && empty($editChargeDesId) && empty($editChargeDesId1) && empty($newChargeClaimTxDescId) && !empty($respChangeClaimTxDescId))) {
                                $claim_tx_desc_id = $this->storeClaimTxnDesc('Edit Charge', ["claim_info_id" => $claimId, 'resp' => $resp]);
                                if (isset($request['backDate'])) {
                                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                    ClaimTXDESCV1::where('id', $claim_tx_desc_id)->update(['created_at' => $back_date]);
                                }
                                $this->createPaymentClaimFinData(['claim_id' => $claimId, 'total_charge' => $request['total_charge'], 'patientId' => $patientId, 'claimTxDesId' => $claim_tx_desc_id], 'UPDATE', $resp);
                                $oneTimeclmTxDes3 = false;
                            } else {
                                if (empty($newChargeClaimTxDescId)) {
                                    $claim_tx_desc_id = empty($editChargeDesId) ? $editChargeDesId1 : $editChargeDesId;
                                } else {
                                    $claim_tx_desc_id = $newChargeClaimTxDescId;
                                }
                                $this->createPaymentClaimFinData(['claim_id' => $claimId, 'total_charge' => $request['total_charge'], 'patientId' => $patientId, 'claimTxDesId' => $claim_tx_desc_id], 'UPDATE', $resp);
                            }
                            $cpttxDesId = $this->storeClaimCptTxnDesc('New Charge', ["claim_info_id" => $claimId, "claim_tx_desc_id" => $claim_tx_desc_id, "claim_cpt_info_id" => $currentCptID, "resp" => $resp, 'charge_amt' => $newClaimCptData['charge'], "created_by" => (isset(Auth::user()->id)) ? Auth::user()->id : @$request['userId'] ]);
                            if (isset($request['backDate'])) {
                                $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                ClaimCPTTXDESCV1::where('id', $cpttxDesId)->update(['created_at' => $back_date]);
                            }
                            $finData = array(array(
                                    'claimCptId' => $currentCptID,
                                    'charge' => $newClaimCptData['charge'],
                                    'cptTxDesId' => $cpttxDesId)
                            );
                            $this->createPaymentClaimCptFinData($finData, $claimId, 'CREATE', $resp);
                        }
                        $temp = array(
                            "claimCptId" => $currentCptID,
                            "patientPaid" => $request['copay_applied'][$i],
                            "claimCptTxId" => Helpers::getEncodeAndDecodeOfId($request['copay_Transcation_ID'][$i], 'decode'),
                            "charge" => $request['charge'][$i]
                        );
                        array_push($claimCptInfoIds, $temp);
                    } else {
                        //if cpt is empty then delete the cpt from claimInfo
                        //cptInfo
                        //cptfin
                        $resp = ($selfPay == 'Yes') ? '0' : $insuranceId;
                        $this->createPaymentClaimFinData(['claim_id' => $claimId, 'total_charge' => $request['total_charge'], 'claimTxDesId' => $editChargeDesId], 'UPDATE', $resp);
                        $rowId = (isset($request['ids'][$i]) && !empty($request['ids'][$i])) ? Helpers::getEncodeAndDecodeOfId($request['ids'][$i], 'decode') : 0;
                        ClaimCPTInfoV1::where('id', $rowId)->forceDelete();
                        PMTClaimCPTFINV1::where('claim_cpt_info_id', $rowId)->forceDelete();
                        $this->updateClaimFinData($claimId);
                        ClaimCPTTXDESCV1::where('claim_cpt_info_id', $rowId)->forceDelete();
                    }
                }
                return $claimCptInfoIds;
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("Create storeClaimCptInfo", $e);
        }
    }

    public function storePatientAuth($data) {
        $auth_data = PatientAuthorization::where('patient_id', $data['patient_id'])
                        ->where('authorization_no', $data['auth_no'])->get()->first();
        if (empty($auth_data)) {
            $authorization_arr = [
                'patient_id' => $data['patient_id'],
                'insurance_id' => $data['insurance_id'],
                'pos_id' => $data['pos_id'],
                'authorization_no' => $data['auth_no'],
                'authorization_notes' => (isset($data['authorization_notes'])) ? $data['authorization_notes'] : '',
            ];
            PatientAuthorization::create($authorization_arr);
        }
    }

    public function savePatientNotes($note, $claim_id, $patient_id) {
        if (!empty(trim($note))) {
            $claim_note['content'] = $note;
            $claim_note['notes_type'] = 'patient';
            $claim_note['patient_notes_type'] = 'claim_notes';
            $claim_note['notes_type_id'] = $patient_id;
            $claim_note['title'] = 'charge';
            $claim_note['created_by'] = (isset(Auth::user()->id)) ? Auth::user()->id : '';
            $claim_note['created_at'] = Date('Y-m-d H:i:s');
            $claim_note['claim_id'] = $claim_id;
            PatientNote::insert($claim_note);
        }
    }

    //ajax Call form the claimDetails popUp
    public function createClaimAddDetails($request) {
        try {
            if ($request) {
                $request['other_date'] = $this->dateformater($request['other_date']);
                $request['illness_box14'] = $this->dateformater($request['illness_box14']);
                $request['unable_to_work_from'] = $this->dateformater($request['unable_to_work_from']);
                $request['unable_to_work_to'] = $this->dateformater($request['unable_to_work_to']);
                $request['otherclaimid'] = $request['otherclaimid'];
                $result = ClaimAddDetailsV1::create($request);
                if ($result) {
                    return Response::json(array('status' => 'success', 'message' => 'Additional claim details added successfully.', 'data' => $result->id));
                } else {
                    return Response::json(array('status' => 'error', 'message' => 'claim detail did not added successfully', 'data' => ''));
                }
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("create ClaimAddDetails", $e);
        }
    }

    public function DefaultClaimDetailEntryProcess($claim_id,$clia_number) {
        $temp = array();
        $temp['claim_id'] = $claim_id;
        $temp['type'] = "default";
        $temp['is_provider_employed'] = "No";
        $temp['is_employment'] = "No";
        $temp['is_autoaccident'] = "No";
        $temp['is_otheraccident'] = "No";
        $temp['accept_assignment'] = "Yes";
        if($clia_number!=''){
            $temp['box23_type'] = 'clia_no';
            $temp['box_23'] = $clia_number;
        }
        ClaimAddDetailsV1::create($temp);
    }

    //create ClaimTransaction Descreption Process
    public function createClaimTransactionDescreption($requestData) {
        try {
            if ($requestData) {
                $returnData = ClaimTXDESCV1::create($requestData);
                return $requestData;
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("create ClaimTransactionDescreption", $e);
        }
    }

    //payment Table Entry
    public function createPaymentInformation($datas, $patientId, $claimId, $request) {
        try {
            if ($datas) {
                $newCreatePaymentInfo = array(
                    "pmt_no" => time(),
                    "pmt_type" => $datas['pmt_type'],
                    "patient_id" => $patientId,
                    "insurance_id" => $this->valueValidation($datas['insurance_id']),
                    "pmt_amt" => isset($datas['pmt_amt']) ? $datas['pmt_amt'] :0,
                    "source" => $datas['source'],
                    "source_id" => $claimId, //its depand up on source
                    "pmt_method" => $datas['pmt_method'],
                    "pmt_mode" => $datas['pmt_mode'],
                    "pmt_mode_id" => $this->createPaymentMode($datas['pmt_mode'], $request),
                    "reference" => $datas['reference'],
                    "created_by" =>  (isset(Auth::user()->id)) ? Auth::user()->id:@$datas['created_by']
                );
                // To handle wallet history 
                if ($datas['pmt_type'] == 'Refund' && $datas['pmt_method'] == 'Patient' && $datas['source'] == 'refundwallet') {
                    $newCreatePaymentInfo['amt_used'] = $datas['pmt_amt'];
                }

                $paymentInfoResultSet = PMTInfoV1::create($newCreatePaymentInfo);
                if (isset($request['backDate'])) {
                    PMTInfoV1::where('id', $paymentInfoResultSet->id)->update(['created_at' => $this->dateformater($request['backDate'])]);
                }
                /*** user activity ***/
                $action = "add";
                $fetch_url = Request::url();
                $split_url = explode("/", $fetch_url);
                if(end($split_url)=="getclaimdenailnotesadded"){
                    $module = "AR Management";
                    $get_name = $newCreatePaymentInfo['pmt_no'].' '.$datas['pmt_mode'].' Denials';
                }
                else{
                    $module = "payments";
                    $get_name = $newCreatePaymentInfo['pmt_no'].' '.$datas['pmt_mode'];
                }
                $submodule = "";
                $this->user_activity($module, $action, $get_name, $fetch_url, $submodule);
                /*** user activity ***/
                return $paymentInfoResultSet->id;
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createPaymentInformation", $e);
        }
    }

    public function createPaymentMode($type, $datas) {
        try {
            if (!empty($type)) {
                if ($type == "Check") {
                    $resultSet = PMTCheckInfoV1::create([
                                "check_no" => isset($datas['check_no']) ? $datas['check_no'] : '0',
                                "check_date" => $this->dateformater($datas['check_date']),
                                "created_by" => Auth::user()->id]
                    );
                    return $resultSet->id;
                } elseif ($type == "Credit") {
					
                    if (isset($datas['card_no']) || isset($datas['card_type']) || isset($datas['check_no'])) {
                        // Considered card not 12 digit and stored it in corresponding fields.
                        $card_no = isset($datas['card_no']) ? $datas['card_no'] : $datas['check_no'];
                        $card_type = isset($datas['card_type']) ? $datas['card_type'] : '';
                        $card_no = ($card_no != '' && strlen($card_no) < 12 ) ? str_pad($card_no, 12, "##", STR_PAD_LEFT) : $card_no;
                        $card_first = ($card_no != '') ? str_replace("#", "", substr($card_no, 0, 4)) : '';
                        $card_middle = ($card_no != '') ? str_replace("#", "", substr($card_no, 4, 4)) : '';
                        $card_last = ($card_no != '') ? str_replace("#", "", substr($card_no, -4, 4)) : '';
                        if(isset($datas['cardexpiry_date']) || isset($datas['check_date'])) {
                            $expDate =  isset($datas['cardexpiry_date']) ? $this->dateformater($datas['cardexpiry_date']) : $this->dateformater($datas['check_date']);
                        } else {
                            $expDate = '';
                        }

                        $resultSetCardInfo = PMTCardInfoV1::create([
                                    "card_type" => $datas['card_type'],
                                    "card_first_4" => $card_first,
                                    "card_center" => $card_middle,
                                    "card_last_4" => $card_last,
                                    // need to split and firt4 and center num and last 4 datas
                                    "name_on_card" => isset($datas['name_on_card']) ? $datas['name_on_card'] : '',
                                    "expiry_date" => $expDate,
                                    "created_by" => Auth::user()->id
                        ]);
                        return $resultSetCardInfo->id;
                    } else {
                        return 0;
                    }
                } elseif ($type == "EFT") {
                    $resultSetEftInfo = PMTEFTInfoV1::create(
                                    [
										"eft_no" => isset($datas['check_no']) ? $datas['check_no'] : '0',
                                        "eft_date" => $this->dateformater($datas['check_date']),
                                        "created_by" => Auth::user()->id
                    ]);
                    return $resultSetEftInfo->id;
                } elseif ($type == "Adjustment") {
                    $insuranceId = (array_key_exists('insurance_id', $datas)) ? $datas['insurance_id'] : '0';
                    $patient_id = Helpers::getEncodeAndDecodeOfId($datas['patient_id'], 'decode');
                    $resultSetAdjInfo = PMTADJInfoV1::create(
                                    [
                                        "adj_type" => isset($datas['check_no']) ? $datas['check_no'] : '',
                                        "patient_id" => $patient_id,
                                        "insurance_id" => $insuranceId,
                                        "adj_amount" => $datas['payment_amt'],
                                        "adj_reason_id" => $datas['adjustment_reason'],
                                        "reference" => $datas['reference'],
                                        "created_by" => Auth::user()->id
                    ]);
                    return $resultSetAdjInfo->id;
                } elseif ($type == "Money Order") {
                    $checkNo = (isset($datas['money_order_no'])) ? $datas['money_order_no'] : $datas['money_order_no'];
                    $checkDate = (isset($datas['money_order_date'])) ? $datas['money_order_date'] : $datas['money_order_date'];
                    $resultSet = PMTCheckInfoV1::create([
                                "check_no" => "MO-" . $checkNo,
                                "check_date" => $this->dateformater($checkDate),
                                "created_by" => Auth::user()->id
                                    ]
                    );
                    return $resultSet->id;
                } elseif ($type == "Cash") {
                    return 0;
                } elseif ($type == "Credit Balance") {
                    return 0;
                }
            } else {
                return 0;
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createPaymentMode", $e);
        }
    }

    public function createPaymentClaimTxDetails($paymentId, $claim_id, $patientId, $datas, $request) {
        try {

            if ($datas) {
                /* $selfPay = (array_key_exists('self', $request)) ? $request['self'] : '0';
                  $selfPay = ($selfPay == '1') ? "Yes" : "No";
                  $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
                 */
                // $adjustment_reason = (array_key_exists('adjustment_reason', $request)) ? $request['adjustment_reason'] : '0';
                $newPmtClaimTx = array(
                    "payment_id" => $paymentId,
                    "claim_id" => $claim_id,
                    "pmt_method" => $datas['pmt_method'],
                    "pmt_type" => $datas['pmt_type'],
                    "patient_id" => $patientId,
                    // "total_paid"=>$datas[' '], trigger form pmt_claim_tx
                    "posting_date" => date("Y-m-d"),
                    "created_by" => Auth::user()->id
                );

                $resultSet = PMTClaimTXV1::create($newPmtClaimTx);
                if (isset($request['backDate'])) {
                    PMTClaimTXV1::where('id', $resultSet->id)->update(['created_at' => $this->dateformater($request['backDate'])]);
                }

                //create Description
                $newclaimDesrecption = array(
                    "claim_info_id" => $claim_id,
                    "pmt_id" => $paymentId,
                    'resp' => $this->getClaimResponsibility($claim_id),
                    'txn_id' => $resultSet->id,
                );
                $txnFor = $datas['pmt_method'] . ' ' . $datas['pmt_type'];

                $claimDesCreateStatus = $this->storeClaimTxnDesc($txnFor, $newclaimDesrecption);
                if (isset($request['backDate'])) {
                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                    ClaimTXDESCV1::where('id', $claimDesCreateStatus)->update(['created_at' => $back_date]);
                }
                return ['pmtClmTxId' => $resultSet->id, "claimTxnDescId" => $claimDesCreateStatus];
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createPaymentClaimTxDetails", $e);
        }
    }

    public function createPaymentClaimCptTxDetails($paymentId, $claim_id, $datas, $flag, $paymentClaimTx_DesIds, $pmt_Data, $request) {
        try {
            if ($datas) {
                $selfPay = (array_key_exists('self', $request)) ? $request['self'] : '0';
                $selfPay = ($selfPay == '1') ? "Yes" : "No";
                $insuranceId = (array_key_exists('insurance_id', $request)) ? $request['insurance_id'] : '0';
                $pmt_claim_txID = $paymentClaimTx_DesIds['pmtClmTxId'];
                $claimTxnDescId = $paymentClaimTx_DesIds['claimTxnDescId'];
                $patientId = Helpers::getEncodeAndDecodeOfId($request['patient_id'], 'decode');
                $resp = $this->getClaimResponsibility($claim_id);
                $txnFor = $pmt_Data['pmt_method'] . ' ' . $pmt_Data['pmt_type'];
                $lastRecord = count($datas) - 1;
                for ($i = 0; $i < count($datas); $i++) {
                    /* @commented  becase of desc need all cpt wheter is not filling
                     * if ((isset($datas[$i]['patientPaid']) && $datas[$i]['patientPaid'] == '0.00') ||
                     * (isset($datas[$i]['patientPaid']) && $datas[$i]['patientPaid'] == '') ||
                     * (isset($datas[$i]['writeoff']) && $datas[$i]['writeoff'] == '0.00')) {
                     * continue;
                     * } */

                    if ($flag == "CREATE") {
                        $cptChargeAmount = (array_key_exists('charge', $request)) ? $request['charge'][$i] : '0';
                        $newPmtCptTx = array(
                            "payment_id" => $paymentId,
                            "claim_id" => $claim_id,
                            "pmt_claim_tx_id" => $pmt_claim_txID,
                            "claim_cpt_info_id" => $datas[$i]['claimCptId'],
                            "paid" => (isset($datas[$i]['patientPaid'])) ? $datas[$i]['patientPaid'] : 0.00,
                            "writeoff" => (isset($datas[$i]['writeoff'])) ? $datas[$i]['writeoff'] : 0.00,
                            "created_by" => Auth::user()->id
                        );

                        $newPmtClaimCptTxId = PMTClaimCPTTXV1::create($newPmtCptTx);
                        if (isset($request['backDate'])) {
                            PMTClaimCPTTXV1::where('id', $newPmtClaimCptTxId->id)->update(['created_at' => $this->dateformater($request['backDate'])]);
                        }
                        //each Tx we need to create
                        //add and update the applied amount in charge screen
                        if (isset($datas[$i]['patientPaid'])) {
                            $paid = $datas[$i]['patientPaid'];
                            PMTInfoV1::updatePaymettAmoutUsed($paymentId, abs($paid));
                        } elseif (isset($datas[$i]['writeoff'])) {
                            $paid = $datas[$i]['writeoff'];
                            PMTInfoV1::updatePaymettAmoutUsed($paymentId, $paid);
                        }
                        // paid amount > chargeAmount then send the remaining amount to wallet

                        $newClaimCptDesrecption = array(
                            "claim_info_id" => $claim_id,
                            "pmt_id" => $paymentId,
                            'claim_cpt_info_id' => $datas[$i]['claimCptId'],
                            'claim_tx_desc_id' => $claimTxnDescId,
                            'txn_id' => $newPmtClaimCptTxId->id,
                            'resp' => $resp
                        );
                        $cptTxDesId = $this->storeClaimCptTxnDesc($txnFor, $newClaimCptDesrecption);
                        if (isset($request['backDate'])) {
                            $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                            ClaimCPTTXDESCV1::where('id', $cptTxDesId)->update(['created_at' => $back_date]);
                        }
                        ///update the claimCptFinTable if payment made
                        $newPmtCptTx['resp'] = $resp;
                        $this->updateClaimCptTxData($newPmtCptTx, $txnFor);
                        $currentFinBalance = $this->updateClaimCptFindData($newPmtCptTx, $txnFor);
                        $this->updateBalanceClaimCPTTXDesc($cptTxDesId, $currentFinBalance['cpt_patient_balance'], $currentFinBalance['cpt_insurance_balance']);

                        if ($newPmtCptTx['paid'] > $cptChargeAmount) {
                            //$walletAmount =  $newPmtCptTx['paid'] -$cptChargeAmount;
                            $newPmtCptTx['resp'] = $resp;
                            $walletTxDesId = $this->checkandSendTheBalanceAmountToWallet($newPmtCptTx);
                            if ($walletTxDesId != 0) {
                                $currentClaimFinBalance = $this->findClaimLevelPatientandInsuranceBal($newPmtCptTx, $walletTxDesId);
                                $this->updateBalanceClaimTXDesc($walletTxDesId, $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                            }
                        }
                    } elseif ($flag == "UPDATE") {
                        //find the before claimCpt  amount
                        if (isset($datas[$i]['patientPaid']) && $datas[$i]['patientPaid'] != '') {
                            $claimCptTxId = $datas[$i]['claimCptTxId'];
                            //if claimCptTxId is empty from data  use $pmtClaimTxId from parameter
                            $newPmtCptTx = array(
                                "payment_id" => $paymentId,
                                "claim_id" => $claim_id,
                                "claim_cpt_info_id" => $datas[$i]['claimCptId'],
                                "paid" => (isset($datas[$i]['patientPaid'])) ? $datas[$i]['patientPaid'] : 0.00,
                                //"writeoff" => (!empty($datas[$i]['writeoff']))?$datas[$i]['writeoff'] :0,
                                "created_by" => Auth::user()->id
                            );
                            /* if ($claimCptTxId == "") {
                              $paymentClaimTx_DesIds = $this->createPaymentClaimTxDetails($paymentId, $claim_id, $patientId, $pmt_Data, $request);
                              $newPmtCptTx['pmt_claim_tx_id'] = $paymentClaimTx_DesIds['pmtClmTxId'];
                              } else {
                              $pmtclaimTxData = PMTClaimCPTTXV1::select('pmt_claim_tx_id')->where('id', $claimCptTxId)->first();
                              $newPmtCptTx['pmt_claim_tx_id'] = $pmtclaimTxData->pmt_claim_tx_id;
                              } */
                            $updateOrCreatePmtClaimCptTxId = PMTClaimCPTTXV1::updateOrCreate(['id' => $claimCptTxId], $newPmtCptTx);
                            $newPmtCptTx['resp'] = $resp;
                            $this->updateClaimCptTxData($newPmtCptTx, $txnFor);
                            $currentFinBalance = $this->updateClaimCptFindData($newPmtCptTx, $txnFor);
                            if ($claimCptTxId == "") {
                                //edit charge -> new line item create-> using copay
                                $newclaimDesrecption = array(
                                    "claim_info_id" => $claim_id,
                                    "pmt_id" => $paymentId,
                                    "claim_cpt_info_id" => $datas[$i]['claimCptId'],
                                    'resp' => ($selfPay == 'Yes') ? '0' : $insuranceId,
                                    'txn_id' => $updateOrCreatePmtClaimCptTxId->id
                                );
                                $claimTxnDescId = $this->storeClaimTxnDesc($txnFor, $newclaimDesrecption);

                                $newcptDesrecption = array(
                                    "claim_info_id" => $claim_id,
                                    "pmt_id" => $paymentId,
                                    'claim_cpt_info_id' => $datas[$i]['claimCptId'],
                                    'claim_tx_desc_id' => $claimTxnDescId,
                                    'resp' => $resp,
                                    'txn_id' => $updateOrCreatePmtClaimCptTxId->id
                                );
                                $cptTxDesId = $this->storeClaimCptTxnDesc($txnFor, $newcptDesrecption);
                                if (isset($request['backDate'])) {
                                    $back_date = date('Y-m-d', strtotime($request['backDate'])).' 13:00:00';
                                    ClaimCPTTXDESCV1::where('id', $cptTxDesId)->update(['created_at' =>  $back_date]);
                                }
                                $this->updateBalanceClaimCPTTXDesc($cptTxDesId, $currentFinBalance['cpt_patient_balance'], $currentFinBalance['cpt_insurance_balance']);
                            }
                        }
                    }

                    if ($i == $lastRecord) {
                        $datasForClaim = array(
                            'claim_id' => $claim_id,
                            'pmt_claim_tx_id' => $pmt_claim_txID,
                            'tnxFor' => $txnFor,
                            'resp' => $resp,
                            'transaction_type' => $txnFor
                        );
                        if ($claimTxnDescId != '') {
                            $currentClaimFinBalance = $this->findClaimLevelPatientandInsuranceBal($datasForClaim, $claimTxnDescId);
                            $this->updateBalanceClaimTXDesc($claimTxnDescId, $currentClaimFinBalance['patient_balance'], $currentClaimFinBalance['insurance_balance']);
                            $this->finalClaimBalanceUpdate(['claim_id' => $claim_id]);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createPaymentClaimCptTxDetails", $e);
        }
    }

    public function getPatientClaimsList($patientId) {
        if (!empty($patientId)) {
            $paymentV1 = new PaymentV1ApiController();
            $claim_lists = App\Models\Payments\ClaimInfoV1::
                            with('facility_detail', 'paymentInfo', 'rendering_provider', 'facility', 'insurance_details', 'billing_provider', 'pmtClaimFinData')
                            ->where('patient_id', $patientId)->orderBy('id', 'DESC')->get();
            for ($m = 0; $m < count($claim_lists); $m++) {
                if (!empty($claim_lists)) {
                    $claim_id = $claim_lists[$m]->id;
                    $total_charge = $claim_lists[$m]->total_charge;
                    $claimSubmittedCount = $claim_lists[$m]->claim_submit_count;
                    $paymentInfoId = @$claim_lists[$m]->paymentInfo->id;
                    $claimStatus = @$claim_lists[$m]->status;
                    //if any payment made against claims unbilled 0 and claimsubmitted count> 0
                    if ($claimSubmittedCount > 0 || ($paymentInfoId > 0 && $claimStatus != 'Ready') || $claimStatus == 'Patient') {
                        $claim_lists[$m]['unbilled'] = false;
                    } else {
                        $claim_lists[$m]['unbilled'] = true;
                    }
                    $pmtClaimFinData = (!empty($claim_lists[$m]->pmtClaimFinData)) ? $claim_lists[$m]->pmtClaimFinData : [];

                    /*
                      //select the finData using claim_id
                      $claim_lists[$m]['total_paid'] = $paymentV1->calculateCptFinBalance($total_charge, $pmtClaimFinData)['totalPaid'];
                      $claim_lists[$m]['balance_amt'] = $paymentV1->calculateCptFinBalance($total_charge, $pmtClaimFinData)['balance'];
                     */
                    $resultData = $paymentV1->getClaimsFinDetails($claim_id, $total_charge);
                    unset($resultData['id']); //no need
                    $claim_lists[$m]['total_paid'] = $resultData['total_paid'];
                    $claim_lists[$m]['balance_amt'] = $resultData['balance_amt'];
                }
            }
            return $claim_lists;
            //return Response::json(array('status' => 'success', 'message' => null, 'data' => compact('claim_lists', 'total')));
        }
    }

    public function getIcdIdsWithValue($request) {
        $icdCodes = '';
        $icd_list = [];
        if (!empty($request['icd1'])) {
            for ($i = 1; $i <= 12; $i++) {
                $icd_list[$i] = !empty($request['icd' . $i]) ? Icd::getIcdIds($request['icd' . $i]) : '';
            }
            $icd_lists = array_filter($icd_list);
            $icdCodes = implode(',', $icd_lists);
        }
        return $icdCodes;
    }

    public function findTheStatus($is_hold, $insurance_id, $selfPay) {
        $insuranceId = (int) $this->valueValidation($insurance_id);
        $status = '';
        if ($is_hold == '1') {
            $status = "Hold";
        } elseif ($insuranceId > 0) {
            $status = "Ready";
        } elseif ($selfPay == "Yes") {
            $status = "Patient";
        }
        return $status;
    }

    public function generateClaimNumber($claim_id) {
        $claim_number = str_pad($claim_id, 5, '0', STR_PAD_LEFT);
        return $claim_number;
    }

    public function valueValidation($param) {
        if (isset($param) && (!empty($param) && !is_null($param))) {
            return $param;
        } else {
            return $param = '';
        }
    }

    /* public function dateformater($date) {
      if(!empty($date)){
      return date('Y-m-d',strtotime($date));
      }else{
      return "";
      }
      } */

    public static function getPayerIdbilledToInsurabce($insurance_id) {
        if (!empty($insurance_id)) {
            $payer_id = Insurance::where('id', $insurance_id)->value('payerid');
            return !empty($payer_id) ? "electronic" : 'paper';
        } else{
            return "paper";
        }
        return "- Nil -";
    }

    public function reindexArray($request) {
        $array_vals = ['dos_from', 'dos_to', 'cpt', 'cpt_amt', 'modifier1', 'modifier2', 'modifier3', 'modifier4', 'unit', 'charge', 'cpt_icd_map', 'cpt_allowed', 'cpt_icd_map_key', 'copay_applied', 'copay_Transcation_ID'];
        if (isset($request['ids'])) {
            array_push($array_vals, 'ids');
        } elseif (isset($request['copay_applied'])) {
            array_push($array_vals, 'copay_applied');
        }
        foreach ($array_vals as $array_val) {
            $arrval = array_values($request[$array_val]);
            $request[$array_val] = array_combine(range(0, count((array)$arrval) - 1), array_values($arrval));
        }
        return $request;
    }

    // update

    public function updateCharge($request) {
        try {
			//echo "<pre>";print_r($request);die;
            $request['claim_id'] = Helpers::getEncodeAndDecodeOfId($request['claim_id'], 'decode');
            $claimId = $request['claim_id'];

            if (!empty($claimId)) {
                $resultSet = ClaimInfoV1::find($claimId);

                if (empty($resultSet['anesthesia_id'])) {

                    $anesthesiaId = $this->createAnesthesiaDetails($request['anesthesia_start'], $request['anesthesia_stop'], $request['anesthesia_minute'], '', 'CREATE');
                    // //dd($anesthesiaId);
                    $responseUpdateData = $this->createClaimInfoDetails($request, $anesthesiaId, $claimId, "UPDATE");
                    return $responseUpdateData;
                } else {
                    $anesthesiaId = $resultSet['anesthesia_id'];
                    $this->createAnesthesiaDetails($request['anesthesia_start'], $request['anesthesia_stop'], $request['anesthesia_minute'], $anesthesiaId, 'UPDATE');
                    $responseUpdateData = $this->createClaimInfoDetails($request, $anesthesiaId, $claimId, "UPDATE");
                    return $responseUpdateData;
                }
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateCharge", $e);
        }
    }

    public function createPaymentClaimFinData($datas, $flag, $resp) {
        try {
            if ($flag == 'CREATE') {
                $finData = array(
                    'claim_id' => $datas['claim_id'],
                    'total_charge' => $datas['total_charge'],
                    'patient_id' => $datas['patientId']
                );
                if ($resp > '0') {
                    $finData['insurance_due'] = $datas['total_charge'];
                    $finData['patient_due'] = '0.00';
                } else {
                    $finData['patient_due'] = $datas['total_charge'];
                    $finData['insurance_due'] = '0.00';
                }
                PMTClaimFINV1::updateOrCreate(['id' => $datas['claim_id']], $finData);
                //update insurance and patientBal in ClaimTxDes Table
                $this->updateBalanceClaimTXDesc($datas['claimTxDesId'], $finData['patient_due'], $finData['insurance_due']);
            } elseif ($flag == 'UPDATE') {

                $finData = array(
                    'total_charge' => $datas['total_charge']
                );
                $claimFinData = PMTClaimFINV1::where('claim_id', $datas['claim_id'])->first();

                if ($resp > '0') {
                    $finData['patient_due'] = '0.00';
					// Update fin details from last fin data. 
                    $insurance_balance = $finData['total_charge'] - ($claimFinData->insurance_paid + $claimFinData->insurance_adj+$claimFinData->withheld);
                    $finData['insurance_due'] = $insurance_balance; //$finData['total_charge'];
                } else {                   
                    $patientBalance = $finData['total_charge'] - ($claimFinData->patient_paid + $claimFinData->insurance_paid + $claimFinData->withheld + $claimFinData->patient_adj + $claimFinData->insurance_adj);                     
                    //\Log::info("Calculated Patient Balance =>".$patientBalance);
                    $finData['patient_due'] = $patientBalance;
                    $finData['insurance_due'] = '0.00';
                }

                PMTClaimFINV1::where('claim_id', $datas['claim_id'])->update($finData);
                //\Log::info("Update pmt fin called: PD ".$finData['patient_due']."ID ".$finData['insurance_due']."# ".$datas['claimTxDesId']);
                $this->updateBalanceClaimTXDesc($datas['claimTxDesId'], $finData['patient_due'], $finData['insurance_due']);
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createPaymentFinDatas", $e);
        }
    }

    public function createPaymentClaimCptFinData($datas, $claimId, $flag, $resp) {
        try {
            if ($flag == 'CREATE') {
                foreach ($datas as $data) {
                    $finData = array(
                        'claim_id' => $claimId,
                        'claim_cpt_info_id' => $data['claimCptId'],
                        'cpt_charge' => $data['charge']
                    );
                    $result = PMTClaimCPTFINV1::updateOrCreate(['claim_cpt_info_id' => $data['claimCptId']], $finData);
                    $currentCptDatas = PMTClaimCPTFINV1::where('id', $result->id)->first();
                    if ($resp > '0') {
                        $PMTClaimCPTFINV1 = PMTClaimCPTFINV1::find($currentCptDatas->id);
                        $PMTClaimCPTFINV1->insurance_balance = $currentCptDatas->cpt_charge;
                        $PMTClaimCPTFINV1->patient_balance = '0.00';
                        $PMTClaimCPTFINV1->save();
                        //update patientBal and insbalance to cptTxDes Table
                        $this->updateBalanceClaimCPTTXDesc($data['cptTxDesId'], '0.00', $currentCptDatas->cpt_charge);
                    } else {
                        $PMTClaimCPTFINV1 = PMTClaimCPTFINV1::find($currentCptDatas->id);
                        $PMTClaimCPTFINV1->insurance_balance = '0.00';
                        $patientBalance = $currentCptDatas->cpt_charge - ($currentCptDatas->patient_paid + $currentCptDatas->patient_adjusted);
                        $PMTClaimCPTFINV1->patient_balance = $patientBalance;
                        $PMTClaimCPTFINV1->save();
                        //update patientBal and insbalance to cptTxDes Table
                        $this->updateBalanceClaimCPTTXDesc($data['cptTxDesId'], $patientBalance, '0.00');
                    }
                }
                $this->updateClaimFinData($claimId);
            } elseif ($flag == 'UPDATE') {
                foreach ($datas as $data) {
                    $finData = array(
                        'cpt_charge' => $data['charge']
                    );
                    $currentCptDatas = PMTClaimCPTFINV1::where('claim_cpt_info_id', $data['claimCptId'])->first();
                    if (isset($currentCptDatas) && !empty($currentCptDatas)) {
                        if ($resp > '0') {
                            $finData['insurance_balance'] = $finData['cpt_charge'];
                            $finData['patient_balance'] = '0.00';
                        } else {
                            $finData['insurance_balance'] = '0.00';
                            $finData['patient_balance'] = $finData['cpt_charge'] - ($currentCptDatas->patient_paid + $currentCptDatas->patient_adjusted);
                        }
                    }
                    PMTClaimCPTFINV1::where('claim_cpt_info_id', $data['claimCptId'])
                            ->where('claim_id', $claimId)->update($finData);
                    $this->updateBalanceClaimCPTTXDesc($data['cptTxDesId'], $finData['patient_balance'], $finData['insurance_balance']);
                }
                $this->updateClaimFinData($claimId);
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("createPaymentClaimCptFinData", $e);
        }
    }

    public function updateClaimCptFindData($datas, $paymentType) {
        try {
            $claim_id = $datas['claim_id'];
            $datas['tnxFor'] = $paymentType;
            $datas['transaction_type'] = $paymentType;
            $claim_cpt_info_id = $datas['claim_cpt_info_id'];

            switch ($paymentType) {
                case "Patient Payment":
                    $currentFinBalance = $this->updateCptFin_ClaimFin($datas, 'paid', 'patient_paid', 'patient_paid');
                    break;
                case "Patient Refund":
                    $currentFinBalance = $this->updateCptFin_ClaimFin($datas, 'paid', 'patient_paid', 'patient_paid');
                    break;
                case "Patient Adjustment":
                    $currentFinBalance = $this->updateCptFin_ClaimFin($datas, 'writeoff', 'patient_adjusted', 'patient_adj');
                    break;
                case "Patient Credit Balance":
                    $currentFinBalance = $this->updateCptFin_ClaimFin($datas, 'paid', 'patient_paid', 'patient_paid');
                    break;
                case "Wallet":
                    $currentFinBalance = $this->updateCptFin_ClaimFin($datas, 'paid', 'patient_paid', 'patient_paid');
                    break;
                case "Insurance Payment":
                    $currentFinBalance = $this->updateInsuranceCptFin_ClaimFin($datas, $claim_id);
                    break;
                case "Insurance Refund":
                    $currentFinBalance = $this->updateInsuranceCptFin_ClaimFin($datas, $claim_id);
                    //$currentFinBalance  = $this->updateInsuranceCptFin_ClaimFin_v1($datas, $claim_id);
                    break;
                case "Insurance Adjustment":
                    $currentFinBalance = $this->updateInsuranceCptFin_ClaimFin($datas, $claim_id);
                    break;
            }
            return $currentFinBalance;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateClaimCptFindData", $e);
        }
    }

    public function updateClaimCptTxData($datas, $paymentType) {
        try {
            if (isset($datas['pmt_claim_tx_id']) && !empty($datas['pmt_claim_tx_id'])) {
                $claim_id = $datas['claim_id'];
                $pmt_claim_tx_id = $datas['pmt_claim_tx_id'];
                switch ($paymentType) {

                    case "Patient Payment":
                        $resultSet = $this->getPatientPaidAmountTotal($pmt_claim_tx_id, $claim_id);
                        $arr = array(
                            'total_paid' => $resultSet->paid
                        );
                        break;
                    case "Patient Refund":
                        $resultSet = $this->getPatientPaidAmountTotal($pmt_claim_tx_id, $claim_id);
                        $arr = array(
                            'total_paid' => $resultSet->paid
                        );
                        break;
                    case "Patient Adjustment":
                        $resultSet = PMTClaimCPTTXV1::select(DB::raw("sum(writeoff) as writeoff"))
                                        ->where('pmt_claim_tx_id', $pmt_claim_tx_id)
                                        ->where('claim_id', $claim_id)->first();
                        $arr = array(
                            'total_writeoff' => $resultSet->writeoff
                        );
                        break;
                    case "Patient Credit Balance":
                        $resultSet = $this->getPatientPaidAmountTotal($pmt_claim_tx_id, $claim_id);
                        $arr = array(
                            'total_paid' => $resultSet->paid
                        );
                        break;
                    case "Wallet":
                        $resultSet = PMTClaimCPTTXV1::select(DB::raw("sum(paid) as paid"))
                                        ->where('pmt_claim_tx_id', $pmt_claim_tx_id)
                                        ->where('claim_id', $claim_id)->first();
                        $arr = array(
                            'total_paid' => $resultSet->paid
                        );
                        break;
                    case "Insurance Payment":
                        $arr = $this->getIns_PaymentUpdateArr($pmt_claim_tx_id, $claim_id);
                        break;
                    case "Insurance Refund":
                        $arr = $this->getIns_PaymentUpdateArr($pmt_claim_tx_id, $claim_id);
                        break;
                    case "Insurance Adjustment":
                        $arr = $this->getIns_PaymentUpdateArr($pmt_claim_tx_id, $claim_id);
                        break;
                }

                PMTClaimTXV1::where('id', $pmt_claim_tx_id)
                        ->where('claim_id', $claim_id)
                        ->update($arr);
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateClaimCptTxData", $e);
        }
    }

    public function updateCptFin_ClaimFin($datas, $cptTxColName, $cptFinColName, $claimFinColName) {
        try {

            $claim_id = $datas['claim_id'];
            $claim_cpt_info_id = $datas['claim_cpt_info_id'];
            $ids = PMTClaimTXV1::select('id')->where('claim_id', $claim_id)->where('pmt_method', 'Patient')->pluck('id')->all();
            $cptResultSet = PMTClaimCPTTXV1::select(
                                    DB::raw("sum(" . $cptTxColName . ") as paid"))
                            ->where('claim_cpt_info_id', $claim_cpt_info_id)
                            ->whereIn('pmt_claim_tx_id', $ids)
                            ->where('claim_id', $claim_id)->first();

            //find the patientPaid from cptTx and cptFin
            PMTClaimCPTFINV1::where('claim_id', $claim_id)
                    ->where('claim_cpt_info_id', $claim_cpt_info_id)
                    ->update(['' . $cptFinColName . '' => $cptResultSet->paid]);

            //get the count claimLevel Patient Paid
            $claimResultSet = PMTClaimCPTFINV1::select(DB::raw("sum(" . $cptFinColName . ") as patient_paid")
                    )->where('claim_id', $claim_id)->first();

            // update the PmtClaimFinData
            PMTClaimFINV1::where('claim_id', $claim_id)
                    ->update(['' . $claimFinColName . '' => $claimResultSet->patient_paid]);

            $currentCptLevelBalance = $this->findClaimCptLevelPatientandInsuranceBal($datas);
            $curretFinData = array(
                'cpt_patient_balance' => $currentCptLevelBalance['patient_balance'],
                'cpt_insurance_balance' => $currentCptLevelBalance['insurance_balance'],
            );
            return $curretFinData;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateCptFin_ClaimFin", $e);
        }
    }

    public function finalClaimBalanceUpdate($datas) {
        try {
            $claimLastTx = ClaimTXDESCV1::select('transaction_type', 'id')
                    ->orderBy('id', 'DESC')
                    ->where('claim_id', $datas['claim_id'])
                    ->first();

            if ($claimLastTx['transaction_type'] == "Wallet") {
                $cptInfoIDs = ClaimCPTTXDESCV1::Select(DB::raw('DISTINCT(claim_cpt_info_id)'))
                        ->where('claim_id', $datas['claim_id'])
                        ->pluck('claim_cpt_info_id')->all();
                $insBal = '0.00';
                $patBal = '0.00';

                foreach ($cptInfoIDs as $cptInfoID) {
                    $cptTxn = ClaimCPTTXDESCV1::select(
                                    DB::raw('ins_bal'), DB::raw('pat_bal'))
                            ->where('claim_id', $datas['claim_id'])
                            ->where('claim_cpt_info_id', $cptInfoID)
                            ->orderBy('id', 'DESC')
                            ->first();
                    $insBal += $cptTxn['ins_bal'];
                    $patBal += $cptTxn['pat_bal'];
                }
                $balanceArr['insurance_balance'] = $insBal;
                $balanceArr['patient_balance'] = $patBal;
                PMTClaimFINV1::where('claim_id', $datas['claim_id'])
                        ->update(['patient_due' => $balanceArr['patient_balance'], 'insurance_due' => $balanceArr['insurance_balance']]);
                $this->updateBalanceClaimTXDesc($claimLastTx['id'], $balanceArr['patient_balance'], $balanceArr['insurance_balance']);
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("finalClaimBalanceUpdate", $e);
        }
    }

    public function findClaimLevelPatientandInsuranceBal($datas, $claimDescID = 0) {
        try {
            global $balanceArr;
           
            $newTransaction = ClaimCPTTXDESCV1::select(
                        DB::raw('sum(ins_bal) as insurance_balance'), DB::raw('sum(pat_bal) as patient_balance'))
                ->where('claim_id', $datas['claim_id'])
                ->where('claim_tx_desc_id', $claimDescID)
                ->first();
            
            $balanceArr['insurance_balance'] = $newTransaction['insurance_balance'];
            $balanceArr['patient_balance'] = $newTransaction['patient_balance'];
            PMTClaimFINV1::where('claim_id', $datas['claim_id'])
                    ->update(['patient_due' => $balanceArr['patient_balance'], 'insurance_due' => $balanceArr['insurance_balance']]);
            return $balanceArr;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("findClaimLevelPatientandInsuranceBal", $e);
        }
    }

    public function findClaimCptLevelPatientandInsuranceBal($datas) {
        try {
            //Initialize output variables
            global $outputBal;
            $newInsBal = 0;
            $newPatBal = 0;
            //Get CPT Balance from Financial table
            $cptFin = PMTClaimCPTFINV1::where('claim_id', $datas['claim_id'])
                            ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])->first();
            //If transaction type is not responsibility, get values from the last transaction in CPT Transaction table
            //Note: This record is the last created record in CPT Tx table whose values needs to be considered and 
            //      updated in Financial table
            if ($datas['transaction_type'] != 'Responsibility') {
                $newCPTTransaction = PMTClaimCPTTXV1::where('claim_id', $datas['claim_id'])
                        ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])
                        ->orderBy('id', 'desc')
                        ->first();
            } elseif ($datas['transaction_type'] == 'Responsibility' && isset($datas['old_insurance_id']) && ($datas['old_insurance_id'] == $datas['new_insurance_id'])) {
                $balanceArr['patient_balance'] = $cptFin->patient_balance;
                $balanceArr['insurance_balance'] = $cptFin->insurance_balance;
                return $balanceArr;
            } else {
                //If transaction type is responsibility create the array using null
                //Note: This record is created because there will not be a row in CPT Transaction table
                $newCPTTransaction['copay'] = '0.00';
                $newCPTTransaction['coins'] = '0.00';
                $newCPTTransaction['paid'] = '0.00';
                $newCPTTransaction['withheld'] = '0.00';
                $newCPTTransaction['writeoff'] = '0.00';
                $newCPTTransaction['deduction'] = '0.00';
            }
            if (!empty($cptFin)) {
                $oldPatientBalance = $cptFin->patient_balance; //First time will be 0
                $oldInsuranceBalance = $cptFin->insurance_balance; //Fitst time will be 0
                $newPatientBalance = $newCPTTransaction['copay'] + $newCPTTransaction['coins'] + $newCPTTransaction['deduction']; //New Payment Patient Balance
                $newPayment = $newCPTTransaction['paid'];
                $newAdjusted = $newCPTTransaction['withheld'] + $newCPTTransaction['writeoff'];
                //Responsibility Insurance
                // Log:info("Transaction Type".$datas['transaction_type']);
                if (isset($datas['resp']) && $datas['resp'] > 0) {
                    //Consider all patient payments and patient adjustments as 0
                    if ($datas['transaction_type'] == "Patient Payment" || $datas['transaction_type'] == "Patient Refund" || $datas['transaction_type'] == "Patient Credit Balance" || $datas['transaction_type'] == "Patient Adjustment"
                    ) {
                        $newPayment = 0.00;
                        $newAdjusted = 0.00;
                    }
                    if ($datas['transaction_type'] == "Responsibility") {
                        $lastCPTDesc = ClaimCPTTXDESCV1::where('claim_id', $datas['claim_id'])
                                        ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])
                                        ->where(function ($q) {
                                            $q->where('transaction_type', 'Insurance Payment');
                                            $q->orwhere('transaction_type', 'Insurance Refund');
                                            $q->orwhere('transaction_type', 'Insurance Adjustment');
                                            $q->orwhere('transaction_type', 'New Charge');
                                            $q->orwhere('transaction_type', 'Denials');
                                            $q->orwhere('transaction_type', 'Edit Charge');
                                            $q->orwhere('transaction_type', 'Submitted');
                                            $q->orwhere('transaction_type', 'Submitted Paper');
                                            $q->orwhere('transaction_type', 'Resubmitted');
                                            $q->orwhere('transaction_type', 'Resubmitted Paper');
                                            $q->orwhere('transaction_type', 'Payer Rejected');
                                            $q->orwhere('transaction_type', 'Clearing House Rejection');
                                            $q->orwhere('transaction_type', 'Clearing House Accepted');
                                            $q->orwhere('transaction_type', 'Payer Accepted');                                            
                                            $q->orwhere('transaction_type', 'Void Check')->where('value_2', '1');
                                        })
                                        ->orderBy('id', 'desc')->first();
                        //If the last transaction on the above conditions is a "Void Check" we should 
                        //take the Deleted amount which is stored in value_1 column and search the description
                        //table again without the Void check transaction type for the last insurance balance                        
                        if ($lastCPTDesc['transaction_type'] == "Void Check" && $lastCPTDesc['value_2'] == "1") {
                            $newPayment = $lastCPTDesc['value_1'];
                            $lastVoidCheckID = $lastCPTDesc['id'];
                            $lastCPTDesc = ClaimCPTTXDESCV1::where('claim_id', $datas['claim_id'])
                                        ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])
                                        ->where(function ($q) {
                                            $q->where('transaction_type', 'Insurance Payment');
                                            $q->orwhere('transaction_type', 'Insurance Refund');
                                            $q->orwhere('transaction_type', 'Insurance Adjustment');
                                            $q->orwhere('transaction_type', 'New Charge');
                                            $q->orwhere('transaction_type', 'Denials');
                                            $q->orwhere('transaction_type', 'Edit Charge');
                                            $q->orwhere('transaction_type', 'Submitted');
                                            $q->orwhere('transaction_type', 'Submitted Paper');
                                            $q->orwhere('transaction_type', 'Resubmitted');
                                            $q->orwhere('transaction_type', 'Resubmitted Paper');
                                            $q->orwhere('transaction_type', 'Payer Rejected');
                                            $q->orwhere('transaction_type', 'Clearing House Rejection');
                                            $q->orwhere('transaction_type', 'Clearing House Accepted');
                                            $q->orwhere('transaction_type', 'Payer Accepted');
                                        })
                                        ->orderBy('id', 'desc')->first();
                            // To handle multi void check
                            $lastInsuranceTxID = $lastCPTDesc['id'];         
                            $newPayment = ClaimCPTTXDESCV1::where('claim_id', $datas['claim_id'])
                                        ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])
                                        ->whereBetween('id', [ $lastInsuranceTxID, $lastVoidCheckID])
                                        ->where('transaction_type', 'Void Check')->where('value_2', '1')
                                        ->select(DB::raw('sum(value_1) AS tot_amt '))
                                       ->value('tot_amt');
                        }                        
                        $oldInsuranceBalance = $lastCPTDesc['ins_bal'];
                        $oldPatientBalance = $lastCPTDesc['pat_bal'];
                    } else {
                        $oldPatientBalance = $cptFin->patient_balance;
                        $oldInsuranceBalance = $cptFin->insurance_balance;
                    } if ($datas['transaction_type'] == "Wallet") {
                        $newPayment = 0;
                        $newAdjusted = 0;
                    }
                    if ($oldInsuranceBalance >= 0) {
                        if ($oldPatientBalance >= 0) { //Patient Balance already exist
                            $newInsBal = ($oldInsuranceBalance + $oldPatientBalance) - ($newPayment + $newAdjusted) - $newPatientBalance;
                            $newPatBal = $newPatientBalance;
                        } else { //Patient Balance is negative (Patient over paid amount)
                            $newInsBal = ($oldInsuranceBalance) - ($newAdjusted) - $newPatientBalance;
                            $newPatBal = $oldPatientBalance + $newPatientBalance;
                        }
                    } else if ($oldInsuranceBalance < 0) { // Insurance amount overpaid
                        if ($oldPatientBalance >= 0) { // Patient Balance already exists
                            $newInsBal = ($oldInsuranceBalance + $oldPatientBalance) - ($newPayment + $newAdjusted) - $newPatientBalance;
                            $newPatBal = $newPatientBalance;
                        } else if ($oldPatientBalance < 0) {
                            $newInsBal = ($oldInsuranceBalance) - ($newAdjusted) - $newPatientBalance;
                            $newPatBal = $oldPatientBalance + $newPatientBalance;
                        }
                    }
                }//Responsibility: Patient
                else if (isset($datas['resp']) && $datas['resp'] == 0) {                   
                    //Consider all previous patient paid and patient adjusted
                    if ($datas['transaction_type'] == "Responsibility") {
                        $totalPatientPaid = $cptFin->patient_paid;
                        $totalPatientAdjusted = $cptFin->patient_adjusted;
                    } else if ($datas['transaction_type'] == "Wallet") {
                        $totalPatientPaid = $cptFin->patient_paid;
                        $totalPatientAdjusted = 0.00;
                        $newPayment = $newCPTTransaction['paid'];
                    } else if ($datas['transaction_type'] == 'Insurance Refund' && $newCPTTransaction['paid'] > 0) {
                        //Handling Insurance Refund Check Deletion when responsibility is "Self"
                        //We gather the last udpated balance from Financial Table and use the deleted amount
                        //to arrive at the updated balance amount
                        //$newCPTTransaction['paid'] is the deleted amount, we get this as a positive value
                        $oldPatientBalance = $cptFin->patient_balance;
                        $oldInsuranceBalance = $cptFin->insurance_balance;
                        $deletedAmt = $newCPTTransaction['paid'];
                        if ($deletedAmt <= $oldPatientBalance) {
                            //If the deleted amount is less than the old patient balance, we just subtract it from the old patient balance
                            $newPatBal = $oldPatientBalance - $deletedAmt;
                            $newInsBal = $oldInsuranceBalance;
                        } else if ($deletedAmt > $oldPatientBalance && $oldPatientBalance >= 0) {
                            //If the deleted amount is greater than the old patient balance it means that there will not be anymore patient balance
                            $newPatBal = 0;
                            $newInsBal = ($oldInsuranceBalance + $oldPatientBalance) - $deletedAmt;
                        } else if ($deletedAmt > $oldPatientBalance && $oldPatientBalance < 0) {
                            $newPatBal = $oldPatientBalance;
                            $newInsBal = $oldInsuranceBalance - $deletedAmt;
                        }
                        //Use the array to return the output balance
                        $outputBal['patient_balance'] = $newPatBal;
                        $outputBal['insurance_balance'] = $newInsBal;
                        PMTClaimCPTFINV1::where('claim_id', $datas['claim_id'])
                                ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])
                                ->update(['patient_balance' => $outputBal['patient_balance'], 'insurance_balance' => $outputBal['insurance_balance']]);
                        return $outputBal;
                    } else {
                        $totalPatientPaid = 0.00;
                        $totalPatientAdjusted = 0.00;
                    }
                    if ($oldInsuranceBalance >= 0) { //Insurance Balance Positive
                        $newInsBal = '0.00';
                        if ($oldPatientBalance >= 0) {
                            $newPatBal = ($oldInsuranceBalance + $oldPatientBalance) - ($newPayment + $newAdjusted) - ($totalPatientPaid + $totalPatientAdjusted);
                        } else if ($oldPatientBalance < 0) {
                            $newPatBal = ($oldInsuranceBalance + $oldPatientBalance) - $newPayment - $newAdjusted;
                        }
                    } else if ($oldInsuranceBalance < 0) { //Insurance Balance Negative
                        if ($oldPatientBalance >= 0) {
                            $newInsBal = $oldInsuranceBalance;
                            $newPatBal = ($oldPatientBalance) - ($newAdjusted) - ($totalPatientPaid + $totalPatientAdjusted);
                        } else if ($oldPatientBalance < 0) {
                            $newInsBal = $oldInsuranceBalance;
                            $newPatBal = ($oldPatientBalance) - $newPayment - $newAdjusted;
                        }
                    }
                }
                $outputBal['patient_balance'] = $newPatBal;
                $outputBal['insurance_balance'] = $newInsBal;
                PMTClaimCPTFINV1::where('claim_id', $datas['claim_id'])
                        ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])
                        ->update(['patient_balance' => $outputBal['patient_balance'], 'insurance_balance' => $outputBal['insurance_balance']]);
                return $outputBal;
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("findClaimCptLevelPatientandInsuranceBal", $e);
        }
    }

    public function checkandSendTheBalanceAmountToWallet($datas) {
        try {
            $claimFinData = PMTClaimFINV1::where('claim_id', $datas['claim_id'])->first();
            $record = PMTClaimCPTFINV1::where('claim_id', $datas['claim_id'])
                            ->where('claim_cpt_info_id', $datas['claim_cpt_info_id'])->first();

            if (!empty($record)) {
                $cptPatientPaid = $record->patient_paid;
                $cptPatientDue = $record->patient_balance;
                $cptPatientAdj = $record->patient_adjusted;
                $cptInsDue = $record->insurance_balance;
                // Responsibility: Insurance
                $tempPatBal = $cptPatientDue;
                if (isset($datas['resp']) && $datas['resp'] > 0) {
                    if ($cptPatientDue >= 0) {
                        if ($cptInsDue >= 0) {
                            $tempPatBal = ($cptPatientDue + $cptInsDue) - ($cptPatientPaid);
                        } else {
                            $tempPatBal = $cptPatientDue - ($cptPatientPaid);
                        }
                        if ($tempPatBal >= 0) {
                            $tempPatBal = $tempPatBal - $cptPatientAdj;
                            if (-$tempPatBal >= $cptPatientPaid) {
                                $tempPatBal = -$cptPatientPaid;
                            }
                        }
                    }
                }      //Responsibility: Patient
                else if (isset($datas['resp']) && $datas['resp'] == 0) {
                    if ($cptPatientDue < 0) {
                        if ($cptPatientPaid > 0) {
                            if ($cptPatientPaid > -$cptPatientDue) {
                                $tempPatBal = $cptPatientDue;
                            } else {
                                $tempPatBal = -$cptPatientPaid;
                            }
                        } else {
                            $tempPatBal = 0;
                        }
                    }
                }
                if ($tempPatBal < 0) {
                    $walletClaimTxDesId = $this->patientWalletBridge($datas, $tempPatBal, $claimFinData);
                    return $walletClaimTxDesId;
                } else {
                    return 0;
                }
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("checkandSendTheBalanceAmountToWallet", $e);
        }
    }

    public function getPatientPaidAmountTotal($pmt_claim_tx_id, $claim_id) {
        try {

            $resultSet = PMTClaimCPTTXV1::select(DB::raw("sum(paid) as paid"))
                            ->where('pmt_claim_tx_id', $pmt_claim_tx_id)
                            ->where('claim_id', $claim_id)->first();
            return $resultSet;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("getPatientPaidAmountTotal", $e);
        }
    }

    public function getIns_PaymentUpdateArr($pmt_claim_tx_id, $claim_id) {
        try {
            // dd($datas);
            $resultSet = PMTClaimCPTTXV1::select(
                                    DB::raw('sum(allowed) as total_allowed'), DB::raw('sum(deduction) as total_deduction'), DB::raw('sum(copay) as total_copay'), DB::raw('sum(coins) as total_coins'), DB::raw('sum(withheld) as total_withheld'), DB::raw('sum(writeoff) as total_writeoff'), DB::raw('sum(paid) as total_paid'))
                            ->where('pmt_claim_tx_id', $pmt_claim_tx_id)
                            ->where('claim_id', $claim_id)->first();
            $updateArr = array(
                "total_allowed" => $resultSet->total_allowed,
                "total_deduction" => $resultSet->total_deduction,
                "total_copay" => $resultSet->total_copay,
                "total_coins" => $resultSet->total_coins,
                "total_withheld" => $resultSet->total_withheld,
                "total_writeoff" => $resultSet->total_writeoff,
                "total_paid" => $resultSet->total_paid
            );
            return $updateArr;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("getIns_PaymentUpdateArr", $e);
        }
    }

    // @todo -- check and remove the below function, for handle insurance refund check this function added,
    public function updateInsuranceCptFin_ClaimFin_v1($data, $claim_id) {
        try {              //dd($data); 
            $cptFinColName = $claimFinColName = 'insurance_paid';
            $cptTxColName = 'paid';
            $claim_id = $data['claim_id'];
            $claim_cpt_info_id = $data['claim_cpt_info_id'];
            $ids = PMTClaimTXV1::select('id')->where('pmt_method', 'Insurance')->where('claim_id', $claim_id)->pluck('id')->all();

            $cptResultSet = PMTClaimCPTTXV1::select(
                                    DB::raw("sum(" . $cptTxColName . ") as paid"))
                            ->where('claim_cpt_info_id', $claim_cpt_info_id)
                            ->whereIn('pmt_claim_tx_id', $ids)
                            ->where('claim_id', $claim_id)->first();

            //find the patientPaid from cptTx and cptFin
            PMTClaimCPTFINV1::where('claim_id', $claim_id)
                    ->where('claim_cpt_info_id', $claim_cpt_info_id)
                    ->update(['' . $cptFinColName . '' => $cptResultSet->paid]);

            //get the count claimLevel Patient Paid
            $claimResultSet = PMTClaimCPTFINV1::select(DB::raw("sum(" . $cptFinColName . ") as insurance_paid")
                    )->where('claim_id', $claim_id)->first();

            // update the PmtClaimFinData
            PMTClaimFINV1::where('claim_id', $claim_id)
                    ->update(['' . $claimFinColName . '' => $claimResultSet->insurance_paid]);

            $currentCptLevelBalance = $this->findClaimCptLevelPatientandInsuranceBal($data);
            $curretFinData = array(
                'cpt_patient_balance' => $currentCptLevelBalance['patient_balance'],
                'patient_balance' => $currentCptLevelBalance['patient_balance'],
                'cpt_insurance_balance' => $currentCptLevelBalance['insurance_balance'],
                'insurance_balance' => $currentCptLevelBalance['insurance_balance'],
            );
            return $curretFinData;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateCptFin_ClaimFin", $e);
        }
    }

    public function updateInsuranceCptFin_ClaimFin($data, $claim_id) {
        try {
            $claim_cpt_info_id = $data['claim_cpt_info_id'];
            $ids = PMTClaimTXV1::select('id')->where('pmt_method', 'Insurance')->where('claim_id', $claim_id)->pluck('id')->all();

            $resultSet = PMTClaimCPTTXV1::with(['dosDetails' => function ($query) {
                                    $query->select('*');
                                }])->select(
                                    DB::raw('sum(allowed) as total_allowed'), DB::raw('sum(withheld) as total_withheld'), DB::raw('sum(writeoff) as total_writeoff'), DB::raw('sum(paid) as total_paid'), 'claim_cpt_info_id')
                            ->where('claim_cpt_info_id', $claim_cpt_info_id)
                            ->whereIn('pmt_claim_tx_id', $ids)
                            ->where('claim_id', $claim_id)->first();

            $updateArr = array(
                "cpt_allowed_amt" => $resultSet->total_allowed,
                "deductable" => $data['deduction'],
                "co_pay" => $data['copay'],
                "co_ins" => $data['coins'],
                "with_held" => $resultSet->total_withheld,
                "insurance_adjusted" => $resultSet->total_writeoff,
                "insurance_paid" => $resultSet->total_paid
            );

            PMTClaimCPTFINV1::where('claim_id', $claim_id)
                    ->where('claim_cpt_info_id', $claim_cpt_info_id)
                    ->update($updateArr);

            $this->updateClaimFinData($claim_id);
            $cptLevelBalance = $this->findClaimCptLevelPatientandInsuranceBal($data);
            $curretFinData = array(
                'patient_balance' => $cptLevelBalance['patient_balance'],
                'insurance_balance' => $cptLevelBalance['insurance_balance'],
            );
            return $curretFinData;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateCptFin_ClaimFin", $e);
        }
    }

    public function updateClaimBalanceAmount($data, $claimId, $tnxFor = 'Payment') {
        $data['claim_id'] = $claimId;
        $data['tnxFor'] = $tnxFor;
        $data['transaction_type'] = $tnxFor;
        $cptLevelBalance = $this->findClaimCptLevelPatientandInsuranceBal($data);
        $returnData = array(
            'cpt_patient_balance' => $cptLevelBalance['patient_balance'], //$patientBal,//$cptLevelBalance['patient_balance'],
            'cpt_insurance_balance' => $cptLevelBalance['insurance_balance']//$insuranceBalance//$cptLevelBalance['insurance_balance'],
        );
        return $returnData;
    }

    public function moveToWalletCheck($walletAmount, $claimId, $data) {
        try {
            //Log::info('input wallet amount-'.$walletAmount);
            if ($walletAmount > 0) {
                $finDatas = PMTClaimFINV1::where('claim_id', $claimId)->first();
                $patientId = $finDatas['patient_id'];

                $claimTxIds = PMTClaimTXV1::where('claim_id', $claimId)
                        ->where('pmt_method', 'Patient')
                        ->where('pmt_type', 'Payment')
                        ->orwhere('pmt_type', 'Credit Balance')
                        ->pluck('id')->all();

                $cptTxDetails = PMTClaimCPTTXV1::select('*', DB::raw('sum(paid) as paid'))
                        ->where('claim_id', $claimId)
                        ->where('claim_cpt_info_id', $data['claim_cpt_info_id'])
                        ->whereIn('pmt_claim_tx_id', $claimTxIds)
                        ->groupBy('payment_id')
                        ->get();

                if (isset($cptTxDetails)) {
                    global $currentAmount;
                    $currentAmount = $walletAmount;
                    $cptTxIds = array();
                    foreach ($cptTxDetails as $cptdetail) {
                        if ($currentAmount != '0.00') {
                            if ($cptdetail['paid'] > $currentAmount) {
                                //create ClaimLevle Tx
                                $newPmtClaimTx = array(
                                    "payment_id" => $cptdetail['payment_id'],
                                    "claim_id" => $cptdetail['claim_id'],
                                    "pmt_method" => 'Patient',
                                    "pmt_type" => 'Payment',
                                    "patient_id" => $patientId,
                                    "posting_date" => date("Y-m-d"),
                                    "created_by" => Auth::user()->id
                                );
                                $resultSet = PMTClaimTXV1::create($newPmtClaimTx);
                                //create Wallet data
                                $newwalletData = array(
                                    'patient_id' => $patientId,
                                    'pmt_info_id' => $cptdetail['payment_id'],
                                    'tx_type' => 'Credit',
                                    'amt_pop' => $currentAmount,
                                    'wallet_ref_id' => '',
                                    'claimId' => $claimId,
                                    'resp' => $data['resp']
                                );
                                $this->paymentV1->storeWalletData($newwalletData, false, false);
                                $this->updatePmt_infoAmt_usedColumn($cptdetail['pmt_claim_tx_id'], $cptdetail['payment_id'], $currentAmount);
                                //cptLevel Tnx
                                $cptDetails = array(
                                    'payment_id' => $cptdetail['payment_id'],
                                    'claim_id' => $cptdetail['claim_id'],
                                    'pmt_claim_tx_id' => $resultSet->id,
                                    'claim_cpt_info_id' => $cptdetail['claim_cpt_info_id'],
                                    'paid' => -1 * abs($currentAmount),
                                    'resp' => $data['resp']
                                );
                                $claimCptTxId = PMTClaimCPTTXV1::create($cptDetails);
                                array_push($cptTxIds, $claimCptTxId->id);
                                //create wallet Desc
                                $this->updateClaimCptTxData($cptDetails, 'Wallet');
                                $this->updateClaimCptFindData($cptDetails, 'Wallet');
                                $currentAmount = $currentAmount - $currentAmount;
                                break;
                            } else {
                                //create Claim Level Tx
                                $newPmtClaimTx = array(
                                    "payment_id" => $cptdetail['payment_id'],
                                    "claim_id" => $cptdetail['claim_id'],
                                    "pmt_method" => 'Patient',
                                    "pmt_type" => 'Payment',
                                    "patient_id" => $patientId,
                                    "posting_date" => date("Y-m-d"),
                                    "created_by" => Auth::user()->id
                                );
                                $resultSet = PMTClaimTXV1::create($newPmtClaimTx);
                                //create wallet Level Data
                                $newwalletData = array(
                                    'patient_id' => $patientId,
                                    'pmt_info_id' => $cptdetail['payment_id'],
                                    'tx_type' => 'Credit',
                                    'amt_pop' => $cptdetail['paid'],
                                    'wallet_ref_id' => '',
                                    'claimId' => $claimId,
                                    'resp' => $data['resp']
                                );
                                $this->paymentV1->storeWalletData($newwalletData, false, false);
                                $this->updatePmt_infoAmt_usedColumn($cptdetail['pmt_claim_tx_id'], $cptdetail['payment_id'], $cptdetail['paid']);
                                //create cptLevel Tx
                                $cptDetails = array(
                                    'payment_id' => $cptdetail['payment_id'],
                                    'claim_id' => $cptdetail['claim_id'],
                                    'pmt_claim_tx_id' => $resultSet->id,
                                    'claim_cpt_info_id' => $cptdetail['claim_cpt_info_id'],
                                    'paid' => -1 * abs($cptdetail['paid']),
                                    'resp' => $data['resp']
                                );
                                $claimCptTxId = PMTClaimCPTTXV1::create($cptDetails);
                                array_push($cptTxIds, $claimCptTxId->id);
                                $this->updateClaimCptTxData($cptDetails, 'Wallet');
                                $this->updateClaimCptFindData($cptDetails, 'Wallet');
                                $currentAmount = $currentAmount - $cptdetail['paid'];
                            }
                        }
                    }
                    $cptTxIds = implode(',', $cptTxIds);
                    //create wallet cptLevelWallet Desc
                    $newCptWalletDesc = array(
                        "claim_tx_desc_id" => $data['claimTxDesID'],
                        "claim_cpt_info_id" => $data['claim_cpt_info_id'],
                        'claim_info_id' => $claimId,
                        'txn_id' => $cptTxIds,
                        'amount' => $walletAmount,
                        'resp' => $data['resp']
                    );

                    $cptTxDesId = $this->storeClaimCptTxnDesc('Wallet', $newCptWalletDesc);
                    $cptLevelFinBalance = PMTClaimCPTFINV1::select('patient_balance', 'insurance_balance')
                            ->where('claim_id', $claimId)
                            ->where('claim_cpt_info_id', $data['claim_cpt_info_id'])
                            ->first();
                    $this->updateBalanceClaimCPTTXDesc($cptTxDesId, $cptLevelFinBalance['patient_balance'], $cptLevelFinBalance['insurance_balance']);
                }
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("moveToWalletCheck", $e);
        }
    }

    public function updatePmt_infoAmt_usedColumn($pmtClaimTxId, $paymentId, $currentAmount) {
        try {
            $claimTxData = PMTClaimTXV1::select('id', 'pmt_type')
                            ->where('id', $pmtClaimTxId)->get()->first();
            if ($claimTxData['pmt_type'] == "Credit Balance") {
                $wallRefData = PMTWalletV1::where('wallet_Ref_Id', $paymentId)->get()->first();
                $paymentId = $wallRefData['pmt_info_id'];
            }
            PMTInfoV1::where('id', $paymentId)->update(['amt_used' => DB::raw('amt_used  - ' . abs($currentAmount) . '')]);
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updatePmt_infoAmt_usedColumn", $e);
        }
    }

    public function updateClaimFinData($claimId) {
        try {
            $cptFinResSet = PMTClaimCPTFINV1::select(
                                    DB::raw('sum(cpt_allowed_amt) as total_allowed'), DB::raw('sum(cpt_charge) as cpt_charge'), DB::raw('sum(with_held) as total_withheld'), DB::raw('sum(insurance_paid) as total_Ins_paid'), DB::raw('sum(insurance_adjusted) as insurance_adj'))
                            ->where('claim_id', $claimId)->first();
            $updateArr1 = array(
                "total_allowed" => $cptFinResSet->total_allowed,
                "withheld" => $cptFinResSet->total_withheld,
                "insurance_paid" => $cptFinResSet->total_Ins_paid,
                "total_charge" => $cptFinResSet->cpt_charge,
                "insurance_adj" => $cptFinResSet->insurance_adj
            );

            PMTClaimFINV1::where('claim_id', $claimId)
                    ->update($updateArr1);
            return $cptFinResSet;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateClaimFinData", $e);
        }
    }

    public function updateBalanceClaimTXDesc($claimTxDesId, $patientBalance, $insuranceBalance) {
        try {
            ClaimTXDESCV1::where('id', $claimTxDesId)
                    ->update(['pat_bal' => $patientBalance, 'ins_bal' => $insuranceBalance]);
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updatePatient_InsuranceBalance", $e);
        }
    }

    public function updateBalanceClaimCPTTXDesc($claimcptTxDesId, $patientBalance, $insuranceBalance) {
        try {
            ClaimCPTTXDESCV1::where('id', $claimcptTxDesId)
                    ->update(['pat_bal' => $patientBalance, 'ins_bal' => $insuranceBalance]);
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("updateCptLevePatient_InsuranceBalance", $e);
        }
    }

    public function getThepreviousClamCptLevelBalance($claimId, $claimCptId) {
        try {
            $currentClaimLevelBalance = PMTClaimFINV1::select('id', 'claim_id', 'patient_due', 'insurance_due')
                            ->where('claim_id', $claimId)->first();

            $currentCptLevelBalance = PMTClaimCPTFINV1::select('id', 'patient_balance', 'insurance_balance')
                            ->where('claim_cpt_info_id', $claimCptId)
                            ->where('claim_id', $claimId)->first();

            $curretFinData = array(
                'clm_patient_balance' => $currentClaimLevelBalance['patient_due'],
                'clm_insurance_balance' => $currentClaimLevelBalance['insurance_due'],
                'cpt_patient_balance' => $currentCptLevelBalance['patient_balance'],
                'cpt_insurance_balance' => $currentCptLevelBalance['insurance_balance'],
            );
            return $curretFinData;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("getThepreviousClamCptLevelBalance", $e);
        }
    }

    public function getClaimLevelTxPatientPaymentBalance($claimTxId, $claimId) {
        try {
            $resultSet = PMTClaimTXV1::with(['claim' => function ($query) {
                            $query->select('id', 'total_charge');
                        }])
                    ->select(DB::raw('sum(total_paid) as total_paid'), DB::raw('sum(total_writeoff) as total_writeoff'), DB::raw('sum(total_withheld) as total_withheld'), 'claim_id', 'pmt_method'
                    )
                    ->where('id', '<=', $claimTxId)
                    ->where('claim_id', $claimId)
                    ->where('pmt_method', 'patient')
                    ->first();
            return $resultSet;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("getClaimBalanceWithClaimLevelTx", $e);
        }
    }

    public function getClaimLevelTxInsurancePaymentBalance($claimTxId, $claimId) {
        try {
            $resultSet = PMTClaimTXV1::with(['claim' => function ($query) {
                            $query->select('id', 'total_charge');
                        }])
                    ->select(
                            DB::raw('sum(allowed) as total_allowed'), DB::raw('sum(total_paid) as total_paid'), DB::raw('sum(total_writeoff) as total_writeoff'), DB::raw('sum(total_withheld) as total_withheld'), 'claim_id', 'pmt_method'
                    )
                    ->where('id', '<=', $claimTxId)
                    ->where('claim_id', $claimId)
                    ->where('pmt_method', 'Insurance')
                    ->first();
            return $resultSet;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("getClaimBalanceWithClaimLevelTx", $e);
        }
    }

    public function getClaimLevelTxPaymentBalance($claimTxId, $claimId) {
        try {
            $resultSet = PMTClaimTXV1::with(['claim' => function ($query) {
                            $query->select('id', 'total_charge');
                        }])
                    ->select(DB::raw('sum(total_paid) as total_paid'), DB::raw('sum(total_writeoff) as total_writeoff'), DB::raw('sum(total_withheld) as total_withheld'), 'claim_id', 'pmt_method'
                    )
                    ->where('id', '<=', $claimTxId)
                    ->where('claim_id', $claimId)
                    ->where('pmt_method', 'patient')
                    ->first();
            return $resultSet;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("getClaimBalanceWithClaimLevelTx", $e);
        }
    }

    public function getcmsdataApi($claim_id) {
        $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        $practice_timezone = Helpers::getPracticeTimeZone();
        $claim_data = ClaimInfoV1::select('*',DB::raw('CONVERT_TZ(submited_date,"UTC","'.$practice_timezone.'") as submited_date'))->with(['rendering_provider', 'refering_provider', 'billing_provider', 'facility_detail', 'insurance_details', 'pmtClaimFinData', 'dosdetails' => function ($q) {
                        $q->where('is_active', 1);
                    }, 'patient', 'claim_details', 'pos'])->where('id', $claim_id)->first();

        $insurance_data = [];
        
        $dob['dob_month'] = $claim_detail['patient_dob_m'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1901-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('m', strtotime($claim_data->patient->dob)) : '';
        $dob['dob_day'] = $claim_detail['patient_dob_d'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1901-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('d', strtotime($claim_data->patient->dob)) : '';
        $dob['dob_year'] = $claim_detail['patient_dob_y'] = (isset($claim_data->patient->dob) && $claim_data->patient->dob != '1901-01-01' && $claim_data->patient->dob != '0000-00-00') ? date('Y', strtotime($claim_data->patient->dob)) : '';
        // dd($dob);
        if (!empty($claim_data['insurance_id'])) {
            $insurance_data = $this->getinsurancedetail($claim_data['patient_insurance_id'], $claim_data['patient_id'], $dob);
            // $insurance_data = $this->getinsurancedetail(array($claim_data['insurance_id']), $claim_data['patient_id']);
        }
        $claim_detail['id'] = $claim_data->id;
        $claim_detail['claim_number'] = $claim_data->claim_number;
        $claim_detail['insurance_address1'] = $this->removeSpecialChar(@$insurance_data['insurance_address1']);
        $claim_detail['insurance_address2'] = $this->removeSpecialChar(@$insurance_data['insurance_address2']);
        $claim_detail['insurance_city'] = $this->removeSpecialChar(@$insurance_data['insurance_city'], 'alpha');
        $claim_detail['insurance_state'] = $this->removeSpecialChar(@$insurance_data['insurance_state'], 'alpha');
        $claim_detail['insurance_zipcode5'] = @$insurance_data['insurance_zipcode5'];
        $claim_detail['insurance_zipcode4'] = @$insurance_data['insurance_zipcode4'];
        $claim_detail['insurance_address_concat'] = @$insurance_data['insurance_city'] . ' ' . @$insurance_data['insurance_state'] . ' ' . @$insurance_data['insurance_zipcode5'];
        if (!empty(@$insurance_data['insurance_zipcode4'])) {
            $claim_detail['insurance_address_concat'] = @$insurance_data['insurance_city'] . ' ' . @$insurance_data['insurance_state'] . ' ' . @$insurance_data['insurance_zipcode5'] . '' . @$insurance_data['insurance_zipcode4'];
        }
        $claim_detail['ins_type'] = isset($claim_data->insurance_details->insurancetype) ? $claim_data->insurance_details->insurancetype->type_name : '';
        $claim_detail['cms_type'] = isset($claim_data->insurance_details->insurancetype) ? $claim_data->insurance_details->insurancetype->cms_type : '';
        $claim_detail['insured_id_number'] = (!empty($insurance_data) && isset($insurance_data['policy_num'])) ? substr(@$insurance_data['policy_num'], 0, 29) : '';
        //$claim_detail['patient_name'] = Helpers::getNameformat(@$claim_data->patient->last_name, @$claim_data->patient->first_name, @$claim_data->patient->middle_name);        
        $claim_detail['patient_name'] = $this->getNameFromFormat(@$claim_data->patient->first_name, @$claim_data->patient->last_name, @$claim_data->patient->middle_name);

        $claim_detail['patient_gender'] = isset($claim_data->patient->gender) ? $claim_data->patient->gender : '';
        $claim_detail['insured_name'] = (!empty($insurance_data) && isset($insurance_data['insured_name'])) ? substr($insurance_data['insured_name'], 0, 29) : '';

        $claim_detail['insured_addr'] = (!empty($insurance_data) && isset($insurance_data['insured_addr'])) ? $insurance_data['insured_addr'] : '';
        $claim_detail['insured_city'] = (!empty($insurance_data) && isset($insurance_data['insured_city'])) ? $insurance_data['insured_city'] : '';
        $claim_detail['insured_state'] = (!empty($insurance_data) && isset($insurance_data['insured_state'])) ? $insurance_data['insured_state'] : '';
        $zip4_data = (isset($insurance_data['insured_zip4']) && !empty($insurance_data['insured_zip4'])) ? $insurance_data['insured_zip4'] : '';
        $claim_detail['insured_zip5'] = (!empty($insurance_data) && isset($insurance_data['insured_zip5'])) ? $insurance_data['insured_zip5'] . $zip4_data : '';

        $claim_detail['patient_addr'] = $this->removeSpecialChar(@$claim_data->patient->address1);
        $claim_detail['patient_city'] = $this->removeSpecialChar(@$claim_data->patient->city, 'alpha');
        $claim_detail['patient_state'] = $this->removeSpecialChar(@$claim_data->patient->state, 'alpha');
        $zip4 = isset($claim_data->patient->zip4) ? $claim_data->patient->zip4 : '';
        $claim_detail['patient_zip5'] = $claim_data->patient->zip5 . $zip4;
        if ($claim_data->patient->phone != '') {
            $claim_detail['patient_phone'] = preg_replace('/[^0-9]/', '', $claim_data->patient->phone);
        } elseif (($claim_data->patient->mobile != '')) {
            $claim_detail['patient_phone'] = preg_replace('/[^0-9]/', '', $claim_data->patient->mobile);
        } else {
            $claim_detail['patient_phone'] = '';
        }
        $claim_detail['insured_phone'] = (!empty($claim_detail['patient_phone']) && !empty($claim_detail['patient_phone'])) ? $claim_detail['patient_phone'] : ''; // Need to keep this field 
        $claim_detail['insured_relation'] = (!empty($insurance_data) && isset($insurance_data['insured_relation'])) ? @$insurance_data['insured_relation'] : '';
        $claim_detail['reserved_nucc_box8'] = '';
        $claim_detail['reserved_nucc_box9b'] = '';
        $claim_detail['reserved_nucc_box9c'] = '';

        $claim_detail['is_employment'] = (isset($claim_data->claim_details->is_employment) && $claim_data->claim_details->is_employment == 'Yes') ? $claim_data->claim_details->is_employment : 'No';
        $claim_detail['is_auto_accident'] = (isset($claim_data->claim_details->is_autoaccident) && $claim_data->claim_details->is_autoaccident == 'Yes') ? $claim_data->claim_details->is_autoaccident : 'No';
        $claim_detail['is_other_accident'] = (isset($claim_data->claim_details->is_otheraccident) && $claim_data->claim_details->is_otheraccident == 'Yes') ? $claim_data->claim_details->is_otheraccident : 'No';
        $claim_detail['accident_state'] = isset($claim_data->claim_details->autoaccident_state) ? $claim_data->claim_details->autoaccident_state : '';
        $claim_detail['is_another_ins'] = 'No';
        $claim_detail['other_insured_name'] = '';
        $claim_detail['other_ins_policy'] = '';
        $claim_detail['other_insur_name'] = '';
        // if(isset($claim_detail['ins_type']) && $claim_detail['ins_type'] == 'Medicare')
        //{

        $claim_detail['is_another_ins'] = (!empty($insurance_data) && isset($insurance_data['is_another_ins'])) ? $insurance_data['is_another_ins'] : 'No';

        // $claim_detail['other_insured_name'] = (!empty($insurance_data) && empty($insurance_data['other_insured_name']) && @$insurance_data['other_insured_relation'] == "Self") ? $claim_detail['patient_name']:@$insurance_data['other_insured_name']; 
        $claim_detail['other_insured_name'] = substr(@$insurance_data['other_insured_name'], 0, 28);
        $claim_detail['other_ins_policy'] = (!empty($insurance_data) && isset($insurance_data['other_ins_policy'])) ? substr($insurance_data['other_ins_policy'], 0, 28) : '';
        $claim_detail['other_insur_name'] = (!empty($insurance_data) && isset($insurance_data['other_insur_name'])) ? substr($insurance_data['other_insur_name'], 0, 28) : '';
        // }           
        $claim_detail['claimcode'] = isset($claim_data->claim_details->claim_code) ? $claim_data->claim_details->claim_code : '';
        //  $claim_detail['ins_policy_no'] = (!empty($insurance_data) && isset($insurance_data['ins_policy_no'])) ? $insurance_data['ins_policy_no'] : '';
        $claim_detail['ins_policy_no'] = (!empty($insurance_data) && isset($insurance_data['group_name'])) ? $insurance_data['group_name'] : ''; // Changed due do new requirement from CMS1500 form as per discussion with testing team

        /* if (@$insurance_data['insured_relation'] == "Self") {
          $claim_detail['insured_dob_m'] = ($insurance_data['primary_ins_type'] != "Medicare") ? $claim_detail['patient_dob_m'] : "";
          $claim_detail['insured_dob_d'] = ($insurance_data['primary_ins_type'] != "Medicare") ? $claim_detail['patient_dob_d'] : "";
          $claim_detail['insured_dob_y'] = ($insurance_data['primary_ins_type'] != "Medicare") ? $claim_detail['patient_dob_y'] : "";
          } else {
          $claim_detail['insured_dob_m'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_m']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_dob_m'] : '';
          $claim_detail['insured_dob_d'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_d']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_dob_d'] : '';
          $claim_detail['insured_dob_y'] = (!empty($insurance_data) && isset($insurance_data['insured_dob_y']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_dob_y'] : '';
          } */
        $claim_detail['insured_dob_m'] = @$insurance_data['insured_dob_m'];
        $claim_detail['insured_dob_d'] = @$insurance_data['insured_dob_d'];
        $claim_detail['insured_dob_y'] = @$insurance_data['insured_dob_y'];
        $claim_detail['insured_gender'] = (!empty($insurance_data) && isset($insurance_data['insured_gender']) && $insurance_data['primary_ins_type'] != "Medicare") ? $insurance_data['insured_gender'] : '';  // Need to keept this field 
        $claim_detail['other_claimid'] = isset($claim_data->claim_details->otherclaimid) ? $claim_data->claim_details->otherclaimid : '';
        $claim_detail['other_claimid_qual'] = (!empty($claim_data->claim_details->otherclaimid_qual)) ? $claim_data->claim_details->otherclaimid_qual : '';

        // $claim_detail['insur_name'] = (!empty($insurance_data) && isset($insurance_data['insur_name']) && $insurance_data['primary_ins_type'] != "Medicare") ? substr($insurance_data['insur_name'], 0,29) : '';
        $claim_detail['insur_name'] = (!empty($insurance_data) && isset($insurance_data['insur_name'])) ? substr($insurance_data['insur_name'], 0, 29) : '';

        $claim_detail['patient_signed'] = (!empty($claim_data->claim_details) && $claim_data->claim_details->print_signature_onfile_box12 == 'No') ? 'NO SIGNATURE ON FILE' : 'SIGNATURE ON FILE';
		// Added H:i:s default in submitted date
        $claim_detail['signed_date'] = (isset($claim_data->submited_date) && $claim_data->submited_date != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claim_data->submited_date)) : Helpers::timezone(date('m/d/Y H:i:s'),'m/d/Y'); //Claim Submited date.
        $claim_detail['insured_signed'] = (!empty($claim_data->claim_details) && $claim_data->claim_details->print_signature_onfile_box13 == 'No') ? 'NO SIGNATURE ON FILE' : 'SIGNATURE ON FILE';
        $claim_detail['amount_paid'] = (isset($claim_data->pmtClaimFinData->insurance_paid) && $claim_data->is_send_paid_amount == "No" && floatval($claim_data->pmtClaimFinData->insurance_paid) != "0.0") ? explode('.', $claim_data->pmtClaimFinData->insurance_paid) : '';
        $patient_id = $claim_data->patient_id;
        // If pregnency LMP was filled take its date and qualifier given at the NUCC pdf

        if (!empty($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') {
            $claim_detail['doi_m'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->claim_details->illness_box14) && $claim_data->claim_details->illness_box14 != '1970-01-01' && $claim_data->claim_details->illness_box14 != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->illness_box14)) : '';
            $claim_detail['doi_qual'] = 484;
        } else {
            //////dd($claim_data)   ;
            $claim_detail['doi_m'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01' && $claim_data->doi != '0000-00-00') ? date('m', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_d'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01' && $claim_data->doi != '0000-00-00') ? date('d', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_y'] = (isset($claim_data->doi) && $claim_data->doi != '1970-01-01' && $claim_data->doi != '0000-00-00') ? date('Y', strtotime($claim_data->doi)) : '';
            $claim_detail['doi_qual'] = ($claim_detail['doi_y'] != "") ? 431 : "";
        }
        // ////dd($claim_detail);
        $claim_detail['other_m'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_d'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_y'] = (isset($claim_data->claim_details->other_date) && $claim_data->claim_details->other_date != '1970-01-01' && $claim_data->claim_details->other_date != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->other_date)) : '';
        $claim_detail['other_qual'] = isset($claim_data->claim_details->other_date_qualifier) ? $claim_data->claim_details->other_date_qualifier : '';

        $claim_detail['box18_from_m'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_d'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_from_y'] = (isset($claim_data->claim_details->unable_to_work_from) && $claim_data->claim_details->unable_to_work_from != '1970-01-01' && $claim_data->claim_details->unable_to_work_from != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->unable_to_work_from)) : '';
        $claim_detail['box18_to_m'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('m', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_d'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('d', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $claim_detail['box18_to_y'] = (isset($claim_data->claim_details->unable_to_work_to) && $claim_data->claim_details->unable_to_work_to != '1970-01-01' && $claim_data->claim_details->unable_to_work_to != '0000-00-00') ? date('Y', strtotime($claim_data->claim_details->unable_to_work_to)) : '';
        $refering_provider_without_middle = @$claim_data->refering_provider->first_name . ' ' . @$claim_data->refering_provider->last_name;
        $refering_provider = (@$claim_data->refering_provider->middle_name != '') ? @$claim_data->refering_provider->first_name . ' ' . @$claim_data->refering_provider->middle_name . ' ' . @$claim_data->refering_provider->last_name : $refering_provider_without_middle;
        $claim_detail['refering_provider'] = isset($claim_data->refering_provider) ? $refering_provider : '';
        $claim_detail['refering_provider_npi'] = isset($claim_data->refering_provider) ? $claim_data->refering_provider->npi : '';
        $claim_detail['refering_provider_degree'] = isset($claim_data->refering_provider) ? (@$claim_data->refering_provider->degrees->degree_name) : '';

        // ////dd($claim_detail['refering_provider']);
        $refering_provider_type = (isset($claim_data->refering_provider->provider_types_id) && $claim_data->refering_provider->provider_types_id != 0) ? $claim_data->refering_provider->provider_types_id : '';
        $provider_qual = '';
        if ($refering_provider_type == config('siteconfigs.providertype.Supervising')) {
            $provider_qual = 'DQ';
        } elseif ($refering_provider_type == config('siteconfigs.providertype.Referring')) {
            $provider_qual = 'DN';
        } elseif ($refering_provider_type == config('siteconfigs.providertype.Ordering')) {
            $provider_qual = 'DK';
        }
        $claim_detail['rendering_provider_qual'] = (isset($claim_data->claim_details->rendering_provider_qualifier)) ? $claim_data->claim_details->rendering_provider_qualifier : '';

        $claim_detail['rendering_provider_otherId'] = (isset($claim_data->claim_details->rendering_provider_otherid)) ? $claim_data->claim_details->rendering_provider_otherid : '';
        $claim_detail['refering_provider_qual'] = $provider_qual;

        $claim_detail['provider_qual'] = (isset($claim_data->claim_details->provider_qualifier) && !empty($claim_detail['refering_provider'])) ? $claim_data->claim_details->provider_qualifier : '';
        $claim_detail['provider_otherid'] = (isset($claim_data->claim_details->provider_otherid) && !empty($claim_detail['refering_provider'])) ? $claim_data->claim_details->provider_otherid : '';

        $claim_detail['admit_date_m'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01' && $claim_data->admit_date != '0000-00-00') ? date('m', strtotime($claim_data->admit_date)) : '';
        $claim_detail['admit_date_d'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01' && $claim_data->admit_date != '0000-00-00') ? date('d', strtotime($claim_data->admit_date)) : '';
        $claim_detail['admit_date_y'] = (isset($claim_data->admit_date) && $claim_data->admit_date != '1970-01-01' && $claim_data->admit_date != '0000-00-00') ? date('Y', strtotime($claim_data->admit_date)) : '';
        $claim_detail['discharge_date_m'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('m', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_d'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('d', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['discharge_date_y'] = (isset($claim_data->discharge_date) && $claim_data->discharge_date != '1970-01-01' && $claim_data->discharge_date != '0000-00-00') ? date('Y', strtotime($claim_data->discharge_date)) : '';
        $claim_detail['addi_claim_info'] = isset($claim_data->claim_details->additional_claim_info) ? $claim_data->claim_details->additional_claim_info : '';
        $claim_detail['outside_lab'] = isset($claim_data->claim_details->outside_lab) ? $claim_data->claim_details->outside_lab : '';
        $claim_detail['lab_charge'] = isset($claim_data->claim_details->lab_charge) ? explode('.', $claim_data->claim_details->lab_charge) : ''; // This will be applied after submission of data   
        $icd_codes = [];
        if (!empty($claim_data->icd_codes)) {
            $icd_codes = Icd::getIcdValues($claim_data->icd_codes);
        }
        //$icd = new Icd();
        //$icd->connecttopracticedb();
        $claim_detail['icd_A'] = isset($icd_codes[1]) ? self::removeDecimalICD($icd_codes[1]) : '';
        $claim_detail['icd_B'] = isset($icd_codes[2]) ? self::removeDecimalICD($icd_codes[2]) : '';
        $claim_detail['icd_C'] = isset($icd_codes[3]) ? self::removeDecimalICD($icd_codes[3]) : '';
        $claim_detail['icd_D'] = isset($icd_codes[4]) ? self::removeDecimalICD($icd_codes[4]) : '';
        $claim_detail['icd_E'] = isset($icd_codes[5]) ? self::removeDecimalICD($icd_codes[5]) : '';
        $claim_detail['icd_F'] = isset($icd_codes[6]) ? self::removeDecimalICD($icd_codes[6]) : '';
        $claim_detail['icd_G'] = isset($icd_codes[7]) ? self::removeDecimalICD($icd_codes[7]) : '';
        $claim_detail['icd_H'] = isset($icd_codes[8]) ? self::removeDecimalICD($icd_codes[8]) : '';
        $claim_detail['icd_I'] = isset($icd_codes[9]) ? self::removeDecimalICD($icd_codes[9]) : '';
        $claim_detail['icd_J'] = isset($icd_codes[10]) ? self::removeDecimalICD($icd_codes[10]) : '';
        $claim_detail['icd_K'] = isset($icd_codes[11]) ? self::removeDecimalICD($icd_codes[11]) : '';
        $claim_detail['icd_L'] = isset($icd_codes[12]) ? self::removeDecimalICD($icd_codes[12]) : '';
        $claim_detail['resub_code'] = isset($claim_data->claim_details->resubmission_code) ? $claim_data->claim_details->resubmission_code : '';
        $claim_detail['original_ref'] = isset($claim_data->claim_details->original_ref_no) ? $claim_data->claim_details->original_ref_no : '';
        $facility_clia_number = isset($claim_data->facility_detail->clia_number) ? @$claim_data->facility_detail->clia_number : '';
        // $claim_detail['prior_auth_no'] = empty($facility_clia_number)?(isset($claim_data->claim_details->box_23) ?$claim_data->claim_details->box_23:""):$facility_clia_number;       
        $claim_detail['prior_auth_no'] = (isset($claim_data->claim_details->box_23)) ? $claim_data->claim_details->box_23 : "";
        if (empty($claim_detail['prior_auth_no'])) {
            $claim_detail['prior_auth_no'] = $claim_data->auth_no;
        }
        $etin_ssn = '';
        $etin_tax = '';
		
        $claim_detail['emergency'] = isset($claim_data->claim_details->emergency) ? $claim_data->claim_details->emergency : '';
        $claim_detail['pos'] = isset($claim_data->facility_detail->pos_details->code) ? $claim_data->facility_detail->pos_details->code : '';
        $claim_detail['epsdt'] = isset($claim_data->claim_details->epsdt) ? $claim_data->claim_details->epsdt : '';
        $claim_detail['billing_provider_npi'] = !empty($claim_data->billing_provider) ? $claim_data->billing_provider->npi : '';
		
		/* Devi mam told to change billing provider taxanomy1 is default print in cms1500 form */
		$claim_detail['billing_provider_taxanomy'] = isset($claim_data->billing_provider->taxanomy) ? $claim_data->billing_provider->taxanomy->code : '';
		
        $claim_detail['rendering_provider_npi'] = !empty($claim_data->rendering_provider) ? $claim_data->rendering_provider->npi : '';
        if (isset($claim_data->billing_provider->etin_type) && ($claim_data->billing_provider->etin_type == 'SSN')) {
            $etin_ssn = 'X';
        } else {
            $etin_tax = 'X';
        }
        $claim_detail['etin_ssn'] = $etin_ssn;
        $claim_detail['etin_tax'] = $etin_tax;
        $claim_detail['accept_assignment'] = (isset($claim_data->claim_details->accept_assignment) && $claim_data->claim_details->accept_assignment == "No") ? $claim_data->claim_details->accept_assignment : 'Yes';
        $claim_detail['ssn_or_taxid_no'] = isset($claim_data->billing_provider->etin_type_number) ? preg_replace('/[^a-z0-9]/i', '', $claim_data->billing_provider->etin_type_number) : '';
        $claim_detail['claim_no'] = @$claim_data->claim_number; // send as patient account number
        $claim_detail['total'] = isset($claim_data->total_charge) ? $claim_data->total_charge : "0.00";
        $claim_detail['reserved_nucc_box30'] = '';
        $rendering_provider_id = @$claim_data->rendering_provider->id;
        $renderingProviderName = (isset($claim_data->rendering_provider->id)) ? Provider::getProviderNamewithDegree($rendering_provider_id) : '';
        $claim_detail['rendering_provider_name'] = isset($renderingProviderName) ? $renderingProviderName : '';
		// Added H:i:s default in submitted date
        $claim_detail['rendering_provider_date'] = (isset($claim_data->submited_date) && $claim_data->submited_date != '0000-00-00 00:00:00') ? date('m/d/Y', strtotime($claim_data->submited_date)) : '';
        if (empty($claim_detail['rendering_provider_date']))
            $claim_detail['rendering_provider_date'] = Helpers::timezone(date('m/d/Y H:i:s'),'m/d/Y');

        $claim_detail['facility_name'] = isset($claim_data->facility_detail->facility_name) ? $claim_data->facility_detail->facility_name : '';
		
		/* When pos selected home then print patient home address */
		
		if($claim_data->pos_id == 12){
			$claim_detail['facility_addr'] = (isset($claim_data->patient->address1) ? $this->removeSpecialChar(@$claim_data->patient->address1) : '');
			$facility_zip_4 = isset($claim_data->patient->zip4) ? '' . $claim_data->patient->zip4 : '';
			if (isset($claim_data->patient->city))
				$claim_detail['facility_city'] = $this->removeSpecialChar(@$claim_data->patient->city, 'alpha') . ' ' . $this->removeSpecialChar(@$claim_data->patient->state, 'alpha') . ' ' . @$claim_data->patient->zip5 . @$facility_zip_4;
			else
				$claim_detail['facility_city'] = '';
		}else{
			$claim_detail['facility_addr'] = isset($claim_data->facility_detail->facility_address->address1) ? $this->removeSpecialChar($claim_data->facility_detail->facility_address->address1) : '';
			$facility_zip_4 = isset($claim_data->facility_detail->facility_address->pay_zip4) ? '' . $claim_data->facility_detail->facility_address->pay_zip4 : '';
			if (isset($claim_data->facility_detail->facility_address->city))
				$claim_detail['facility_city'] = $this->removeSpecialChar(@$claim_data->facility_detail->facility_address->city, 'alpha') . ' ' . $this->removeSpecialChar(@$claim_data->facility_detail->facility_address->state, 'alpha') . ' ' . @$claim_data->facility_detail->facility_address->pay_zip5 . @$facility_zip_4;
			else
				$claim_detail['facility_city'] = '';
		}	
		/* When pos selected home then print patient home address */
		
        $claim_detail['facility_npi'] = isset($claim_data->facility_detail->facility_npi) ? $claim_data->facility_detail->facility_npi : '';
        $service_facility_qual = isset($claim_data->claim_details->service_facility_qual) ? $claim_data->claim_details->service_facility_qual : '';
        $facility_otherid = isset($claim_data->claim_details->facility_otherid) ? $claim_data->claim_details->facility_otherid : '';
        $billing_prov_zip_4 = isset($claim_data->billing_provider->zipcode4) ? '' . $claim_data->billing_provider->zipcode4 : '';
        $claim_detail['box_32b'] = !empty($service_facility_qual && $facility_otherid) ? $service_facility_qual . $facility_otherid : '';
        $billing_provider_id = @$claim_data->billing_provider->id;
        $billingProviderName = (isset($claim_data->billing_provider->id)) ? Provider::getProviderNamewithDegree($billing_provider_id) : '';
        $claim_detail['bill_provider_name'] = isset($billingProviderName) ? substr($billingProviderName, 0, 29) : '';
        $claim_detail['bill_provider_addr'] = isset($claim_data->billing_provider->address_1) ? substr($claim_data->billing_provider->address_1, 0, 29) : '';
        $claim_detail['bill_provider_phone'] = isset($claim_data->billing_provider->phone) ? str_replace(array('(', ')'), '', $claim_data->billing_provider->phone) : '';
        $claim_detail['bill_provider_city'] = substr(@$claim_data->billing_provider->city, 0, 17) . ' ' . @$claim_data->billing_provider->state . ' ' . @$claim_data->billing_provider->zipcode5 . @$billing_prov_zip_4;
        // $claim_detail['bill_provider_city'] = !empty($claim_data->billing_provider->city) ? $claim_data->billing_provider->city . ' ' : '' . !empty($claim_data->billing_provider->state) ? $claim_data->billing_provider->state . ' ' : '' . !empty($claim_data->billing_provider->zipcode5) ? $claim_data->billing_provider->zipcode5 : '' . $billing_prov_zip_4;
        $billing_provider_qual = isset($claim_data->claim_details->billing_provider_qualifier) ? $claim_data->claim_details->billing_provider_qualifier : '';
        $billing_provider_otherid = isset($claim_data->claim_details->billing_provider_otherid) ? $claim_data->claim_details->billing_provider_otherid : '';
        $claim_detail['box_33b'] = !empty($billing_provider_qual && $billing_provider_otherid) ? $billing_provider_qual . $billing_provider_otherid : '';
        $claim_detail['box_24I'] = "G2"; // Need to make as dynamic
        $claim_detail['box_24J'] = "987878978";
        $i = 1;
        $trans_key = ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E', '6' => 'F', '7' => 'G', '8' => 'H', '9' => 'I', '10' => 'J', '11' => 'K', '12' => 'L', ',' => ''];
        $doc = [];
        //////dd($claim_detail['epsdt']);
        $total_charge_perpage = 0;
        foreach ($claim_data->dosdetails as $dos_detail) {
            if (!empty($dos_detail)) {
                $doc['row_' . $i]['box_24AtoJ'] = @$dos_detail->claimCptShadedDetails->box_24_AToG;
                $doc['row_' . $i]['from_mm'] = date('m', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['from_dd'] = date('d', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['from_yy'] = date('y', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_mm'] = date('m', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_dd'] = date('d', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['to_yy'] = date('y', strtotime($dos_detail->dos_from));
                $doc['row_' . $i]['cpt'] = $dos_detail->cpt_code;
                $doc['row_' . $i]['mod1'] = isset($dos_detail->modifier1) ? $dos_detail->modifier1 : '';
                $doc['row_' . $i]['mod2'] = isset($dos_detail->modifier2) ? $dos_detail->modifier2 : '';
                $doc['row_' . $i]['mod3'] = isset($dos_detail->modifier3) ? $dos_detail->modifier3 : '';
                $doc['row_' . $i]['mod4'] = isset($dos_detail->modifier4) ? $dos_detail->modifier4 : '';
                $doc['row_' . $i]['billed_amt'] = isset($dos_detail->charge) ? explode('.', $dos_detail->charge) : '';
                $doc['row_' . $i]['icd_pointer'] = isset($dos_detail->cpt_icd_map_key) ? substr(strtr($dos_detail->cpt_icd_map_key, $trans_key), 0, 4) : '';
                $doc['row_' . $i]['unit'] = isset($dos_detail->unit) ? $dos_detail->unit : 1;
                $doc['row_' . $i]['rendering_provider_npi'] = (!empty($dos_detail->cpt_code)) ? $claim_detail['rendering_provider_npi'] : "";
                $doc['row_' . $i]['box_24I'] = (!empty($dos_detail->cpt_code)) ? $claim_detail['rendering_provider_qual'] : "";
                $doc['row_' . $i]['box_24J'] = (!empty($dos_detail->cpt_code)) ? $claim_detail['rendering_provider_otherId'] : "";
                $doc['row_' . $i]['pos'] = (!empty($claim_data->pos->code)) ? $claim_data->pos->code : "";
                $doc['row_' . $i]['emergency'] = (!empty($dos_detail->cpt_code) && $claim_detail['emergency'] != "No") ? $claim_detail['emergency'] : "";
                $doc['row_' . $i]['epsdt'] = (!empty($dos_detail->cpt_code) && $claim_detail['epsdt'] != "No") ? substr($claim_detail['epsdt'], 0, 1) : "";
                $doc['row_' . $i]['total_claim_charge'] = $dos_detail->charge;
                $total_charge_perpage = $dos_detail->charge + $total_charge_perpage;
                if ($i % 6 == 0) {
                    $doc['row_' . $i]['total_claim_charge'] = number_format($total_charge_perpage, 2, '.', '');
                    $total_charge_perpage = 0;
                } else if ($i == count($claim_data->dosdetails)) {  
                    $doc['row_' . $i]['total_claim_charge'] = number_format($total_charge_perpage, 2, '.', '');
                }
            }
            $i++;
        }
        $claim_detail['box_24'] = $doc;
        $box_count = count((array)$claim_detail['box_24']);
        return response::json(array('status' => '', 'data' => compact('claim_detail', 'box_count'), 'message' => ''));
    }

    public function removeSpecialChar($str, $type = null) {
        if ($type == "alpha") {
            $data = preg_replace("/[^a-zA-Z]/", " ", $str);
        } else {
            $data = preg_replace("/[^0-9a-zA-Z]/", " ", $str);
        }
        return $data;
    }

    public static function removeDecimalICD($icd = null) {
        return ($icd != "") ? str_replace('.', '', $icd) : "";
    }

    /* function getinsurancedetail($insurance, $patient_id)
      {
      $getpatientins = PatientInsurance::with(array('insurance_details' => function ($query) {
      $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'insurancetype_id')->with('insurancetype');
      }, 'patient'))->where('patient_id', $patient_id)->whereIn('insurance_id', $insurance)->get();
      $ins_data = [];
      //////dd($insurance)      ;
      $other_ins_detail = PatientInsurance::with(array('insurance_details' => function ($query) {
      $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'insurancetype_id')->with('insurancetype');
      }, 'patient'))->whereIn('category', ['Secondary','Tertiary'])->where('patient_id', $patient_id)->first();
      //->whereNotIn('insurance_id', $insurance)
      // ////dd($other_ins_detail);
      $other_ins_data = ['other_insured_name' => '', 'other_insured_relation' => ''];

      if (!empty($other_ins_detail)) {
      if(empty(@$other_ins_detail->middle_name)){
      $insured_name = @$other_ins_detail->last_name . ", " .@$other_ins_detail->first_name . ", " .@$other_ins_detail->middle_name;
      }   else{
      $insured_name = @$other_ins_detail->last_name . ", " .@$other_ins_detail->first_name;
      }
      $other_ins_data = [
      'is_another_ins' => 'Yes',
      'other_insured_relation' => $other_ins_detail->relationship,
      'other_insured_name' => @$insured_name,
      'other_ins_policy' => @$other_ins_detail->policy_id,
      'other_insur_name' => @$other_ins_detail->insurance_details->insurance_name,
      'other_insur_type' => @$other_ins_detail->insurance_details->insurancetype->type_name,
      ];
      }

      if (!empty($getpatientins) && count($getpatientins)) {
      foreach ($getpatientins as $insurance_detail) {
      $insurance_type = @$insurance_detail->insurance_details->insurancetype->type_name;
      $patient_name = @$insurance_detail->patient->last_name . ", " .@$insurance_detail->patient->first_name . ", " .@$insurance_detail->patient->middle_name;
      $insured_name = @$insurance_detail->last_name . ", " .@$insurance_detail->first_name . ", " .@$insurance_detail->middle_name;
      //$insured_name = Helpers::getNameformat($insurance_detail->last_name, $insurance_detail->first_name, $insurance_detail->middle_name);
      // $patient_name = Helpers::getNameformat($insurance_detail->patient->last_name, @$insurance_detail->patient->first_name, @$insurance_detail->patient->middle_name);
      $insured_dob_m = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('m', strtotime($insurance_detail->insured_dob)) : '';
      $insured_dob_d = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('d', strtotime($insurance_detail->insured_dob)) : '';
      $insured_dob_y = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('Y', strtotime($insurance_detail->insured_dob)) : '';
      $zip_code = isset($insurance_detail->insured_zip5) ? $insurance_detail->insured_zip5 : '';
      $zip_code4 = (!empty($insurance_detail->insured_zip4)) ? '-' . $insurance_detail->insured_zip4 : '';
      $insured_name = ($insurance_detail->relationship == "Self") ? $patient_name : $insured_name;
      $ins_data = [
      'policy_num' => $insurance_detail->policy_id,
      'insured_dob' => $insurance_detail->insured_dob,
      'insured_addr' => (@$insurance_detail->relationship == "Self" || empty($insurance_detail->insured_address1)) ?$this->removeSpecialChar($insurance_detail->patient->address1) : $this->removeSpecialChar(@$insurance_detail->insured_address1),
      'insured_address2' => (@$insurance_detail->relationship == "Self" || empty($insurance_detail->insured_address2)) ? $this->removeSpecialChar($insurance_detail->patient->address2) : $this->removeSpecialChar($insurance_detail->insured_address2),
      'insured_city' => (@$insurance_detail->relationship == "Self" || empty($insurance_detail->insured_city)) ? $this->removeSpecialChar($insurance_detail->patient->city) : $this->removeSpecialChar($insurance_detail->insured_city),
      'insured_state' => (@$insurance_detail->relationship == "Self" || empty($insurance_detail->insured_state)) ? $this->removeSpecialChar($insurance_detail->patient->state) : $this->removeSpecialChar($insurance_detail->insured_state),
      'insured_zip5' => (@$insurance_detail->relationship == "Self" || empty($insurance_detail->insured_zip5)) ? $insurance_detail->patient->zip5 : $insurance_detail->insured_zip5,
      'insured_zip4' => (@$insurance_detail->relationship == "Self" || empty($insurance_detail->insured_zip4)) ? $insurance_detail->patient->zip4 : $insurance_detail->insured_zip4,
      'group_name' => $insurance_detail->group_name,
      'insured_relation' => $insurance_detail->relationship,
      'group_id' => $insurance_detail->group_id,
      'insur_name' => $insurance_detail->insurance_details->insurance_name,
      'insured_phone' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->phone : $insurance_detail->insurance_details->insured_phone,
      'insured_name' => ($insurance_type != "Medicare") ? $insured_name : "",
      // 'ins_policy_no' => ($insurance_type != "Medicare" && isset($other_ins_data['other_insur_type']) && $other_ins_data['other_insur_type'] == "Medicare") ? @$other_ins_data['other_ins_policy'] : (($insurance_type == "Medicare"  && @$other_ins_data['other_insur_type'] != "Medicare")?"NONE":""),
      'ins_policy_no' => ($insurance_type != "Medicare" && isset($other_ins_data['other_insur_type']) && $other_ins_data['other_insur_type'] == "Medicare") ? @$other_ins_data['other_ins_policy'] : "NONE",
      'insured_dob_m' => $insured_dob_m,
      'insured_dob_d' => $insured_dob_d,
      'insured_dob_y' => $insured_dob_y,
      'insured_gender' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->gender : $insurance_detail->insured_gender,
      'insured_relation' => $insurance_detail->relationship,
      'insured_id_number' => $insurance_detail->policy_num,
      'insurance_address1' => $this->removeSpecialChar($insurance_detail->insurance_details->address_1),
      'insurance_address2' => $this->removeSpecialChar($insurance_detail->insurance_details->address_2),
      'insurance_city' => $this->removeSpecialChar($insurance_detail->insurance_details->city),
      'insurance_state' => $this->removeSpecialChar($insurance_detail->insurance_details->state),
      'insurance_zipcode5' => $insurance_detail->insurance_details->zipcode5,
      'insurance_zipcode4' => $insurance_detail->insurance_details->zipcode4,
      'primary_ins_type' => $insurance_type];
      }
      } else {
      // return $this->getpateintarcheiveinsurancedetail($insurance, $patient_id); // We wont delete insurance if it used at claims so this was no need
      }
      $ins_data = array_merge($other_ins_data, $ins_data);
      return $ins_data;
      }
     */

    /**
     * @param $datas
     * @param $patientPaid
     * @param $claimFinData
     * @param $paymentV1
     * @return mixed
     */
    public function patientWalletBridge($datas, $patientPaid, $claimFinData) {
        try {
            $walletAmount = abs($patientPaid);
            static $paidAmountStatus1 = true;
            static $claimTxDecId = 0;
            $newwalletData = array(
                'patient_id' => $claimFinData['patient_id'],
                'pmt_info_id' => '',
                'tx_type' => '',
                'amt_pop' => -1 * $patientPaid,
                'wallet_ref_id' => '',
                'claimId' => $datas['claim_id'],
                'resp' => $datas['resp']
            );
            if ($paidAmountStatus1 && $walletAmount > 0) {
                //it's use create a ClaimLevel Desc only
                $claimTxDecId = $this->paymentV1->storeWalletData($newwalletData, true, false)['claimTxID'];
                $paidAmountStatus1 = false;
            } else {
                ClaimTXDESCV1::where('id', $claimTxDecId)->update(['value_1' => DB::raw('value_1 +' . $walletAmount . '')]);
            }
            $datas['claimTxDesID'] = $claimTxDecId;
            $this->moveToWalletCheck($walletAmount, $datas['claim_id'], $datas);
            return $claimTxDecId;
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("patientWalletBridge", $e);
        }
    }

    public function findPatientInsuranceDetails($data, $action, $patientInsuranceId = null) {
        try {
            if ($action == 'GET' && !empty($data)) {
                $currentInsuranceId = PatientInsurance::where('patient_id', $data['patientId'])
                        ->where('insurance_id', $data['insuranceId'])
                        ->where('category', $data['insuranceCategory'])
                        ->value('id');
                return $currentInsuranceId;
            } elseif ($action == 'UPDATE' && !empty($patientInsuranceId)) {
                
            }
        } catch (Exception $e) {
            $resp = $this->showErrorResponse("findPatientInsuranceDetails", $e);
        }
    }

    public function getinsurancedetail($pat_ins_id, $patient_id, $dob = array()) {
        $insurance_detail = PatientInsurance::with(array('insurance_details' => function ($query) {
                        $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'insurancetype_id')->with('insurancetype');
                    }, 'patient'))->where('id', $pat_ins_id)->first();
        $other_ins_detail = PatientInsurance::with(array('insurance_details' => function ($query) {
                                $query->select('id', 'insurance_name', 'address_1', 'address_2', 'city', 'state', 'zipcode5', 'zipcode4', 'insurancetype_id')->with('insurancetype');
                            }, 'patient'))
                        ->whereNotIn('id', [$pat_ins_id])
                        ->orderBy('category')
                        ->whereIn('category', ['Primary', 'Secondary', 'Tertiary'])
                        ->where('patient_id', $patient_id)->first();
        $other_ins_data = ['other_insured_name' => '', 'other_insured_relation' => ''];
        //  dd($other_ins_detail);            
        if (!empty($other_ins_detail)) {
            $insured_name = $this->getNameFromFormat(@$other_ins_detail->first_name, @$other_ins_detail->last_name, @$other_ins_detail->middle_name);
            $patient_name = $this->getNameFromFormat(@$other_ins_detail->patient->first_name, @$other_ins_detail->patient->last_name, @$other_ins_detail->patient->middle_name);
            $other_ins_data['is_another_ins'] = 'Yes';
            $other_ins_data['other_insured_relation'] = $other_ins_detail->relationship;
            if ($other_ins_detail->relationship == "Self" && ( @$insurance_detail->insurance_details->insurancetype->type_name == "Medicare" || @$other_ins_detail->insurance_details->insurancetype->type_name == "Medicare")) {
                $insured_name = "";
                $insured_policy = "";
                $insurance_name = '';
            } else {
                $insured_name = (@$other_ins_detail->relationship == "Self" ? $patient_name : $insured_name);
                $insured_policy = @$other_ins_detail->policy_id;
                $insurance_name = substr($other_ins_detail->insurance_details->insurance_name, 0, 28);
            }
            $other_ins_data['other_insured_name'] = $insured_name;
            $other_ins_data['other_ins_policy'] = $insured_policy;
            $other_ins_data['other_insur_name'] = $insurance_name;
            // conditions based on medicare related details ends here
        }
        $ins_data = [];
        if (!empty($insurance_detail) && count((array)$insurance_detail)) {
            //dd($insurance_detail);
            $insurance_type = @$insurance_detail->insurance_details->insurancetype->type_name;
            $patient_name = $this->getNameFromFormat(@$insurance_detail->patient->first_name, @$insurance_detail->patient->last_name, @$insurance_detail->patient->middle_name);
            $insured_name = $this->getNameFromFormat(@$insurance_detail->first_name, @$insurance_detail->last_name, @$insurance_detail->middle_name);
            $insured_dob_m = (@$insurance_detail->relationship == "Self") ? $dob['dob_month'] : (!is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('m', strtotime($insurance_detail->insured_dob)) : '');
            $insured_dob_d = (@$insurance_detail->relationship == "Self") ? $dob['dob_day'] : (!is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('d', strtotime($insurance_detail->insured_dob)) : '');
            $insured_dob_y = (@$insurance_detail->relationship == "Self") ? $dob['dob_year'] : (!is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('Y', strtotime($insurance_detail->insured_dob)) : '');
            //dd($dob['dob_year']);
            /* $insured_dob_m = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('m', strtotime($insurance_detail->insured_dob)) : '';
              $insured_dob_d = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('d', strtotime($insurance_detail->insured_dob)) : '';
              $insured_dob_y = !is_null($insurance_detail->insured_dob && $insurance_detail->insured_dob != '0000-00-00' && $insurance_detail->insured_dob != '1970-01-01') ? date('Y', strtotime($insurance_detail->insured_dob)) : ''; */
            $zip_code = isset($insurance_detail->insured_zip5) ? $insurance_detail->insured_zip5 : '';
            $zip_code4 = (!empty($insurance_detail->insured_zip4)) ? '' . $insurance_detail->insured_zip4 : '';
            $insured_name = ($insurance_detail->relationship == "Self") ? $patient_name : $insured_name;
            $ins_data = [
                'policy_num' => $insurance_detail->policy_id,
                'insured_dob' => $insurance_detail->insured_dob,
                'insured_addr' => (@$insurance_detail->relationship == "Self" || @$insurance_detail->same_patient_address == 'yes') ? $this->removeSpecialChar($insurance_detail->patient->address1) : $this->removeSpecialChar(@$insurance_detail->insured_address1),
                'insured_address2' => (@$insurance_detail->relationship == "Self" || @$insurance_detail->same_patient_address == 'yes') ? $this->removeSpecialChar($insurance_detail->patient->address2) : $this->removeSpecialChar($insurance_detail->insured_address2),
                'insured_city' => (@$insurance_detail->relationship == "Self" || @$insurance_detail->same_patient_address == 'yes') ? $this->removeSpecialChar($insurance_detail->patient->city, 'alpha') : $this->removeSpecialChar($insurance_detail->insured_city, 'alpha'),
                'insured_state' => (@$insurance_detail->relationship == "Self" || @$insurance_detail->same_patient_address == 'yes') ? $this->removeSpecialChar($insurance_detail->patient->state, 'alpha') : $this->removeSpecialChar($insurance_detail->insured_state, 'alpha'),
                'insured_zip5' => (@$insurance_detail->relationship == "Self" || @$insurance_detail->same_patient_address == 'yes') ? $insurance_detail->patient->zip5 : $insurance_detail->insured_zip5,
                'insured_zip4' => (@$insurance_detail->relationship == "Self" || @$insurance_detail->same_patient_address == 'yes') ? $insurance_detail->patient->zip4 : $insurance_detail->insured_zip4,
                // 'group_name' =>  ($insurance_type != "Medicare") ?$insurance_detail->group_name : "", 
                'group_name' => $insurance_detail->group_name,
                'insured_relation' => $insurance_detail->relationship,
                'group_id' => $insurance_detail->group_id,
                'insur_name' => $insurance_detail->insurance_details->insurance_name,
                'insured_phone' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->phone : $insurance_detail->insurance_details->insured_phone,
                // 'insured_name' => ($insurance_type != "Medicare") ? $insured_name : "", 
                'insured_name' => $insured_name,
                'ins_policy_no' => ($insurance_type != "Medicare" && isset($other_ins_data['other_insur_type']) && $other_ins_data['other_insur_type'] == "Medicare") ? @$other_ins_data['other_ins_policy'] : "NONE",
                'insured_dob_m' => $insured_dob_m,
                'insured_dob_d' => $insured_dob_d,
                'insured_dob_y' => $insured_dob_y,
                'insured_gender' => (@$insurance_detail->relationship == "Self") ? $insurance_detail->patient->gender : $insurance_detail->insured_gender,
                'insured_relation' => $insurance_detail->relationship,
                'insured_id_number' => $insurance_detail->policy_num,
                'insurance_address1' => $this->removeSpecialChar($insurance_detail->insurance_details->address_1),
                'insurance_address2' => $this->removeSpecialChar($insurance_detail->insurance_details->address_2),
                'insurance_city' => $this->removeSpecialChar($insurance_detail->insurance_details->city, 'alpha'),
                'insurance_state' => $this->removeSpecialChar($insurance_detail->insurance_details->state, 'alpha'),
                'insurance_zipcode5' => $insurance_detail->insurance_details->zipcode5,
                'insurance_zipcode4' => $insurance_detail->insurance_details->zipcode4,
                'primary_ins_type' => $insurance_type
            ];
        }        
        $ins_data = array_merge($other_ins_data, $ins_data);        
        return $ins_data;
    }

    public function getNameFromFormat($first_name, $last_name, $middle_name = "") {
        $insured_name = '';
        if (!empty($middle_name)) {
            $insured_name = $last_name . ", " . @$first_name . ", " . @$middle_name;
        } elseif (!empty($last_name) && !empty($first_name)) {
            $insured_name = @$last_name . ", " . @$first_name;
        }
        return $insured_name;
    }

    public function GetandChangePatientInsuranceID() {
        $claims = ClaimInfoV1::where("insurance_category", '!=', '')->where("patient_insurance_id", '=', '0')
                        ->select('patient_id', 'insurance_id', 'insurance_category', 'id', 'claim_number')->get()->toArray();
        try {
            foreach ($claims as $key => $claim) {
                $patient_ins_id = $this->findPatientInsuranceDetails(['patientId' => $claim['patient_id'], 'insuranceId' => $claim['insurance_id'], 'insuranceCategory' => $claim['insurance_category']], 'GET');
                if (!empty($patient_ins_id)) {
                    ClaimInfoV1::where('id', $claim['id'])->update(['patient_insurance_id' => $patient_ins_id]);
                } else {
                    Log::info("Claims not updated #".$claim['id']);
                    //Log::info($claim);
                }
            }
        } catch (Exception $e) {
            echo "Not updated";
        }
    }

    // public function getClaimStatus($id)
    // {
    //     $get_ticket_stat = ClaimInfoV1::where('created_by',$id)->orderBy('id', 'desc')->take(5)->select('claim_number','status','updated_at')->get()->toArray();
    //     $resultArray = array_map( function($value) { return (array)$value; }, $get_ticket_stat );        
    //     return $resultArray;
    // }

}