<?php

namespace App\Traits;

use App;
use App\Http\Helpers\Helpers;
use App\Models\Payments\ClaimCPTTXDESCV1;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\ClaimTXDESCV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTWalletV1;
use App\Models\Patients\PatientInsurance as PatientInsurance;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

// should include to make custome response Traits

trait ClaimUtil {

    use CommonUtil;

    /**
     * To get claim details.
     * Params: $claimId: integer
     *
     */
    public function getClaimDetails($claimId = '') {
        $practice_timezone = Helpers::getPracticeTimeZone(); 
        $resultSet = [];
        if (!empty($claimId)) {
            $resultSet = ClaimInfoV1::select("*", DB::raw("CONVERT_TZ(created_at,'UTC','".$practice_timezone."') as created_at"), DB::raw("CONVERT_TZ(submited_date,'UTC','".$practice_timezone."') as submited_date"), DB::raw("CONVERT_TZ(last_submited_date,'UTC','".$practice_timezone."') as last_submited_date"))->with(['rendering_provider','refering_provider','billing_provider','facility_detail',
                            'insurance_details', 'dosdetails', 'patient', 'pos', 'anesthesia_details'] )
                            ->where('id', $claimId)->first();
        }
        return $resultSet;
    }

    /**
     * To get claim Responsibility
     * Params: $claimId: integer
     *
     */
    public function getClaimResponsibility($claimId = '')
    {
        try {
            if (!empty($claimId)) {
                $climInfo = ClaimInfoV1::select(['id', 'insurance_id', 'self_pay'])
                    ->where('id', $claimId)->first()->toArray();
                $resp = ($climInfo['insurance_id'] > 0) ? $climInfo['insurance_id'] : 0;
                return $resp;
            }
        }catch (Exception $e) {
            $resp = $this->showErrorResponse("getClaimResponsibility", $e);
            //dd($resp);
        }
    }
    
    /**
     * To get Claim insurance Category
     * Params: $claimInsuranceId: integer , $patientId: integer, $payerInsuranceId : Integer
     *
     */
    
    public function getClaimInsuranceCategory($claimInsuranceId, $payerInsuranceId, $patientId, $insuranceCategory){
        if(isset($insuranceCategory) && !empty($insuranceCategory))
            return $insuranceCategory;
        
        $insuranceId = ($payerInsuranceId > 0) ? $payerInsuranceId : (($claimInsuranceId > 0) ? $claimInsuranceId : '');
        if(!empty($insuranceId)){
            $InsuranceCategory = PatientInsurance::where('insurance_id',$insuranceId)->where('patient_id',$patientId)->whereIn('category',['Primary','Secondary','Tertiary'])->select('category')->first();
            if(!empty($InsuranceCategory)){
                return $InsuranceCategory->category;
            }else{
                return '';
            }
        }
        return '';
    }

    /**
     * To store claim transaction description.
     * Params: $txn string, $data array of details
     *
     */
    public function storeClaimTxnDesc($txnFor, $data) {
        /*Log::info("Claim TX For ".$txnFor);
        Log::info($data);
       */ //dd('from storClaimTxnDat');
        // Store claim txn details
        // Handle for 'Responsibility','Payment','New Charge','Denials','Refund','Adjustment','Credit Balance'
        // `transaction_type`, `claim_id`, `payment_id`, `value_1`, `value_2`,'created_by' 
        // New claim: claim_info_id,         
        
        // 'New Charge','Patient Payment','Insurance Payment','Responsibility','Denials','Insurance Refund','Patient Refund','Patient Adjustment','Insurance Adjustment','Patient Credit Balance','Edit Charge','Submitted','Resubmitted','Payer Rejected','Payer Accepted','Clearing House Rejection','Clearing House Accepted','Void Check'


        // @todo new payment with out paid and deductable needs to handle.
        try {
            $txn_type = $claim_id = $pmt_id = $responsibility = $value1 = $value2 = '';
            $claim_id = isset($data['claim_info_id']) ? $data['claim_info_id'] : 0; // claim_info_id required
            $txn_id = isset($data['txn_id']) ? $data['txn_id'] : 0;
            $responsibility = isset($data['resp']) ? $data['resp'] : 0;
            $pmt_id = isset($data['pmt_id']) ? $data['pmt_id'] : 0;
            $ins_bal = isset($data['ins_bal']) ? $data['ins_bal'] : 0;
            $pat_bal = isset($data['pat_bal']) ? $data['pat_bal'] : 0;
            switch ($txnFor) {
                case 'New Charge':
                    // Claim Created
                    $txn_type = 'New Charge';
                    $pmt_id =  $value2 = '';
                    if(isset($data['charge_amt']))
                        $value1 =json_encode(array('charge_amt' => $data['charge_amt']));
                    break;

                case 'Edit Charge':
                    $txn_type = 'Edit Charge';
                    $pmt_id = $value1 = $value2 = '';
                    if(isset($data['old_charge_amt']) && isset($data['new_charge_amt'])){
                        $value1 =json_encode(array('charge_amt' => $data['new_charge_amt'], 'old_charge_amt' => $data['old_charge_amt']));
                    }
                    break;

                case 'Responsibility':
                    $txn_type = 'Responsibility';
                    $value1 = $data['old_insurance_id'];
                    $responsibility = $value2 = $data['new_insurance_id'];
                    break;

                case 'Patient Payment':
                    // Handle Pat. pmt 
                    $txn_type = 'Patient Payment';
                    // $responsibility = '';
                    //$value1 = $data['txn_amount']; // VALUE IN ORDER - (Ins), AMOUNT (Pat.) no need becase we  dont know the over all paid amount
                    // $value2 = ''; // Resp
                    break;

                case 'Insurance Payment':
                    // Handle Ins. Pmt
                    $txn_type = 'Insurance Payment';
                    $value1 = $data['value_1'];
                    $value2 = $data['denials_code'];
                    //$value2 = $responsibility = $data['insurance_id'];
                    //$value1 = $data['txn_amount']; // VALUE IN ORDER - (Ins), AMOUNT (Pat.)
                    break;

                case 'Patient Refund':
                    $txn_type = 'Patient Refund';
                    //$value1 = $data['txn_amount']; // Refund amount
                    // $value2 = $data['reason']; // Resp
                    break;
                    
                case 'Insurance Refund':
                    // Handle refunds
                    $txn_type = 'Insurance Refund';
                    //   $value1 = $data['txn_amount']; // Refund amount
                    // $value2 = $data['reason']; // Resp
                    break;

                case 'Patient Adjustment':
                    $txn_type = 'Patient Adjustment';
                    //$value1 = $data['txn_amount'];//
                    // $value2 = $data['reasonId']; // we get it from the payment Info Tbl using pmd_mode_Id
                    break;

                case 'Insurance Adjustment':
                    // Handle refunds
                    $txn_type = 'Insurance Adjustment';
                    $value1 = $data['value_1'];
                    $value2 = $data['denials_code'];
                    //$value1 = $data['txn_amount']; // Adjustment amount
                    break;

                case 'Wallet':
                    // Handle Wallet 
                    $txn_type = 'Wallet';
                    $pmt_id = '';
                    $value1 = $data['amount']; // Wallet amount
                    break;

                case 'Patient Credit Balance':
                    // Handle credit balance
                    $txn_type = 'Patient Credit Balance';
                    break;

                case 'Denials':
                    // Handle denials
                    $txn_type = 'Denials';
                    $value1 = $data['denials_code']; // Denial code
                    break;

                case 'Submitted': // Handle Edi Submitted
                    $txn_type = 'Submitted';
                    if(isset($data['patient_ins_id']) && isset($data['pat_ins_category'])){
                        $value1 =json_encode(array('patient_insurance_id' => $data['patient_ins_id'], 'insurance_category' => $data['pat_ins_category']));
                    }
                    break;
                    
                case 'Submitted Paper': // Handle Paper Submitted
                    $txn_type = 'Submitted Paper';
                    if(isset($data['patient_ins_id']) && isset($data['pat_ins_category'])){
                        $value1 =json_encode(array('patient_insurance_id' => $data['patient_ins_id'], 'insurance_category' => $data['pat_ins_category']));
                    }
                    break;

                case 'Resubmitted': // Handle Edi Submitted
                    $txn_type = 'Resubmitted';
                    if(isset($data['patient_ins_id']) && isset($data['pat_ins_category'])){
                        $value1 =json_encode(array('patient_insurance_id' => $data['patient_ins_id'], 'insurance_category' => $data['pat_ins_category']));
                    }
                    break;
                    
                case 'Resubmitted Paper': // Handle Paper Submitted
                    $txn_type = 'Resubmitted Paper';
                    if(isset($data['patient_ins_id']) && isset($data['pat_ins_category'])){
                        $value1 =json_encode(array('patient_insurance_id' => $data['patient_ins_id'], 'insurance_category' => $data['pat_ins_category']));
                    }
                    break;
                    
                case 'payer_rejected':  // Handle ClearingHouse Status
                    $txn_type = 'Payer Rejected';
                    break;
                    
                case 'payer_accepted':  // Handle ClearingHouse Status
                    $txn_type = 'Payer Accepted';
                    break;
                    
                case 'Clearing_House_Rejection':    // Handle ClearingHouse Status
                    $txn_type = 'Clearing House Rejection';
                    break;
                    
                case 'Clearing_House_Accepted': // Handle ClearingHouse Status
                    $txn_type = 'Clearing House Accepted';
                    break;

                case 'Void Check':
                    $txn_type = 'Void Check';
                    $value1 = $data['check_amount'];
                    $value2 = isset($data['value2']) ? $data['value2'] : '';
                    break;

                default:
                    $txn_type = ucwords(str_replace("_", " ", $txnFor));
                    break;
            }

            $claimTxnDesc = array(
                "transaction_type" => $txn_type,
                "claim_id" => $claim_id,
                "payment_id" => $pmt_id,
                "txn_id" => $txn_id,
                "responsibility" => $responsibility,
                "ins_bal" => $ins_bal,
                "pat_bal" => $pat_bal,
                "value_1" => $value1,
                "value_2" => $value2,
                "created_at" => date("Y-m-d H:i:s"),
                "created_by" => (isset(Auth::user()->id)) ? Auth::user()->id:@$data['created_by']
            );
            
            //dd($claimTxnDesc);
            //DB::enableQueryLog();
            $resultSet = ClaimTXDESCV1::create($claimTxnDesc);
            if ($resultSet) {
                return $resultSet->id;
            } else {
                //$query = DB::getQueryLog();
                Log::info("Claim Tx not added inside ClaimTXDESCV1");
                //Log::info($query);
            }
        } catch (Exception $e) {
            Log::info("Error on store claim txn; Message: " . $e->getMessage());
            $resp = $this->showErrorResponse("storeClaimTxnDesc", $e);
            // dd($resp);
        }
    }

