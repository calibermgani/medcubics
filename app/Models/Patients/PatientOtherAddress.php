<?php

namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class PatientOtherAddress extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'patient_other_address';
    protected $fillable = ['patient_id', 'address1', 'address2', 'city', 'state', 'zip5', 'zip4', 'status'];
	
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
	
    public function user() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function claims() {
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id');
    }

    public static $rules = [
        'content' => 'required'
    ];


}
