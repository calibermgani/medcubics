<?php namespace App\Models\Payments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PMTCheckInfoV1 extends Model {
    use SoftDeletes;
    protected $table  ="pmt_check_info_v1";
    protected $fillable =  ['check_no','check_date','bank_name','created_by'];
   
    public static function getCheckNo($check_name, $id){
      return (PMTCheckInfoV1::where('id', $id)->pluck('check_no')->first());
    }
	
	public function pmt_details(){
		return $this->belongsTo('App\Models\Payments\PMTInfoV1', 'id', 'pmt_mode_id')->selectRaw('SUM(pmt_amt) as pmt_amt, pmt_mode_id,SUM(amt_used) as amt_used')->where('pmt_mode','Check')->groupBy('pmt_mode_id');
	}
	
	public static function boot() {
       parent::boot();
       // create a event to happen on saving
       static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value) && $name <> 'deleted_at' && $name <> 'check_no') {
                    $table->{$name} = '';
                }
            }
            return true;
       });
    }
}
