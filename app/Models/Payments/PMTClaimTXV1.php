<?php

namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class PMTClaimTXV1 extends Model {

    use SoftDeletes;

    protected $table = "pmt_claim_tx_v1";
    protected $fillable = [
        'payment_id',
        'claim_id',
        'pmt_method',
        'pmt_type',
        'patient_id',
        'payer_insurance_id',
        'claim_insurance_id',
        'total_allowed',
        'total_deduction',
        'total_copay',
        'total_coins',
        'total_withheld',
        'total_writeoff',
        'total_paid',
        'posting_date',
        'ins_category',
        'created_by',
        'updated_by',
        'created_at'];
		
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

    public function claim_txn_desc() {
        return $this->hasOne('App\Models\Payments\ClaimTXDESCV1', 'txn_id', 'id');
    }

    public function claim_patient_det() {
        return $this->hasOne('App\Models\Patients\Patient', 'id', 'patient_id')->select('id', 'last_name', 'first_name', 'middle_name', 'account_no','title')->with('pmt_info');
    }

    public function payment_info() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->with('adjustment_reason', 'insurancedetail', 'created_user','pmtadjinfov1_details');
    }

    //baskar
    public function claim_txn_desc_v1() {
        return $this->hasOne('App\Models\Payments\ClaimTXDESCV1', 'txn_id', 'id')->whereRaw("transaction_type = 'Responsibility' and ins_bal < 0 and txn_id != 0");
    }

    public function pmt_info() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->with('insurancedetail', 'created_user','patient',"checkDetails",'eftDetails','creditCardDetails');
    }

    public function cpt_fin()
    {
        return $this->hasMany('App\Models\Payments\PMTClaimCPTTXV1', 'pmt_claim_tx_id', 'id')->with('claimcpt');
    }

    public function fin()
    {
        return $this->hasMany('App\Models\Payments\PMTClaimFINV1', 'claim_id', 'claim_id');
    }
    //-end

    public function insurance_detail() {
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id');
    }

    public function claim() {
		// switch practice page added condition for provider login based showing values
		// Revision 1 - Ref: MR-2719 22 Aug 2019: Selva
		if(Auth::check() && Auth::user()->isProvider())
			return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id')->where('claim_info_v1.rendering_provider_id',Auth::user()->provider_access_id)->with('patient', 'rendering_provider', 'facility_detail', 'billing_provider', 'insurance_details');
		else
			return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id')->with('patient', 'rendering_provider', 'facility_detail', 'billing_provider', 'insurance_details');
    }
	
    public function claims() {
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }
    
	public function claimcptinfoV1(){
		return $this->hasMany('App\Models\Payments\ClaimCPTInfoV1', 'claim_id', 'claim_id');
	}
	
	public function pmtclaimcpttxV1(){
		return $this->hasMany('App\Models\Payments\PMTClaimCPTTXV1', 'pmt_claim_tx_id', 'id')->with('dosDetails');
	}
	
    public function latest_payment() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->orderBy('id', 'desc');
    }

	public function latest_payment_check() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->orderBy('id', 'desc')->with('checkDetails');
    }		

}
