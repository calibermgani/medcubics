<?php 
namespace App\Models\Patients;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SuperbillExistingCpt extends Model {

	protected $table = 'superbill_existing_cpt';
	use SoftDeletes;
    protected $dates = ['deleted_at'];

	public function patient(){
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id');
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
	
	protected $fillable=[
			'patient_id','cpt_ids'
	];
	
}
