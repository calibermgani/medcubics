<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimCPTFINV1;

class ClaimCPTInfoV1 extends Model {

    use SoftDeletes;

    protected $table = "claim_cpt_info_v1";
    protected $fillable = ['patient_id', 'claim_id', 'dos_from', 'dos_to', 'cpt_code', 'modifier1', 'modifier2', 'modifier3',
        'modifier4', 'cpt_icd_code', 'cpt_icd_map_key', 'unit', 'charge', 'is_active', 'updated_by', 'created_by','created_at'];
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
    public function claimCptFinDetails() {
        return $this->hasOne('App\Models\Payments\PMTClaimCPTFINV1', 'claim_cpt_info_id');
    }

    public function claimCptTxDetails() {
        return $this->hasOne('App\Models\Payments\PMTClaimCPTTXV1', 'claim_cpt_info_id');
    }
    public function claimCptPatientTxDetails() {
        return $this->hasOne('App\Models\Payments\PMTClaimCPTTXV1', 'claim_cpt_info_id')->has('patient_payment_info');
    }

    public static function getAllBilledAmountByActiveLineItem() {
        $billed_amount_arr = ClaimCPTInfoV1::select(DB::raw("SUM(charge) as total_charge, claim_id"))->where('is_active', 1)
                        ->groupBy('claim_id')->pluck('total_charge', 'claim_id')->all();
        return $billed_amount_arr;
    }

    public function cptdetails() {
        return $this->belongsTo('App\Models\Cpt', 'cpt_code', 'cpt_hcpcs')->with('pro_category');
    }
        
    public function claim_details()   {
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id')->with('rend_providers','facility'); 
    }
    public function patient_details()   {
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id'); 
    }

     public function claimCptShadedDetails() {
        return $this->hasOne('App\Models\Payments\ClaimCPTShadedInfoV1', 'claim_cpt_info_v1_id');
    }

     public function claim_cptfin(){
        return $this->hasOne('App\Models\Payments\PMTClaimCPTFINV1','id', 'claim_cpt_info_id');
    }

    public function claim_cpttx() {         
        return $this->hasMany('App\Models\Payments\PMTClaimCPTTXV1','id', 'claim_cpt_info_id');
    }
    public function claimtxdescv1() {         
        return $this->hasMany('App\Models\Payments\ClaimTXDESCV1','claim_id', 'claim_id');
    }
    /*
    public function cpttransaction() {
        return $this->hasMany('App\Models\Patients\PaymentClaimCtpDetail', 'claimdoscptdetail_id', 'id')->orderBy('id', 'desc')->with('paymentdetail', 'insurancedetail');
    }

    public static function getClaimPaymentData($claim_id)   {
    	$value = Claimdoscptdetail::where('claim_id', $claim_id)->select(
    		DB::raw("SUM(co_ins) as coinsurance"),DB::raw("SUM(co_pay) as copay"),DB::raw("SUM(deductable) as deductable"),DB::raw("SUM(with_held) as withheld"))->first();
    	return $value;
    }
    
    public static function patientPaidData($claim_id)
    {
       // $claim_data = Claimdoscptdetail::where('claim_id', $claim_id)->where('cpt_code', 'Patient')->pluck('patient_paid');
        $claim_data = Claims::where('id', $claim_id)->pluck('patient_paid');
        return $claim_data;
    }

    public static function getBilledAmountByActiveLineItem($claim_id){
        $billed_amount = Claimdoscptdetail::where('claim_id', $claim_id)->select(
            DB::raw("SUM(charge) as total_charge"))->where('is_active',1)->first();
        return $billed_amount->total_charge;
    }
     */
}
