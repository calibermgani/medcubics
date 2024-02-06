<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Lang;
class PatientEligibility extends Model {

	protected $table = 'patient_eligibility';
	
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
	
	public function patient_insurance() 
	{
		return $this->belongsTo('App\Models\Patients\PatientInsurance', 'patient_insurance_id', 'id');
	}
	
	public function insurance_details() 
	{		
		return $this->belongsto('App\Models\Insurance', 'patient_insurance_id', 'id');
	}

	public function patient() 
	{
		return $this->belongsTo('App\Models\Patients\Patient', 'patients_id', 'id');
	}

	public function facility() 
	{
		return $this->belongsTo('App\Models\Facility', 'facility_id', 'id');
	}

	public function provider() 
	{
		return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
	}

	public function template() 
	{
		return $this->belongsTo('App\Models\Template', 'template_id', 'id');
	}

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}       

	public function get_eligibilityinfo()
	{
		return $this->belongsTo('App\Models\EdiEligibility','id','patient_eligibility_id');
	}       	
		
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}

	public static $rules = [			
		'dos_from' => 'required',
		'dos_to' => 'required',
		'patient_insurance_id' => 'required',
		'provider_id' => 'required',
		'facility_id' => 'required',
		'content' => 'required',		
	];	

	public static function messages(){
		return [
			'dos_from.required' 		=> Lang::get("practice/patients/correspondence.validation.dos_from_req"),
			'dos_to.required' 			=> Lang::get("practice/patients/correspondence.validation.dos_to_req"),
			'patient_insurance_id.required' => Lang::get("practice/patients/eligibility.validation.patient_insurance_id"),
			'provider_id.required' 		=> Lang::get("practice/patients/eligibility.validation.provider_required"),
			'facility_id.required' 		=> Lang::get("practice/patients/eligibility.validation.facility_required"),
			'content.required' 			=> Lang::get("practice/patients/eligibility.validation.template_id"),
		];
	}
	protected $fillable = [	
		'patients_id',
		'content',
		'dos_from',
		'dos_to',
		'patient_insurance_id',
		'provider_id',
		'facility_id',
		'filename',
		'file_path',
		'template_id'
	];
}