    /**
     * To store claim cpt transaction description.
     * Params: $txn string, $data array of details
     *
     */
    public function storeClaimCptTxnDesc($txnFor, $data) {
            try {
            $claim_tx_desc_id = isset($data['claim_tx_desc_id']) ? $data['claim_tx_desc_id'] : 0;
            $txn_type = $claim_id = $responsibility = $cpt_info_id = $pmt_id = $value1 = $value2 = '';
            $claim_id = isset($data['claim_info_id']) ? $data['claim_info_id'] : 0; // claim_info_id required
            $claim_cpt_info_id = isset($data['claim_cpt_info_id']) ? $data['claim_cpt_info_id'] : 0; // claim_info_id required
            $txn_id = isset($data['txn_id']) ? $data['txn_id'] : 0;
            $responsibility = isset($data['resp']) ? $data['resp'] : 0;
            $pmt_id = isset($data['pmt_id']) ? $data['pmt_id'] : 0;
            $ins_bal = isset($data['ins_bal']) ? $data['ins_bal'] : 0;
            $pat_bal = isset($data['pat_bal']) ? $data['pat_bal'] : 0;
            $value1 = isset($data['value_1']) ? $data['value_1'] : '';
            switch ($txnFor) {
                case 'New Charge':
                    $txn_type = 'New Charge';
                    $value1 = $value2 = '';
                    if(isset($data['charge_amt']))
                        $value1 = json_encode(array('charge_amt' => $data['charge_amt']));
                    break;

                case 'Edit Charge':
                    $txn_type = 'Edit Charge';
                    $value1 = $data['value1'];
                    $value2 = $data['value2'];
                    if(isset($data['old_charge_amt']) && isset($data['new_charge_amt'])){
                        $value1 =json_encode(array('charge_amt' => $data['new_charge_amt'], 'old_charge_amt' => $data['old_charge_amt']));
                    }
                    break;

                case 'New Payment':
                    $txn_type = 'New Payment';
                    $value1 = $value2 = '';
                    break;

                case 'Patient Payment':
                    // Handle Pat. pmt
                    $txn_type = 'Patient Payment';
                    //  $responsibility = $data['resp'];
                    // $responsibility = '';
                    //$value1 = $data['txn_amount']; // VALUE IN ORDER - (Ins), AMOUNT (Pat.) no need becase we  dont know the over all paid amount
                    // $value2 = ''; // Resp
                    break;

                case 'Change Payment':
                    $txn_type = 'Change Payment';
                    $value1 = $value2 = '';
                    break;

                case 'Responsibility':
                    $txn_type = 'Responsibility';
                    $responsibility = $data['resp'];
                    $value1 = $data['old_insurance_id'];
                    $value2 = $data['new_insurance_id'];
                    break;

                case 'Insurance Payment':
                    // Handle Ins. Pmt
                    $txn_type = 'Insurance Payment';
                    // $responsibility = $data['resp'];
                    //$value2 = $responsibility = $data['insurance_id'];
                    //$value1 = $data['txn_amount']; // VALUE IN ORDER - (Ins), AMOUNT (Pat.)
                    break;

                case 'Patient Refund':
                    $txn_type = 'Patient Refund';
                    //$responsibility = $data['resp'];
                    //$value1 = $data['txn_amount']; // Refund amount
                    // $value2 = $data['reason']; // Resp
                    break;

                case 'Insurance Refund':
                    // Handle refunds
                    $txn_type = 'Insurance Refund';
                    //$responsibility = $data['resp'];
                    //   $value1 = $data['txn_amount']; // Refund amount
                    // $value2 = $data['reason']; // Resp
                    break;

                case 'Patient Adjustment':
                    $txn_type = 'Patient Adjustment';
                    //$value2 = $data['resp'];
                    //$value1 = $data['txn_amount'];//
                    // $value2 = $data['reasonId']; // we get it from the payment Info Tbl using pmd_mode_Id
                    break;

                case 'Insurance Adjustment':
                    // Handle refunds
                    $txn_type = 'Insurance Adjustment';
                    //$value1 = $data['txn_amount']; // Adjustment amount
                    //$value2 = $data['resp']; // Resp
                    break;

                case 'Wallet':
                    // Handle Wallet
                    $txn_type = 'Wallet';
                    $pmt_id = '';
                    $value1 = $data['amount']; // Wallet amount
                    break;

                case 'Patient Credit Balance':
                    // Handle credit balance
                    $txn_type = 'Patient Credit Balance';
                    $value1 = $data['value1'];
                    $value2 = $data['value2'];
                    // $responsibility = $data['resp'];
                    //$value1 = $data['amount']; // Adjustment amount
                    //S$responsibility = $data['resp']; // Resp
                    break;

                case 'Denials':
                    // Handle denials
                    $txn_type = 'Denials';
                    $value1 = $data['denials_code'];  // Denial code
                    // $value2 = $data['denial_reason']; // Resp
                    break;

                case 'Submitted': // Handle Edi Submitted
                    $txn_type = 'Submitted';
                    break;
                    
                case 'Submitted Paper': // Handle Paper Submitted
                    $txn_type = 'Submitted Paper';
                    break;

                case 'Resubmitted': // Handle Edi Submitted
                    $txn_type = 'Resubmitted';
                    break;
                    
                case 'Resubmitted Paper': // Handle Paper Submitted
                    $txn_type = 'Resubmitted Paper';
                    break;
                    
                case 'payer_rejected':  // Handle ClearingHouse Status
                    $txn_type = 'Payer Rejected';
                    break;
                    
                case 'payer_accepted':  // Handle ClearingHouse Status
                    $txn_type = 'Payer Accepted';
                    break;
                    
                case 'Clearing_House_Rejection':    // Handle ClearingHouse Status
                    $txn_type = 'Clearing House Rejection';
                    break;
                    
                case 'Clearing_House_Accepted': // Handle ClearingHouse Status
                    $txn_type = 'Clearing House Accepted';
                    break;

                case 'Void Check':
                    $txn_type = 'Void Check';
                    $value1 = $data['check_amount'];
                    $value2 = isset($data['value2']) ? $data['value2'] : '';
                    break;

                default:
                    $txn_type = ucwords(str_replace("_", " ", $txnFor)); // For non handled items.
                    break;
            }

            $cptTxnDesc = array(
                "claim_tx_desc_id" => $claim_tx_desc_id,
                "transaction_type" => $txn_type,
                "claim_id" => $claim_id,
                "claim_cpt_info_id" => $claim_cpt_info_id,
                "payment_id" => $pmt_id,
                "txn_id" => $txn_id,
                "responsibility" => $responsibility,
                "ins_bal" => $ins_bal,
                "pat_bal" => $pat_bal,
                "value_1" => $value1,
                "value_2" => $value2,
                //"created_at" => date("Y-m-d"),
                "created_by" => (isset(Auth::user()->id)) ? Auth::user()->id:@$data['created_by']
            );

            //Log::info($cptTxnDesc);
            $resultSet = ClaimCPTTXDESCV1::create($cptTxnDesc);
            if ($resultSet) {
                return $resultSet->id;
            } else {
                //$query = DB::getQueryLog();
                Log::info("Claim CPT Tx not added inside ClaimCptTXDESCV1");
               // Log::info($query);
            }
        } catch (Exception $e) {
            Log::info("Error on store claim cpt txn; Message: " . $e->getMessage());
            $resp = $this->showErrorResponse("storeClaimCptTxnDesc", $e);
            //dd($resp);
        }
    }

