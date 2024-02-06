<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model {
	protected $table 	= 'practice_registration';
	protected $fillable	=	[
		'email_id', 'driving_license', 'ethnicity', 'race', 'preferred_language', 'marital_status', 'student_status', 'primary_care_provider', 'primary_facility', 'send_email_notification', 'auto_phone_call_reminder', 'preferred_communication', 'insured_ssn', 'insured_dob', 'group_name_id', 'adjustor_ph', 'adjustor_fax', 'guarantor', 'emergency_contact', 'employer', 'attorney', 'requested_date', 'contact_person', 'alert_on_appointment', 'allowed_visit','pos', 'work_phone', 'alert_on_billing', 'total_allowed_amount', 'amount_used', 'amount_remaining','documents','notes', 'created_by', 'updated_by'
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