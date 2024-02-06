<?php

namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class PatientNote extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'patient_notes';
    protected $fillable = ['content', 'follow_up_content', 'notes_type', 'claim_id', 'patient_notes_type', 'notes_type_id', 'created_by', 'updated_by', 'user_id', 'title','status','source_id'];
	
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

    public static function messages() {
        return [
            'patient_notes_type.required' => Lang::get("practice/patients/patients_notes.validation.type"),
            'content.required' => Lang::get("common.validation.content"),
            'patient_notes_type.chk_notes_type_exists' => Lang::get("practice/patients/patients_notes.validation.type_exist")
        ];
    }

}
