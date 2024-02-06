<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PMTClaimFINV1 extends Model {
    use SoftDeletes;
    protected  $table = "pmt_claim_fin_v1";
    protected $fillable = [
        'claim_id',
        'patient_id',
        'total_charge',
        'total_allowed',
        'patient_paid',
        'insurance_paid',
        'patient_due',
        'insurance_due',
        'patient_adj',
        'insurance_adj',
        'withheld',
        'created_by',
        'created_at',
        'updated_by'
    ];

    public function claimdetails(){
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1','claim_id', 'id');
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