    /**
     * To get claim transaction description.
     * Params: claimId
     *
     */
    public function getClaimTxnDesc($claimId) {
        // populate the claim txn list by claim id
        // List: Txn date, Resp, Desc, Payment Type, Charges, Pmts, Adj, Pat.Bal., Ins.Bal
        // Eg: Claim denied Pmt : 
        // Trans Date | Responsibility | Description | Payment Type | Charges | Payments | Adj  | Pat Bal | Ins Bal
        // 08/07/17     BCBSA           Charge created              250.00      0.00        0.00    0.00    250.00
        $descArr = [];
        $descArr = ClaimInfoV1::getClaimTxnList($claimId);
        return $descArr;
    }

    /**
     * To get claim cpt transaction description.
     * Params: $claimId, $cptId
     *
     */
    public function getClaimCptTxnDesc($claimId, $cptId = '', $cptTxId = 0) {
        // populate the claim cpt txn list by claim id
        // List: CPT, Txn date, Resp, Desc, Charges, Pmts, Adj, Pat.Bal., Ins.Bal
        // Eg: Claim denied Denial/Remark Codes - PR21, | 
        $descArr = ClaimInfoV1::getClaimCptTxnList($claimId, $cptId, $cptTxId);
        return $descArr;
    }

    /*
     * Common function for get patient balance.
     * Params: patient_id - integer
     * response: Patient_paid, insurance_paid, patient_due, insurance_due, patient_adj, insurance_adj, withheld - array
     * 
     */

