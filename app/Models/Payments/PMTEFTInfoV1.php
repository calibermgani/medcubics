<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PMTEFTInfoV1 extends Model {

	use SoftDeletes;
	protected $table = 'pmt_eft_info_v1';
	protected  $fillable = ['eft_no','eft_date','updated_by','created_by'];

	public function pmt_details(){
		return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'id', 'pmt_mode_id')->selectRaw('SUM(pmt_amt) as pmt_amt, pmt_mode_id,SUM(amt_used) as amt_used')->where('pmt_mode','EFT')->groupBy('pmt_mode_id');
	}
	
	public static function boot() {
       parent::boot();
       // create a event to happen on saving
       static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value) && $name <> 'deleted_at' && $name <> 'eft_no') {
                    $table->{$name} = '';
                }
            }
            return true;
       });
    }
}
