<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PMTADJInfoV1 extends Model {

    use SoftDeletes;
    protected $table = 'pmt_adj_info_v1';
    protected $fillable = ['adj_type', 'patient_id', 'insurance_id', 'adj_amount', 'adj_reason_id', 'reference', 'updated_by', 'created_by'];


    // Payment adjustment reason details     
    public function pmtadjustment_details(){
    	return $this->belongsTo('App\Models\AdjustmentReason','adj_reason_id', 'id');
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