    public function getPatientFinDetails($patient_id = 0) {
        // Patient_paid, insurance_paid, patient_due, insurance_due, patient_adj, insurance_adj, withheld
        // have to check the values for status of claims etc...
        $pmtData = [];
        if ($patient_id > 0) {
            $pmt = PmtClaimFinV1::where('patient_id', $patient_id)
                    ->groupBy('patient_id')
                    ->select(DB::raw('sum(patient_paid) AS tpat_paid, '
                                    . ' sum(insurance_paid) AS tins_paid,  '
                                    . ' sum(total_charge) AS tcharge_amt,  '
                                    . ' sum(patient_due) AS tpat_due,  '
                                    . ' sum(insurance_due-(pmt_claim_fin_v1.patient_paid+pmt_claim_fin_v1.patient_adj)) AS tins_due,  '
                                    . ' sum(patient_adj) AS tpat_adj,  '
                                    . ' sum(insurance_adj) AS tins_adj, '
                                    . ' sum(withheld) AS twithheld '))
                    ->first();

            if (!empty($pmt)) {
                $tpat_paid = App\Http\Helpers\Helpers::priceFormat($pmt->tpat_paid);
                $tins_paid = App\Http\Helpers\Helpers::priceFormat($pmt->tins_paid);
                $tcharge_amt = App\Http\Helpers\Helpers::priceFormat($pmt->tcharge_amt);
                $tpat_due = $pmt->tpat_due;
                $tins_due = $pmt->tins_due;
                $tpat_adj = App\Http\Helpers\Helpers::priceFormat($pmt->tpat_adj);
                $tins_adj = App\Http\Helpers\Helpers::priceFormat($pmt->tins_adj);
                $twithheld = App\Http\Helpers\Helpers::priceFormat($pmt->twithheld);
                $tot_ar = App\Http\Helpers\Helpers::priceFormat($pmt->tcharge_amt - ($pmt->tpat_paid + $pmt->tins_paid + $pmt->twithheld + $pmt->tpat_adj + $pmt->tins_adj));
            }
        }
        $pmtData['total_patient_paid'] = isset($tpat_paid) ? $tpat_paid : 0.0;
        $pmtData['total_insurance_paid'] = isset($tins_paid) ? $tins_paid : 0.0;
        $pmtData['total_charge_amt'] = isset($tcharge_amt) ? $tcharge_amt : 0.0;
        $pmtData['total_patient_due'] = isset($tpat_due) ? $tpat_due : 0.0;
        $pmtData['total_insurance_due'] = isset($tins_due) ? $tins_due : 0.0;
        $pmtData['total_patient_adj'] = isset($tpat_adj) ? $tpat_adj : 0.0;
        $pmtData['total_insurance_adj'] = isset($tins_adj) ? $tins_adj : 0.0;
        $pmtData['total_withheld'] = isset($twithheld) ? $twithheld : 0.0;
        $pmtData['total_ar'] = isset($tot_ar) ? $tot_ar : 0.0;

        return $pmtData;
    }

    public function getPatientWalletAmount($patient_id = 0) {
        $walletAmt = PMTWalletV1::where('patient_id', $patient_id)
                ->groupBy('patient_id')
                ->select(DB::raw('sum(amount) AS tot_amt '))
                ->pluck('tot_amt')->first();
        return (!empty($walletAmt)) ? $walletAmt : 0.0;
    }

    // To get patients last payment amount
    public function getPatientLastPaymentAmount($patient_id = 0) {
        return Helpers::getPatientLastPaymentAmount($patient_id);
    }

    // Last payment Date for patient.
    public function getPatientLastPaymentDate($patient_id = 0) {        
        return Helpers::getPatientLastPaymentDate($patient_id);
    }

    // Last payment Type 
    public function getPatientLastPaymentType($patient_id = 0) {
        $pmt_type = PMTInfoV1::where('patient_id', $patient_id)->orderBy('id', 'DESC')->pluck('pmt_mode')->first();
        return $pmt_type; // cash, or card
    }

    // Patient Balance
    public function getPatientBalance($patient_id = 0) {
        return Helpers::getPatientBalance($patient_id);
    }

    //Patient Due
    public function getPatientDue($patient_id = 0) {
        $finDetails = $this->getPatientFinDetails($patient_id);
        $patient_due = $finDetails['total_patient_due'];
        $patient_due = ($finDetails == 0) ? Helpers::priceFormat(0) : $patient_due;
        return $patient_due;                        
    }

    // Patient outstanding insurance due  (AR)
    public function getPatientInsuranceDue($patient_id = 0) {
        $finDetails = $this->getPatientFinDetails($patient_id);
        $ins_due = $finDetails['total_insurance_due'];
        $ins_due = ($finDetails == 0) ? Helpers::priceFormat(0) : $ins_due;
        return $ins_due;
    }

    //    Patient outstanding AR
    public function getPatientARDue($patient_id = 0) {
        $patient_ar_due = $this->getPatientFinDetails($patient_id);
        $patient_ar_due = $patient_ar_due['total_ar'];
        $patient_ar_due = ($patient_ar_due == 0) ? Helpers::priceFormat(0) : $patient_ar_due;
        return $patient_ar_due;
    }

    // AR Days
    public function getPatientARDays($patient_id = 0) {
        return ClaimInfoV1::arDays('', $patient_id);
    }

    // Billed Amount
    public function getPatientBilledAmount($patient_id = 0) {
        return Helpers::getPatientBilledAmount($patient_id);
    }

    //  Unbilled Amount
    public function getPatientUnBilledAmount($patient_id = 0) {
        return Helpers::getPatientUnBilledAmount($patient_id);
    }

    // Insurance Payment
    public function getPatientInsurancePayment($patient_id = 0) {
        $patient_due = $this->getPatientFinDetails($patient_id);
        $patient_due = $patient_due['total_insurance_paid'];
        $patient_due = ($patient_due == 0) ? Helpers::priceFormat(0) : $patient_due;
        return $patient_due;
    }

    // Patient Payment
    public function getPatientPayment($patient_id = 0) {
        $patient_due = $this->getPatientFinDetails($patient_id);
        $patient_due = $patient_due['total_patient_paid'];
        $patient_due = ($patient_due == 0) ? Helpers::priceFormat(0) : $patient_due;
        return $patient_due;
    }


    // Last Statement
    public function getPatientLastStatmentDate($patient_id = 0) {        
        return PatientBudget::where('patient_id',$patient_id)->pluck('last_statement_sent_date')->first();
    }

    // Patient Budget Amount
    public function getPatientBudgetAmount($patient_id = 0) {
        return Helpers::priceFormat(PatientBudget::where('patient_id',$patient_id)->pluck('budget_amt')->first(), 'no');
    }

    // Claim wise Listing page                                  
    public function getClaimBilledAmount($claim_id = 0) {
        return Helpers::priceFormat(PMTClaimFINV1::where('claim_id', $claim_id)->pluck('total_charge')->first(), 'no');
    }

    // Claim Allowed
    public function getClaimAllowedAmount($claim_id = 0) {
        return Helpers::priceFormat(PMTClaimFINV1::where('claim_id', $claim_id)->pluck('total_allowed')->first(), 'no');
    }

    // Claim Paid Amount,
    public function getClaimPaidAmount($claim_id = 0) {
        return Helpers::priceFormat(PMTClaimFINV1::where('claim_id', $claim_id)->select(DB::raw('patient_paid + insurance_paid')), 'no');
    }

