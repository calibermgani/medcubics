<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdiEligibility extends Model {
	protected $table = 'edi_eligibility';
	
	protected $fillable = ['patient_eligibility_id','edi_eligibility_id','edi_eligibility_created','patient_id', 'provider_id','provider_npi','insurance_id'];

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

	public function patient() 
	{
		return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id');
	}
	
	public function provider() 
	{
		return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}

	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}	

	public function contact_details() 
	{
        return $this->belongsTo('App\Models\EdiEligibilityDemo', 'contact_detail', 'id');
    }	   
}