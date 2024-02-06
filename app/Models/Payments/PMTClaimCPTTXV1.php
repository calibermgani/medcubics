<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Helpers;

class PMTClaimCPTTXV1 extends Model {

    use SoftDeletes;

    protected $table = "pmt_claim_cpt_tx_v1";
    protected $fillable = ['payment_id',
        'claim_id', 'pmt_claim_tx_id', 'claim_cpt_info_id', 'allowed',
        'deduction', 'copay', 'coins', 'withheld', 'writeoff', 'paid',
        'denial_code', 'created_by', 'updated_by','created_at'];
		
		
	public static function boot() {
       parent::boot();
       // create a event to happen on saving
       static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value) && $name <> 'deleted_at') {
                    $table->{$name} = '';
                }
            }
            return true;
       });
    }

    public function dosDetails () {
        return $this->belongsTo('App\Models\Payments\ClaimCPTInfoV1', 'claim_cpt_info_id', 'id');
    }

    public static function getInsurancePaidAmount($insurance_id, $claimdos_id, $type ,$payment_id) {
        $payment_id = Helpers\Helpers::getEncodeAndDecodeOfId($payment_id,'decode');
        $sum_data = PMTClaimCPTTXV1::whereHas('payment_info', function($query) use ($insurance_id){
                    $query->where('pmt_method', 'Insurance')->where('insurance_id', $insurance_id);
                    })
                    ->where('claim_cpt_info_id', $claimdos_id)
                    ->where(function ($query) use ($payment_id){
                        if(!empty($payment_id)){
                            $query->where('payment_id', $payment_id);
                        }
                    })
                    ->sum($type);

        /*if(!is_null($payment_id) && !empty($payment_id) && $sum_data !=0) {  // Sum value != 0 checked because if refund done it shows the paid amount

            $sum_value = $sum_data->where('payment_id', $payment_id)->sum($type);
            if($sum_value_paid<$sum_value) {
                $sum_value = $sum_value_paid;  // Some time we may refund the amount so, we can't able to allow theem to make the full takeback amount
            }
        }*/
        return $sum_data;
    }

    public function payment_info() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->with("checkDetails",'eftDetails', 'creditCardDetails');
    }

    public function lastWorkbench() {
        return $this->hasOne('App\Models\Patients\ProblemList', 'claim_id', 'claim_id')->latest();
    }

    public function other_adj() {
        return $this->hasMany('App\Models\Payments\ClaimCPTOthersAdjustmentInfoV1', 'claim_cpt_tx_id','id')->with('adjustment_details');
    }

    public function patient_payment_info() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->where('source', '=','charge');
    }

    public static function getInsuranceAdjesment($insurance_id, $claimdos_id, $type) {
        $sum_data = PMTClaimCPTTXV1::whereHas('payment_info', function($query) use ($insurance_id) {
            $query->where('pmt_method', 'Insurance')
                ->where('insurance_id', $insurance_id);
        })->where('claim_cpt_info_id', $claimdos_id)->sum($type);
        return $sum_data;
    }

    public function claim() {
        // ->with('rendering_provider', 'facility_detail', 'billing_provider', 'insurance_details')
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id')->with('patient');
    }

    public function claimcpt() {
        return $this->belongsTo('App\Models\Payments\ClaimCPTInfoV1', 'claim_cpt_info_id', 'id');
    }

    public function cptfin() {
        return $this->hasOne('App\Models\Payments\PMTClaimCPTFINV1','id', 'claim_cpt_info_id');
    }

    public function claimtxdetails() {
        return $this->belongsTo('App\Models\Payments\PMTClaimTXV1', 'pmt_claim_tx_id', 'id');
    }

    public static function  getLastCptAllowedAmountFromClaimTx($claim_id){
        $resultData = PMTClaimTXV1::select('id','total_allowed')->where('claim_id',$claim_id)->orderBy('id','desc')->first();
        $latestAllowedAmount = (!empty($resultData))? $resultData ['total_allowed']:'0.00';
        return $latestAllowedAmount;
    }
}