    //  AR Due,
    public function getClaimARDue($claim_id = 0) {
        $tot_ar = 0.0;
        //Claims::whereNotIn('status', ['Hold'])->sum('balance_amt');
        $pmt = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->whereNotIn('claim_info_v1.status', ['Hold'])
                ->select(DB::raw('sum(patient_paid) AS tpat_paid, '
                                . ' sum(insurance_paid) AS tins_paid,  '
                                . ' sum(pmt_claim_fin_v1.total_charge) AS tcharge_amt,  '
                                . ' sum(patient_adj) AS tpat_adj,  '
                                . ' sum(insurance_adj) AS tins_adj, '
                                . ' sum(withheld) AS twithheld '))
                ->where('claim_id', $claim_id)
                ->first();
        if (!empty($pmt)) {
            $tot_ar = App\Http\Helpers\Helpers::priceFormat($pmt->tcharge_amt - ($pmt->tpat_paid + $pmt->tins_paid + $pmt->twithheld + $pmt->tpat_adj + $pmt->tins_adj));
        }
        return $tot_ar;
    }

    // Claim adjustment Amount,
    public function getClaimAdjustmentAmount($claim_id = 0) {
        return Helpers::priceFormat(PMTClaimFINV1::where('claim_id', $claim_id)->select(DB::raw('patient_adj + insurance_adj')), 'no');        
    }

    //  First Claim submission date
    public function getClaimFirstSubmissionDate($claim_id = 0) {
        return ClaimInfoV1::where('id', $claim_id)->pluck('submited_date')->first();
    }

    //  Last claim submission date
    public function getClaimLastSubmissionDate($claim_id = 0) {
        return ClaimInfoV1::where('id', $claim_id)->pluck('last_submited_date')->first();
    }

    // DOS
    public function getClaimDos($claim_id = 0) {
        return ClaimInfoV1::where('id', $claim_id)->pluck('date_of_service')->first();
    }

    //  Insurance Over payment
    public function getClaimInsuranceOverPayment($claim_id = 0) {
        return ClaimInfoV1::InsuranceOverPayment($claim_id);
    }

    // Insurance refund
    public function getClaimInsuranceRefundAmount($claim_id = 0) {
        /*
        $due = PaymentClaimDetail::whereHas('latest_payment', function($query) {
                    $query->where('void_check', NULL);
                })->where('claim_id', $claim_id)->where($type, '<', 0)->whereNotIn('payment_type', ["Addwallet"])->sum($type);

        return abs($due);
        */
        return ClaimInfoV1::getRefund($claim_id, 'insurance_paid_amt');
    }

    // Patient refund
    public function getClaimPatientRefundAmount($claim_id = 0) {
        /*
        $due = PaymentClaimDetail::whereHas('latest_payment', function($query) {
                    $query->where('void_check', NULL);
                })->where('claim_id', $claim_id)->where($type, '<', 0)->whereNotIn('payment_type', ["Addwallet"])->sum($type);

        return abs($due);
        */
        return ClaimInfoV1::getRefund($claim_id, 'patient_paid_amt');
    }

    // Rendering Provider
    // Claim id based details - Billed amount, paid, AR Due
    // Dashboard related start
    // Revision 1 - MR-2726 - 26-08-2019 - Kannan - Removed financial table join, since charges which are not submitted even once and it is not 
    // in patient responsibility are unbilled charges. 
    public function getClaimUnbilledTotalCharge() {
        $practice_timezone = Helpers::getPracticeTimeZone();
        $cur_end_date = Carbon::now($practice_timezone)->toDateString();  
        $claims = DB::table('claim_info_v1')->select(DB::raw('sum(claim_info_v1.total_charge) as `total_amt`'),DB::raw('COUNT(claim_info_v1.id) as `total_charges`'),'claim_info_v1.id')->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'");
         $claims->where(function($qry){
                $qry->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count',0);
            }); 
            $claims->whereNull('claim_info_v1.deleted_at');

            $rec = $claims->first();
          return $rec->total_amt;
    }    
    
    public function getHoldClaimTotal() 
    {
       $hold_total = ClaimInfoV1::select(DB::raw('sum(total_charge) as total_charge'))->where('status', 'Hold')->sum("total_charge");       
        return $hold_total;        
    }  

    public function getCurMonOutstandingArPerc() {
        /*
          $last_month = Claims::whereRaw('MONTH(created_at) = ' . Carbon::today()->subMonths(1)->month . ' AND YEAR(created_at) = YEAR(CURDATE()) AND claim_submit_count > 0')
          ->whereNotIn('status', ['Hold', 'Pending'])
          ->sum(DB::raw('patient_due + insurance_due'));
          $current_month = Claims::whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND claim_submit_count > 0')
          ->whereNotIn('status', ['Hold', 'Pending'])
          ->sum(DB::raw('patient_due + insurance_due'));
          return $current_month == 0 ? 0 : (($current_month - $last_month) / $current_month) * 100;
         */
       
        $last_month = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->whereRaw('pmt_claim_fin_v1.created_at <= MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')
                ->where("claim_info_v1.patient_id", "!=", "0")
                ->sum(DB::raw('patient_due + insurance_due - patient_paid'));

        $current_month = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->where("claim_info_v1.patient_id", "!=", "0")
                ->sum(DB::raw('patient_due + insurance_due - patient_paid'));


        $difference = $current_month - $last_month;
        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;
    }

    public function getWeeklyBilledPercent() {
        /*
          //last first week
          $get_preWeeks = $this->getLastFirstweekDates()->getData();
          $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
          $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;

          $firstWeekChargeBilled = $this->getFirstWeekChargeBilledTotal(); //Claims::whereNotIn('status', ['Hold'])->whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('total_charge');
          //last second week
          $get_secWeeks = $this->getLastSecondweekDates()->getData();
          $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
          $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

          $secondWeekChargeBilled = $this->getSecondWeekChargeBilledTotal();//Claims::whereNotIn('status', ['Hold'])->where('claim_submit_count', '=', 0)->whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('total_charge');

          // Calculate percentage of carge
          return $firstWeekChargeBilled == 0 ? 0 : (($firstWeekChargeBilled - $secondWeekChargeBilled) / $firstWeekChargeBilled) * 100;
         */
        return 0;
    }

    public function getWeeklyUnBilledPercent() {
        /*
          //last first week
          $get_preWeeks = $this->getLastFirstweekDates()->getData();
          $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
          $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
          $firstWeekChargeUnBilled = $this->getFirstWeekTotalUnBilled(); //Claims::whereIn('status', ['Ready'])->where('claim_submit_count', '=', 0)->whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('total_charge');

          //last second week
          $get_secWeeks = $this->getLastSecondweekDates()->getData();
          $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
          $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

          $secondWeekChargeUnBilled = $this->getSecondWeekTotalUnBilled(); //Claims::whereIn('status', ['Ready'])->where('claim_submit_count', '=', 0)->whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('total_charge');

          // Calculate percentage of carge
          return $firstWeekChargeUnBilled == 0 ? 0 : (($firstWeekChargeUnBilled - $secondWeekChargeUnBilled) / $firstWeekChargeUnBilled) * 100;
         */
        return 0;
    }

