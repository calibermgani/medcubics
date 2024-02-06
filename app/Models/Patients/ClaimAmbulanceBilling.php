<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ClaimAmbulanceBilling extends Model {

	protected $fillable = [
		'is_emergency',
		'patient_weight',
		'tr_distance',
		'tr_code',
		'tr_reason_code',
		'drop_location',
		'drop_addr1',
		'drop_addr2',
		'drop_city',
		'drop_state',
		'drop_zip4',
		'drop_zip5',
		'pick_addr1',
		'pick_addr2',
		'pick_city',
		'pick_state',
		'pick_zip4',
		'pick_zip5',
		'strecher_purpose',
		'ambulance_cert',
		'medical_note',
		'round_trip',
		'business_note',
		'created_by',
		'updated_by',
		'patient_id'
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