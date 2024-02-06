<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimCPTOthersAdjustmentInfoV1 extends Model {
    
    use SoftDeletes;
    protected  $table = "claim_cpt_others_adjustment_info_v1";
    protected $fillable = [
		'claim_id',
		'claim_cpt_id',
		'adjustment_id',
		'adjustment_amt',
        'claim_cpt_tx_id'
	];

	public function adjustment_details()
    {
        return $this->belongsTo('App\Models\AdjustmentReason', 'adjustment_id', 'id'); 
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