    public function getWeeklyInsPaymentPercentage() {
        /*
          $get_preWeeks = $this->getLastFirstweekDates()->getData();
          $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
          $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
          $firstWeekChargeInsPayment = Claims::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('insurance_paid');
          //last second week
          $get_secWeeks = $this->getLastSecondweekDates()->getData();
          $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
          $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

          $secondWeekChargeInsPayment = Claims::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('insurance_paid');

          // Calculate percentage of carge
          return $firstWeekChargeInsPayment == 0 ? 0 : (($firstWeekChargeInsPayment - $secondWeekChargeInsPayment) / $firstWeekChargeInsPayment) * 100;
         */
        return 0;
    }

    public function getWeeklyPatPaymentPercent() {
        /*
          //last first week
          $get_preWeeks = $this->getLastFirstweekDates()->getData();
          $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
          $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
          $firstWeekChargePatPayment = Claims::whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum('patient_paid');

          //last second week
          $get_secWeeks = $this->getLastSecondweekDates()->getData();
          $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
          $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;
          $secondWeekChargePatPayment = Claims::whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum('patient_paid');

          // Calculate percentage of carge
          return $firstWeekChargePatPayment == 0 ? 0 : (($firstWeekChargePatPayment - $secondWeekChargePatPayment) / $firstWeekChargePatPayment) * 100;
         */
        return 0;
    }

    public function getEdiRejectionTotal() {
        return Helpers::priceFormat(ClaimInfoV1::whereIn('status', ['Rejection'])
                                ->where('self_pay', 'No')
                                ->sum('total_charge'), 'yes');
    }

    public function getWeeklyOutstandingPercent() {
        /*
          //last first week
          $get_preWeeks = $this->getLastFirstweekDates()->getData();
          $get_last_first_sunday = $get_preWeeks->data->get_last_first_sunday;
          $get_last_first_satday = $get_preWeeks->data->get_last_first_satday;
          $firstWeekChargeOutstanding_ar = Claims::where("patient_id", "!=", "0")->whereBetween('created_at', [$get_last_first_sunday, $get_last_first_satday])->sum(DB::raw('patient_due + insurance_due'));

          //last second week
          $get_secWeeks = $this->getLastSecondweekDates()->getData();
          $get_last_second_sunday = $get_secWeeks->data->get_last_second_sunday;
          $get_last_second_satday = $get_secWeeks->data->get_last_second_satday;

          $secondWeekChargeOutstanding_ar = Claims::where("patient_id", "!=", "0")->whereBetween('created_at', [$get_last_second_sunday, $get_last_second_satday])->sum(DB::raw('patient_due + insurance_due'));
          // Calculate percentage of carge
          return $firstWeekChargeOutstanding_ar == 0 ? 0 : (($firstWeekChargeOutstanding_ar - $secondWeekChargeOutstanding_ar) / $firstWeekChargeOutstanding_ar) * 100;
         */
        return 0;
    }

    // Net Collection Rate in Practice Analytics Dashboard
    // Revision 1: Ref: MR-2481 - 06 August 2019 - Kannan
    // Added SUM(withheld) to Select query to account for total collection
    public function getClaimTotalCollections() {
        $total_amount = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                        ->whereNotIn('claim_info_v1.status', ['Hold'])
                        ->select(DB::raw('(SUM(pmt_claim_fin_v1.patient_paid)+SUM(pmt_claim_fin_v1.insurance_paid) + SUM(pmt_claim_fin_v1.patient_adj)+ SUM(pmt_claim_fin_v1.insurance_adj) + SUM(withheld))/ SUM(pmt_claim_fin_v1.total_charge) as collection'))->pluck('collection')->first();
        $total_amount = $total_amount * 100;
        return $total_amount;
    }

    public function getClaimsTotalCharges() {
        $charges_amount = ClaimInfoV1::sum(DB::raw('total_charge'));
        return $charges_amount;
    }

    public function getClaimsTotalARBalance() {
        $tot_ar = 0.0;
        //Claims::whereNotIn('status', ['Hold'])->sum('balance_amt');
        $pmt = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->whereNotIn('claim_info_v1.status', ['Hold'])
                ->select(DB::raw('sum(patient_paid) AS tpat_paid, '
                                . ' sum(insurance_paid) AS tins_paid,  '
                                . ' sum(pmt_claim_fin_v1.total_charge) AS tcharge_amt,  '
                                . ' sum(patient_adj) AS tpat_adj,  '
                                . ' sum(insurance_adj) AS tins_adj, '
                                . ' sum(withheld) AS twithheld '))
                ->first();
        if (!empty($pmt)) {
            $tot_ar = App\Http\Helpers\Helpers::priceFormat($pmt->tcharge_amt - ($pmt->tpat_paid + $pmt->tins_paid + $pmt->twithheld + $pmt->tpat_adj + $pmt->tins_adj));
        }
        return $tot_ar;
    }
    
    public function getClaimsTotalCreatedBetween($start_date, $end_date, $type = 'Charges') { 

        $balance_amt = DB::table('claim_info_v1')
        ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')                           
        ->select(DB::raw('(sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) AS total_ar'))
        ->whereRaw('claim_info_v1.date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL '.$start_date.' day)');
        if($end_date != 'above'){
            $balance_amt->whereRaw('claim_info_v1.date_of_service  <=  DATE(UTC_TIMESTAMP() - INTERVAL '.$end_date.' day)'); 
        }
        $balance_amt->whereNull('claim_info_v1.deleted_at');  
        $balance = $balance_amt->first();                
        return $balance->total_ar; 
    }

     /*public function getClaimsTotalCreatedBetween($start_date, $end_date, $type = 'Charges') {
        $balance_amt = DB::table('claim_info_v1')
                    ->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')                           
                    ->select(DB::raw('(sum(pmt_claim_fin_v1.total_charge)-(sum(pmt_claim_fin_v1.patient_paid)+sum(pmt_claim_fin_v1.insurance_paid) + sum(pmt_claim_fin_v1.withheld) + sum(pmt_claim_fin_v1.patient_adj) + sum(pmt_claim_fin_v1.insurance_adj) )) AS total_ar'))
                    ->where('claim_info_v1.date_of_service', '>=', $start_date)
                    ->where('claim_info_v1.date_of_service', '<=', $end_date)
                     ->whereNull('claim_info_v1.deleted_at')->first(); dd($balance_amt->total_ar);                           
        return $balance_amt->total_ar;
    }*/
    public function getClaimsTotalAdjusted() {
        //Claims::whereRaw("(total_adjusted <> '0' or total_withheld <> '0')")->sum('total_charge');
        $total_adjusted = PMTClaimFINV1::whereRaw("((patient_adj+insurance_adj) <> '0' or withheld <> '0')")
                ->sum('total_charge');
        return $total_adjusted;
    }

