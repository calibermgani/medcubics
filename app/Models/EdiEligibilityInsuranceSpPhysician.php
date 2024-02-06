<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdiEligibilityInsuranceSpPhysician extends Model 
{
	protected $table = 'edi_eligibility_insurance_sp_physicians';

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

	public function contact_details() 
	{
        return $this->belongsTo('App\Models\EdiEligibilityContact_detail', 'id', 'edi_eligibility_insurance_id');
  }
	
	protected $fillable=['edi_eligibility_insurance_id','insurance_type','insurance_type_label','eligibility_code','eligibility_code_label','primary_care','restricted'];
}