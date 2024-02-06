<?php

namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PatientInsuranceArchive extends Model
{
	protected $table = 'patient_insurance_archive';
	public $timestamps = false;
	
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
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id');
    }
	
	public function patient() 
	{
        return $this->belongsTo('App\Models\Patients\Patient', 'patient_id', 'id')->select('id', 'gender', 'address1', 'address2', 'city', 'state', 'zip5', 'zip4', 'phone');
    }
	protected $fillable = [ 
		'patient_insurence_id',
		'patient_id',
		'insurance_id',
		'medical_secondary_code',
		'category',
		'relationship',
		'last_name',
		'first_name',
		'middle_name',
		'insured_ssn',
		'insured_dob',
		'policy_id',
		'group_name',
		'effective_date',
		'termination_date',
		'adjustor_ph',
		'adjustor_fax',
		'accept_assignment',
		'release_of_information',
		'insurance_notes',
		'from',
		'to',
		'created_at',
		'created_by'	
	];
}