<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimCPTTXDESCV1 extends Model {

	use SoftDeletes;
    protected $table  ="claim_cpt_tx_desc_v1";

    protected $fillable =  ['claim_tx_desc_id','transaction_type','claim_id','claim_cpt_info_id','payment_id','txn_id','responsibility','pat_bal', 'ins_bal','value_1','value_2','updated_at','created_by','created_at'];

    public function claimcpt_info(){
    	return $this->belongsTo('App\Models\Payments\ClaimCptInfoV1','claim_cpt_info_id', 'id');
    }

    public function claimcpt_txn(){
    	return $this->belongsTo('App\Models\Payments\PMTClaimCPTTXV1','txn_id', 'id')->with('claimtxdetails');
    }

    public function pmtinfo() {
        return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'payment_id', 'id')->with("checkDetails",'eftDetails', 'creditCardDetails');
    }

    public function insurance_details(){
        return $this->belongsTo('App\Models\Insurance', 'responsibility', 'id' )->select('id','insurancetype_id','insurance_name','short_name','address_1','address_2','city','state','zipcode5','zipcode4','payerid','insurance_name');
    }
	
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
}