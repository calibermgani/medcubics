<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientEligibilityWaystar extends Model
{
	protected $fillable = [ 'patient_id','insurance_id','content','created_by','updated_by' ];
	protected $table = 'patient_eligibility_waystar';
	
	public static $rules = [];

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
	
	public function insurance_details() 
	{		
		return $this->belongsto('App\Models\Insurance', 'insurance_id', 'id');
	}
}