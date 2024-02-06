<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class MultiFeeschedule extends Model {

	use SoftDeletes;

	protected $dates = ['deleted_at'];
	protected $table = 'multi_fee_schedule';

	protected $fillable=array(
	'fee_schedule_id', 'year','insurance_id','cpt_id','billed_amount','allowed_amount','modifier_id','status','created_by','updated_by'
	);
	
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
	
	public function cptInfo(){
		return $this->belongsTo('App\Models\Cpt','cpt_id','id')->select('id','cpt_hcpcs');
	}
	
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	
	public function insuranceInfo()
	{
		return $this->belongsTo('App\Models\Insurance','insurance_id','id')->select('id','short_name');
	}
	
	public function feeSchedule(){
		return $this->belongsTo('App\Models\Feeschedule','fee_schedule_id','id');
	}	
}