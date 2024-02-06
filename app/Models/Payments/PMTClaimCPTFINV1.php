<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PMTClaimCPTFINV1 extends Model {
    use SoftDeletes;
    protected  $table = "pmt_claim_cpt_fin_v1";
    protected $fillable = [
							'claim_id',
							'claim_cpt_info_id',
							'cpt_charge',
							'cpt_allowed_amt',
							'paid_amt',
							'co_ins',
							'co_pay',
							'deductable',
							'with_held',
							'adjustment',
							'patient_balance',
							'insurance_balance',
							'patient_paid',
							'created_at',
							'insurance_paid'];
							
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

	public function claim_cpt()
    {
        return $this->hasOne('App\Models\Payments\ClaimCPTInfoV1', 'id');
    }

    public function claim() {
        // ->with('rendering_provider', 'facility_detail', 'billing_provider', 'insurance_details')
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id')->with('patient');
    }

    public function recClaimTxn() {
        //return $this->hasOne('App\Models\Payments\PMTClaimTXV1', 'id', 'last_txn_id')->where('pmt_method', 'Insurance')->latest()->with('pmtclaimcpttxV1'); 
        return $this->hasOne('App\Models\Payments\PMTClaimTXV1', 'claim_id', 'claim_id')->where('pmt_method', 'Insurance')->latest()
        	->with('pmtclaimcpttxV1'); 
        //->latest()->limit(1)->with('payment_info');
    }


    public function recClaimTxDesc() {
    	return $this->hasOne('App\Models\Payments\ClaimTXDESCV1', 'claim_id', 'claim_id')->where('pmt_method', 'Insurance')->latest()
        	->with('pmtclaimcpttxV1'); 
    }

    public function lastcptdenialdesc() {
    	return $this->hasOne('App\Models\Payments\ClaimCPTTXDESCV1', 'claim_cpt_info_id', 'claim_cpt_info_id')->whereIn('transaction_type', ['Denials','Insurance Payment'])->latest()->with(['claimcpt_txn','pmtinfo']); 
    }

    public function lastInsTxn() {
        return $this->hasOne('App\Models\Payments\PMTClaimCPTTXV1', 'pmt_claim_tx_id', 'last_txn_id'); 
        //->latest()->limit(1)->with('payment_info');
    }

    public function recentCptTxn() {
        return $this->hasOne('App\Models\Payments\PMTClaimCPTTXV1', 'claim_cpt_info_id', 'claim_cpt_info_id')->latest()->limit(1)->with('payment_info');
    }

     public function lastWorkbench() {
        return $this->hasOne('App\Models\Patients\ProblemList', 'claim_id', 'claim_id')->latest();
    }

    public function claimcpt() {
        return $this->belongsTo('App\Models\Payments\ClaimCPTInfoV1', 'claim_cpt_info_id', 'id');
    }

    public static function getClaimCptLastInsuranceDenials($txn_id, $cpt_id){
    	$result = PMTClaimCPTTXV1::with('payment_info')->where('pmt_claim_tx_id', $txn_id)->where('claim_cpt_info_id', $cpt_id)->first();
    	$denails = [];
    	if(!empty($result)) {
    		$denails['denial_code'] = $result['denial_code'];

    		if(isset($result->payment_info)) {
				if($result->payment_info->pmt_mode == 'EFT') {
					$denial_date = @$result->payment_info->eftDetails->eft_date;
				} elseif($result->payment_info->pmt_mode == 'Credit') {	
					$denial_date = @$result->payment_info->creditCardDetails->expiry_date ;
				}	else  {	
					$denial_date = @$result->payment_info->checkDetails->check_date ;
				}
			}
    		$denails['denial_date'] = $denial_date;
    	} 
    	return $denails;
    }


}
