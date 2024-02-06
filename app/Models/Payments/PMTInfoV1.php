<?php

namespace App\Models\Payments;

use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Http\Controllers\Payments\Api\PaymentV1ApiController;
use App\Http\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Payments\ClaimCPTInfoV1;
use DB;
use Auth;
use Carbon\Carbon;
use Response;
use App\Traits\CommonUtil;
use Request;
class PMTInfoV1 extends Model
{
    use SoftDeletes;
    use CommonUtil;

    protected $table = "pmt_info_v1";

    public static function boot(){
        parent::boot();
        static::saving(function($table){
            $request = Request::all();
            // $table->pmt_info_id = !empty($request['pmt_info_id'])?$request['pmt_info_id']:'';
            $table->reference = !empty($request['reference'])?$request['reference']:'';
        });
    }

    protected $fillable = ['pmt_no', 'pmt_type', 'patient_id', 'insurance_id', 'pmt_amt', 'amt_used', 'balance', 'source', 'source_id', 'pmt_method', 'pmt_mode', 'pmt_mode_id', 'reference', 'void_check', 'updated_at', 'created_by'];
    
    public function paymentsable()
    {
        return $this->morphTo();
    }

    public function created_user()
    {
        return $this->belongsTo('App\Models\Medcubics\Users', 'created_by', 'id')->select('id', 'name', 'short_name');
    }

    function checkDetails()
    {
        return $this->belongsTo("App\Models\Payments\PMTCheckInfoV1", "pmt_mode_id");
    }
	public function payment_adj_info() {
        return $this->belongsTo('App\Models\Payments\PMTADJInfoV1', 'pmt_mode_id', 'id')->with('pmtadjustment_details');
    }
	function pmtNotes()
    {
        return $this->belongsTo("App\Models\Payments\PMTUnpostedNotesV1", "id", 'pmt_id');
    }


