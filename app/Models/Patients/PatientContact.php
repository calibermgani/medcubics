<?php

namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PatientContact extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'patient_contacts';

	protected $fillable = [ 
		 'patient_id', 'category', 'guarantor_last_name', 'guarantor_middle_name', 'guarantor_first_name', 'guarantor_relationship', 'guarantor_home_phone', 'guarantor_phone_ext', 'guarantor_cell_phone', 'guarantor_email', 'guarantor_address1', 'guarantor_address2', 'guarantor_city', 'guarantor_state', 'guarantor_zip5', 'guarantor_zip4', 'emergency_last_name', 'emergency_middle_name', 'emergency_first_name', 'emergency_relationship', 'emergency_home_phone', 'emergency_phone_ext', 'emergency_cell_phone', 'emergency_email', 'emergency_address1', 'emergency_address2', 'emergency_city', 'emergency_state', 'emergency_zip5', 'emergency_zip4', 'employer_status', 'employer_name', 'employer_work_phone', 'employer_phone_ext', 'employer_address1', 'employer_address2', 'employer_city', 'employer_state', 'employer_zip5', 'employer_zip4', 'attorney_adjuster_name', 'attorney_doi', 'attorney_claim_num', 'attorney_work_phone', 'attorney_phone_ext', 'attorney_fax', 'attorney_email', 'attorney_address1', 'attorney_address2', 'attorney_city', 'attorney_state', 'attorney_zip5', 'attorney_zip4','employer_organization_name','employer_occupation','employer_student_status','same_patient_address'
	];
	
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
	
}