    public function getMonthWiseChargeBar($type = 'total_charge') {
        $practice_timezone = Helpers::getPracticeTimeZone();  
        $resp = ClaimInfoV1::select(DB::raw('sum(total_charge) as `total_charge`'), DB::raw("DATE_FORMAT(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."'),'%M') as monthNum"))
                ->orderBy("created_at")
                ->groupBy(DB::raw("month(created_at)"))
                ->whereNotIn('status', ['Hold'])
                ->pluck('total_charge', 'monthNum')->all();
        return $resp;
    }

    public function getClaimAttachemts($create_claim_id) {
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
            $resp = $this->showErrorResponse("createCptTx_CptDes", $e);
            // dd($resp);
        }
    }

    // Dashboard related end
    public function getInsurancebalance($claim_id = '') {
        $claim_insurance_due_value = 0.0;
        $claim_insurance_due_value = PMTClaimFINV1::where('claim_id', $claim_id)->orderBy('id', 'DESC')->pluck('insurance_due')->first();
        return $claim_insurance_due_value;
    }

     public function getCleanClaims() {
       /* $total_claims = ClaimInfoV1::whereIn('status', ['Submitted', 'Ready'])->whereRaw('Date(created_at) = CURDATE()')
                ->where('self_pay', 'No')
                ->where('claim_submit_count', 1)
                ->count();
        $accepted_claims = ClaimInfoV1::whereIn('status', ['Submitted'])->whereRaw('Date(created_at) = CURDATE()')
                ->where('self_pay', 'No')
                ->where('claim_submit_count', 1)
                ->count();*/
        $total_claims = ClaimTXDESCV1::whereIn('transaction_type', ['Submitted', 'Resubmitted'])->whereRaw('Date(created_at) = CURDATE()')
                ->count();
        $accepted_claims = ClaimTXDESCV1::whereIn('transaction_type', ['Clearing House Accepted'])->whereRaw('Date(created_at) = CURDATE()')
                ->count();
        if ($total_claims != '0')
            return ($accepted_claims / $total_claims) * 100;
        else
            return 0;
    }

    
    // Dashboard and Stats details started 


    //  Claims Stats details
    // Return array with type as key and count & amount => $claims['unbilled'] = ['total' => xx, 'amount' => xx]
    // Params: $type => billed / unbilled / hold / rejected / All 
    // date_range => '08/01/2018 - 09/12/2018'
    public function getClaimStats($type, $date_range='All', $patient_id=0,$practice_id = ''){
        // Billed, Unbilled, 
        $resp = [];
        $type = strtolower($type);

        /*
        Unbilled Charges:       Claim Submit Count = 0 and Status = Ready
        Billed Charges:         Claim Submit Count = 0 and Status = Ready   Or      Status = Patient, Paid, Submitted, Denied
        Rejections:             Status = Rejection
        Charges On Hold:        Status = Hold
        */
        if($practice_id != ''){
            $practice_timezone = Helpers::getPracticeTimeZone($practice_id);
        }else{
            $practice_timezone = Helpers::getPracticeTimeZone();
        }
         $cur_end_date = Carbon::now($practice_timezone)->toDateString();  
         $claims = DB::table('claim_info_v1')->select(DB::raw('sum(claim_info_v1.total_charge) as `total_amt`'),DB::raw('COUNT(claim_info_v1.id) as `total_charges`'),'claim_info_v1.id')->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'");
		// switch practice page added condition for provider login based showing values
		// Revision 1 - Ref: MR-2719 22 Aug 2019: Selva
        switch ($type) {

            case 'billed':
                 $claims->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->whereRaw('MONTH(claim_info_v1.created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(claim_info_v1.created_at) = YEAR(UTC_TIMESTAMP())');
					if(Auth::check() && Auth::user()->isProvider())
						$claims->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
                break;
            
            case 'unbilled':

                 $claims->where(function($qry){
                    $qry->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count',0);
                }); 
				if(Auth::check() && Auth::user()->isProvider())
					$claims->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
                break;

            case 'hold':
                $claims->whereIn('status', ['Hold']);
                break;   

            case 'rejected':
                $claims->whereIn('status', ['Rejection']);
				if(Auth::check() && Auth::user()->isProvider())
						$claims->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
                break;   
            
            case 'all':                
                $billed = SELF::getClaimStats('billed',$date_range);                
                $unbilled = SELF::getClaimStats('unbilled',$date_range);                                
                $hold = SELF::getClaimStats('hold',$date_range);
                $rejected = SELF::getClaimStats('rejected',$date_range);  

                return array_merge_recursive($billed, $unbilled, $hold, $rejected);                             
                break;                    
        }

        // Date range handled date fo service field
        if(trim($date_range) != '' && trim(strtolower($date_range)) != 'all') {
            $date = explode('-',trim($date_range));
            $from = date("Y-m-d", strtotime(@$date[0]));
            if($from == '1970-01-01'){
                $from = '0000-00-00';
            }
            $to = date("Y-m-d", strtotime(@$date[1]));         
            $claims->where(function($query) use ($from, $to, $practice_timezone){ 
                $query->whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$from."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$to."'"); 
            });
        }
        if($patient_id != 0) {
            $claims->where('patient_id', $patient_id);
        }
        $claims->whereNull('claim_info_v1.deleted_at');

        $rec = $claims->first();
        
        if(!empty($rec)) {      
            $total_amount = (!empty($rec->total_amt) )? $rec->total_amt : 0;
            $resp[$type]['total_amount'] =  $total_amount;
            $resp[$type]['total_charges'] = isset($rec->total_charges) ? $rec->total_charges : 0;
        } else {
            $resp[$type]['total_amount'] = $resp[$type]['total_charges'] = 0;
        }
        return $resp;
    }

    public function getClaimBilledTotalCharge() {
        
        $practice_timezone = Helpers::getPracticeTimeZone();       
        $start_date = date('Y-m-01',strtotime(Carbon::now($practice_timezone)));
        $end_date = date('Y-m-d',strtotime(Carbon::today($practice_timezone)));

        $chargeQry = ClaimInfoV1::whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    });

