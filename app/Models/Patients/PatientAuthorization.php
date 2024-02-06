<?php

namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientAuthorization extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'patient_authorizations';
	public function insurance_details() 
	{
        return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id');
    }

	public function pos_detail() 
	{
        return $this->belongsTo('App\Models\Pos', 'pos_id', 'id');
    }

    public function provider_details() 
	{
        return $this->belongsTo('App\Models\Provider', 'provider_id', 'id')->select('id', 'provider_name');
    }

	protected $fillable = [ 
		 'patient_id', 
		 'authorization_no', 
		 'provider_id', 
		 'requested_date',
		 'authorization_contact_person', 
		 'alert_appointment', 
		 'allowed_visit', 
		 'insurance_id',
		 'pos_id',
		 'start_date',
		 'end_date',
		 'authorization_phone',
		 'authorization_phone_ext',
		 'alert_billing', 
		 'allowed_amt', 
		 'amt_used',
		 'amt_remaining',
         'authorization_notes',
		 'document_save_id',
		 'created_by',
		 'updated_by'
	];
	
	public static function getalertonAuthorization($patient_id, $insurance_id)
	{
		$auth_count = PatientAuthorization::where('patient_id', $patient_id)->where('insurance_id', $insurance_id)->count();
		return $auth_count;
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
}