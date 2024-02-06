<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdiEligibilityInsurance extends Model 
{
	protected $table = 'edi_eligibility_insurance';
	
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
    
	public function InsuranceSpPhysician() 
	{
        return $this->belongsTo('App\Models\EdiEligibilityInsuranceSpPhysician', 'edi_eligibility_insurance_id', 'id');
  }
	
	protected $fillable=['edi_eligibility_id','name	','payer_type','payer_type_label','insurance_id'];
}