        $total_billed_charge = $chargeQry->sum('total_charge');           
        return $total_billed_charge;
        //return Helpers::priceFormat($total_billed_charge, 'yes');
        /*
            $unbilled_change =ClaimInfoV1::where('claim_submit_count', '<', 1)

                                ->whereIn('status', ['Ready','hold', 'pending'])
                                ->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->sum('total_charge');

            $billed_charge = ClaimInfoV1::whereNotIn('status', ['Hold'])
                            ->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')
                            ->sum('total_charge');
            $total_billed_charge = $billed_charge - $unbilled_change;
            return Helpers::priceFormat($total_billed_charge, 'yes');
       */
    }
    
     public function getClaimBilledTotalCharge_statsnew() {
        $resp = [];
        $chargeQry =  DB::table('claim_info_v1')->leftjoin('pmt_claim_fin_v1', 'pmt_claim_fin_v1.claim_id', '=', 'claim_info_v1.id')->select(DB::raw('sum(claim_info_v1.total_charge) as `total_amt`'),DB::raw('COUNT(claim_info_v1.id) as `total_charges`'))->whereRaw('MONTH(claim_info_v1.created_at) = MONTH(CURDATE()) AND YEAR(claim_info_v1.created_at) = YEAR(CURDATE())');
                    $chargeQry->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('claim_info_v1.claim_submit_count', '>' ,0)
                                ->where('claim_info_v1.insurance_id','!=',0);                         
                        })->orWhere('claim_info_v1.insurance_id', 0);
                    });

        $total_billed_charge = $chargeQry->select(DB::raw('sum(claim_info_v1.total_charge) as `total_amt`'),DB::raw('COUNT(claim_info_v1.id) as `total_charges`'))->first();
        $total_amount = $total_charges = 0;
        if(!empty($total_billed_charge)) {
            $total_amount = (!empty($total_billed_charge->total_amt) )? $total_billed_charge->total_amt : 0; ;
            $total_charges = $total_billed_charge->total_charges;
        }
        $resp['total_amount'] = $total_amount;
        $resp['total_charges'] = $total_charges;
        return $resp;        
    }


    public function getCurrentMonthInsPaidPercentage() {
        $last_month = PMTClaimTXV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')
                ->where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment','Refund'])
                ->where('deleted_at',Null)
                ->sum(DB::raw('total_paid'));    
        
        $current_month = PMTClaimTXV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')
                ->where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment','Refund'])
                ->where('deleted_at',Null)
                ->sum(DB::raw('total_paid'));
      
        $difference = $current_month - $last_month;        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;
    }

   public function getCurMonPatPercentage() {     
        
        $currentPatPmt = PMTInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')->where('pmt_method','Patient')->whereIn('pmt_type',['Payment','Credit Balance'])->where('void_check',Null)->where('deleted_at',Null)->sum(DB::raw('pmt_amt'));      
        $currentPatRefund = PMTInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')->where('pmt_method','Patient')->whereIn('pmt_type',['Refund'])->where('source','posting')->where('void_check',Null)->where('deleted_at',Null)->sum(DB::raw('pmt_amt'));

        $LastPatPmt = PMTInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')->where('pmt_method','Patient')->whereIn('pmt_type',['Payment','Credit Balance'])->where('void_check',Null)->where('deleted_at',Null)->sum(DB::raw('pmt_amt'));       
        $LastPatRefund = PMTInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')->where('pmt_method','Patient')->whereIn('pmt_type',['Refund'])->where('source','posting')->where('void_check',Null)->where('deleted_at',Null)->sum(DB::raw('pmt_amt'));

        $current_month = $currentPatPmt - $currentPatRefund;
        $last_month = $LastPatPmt - $LastPatRefund;
        
        $difference = $current_month - $last_month;        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;
    }


    public function getClaimTotalInsuranceAr() {    
        $unbilled = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', '=', 0)->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)')); 

        $insurance_ar = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count','!=' ,0)
        ->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
        return Helpers::priceFormat($insurance_ar+$unbilled, 'yes');
    }

    public function getClaimTotalPatintAr() {
       $patient_ar = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->where("claim_info_v1.insurance_id", "0")
                ->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
        return Helpers::priceFormat($patient_ar, 'yes');
    }

    public function getClaimTotalOutstandingAr($practice_id = '') {
        if($practice_id != ''){
            $practice_timezone = Helpers::getPracticeTimeZone($practice_id);  
        }else{
            $practice_timezone = Helpers::getPracticeTimeZone();
        }
        $cur_end_date = Carbon::now($practice_timezone)->toDateString();  
        $unbilled = ClaimInfoV1::whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'")->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', '=', 0);
	
		if(Auth::check() && Auth::user()->isProvider())
			$unbilled->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
		
		$unbilled = $unbilled->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
		
		
        $billed = ClaimInfoV1::whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'")->join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                        })->orWhere('claim_info_v1.insurance_id',0);
                    });
		if(Auth::check() && Auth::user()->isProvider())
			$billed->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id);
				
		$billed = $billed->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
		
        return $unbilled+$billed;
    }

    public function getCurrentMonthUnbilledPercentage() {       
        $last_month = ClaimInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')->where('insurance_id','!=',0)->where('claim_submit_count', 0)
                        ->sum('total_charge');                    
        $current_month = ClaimInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')
                        ->where('insurance_id','!=',0)->where('claim_submit_count', 0)
                        ->sum('total_charge');
        
        $difference = $current_month - $last_month;
        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;
    }

    public function getCurrentMonthEdiRejectionPercentage() {
       
        $last_month = ClaimInfoV1::whereIn('status', ['Rejection'])
                      ->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')
                      ->sum('total_charge');
        $current_month = ClaimInfoV1::whereIn('status', ['Rejection'])
                      ->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')
                      ->sum('total_charge');

        $difference = $current_month - $last_month;
        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;
    }

    public function getCurrentMonthBilledPercentage() {               
        $last_month = ClaimInfoV1::where(function($qry){
                        $qry->where(function($query){ 
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    })->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP() - INTERVAL 1 MONTH) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP() - INTERVAL 1 MONTH)')
                             ->sum('total_charge');      
       
        $current_month = ClaimInfoV1::where(function($qry){
                            $qry->where(function($query){ 
                                $query->whereIn('status', ['Ready'])->where('claim_submit_count', '>' ,0);
                            })->orWhereIn('status', ['Patient','Paid','Submitted','Denied','Rejection']);               
                        })->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')
                             ->sum('total_charge');                             
                
        $difference = $current_month - $last_month;
        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;              
    }

    public function getCurrentMonthHoldPercentage() {
       $last_month = ClaimInfoV1::select( DB::raw('sum(total_charge) as total_charge'))
                    ->whereRaw('MONTH(created_at) = ' . Carbon::today()->subMonths(1)->month . ' AND YEAR(created_at) = YEAR(CURDATE())')
                    ->where('status', 'Hold')->sum("total_charge");

       $current_month = ClaimInfoV1::select( DB::raw('sum(total_charge) as total_charge'))
                    ->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())') 
                    ->where('status', 'Hold')->sum("total_charge");
      
        $difference = $current_month - $last_month;        
        if($current_month == 0 && $last_month ==0)
            return 0;
        if($current_month == 0 && $last_month <> 0)
            return -100;
        if($current_month <> 0 && $last_month == 0)
            return 0;
        else
            return ($difference / $last_month)*100;  
    }
    
    // Dashboard and Stats details Ends
}