    public function patient_payment_info() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->where('source', '=','charge');
    }

    function creditCardDetails()
    {
        return $this->belongsTo("App\Models\Payments\PMTCardInfoV1", "pmt_mode_id");
    }

    function eftDetails()
    {
        return $this->belongsTo("App\Models\Payments\PMTEFTInfoV1", "pmt_mode_id");
    }

    public function attachment_detail()
    {
        return $this->hasOne('App\Models\Document', 'payment_id', 'id');
    }

    public function insurancedetail()
    {
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id')->select('id', 'insurance_name', 'short_name');
    }
    
    public function claims()
    {
        return $this->hasMany('App\Models\Payments\PMTClaimTXV1', 'payment_id', 'id');
    }

    public function claim_providers() {
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'patient_id', 'patient_id');
    }
    
    // Added this function fetch payment for all claims.
    public static function getAllpaymentClaimDetails($type = null)
    {
        if ($type != "patient") {
            $paymentDetails = PMTClaimTXV1::where('payment_id', '!=', 0)->where('pmt_type', 'Insurance')->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count', 'claim_id')->all();
        } else {
            $paymentDetails = PMTClaimTXV1::where('payment_id', '!=', 0)->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count', 'claim_id')->all();
        }
        return $paymentDetails;
    }

    public function payment_claim_detail()
    {
        return $this->hasMany('App\Models\Payments\PMTClaimTXV1', 'payment_id', 'id')->with('claim');
        //return $this->hasMany('App\Models\Patients\PaymentClaimDetail', 'payment_id', 'id')->with('claim');
    }

    public function payment_claim_cpt_detail()
    {
        return $this->hasMany('App\Models\Payments\PMTClaimCPTTXV1', 'payment_id', 'id')->with('claim', 'claimcpt');
    }

    public function providerdetail()
    {
        return $this->belongsTo('App\Models\Provider', 'billing_provider_id', 'id')->with('degrees');
    }

    public function claim()
    {
		// switch practice page added condition for provider login based showing values
		// Revision 1 - Ref: MR-2719 22 Aug 2019: Selva
        // Revision 2 - Auth check included for handle null.
		if(Auth::check() && Auth::user()->isProvider())
			return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'source_id', 'id')->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id)->with('patient', 'rendering_provider', 'facility_detail', 'billing_provider', 'insurance_details');
		else
			return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'source_id', 'id')->with('patient', 'rendering_provider', 'facility_detail', 'billing_provider', 'insurance_details');;
    }
        
    public function patient()
    {
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id');
    }

    public function adjustment_reason()
    {
        return $this->belongsTo('App\Models\AdjustmentReason', 'pmt_mode_id', 'id'); //->where('pmt_type', 'Adjustment');
    }
     public function pmtadjinfov1_details()
    {
        return $this->belongsTo('App\Models\Payments\PMTADJInfoV1', 'pmt_mode_id', 'id')->with('pmtadjustment_details'); //->where('pmt_type', 'Adjustment');
    }

    /* Reference from payment model
      public function payment_claim_cpt_data()
      {
      return $this->hasMany('App\Models\Patients\PaymentClaimCtpDetail', 'payment_id', 'id');
      }

      public function appointment()
      {
      return $this->belongsTo('App\Models\Scheduler\PatientAppointment', 'type_id', 'id')->where('type','scheduler');
      }

      public function paymentclaimdetail()
      {
      //return $this->hasOne('App\Models\Patients\Claims', 'type_id', 'id')->where('type','charge');
      return $this->hasOne('App\Models\Payments\ClaimInfoV1','type_id', 'id')->where('type','charge');
      }
     */

    public static function getPaymentInfo($claim_id)
    {
        $payment_return_value = PMTInfoV1::where('source', 'charge')
            ->where('source_id', $claim_id)->first();

        if (!empty($payment_return_value)) {
            $payment_return_value = $payment_return_value->toArray();
            $payment_return_value['copay_amt'] = $payment_return_value['pmt_amt'];
            //dd($payment_return_value);
            $type = $payment_return_value['pmt_mode'];
            $typeId = $payment_return_value['pmt_mode_id'];
            $paymentModeData = array();
            if (!empty($type)) {
                if ($type == "Check") {
                    $temp = PMTCheckInfoV1::where('id', $typeId)->first();
                    $paymentModeData['check_no'] = $temp['check_no'];
                    $paymentModeData['check_date'] = $temp['check_date'];
                    $paymentModeData['bank_name'] = $temp['bank_name'];
                } else if ($type == "Credit") {
                    $resultSetCardInfo = PMTCardInfoV1::where('id', $typeId)->first();
                    if(!empty($resultSetCardInfo)) {
                        $resultSetCardInfo['card_no'] = $resultSetCardInfo['card_first_4'] . '' . $resultSetCardInfo['card_center'] . '' . $resultSetCardInfo['card_last_4'];
                        $resultSetCardInfo['check_date'] = $resultSetCardInfo['expiry_date'];
                        $paymentModeData = $resultSetCardInfo->toArray();
                    }else{
                        $resultSetCardInfo['id'] = '0';
                        $resultSetCardInfo['card_no'] = '';
                        $resultSetCardInfo['check_date'] = '';
                        $paymentModeData = $resultSetCardInfo;
                    }
                } else if ($type == "Credit Balance") {
                    $resultAdjInfo = PMTADJInfoV1::where('id', $typeId)->first();
                    // To handle to array on nulll issue
                    $paymentModeData = ($resultAdjInfo) ? $resultAdjInfo->toArray() : [];
                } else if ($type == "EFT") {
                    $resultEftInfo = PMTEFTInfoV1::where('id', $typeId)->first();
                    $paymentModeData = ($resultEftInfo) ? $resultEftInfo->toArray() : [];
                } else if ($type == "Cash") {
                    $paymentModeData = $payment_return_value;
                } else if ($type == "Credit Balance") {
                    return 0;
                } else if ($type == "Money Order") {
                    $temp = PMTCheckInfoV1::where('id', $typeId)->first(); 
                    $checkNum = explode('-', $temp['check_no']);
                    $paymentModeData['check_no'] = isset($checkNum[1]) ? $checkNum[1] : '';
                    $paymentModeData['check_date'] = $temp['check_date'];
                    $paymentModeData['bank_name'] = $temp['bank_name'];
                } 
            }
            $paymentModeData = array_merge($payment_return_value, $paymentModeData);
            //  dd($paymentModeData);
            return (object)$paymentModeData;
        } else {
            return [];
        }
    }

    public static function getPaymentInfoById($claim_id, $pmt_id = 0)
    {
        $payment_return_value = PMTInfoV1::where('source_id', $claim_id);
        if ($pmt_id > 0)
            $payment_return_value->where('id', $pmt_id);
        $payment_return_value = $payment_return_value->first();

        if ($payment_return_value) {
            $payment_return_value = $payment_return_value->toArray();
            $payment_return_value['copay_amt'] = $payment_return_value['pmt_amt'];
            //dd($payment_return_value);
            $type = $payment_return_value['pmt_mode'];
            $typeId = $payment_return_value['pmt_mode_id'];
            $pmt_type = $payment_return_value['pmt_type'];
            $paymentModeData = array();
            if ($pmt_type == 'Adjustment') {
				$resultAdjInfo = PMTADJInfoV1::with('pmtadjustment_details')->where('id', $typeId)->first();
                if(!empty($resultAdjInfo)) {
                    $resultAdjInfo = $resultAdjInfo->toArray();
                } else {
                    $resultAdjInfo = [];
                }                
                $paymentModeData = $resultAdjInfo;
            } elseif ($pmt_type == 'Refund') {
                if (!empty($type) && $type == 'Check') {
                    $temp = PMTCheckInfoV1::where('id', $typeId)->first();
                    $paymentModeData['check_no'] = $temp['check_no'];
                    $paymentModeData['check_date'] = $temp['check_date'];
                    $paymentModeData['bank_name'] = $temp['bank_name'];
                }
            } elseif ($pmt_type == 'Payment') {
                if (!empty($type)) {
                    switch ($type) {
                        case "Check":
                            $temp = PMTCheckInfoV1::where('id', $typeId)->first();
                            $paymentModeData['check_no'] = $temp['check_no'];
                            $paymentModeData['check_date'] = $temp['check_date'];
                            $paymentModeData['bank_name'] = $temp['bank_name'];
                            break;

                        case "Cash":
                            // 
                            break;

                        case "Credit":
                            $resultSetCardInfo = PMTCardInfoV1::where('id', $typeId)->first();
                            $paymentModeData = ($resultSetCardInfo) ? $resultSetCardInfo->toArray() : [];
                            break;

                        case "EFT":
                            $resultEftInfo = PMTEFTInfoV1::where('id', $typeId)->first();
                            $paymentModeData = (!empty($resultEftInfo)) ? $resultEftInfo->toArray() : [];
                            break;

                        case "Credit Balance":
                            $resultAdjInfo = PMTADJInfoV1::where('id', $typeId)->first();
                            $paymentModeData = (!empty($resultAdjInfo)) ? $resultAdjInfo->toArray() : [];  
                            break;

                        case "Money Order":
                            $temp = PMTCheckInfoV1::where('id', $typeId)->first();
                            $checkNum = explode('-', $temp['check_no']);
                            $paymentModeData['check_no'] = isset($checkNum[1]) ? $checkNum[1] : '';
                            $paymentModeData['check_date'] = $temp['check_date'];
                            $paymentModeData['bank_name'] = $temp['bank_name'];
                            break;

                        default:
                            //
                            break;
                    }
                }
            }

            $paymentModeData = array_merge($payment_return_value, $paymentModeData);
            return (object)$paymentModeData;
        }
    }

    public static function getPaymentInfoDetailsById($pmt_id = 0)
    {
        if ($pmt_id > 0) {
            $payment_return_value = PMTInfoV1::with('patient')->select('*', DB::raw('pmt_amt - amt_used as balance'))->where('id', $pmt_id)->first();
        }

        if (!empty($payment_return_value)) {
            $payment_return_value = $payment_return_value->toArray();
            //    dd($payment_return_value);
            $type = $payment_return_value['pmt_mode'];
            $typeId = $payment_return_value['pmt_mode_id'];
            $pmt_type = $payment_return_value['pmt_type'];
            $paymentModeData = array();
            if ($pmt_type == 'Adjustment') {
                $resultAdjInfo = PMTADJInfoV1::with('pmtadjustment_details')->where('id', $typeId)->first();
                if(!empty($resultAdjInfo)) {
                    $resultAdjInfo = $resultAdjInfo->toArray();
                } else {
                    $resultAdjInfo = [];
                }                
                $paymentModeData = $resultAdjInfo;
            } elseif (!empty($pmt_type)) {
                if (!empty($type)) {
                    switch ($type) {
                        case "Check":
                            $temp = PMTCheckInfoV1::where('id', $typeId)->first();
                            $paymentModeData['check_no'] = $temp['check_no'];
                            $paymentModeData['check_date'] = $temp['check_date'];
                            break;

                        case "Cash":
                            $paymentModeData = $payment_return_value;
                            break;

                        case "Credit":
                            $resultSetCardInfo = PMTCardInfoV1::where('id', $typeId)->first();
                            if(!empty($resultSetCardInfo)) {
                                $resultSetCardInfo['card_no'] = $resultSetCardInfo['card_first_4'] . '' . $resultSetCardInfo['card_center'] . '' . $resultSetCardInfo['card_last_4'];
                                $resultSetCardInfo['check_date'] = $resultSetCardInfo['expiry_date'];
                                $paymentModeData = $resultSetCardInfo->toArray();
                            }else{
                                $resultSetCardInfo['id'] = '0';
                                $resultSetCardInfo['card_no'] = '';
                                $resultSetCardInfo['check_date'] = '';
                                $paymentModeData = $resultSetCardInfo;
                            }
                            unset($paymentModeData['id']);
                            break;

                        case "EFT":
                            $resultEftInfo = PMTEFTInfoV1::where('id', $typeId)->first();
                            if(!empty($resultEftInfo)) {
                                $resultEftInfo = $resultEftInfo->toArray();
                            }
                            $paymentModeData['check_no'] = $resultEftInfo['eft_no'];
                            $paymentModeData['check_date'] = $resultEftInfo['eft_date'];
                            break;

                        case "Credit Balance":
                            $resultAdjInfo = PMTADJInfoV1::where('id', $typeId)->first();
                            // To handle to array on nulll issue
                            $paymentModeData = (!empty($resultAdjInfo)) ? $resultAdjInfo->toArray() : [];
                             unset($paymentModeData['id']);
                            break;
                        case "Money Order":
                            $temp = PMTCheckInfoV1::where('id', $typeId)->first();
                            $checkNum = explode('-', $temp['check_no']);
                            $paymentModeData['check_no'] = isset($checkNum[1]) ? $checkNum[1] : '';
                            $paymentModeData['check_date'] = $temp['check_date'];
                            $paymentModeData['bank_name'] = $temp['bank_name'];
                            break;

                        default:
                            //
                            break;
                    }
                }
            }
            $paymentModeData = array_merge($payment_return_value, $paymentModeData);
            return (object)$paymentModeData;
        }
        return [];
    }

    public function updatePaymentInfoDetailsById($datas, $pmt_mode, $pmtModeId, $payment_id)
    {
        $type = $pmt_mode;
        $typeId = $pmtModeId;
        $temp = $temp1 = [];
        $pmt_type = 'Payment';
        if ($pmt_type == 'Adjustment') {
            $resultAdjInfo = PMTADJInfoV1::with('pmtadjustment_details')->where('id', $typeId);
            $paymentModeData = $resultAdjInfo;
        } elseif ($pmt_type == 'Payment') {
            if (!empty($type)) {
                $payment = PMTInfoV1::where('id', $payment_id);
                switch ($type) {
                    case "Check":
                        $temp1['pmt_amt'] = $datas['pmt_amt'];
                        $payment->update($temp1);
                        $cheData = PMTCheckInfoV1::where('id', $typeId);
                        $temp['check_no'] = $datas['check_no'];
                        $temp['check_date'] = $datas['check_date'];
                        $cheData->update($temp);
                        break;

                    case "Cash":
                        $temp1['pmt_amt'] = $datas['pmt_amt'];
                        $payment->update($temp1);
                        break;

                    case "Credit":
                        $temp1['pmt_amt'] = $datas['pmt_amt'];
                        $payment->update($temp1);

                        $resultSetCardInfo = PMTCardInfoV1::where('id', $typeId);
                        if(isset($datas['card_no']) || isset($datas['check_no'])) {
                            $card_no = isset($datas['card_no']) ? $datas['card_no'] : $datas['check_no'];
                            $card_no = ($card_no != '' && strlen($card_no) < 12 ) ? str_pad($card_no, 12, "##", STR_PAD_LEFT): $card_no;
                            $card_first = ($card_no != '') ? str_replace("#","",substr($card_no,0, 4)) : '';
                            $card_middle = ($card_no != '') ? str_replace("#","",substr($card_no, 4,4)) : '';
                            $card_last = ($card_no != '') ? str_replace("#","",substr($card_no, -4,4)) : '';
                            $temp['card_first_4'] = $card_first;
                            $temp['card_center'] = $card_middle;
                            $temp['card_last_4'] = $card_last;
                        }

                        if(isset($datas['card_type']) && $datas['card_type'] != '')
                            $temp['card_type'] = $datas['card_type'];
                        
                        if(isset($datas['name_on_card']) && $datas['name_on_card'] != '')    
                            $temp['name_on_card'] = $datas['name_on_card'];

                        if((isset($datas['cardexpiry_date'])) || (isset($datas['check_date']))) {
                            $cardExpiryDate = isset($datas['cardexpiry_date']) ? $this->dateformater($datas['cardexpiry_date']) : $this->dateformater($datas['check_date']);
                            $temp['expiry_date'] = $cardExpiryDate;
                        }
                        // Update only if update fields available.
                        if(!empty($temp))
                            $resultSetCardInfo->update($temp);
                        break;

                    case "EFT":
                        $temp1['pmt_amt'] = $datas['pmt_amt'];
                        $payment->update($temp1);

                        $resultEftInfo = PMTEFTInfoV1::where('id', $typeId);
                        $temp['eft_no'] = $datas['check_no'];
                        if (!empty($datas['check_date']))
                            $temp['eft_date'] = $datas['check_date'];
                        $resultEftInfo->update($temp);
                        //$paymentModeData = $resultEftInfo;
                        break;

                    case "Credit Balance":
                        $temp1['pmt_amt'] = $datas['pmt_amt'];
                        $payment->update($temp1);
                        // $resultAdjInfo = PMTADJInfoV1::where('id', $typeId)->first()->toArray();
                        //$paymentModeData = $resultAdjInfo;
                        break;

                    case "Money Order":
                        $temp1['pmt_amt'] = $datas['pmt_amt'];
                        $payment->update($temp1);

                        $temp = PMTCheckInfoV1::where('id', $typeId)->first();
                        $checkNum = explode('-', $temp['check_no']);
                        $paymentModeData['check_no'] = isset($checkNum[1]) ? $checkNum[1] : '';
                        $paymentModeData['check_date'] = $temp['check_date'];
                        $paymentModeData['bank_name'] = $temp['bank_name'];
                        break;                                       

                    default:
                        //
                        break;
                }
            }
        }
    }

    public static function checkpaymentDone($claim_id, $type = null)
    {
        $type = trim(strtolower($type));
        // Insurance / patient / Patient /         
        $pmt_count = 0;
        switch ($type) {
            case 'any':
                return PMTClaimTXV1::where('claim_id', $claim_id)
                    ->where('payment_id', '!=', 0)
                    ->whereIn('pmt_method', ['Insurance', 'Patient'])->count();
                break;

            case 'insurance':
                return PMTClaimTXV1::where('claim_id', $claim_id)
                    ->where('payment_id', '!=', 0)
                    ->where('pmt_method', 'Insurance')->count();
                break;

            case 'patient':
                return PMTClaimTXV1::where('claim_id', $claim_id)
                    ->where('payment_id', '!=', 0)
                    ->where('pmt_method', 'Patient')->count();
                break;


            default:
                return PMTClaimTXV1::where('claim_id', $claim_id)
                    ->where('payer_insurance_id', '!=', '')
                    ->Where('pmt_method', '!=', '')
                    ->Where('pmt_method', '!=', '0')->count();
                break;
        }
        return 0;
        /*
        if (!empty($type) && $type != "Patient") {
            return PMTClaimTXV1::where('claim_id', $claim_id)
                            ->where('payment_id', '!=', 0)
                            ->where('pmt_method', 'Insurance')->count();
        }
        if (!empty($type) && $type == "Patient") {            
            return PMTClaimTXV1::where('claim_id', $claim_id)
                            ->where('payment_id', '!=', 0)
                            ->where('pmt_method', 'Patient')->count();
        } else {
            return PMTClaimTXV1::where('claim_id', $claim_id)
                            ->where('payer_insurance_id', '!=', '')
                            ->Where('pmt_method', '!=', '')
                            ->Where('pmt_method', '!=', '0')->count();
        }
        */
    }

    public static function CheckInsuranceAdjusted()
    {
        /*
          $current_month = Carbon::today()->month;
          $previous_month = Carbon::now()->subMonth()->month;
          $adjusted_sum_current_month = PMTClaimTXV1::whereHas('latest_payment',function($query){
          return $query->where('pmt_type', 'Payment')->where('pmt_method', 'Insurance') ;
          })->whereMonth('created_at', '=', $current_month)->sum('total_adjusted');

          $adjusted_sum_last_month = PMTClaimTXV1::whereHas('latest_payment',function($query){
          return $query->where('payment_type', 'Payment')->where('payment_method', 'Insurance') ;
          })->whereMonth('created_at', '=', $previous_month)->sum('total_adjusted');

          $adjust_percentage = $adjusted_sum_current_month+$adjusted_sum_last_month;
          $adjusted_sum =  ($adjust_percentage != 0)?($adjusted_sum_current_month/$adjust_percentage)*100 : 0;
          $adjustment_data['adjustment_percent'] = round($adjusted_sum);
          $adjustment_data['adjustment_sum'] = $adjusted_sum_current_month;
         */
        $adjustment_data['adjustment_percent'] = $adjustment_data['adjustment_sum'] = 0;
        return $adjustment_data;
    }

    public static function CheckTotalRefund()
    {
        $adjustment_data['refund_percent'] = $adjustment_data['refund_sum'] = 0;
        $current_month = Carbon::today()->month;
        $previous_month = Carbon::now()->subMonth()->month;

        $get_refund_value_current_month = PMTInfoV1::where('void_check', NULL)->whereRaw('(pmt_type = "Refund")')->whereMonth('created_at', '=', $current_month)->sum('amt_used');
        $get_refund_value_wallet_current = PMTInfoV1::where('void_check', NULL)->where('source', "refundwallet")->whereMonth('created_at', '=', $current_month)->sum('pmt_amt');

        $get_refund_value_last_month = PMTInfoV1::where('void_check', NULL)->whereRaw('(pmt_type = "Refund")')->whereMonth('created_at', '=', $previous_month)->sum('amt_used');
        $get_refund_value_wallet_last = PMTInfoV1::where('void_check', NULL)->where('source', "refundwallet")->whereMonth('created_at', '=', $previous_month)->sum('pmt_amt');

        $current_refund = $get_refund_value_current_month + $get_refund_value_wallet_current;
        $last_refund = $get_refund_value_last_month + $get_refund_value_wallet_last;
        $redfund_percentage = $current_refund + $last_refund;
        $get_refund_value = ($redfund_percentage != 0) ? ($current_refund / $redfund_percentage) * 100 : 0;
        $adjustment_data['refund_percent'] = round($get_refund_value);
        $adjustment_data['refund_sum'] = $current_refund;

        return $adjustment_data;
    }

    public static function getPaymentDadetailData($payment_id)
    {
        $data = PMTInfoV1::select('*', DB::raw('pmt_amt - amt_used as balance'))->where('id', $payment_id)->first();
        return $data;
    }

    public static function updatePaymettAmoutUsed($payment_id, $amount)
    {
        if (!empty($payment_id)) {
            $resultSet = PMTInfoV1::where('id', $payment_id)->update(['amt_used' => DB::raw('amt_used +' . $amount . '')]);
            return $resultSet;
        }
    }

    public static function getAllpaymentClaimDetailsByPatient($type = null, $patient_id)
    {
        if ($type != "patient") {
            $paymentDetails = PMTClaimTXV1::where('payment_id', '!=', 0)->where('pmt_type', 'Insurance')->where('patient_id', $patient_id)->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count', 'claim_id')->all();
        } else {
            $paymentDetails = PMTClaimTXV1::where('payment_id', '!=', 0)->where('patient_id', $patient_id)->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count', 'claim_id')->all();
        }
        return $paymentDetails;
    }

    public static function findCheckExistsOrNot($checkNo, $paymentMethod, $paymentMode, $paymentType, $patientId = 0)
    {
        $checkCount = 0;
        try {         
            if ($paymentType == 'patientPayment' || $paymentType == 'patientRefund') {
                
                if ($paymentMode == "EFT") {
                    $checkCount = PMTInfoV1::with('eftDetails')
                        ->whereHas('eftDetails', function ($q) use ($checkNo) {
                            $q->where('eft_no', '=', $checkNo);
                        })->where('pmt_method', '=', $paymentMethod)
                        ->where('pmt_mode', '=', $paymentMode)
                        ->whereNull('void_check')
                        ->count();
                }if ($paymentMode == "MO") {                    
                     $checkCount = PMTInfoV1::with('checkDetails')
                        ->whereHas('checkDetails', function ($q) use ($checkNo) {
                            $q->where('check_no', '=', "MO-".$checkNo);
                        })
                        ->where('pmt_method', '=', $paymentMethod)
                        ->where('patient_id', '=', $patientId)                        
                        ->whereNull('void_check')
                        ->count();
                }else {
                    $checkCount = PMTInfoV1::with('checkDetails')
                        ->whereHas('checkDetails', function ($q) use ($checkNo) {
                            $q->where('check_no', '=', $checkNo);
                        })
                        ->where('pmt_method', '=', $paymentMethod)
                        ->where('patient_id', '=', $patientId)
                        ->where('pmt_mode', '=', $paymentMode)
                        ->whereNull('void_check')
                        ->count();
                }
            } else if ($paymentType == 'insurancePayment' || $paymentType =='insuranceRefund') {
               
                if ($paymentMode == "EFT") {
                    $checkCount = PMTInfoV1::with('eftDetails')
                        ->whereHas('eftDetails', function ($q) use ($checkNo) {
                            $q->where('eft_no', '=', $checkNo);
                        })->where('pmt_method', '=', $paymentMethod)
                        ->where('pmt_mode', '=', $paymentMode)
                        ->whereNull('void_check')
                        ->count();
                }else if ($paymentMode == "Credit") {                  
                         $checkCount = PMTInfoV1::with('creditCardDetails')
                        ->whereHas('creditCardDetails', function ($q) use ($checkNo) {                          
                            $q->whereRaw('CONCAT(card_first_4,card_center,card_last_4) = ? ',[$checkNo]);
                        })->where('pmt_method', '=', $paymentMethod)
                        ->where('pmt_mode', '=', $paymentMode)
                        ->whereNull('void_check')
                        ->count();
                } else {
                    $checkCount = PMTInfoV1::with('checkDetails')
                        ->whereHas('checkDetails', function ($q) use ($checkNo) {
                            $q->where('check_no', '=', $checkNo);
                        })->where('pmt_method', '=', $paymentMethod)
                        ->where('pmt_mode', '=', $paymentMode)
                        ->whereNull('void_check')
                        ->count();
                }

            }
            return ($checkCount > 0) ? true : false;
        } catch (Exception $e) {
            $respMsg = "findCheckExistsOrNot | Error Msg: " . $e->getMessage() . ". | Occured on Line# " . $e->getLine();
            $respMsg .= "Trace |" . $e->getTraceAsString();
            \Log::info(' Exception Occurred: >>' . $respMsg);
        }
    }

    public static function CheckDateInfo($check_date) {
        return ($check_date != '0000-00-00') ? Helpers::dateFormat($check_date, 'date') : '';
    }

    public static function getClaimTxAmtById($txn_id = 0) {
        $txn_amt =0;
        if($txn_id > 0){
            $txn_amt = PMTClaimTXV1::where('id', $txn_id)->select('total_paid')->pluck('total_paid')->first();
        }
        return $txn_amt;
    }
    
    public static function getClaimCptTxAmtById($txn_id = 0) {
        $txn_amt =0;
        if($txn_id > 0){
            $txn_amt = PMTClaimCPTTXV1::where('id', $txn_id)->select('paid')->pluck('paid')->first();
        }
        return $txn_amt;
    }
    /* Reference from payment model

      // Get the patient balance amount starts here
      public static function getPateintWalletCredit($patient_id)
      {
      if(!is_numeric($patient_id))
      $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
      $wallet_balance = Payment::where('patient_id', $patient_id)->where('payment_method', 'Patient')->where('payment_type', 'Payment')->whereNotIn('type', ['refundwallet'])->where('void_check', NULL)->sum('balance');
      if($wallet_balance > 0){
      return $wallet_balance;
      } else {
      return $wallet_balance;
      }
      }

      public static function getBalance($patient_id, $type)
      {
      if(!is_numeric($patient_id))
      $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
      $due = Claims::where('patient_id', $patient_id)->sum($type);
      return $due;
      }

      public static function getInsuranceOverPayment($patient_id)
      {
      $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
      $wallet_balance = Payment::where('patient_id', $patient_id)->where('payment_method', 'Patient')->sum('balance');
      }

      public static function getPateintInsuranceRefund($patient_id)
      {
      $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
      }

      public static function getPateintPaytientRefund($patient_id)
      {
      $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
      }

      public static function getPateintUnbilledAmount($patient_id)
      {
      if(!is_numeric($patient_id))
      $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id,'decode');
      $unbilled = Claims::where('patient_id', $patient_id)->whereIn('status', ['Patient', 'Ready'])->sum('total_charge');
      return $unbilled;
      }
      // Get the patient balance amount ends here

      // Payment Id generation starts here
      public static function generatepaymentid($payment_id)
      {
      return time();
      }

      //Payment Id generation function ends here
      public static function getPaymentInfo($claim_id) {
      $payment_return_value = Payment::where('type', 'charge')->where('type_id', $claim_id)->first();
      return $payment_return_value;
      }

      // Added this function fetch payment for all claims.
      public static function getAllpaymentClaimDetails($type= null) {
      if($type != "patient")  {
      $paymentDetails = PaymentClaimDetail::where('payment_id', '!=', 0)->where('payment_type', 'Insurance')->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count','claim_id')->all();
      } else {
      $paymentDetails = PaymentClaimDetail::where('payment_id', '!=', 0)->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count','claim_id')->all();
      }
      return $paymentDetails;
      }

      public static function getAllpaymentClaimDetailsByPatient($type= null, $patient_id) {
      if($type != "patient")  {
      $paymentDetails = PaymentClaimDetail::where('payment_id', '!=', 0)->where('payment_type', 'Insurance')->where('patient_id', $patient_id)->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count','claim_id')->all();
      } else {
      $paymentDetails = PaymentClaimDetail::where('payment_id', '!=', 0)->where('patient_id', $patient_id)->select(DB::raw('COUNT(id) AS total_count, claim_id'))->groupBy('claim_id')->pluck('total_count','claim_id')->all();
      }
      return $paymentDetails;
      }

      public static function checkpaymentDone($claim_id, $type= null)
      {
      if(!empty($type) && $type != "patient") {
      return PaymentClaimDetail::where('claim_id', $claim_id)->where('payment_id', '!=', 0)->where('payment_type', 'Insurance')->count();
      }
      if(!empty($type) && $type == "patient") {
      return PaymentClaimDetail::where('claim_id', $claim_id)->where('payment_id', '!=', 0)->count();
      } else {
      return PaymentClaimDetail::where('claim_id', $claim_id)->where('insurance_id', '!=', '')->Where('payment_type', '!=', '')->Where('payment_type', '!=', '0')->count();
      }
      }

      //Save Eob attachment  starts here.
      public static function updatePaymentId($eob_id, $payment_id)
      {
      Document::where('id', '=', $eob_id)->update(['type_id' => $payment_id,'main_type_id' => $payment_id]);
      }
      //Save Eob attachment ends here.

      // Check date correction starts here
      public static function CheckDateInfo($check_date)
      {
      return  ($check_date != '0000-00-00')?Helpers::dateFormat($check_date,'date'):'';
      }

      public static function getInsurancePaidAmount($insurance_id, $claimdos_id, $type, $payment_id = null)
      {
      $sum_data = PaymentClaimCtpDetail::where('payer_insurance_id', $insurance_id)
      ->where('claimdoscptdetail_id', $claimdos_id)->where('posting_type', 'Insurance');
      $sum_value = $sum_data->sum($type);
      $sum_value_paid = $sum_value;

      if(!is_null($payment_id) && !empty($payment_id) && $sum_value !=0) {  // Sum value != 0 checked because if refund done it shows the paid amount
      $payment_id = Helpers::getEncodeAndDecodeOfId($payment_id,'decode');
      $sum_value = $sum_data->where('payment_id', $payment_id)->sum($type);
      if($sum_value_paid<$sum_value) {
      $sum_value = $sum_value_paid;  // Some time we may refund the amount so, we can't able to allow theem to make the full takeback amount
      }
      }
      return $sum_value;
      }

      public static function CheckInsurancePatientPaid($id)
      {
      $id = Helpers::getEncodeAndDecodeOfId($id,'decode'); // decode id
      $payment_count = PaymentClaimDetail::where(function($query){
      return $query->where('payment_type', 'Insurance')->where('insurance_paid_amt', '>', 0)
      ->orwhere('payment_type', 'Patient')->where('patient_paid_amt', '>', 0);
      })->where('payment_id', $id)->count();
      return $payment_count;
      }
     */

    // Save all the check or amount related information here starts here
}