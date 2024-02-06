<?php

namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class ClinicalNotes extends Model {

    use SoftDeletes;

    protected $table = 'documents';
	
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

    public function rendering_provider() {
        return $this->belongsTo('App\Models\Provider', 'provider_id', 'id');
    }

    public function facility_detail() {
        return $this->belongsTo('App\Models\Facility', 'facility_id', 'id')->select('id', 'facility_name', 'facility_npi', 'pos_id', 'speciality_id', 'short_name')->with(array('facility_address' => function($query) {
                        $query->select('id', 'facilityid', 'address1', 'address2', 'city', 'state', 'pay_zip5', 'pay_zip4');
                    }, 'pos_details' => function($query) {
                        $query->select('id', 'code');
                    }, 'speciality_details'));
    }

    protected $fillable = ['practice_id', 'type_id', 'document_type', 'clinical_note', 'main_type_id', 'claim_id', 'facility_id', 'provider_id', 'dos', 'upload_type', 'document_path', 'document_extension', 'document_domain', 'title', 'description', 'document_categories_id', 'filename', 'filesize', 'user_email', 'mime', 'original_filename', 'created_by', 'updated_by'];

    public static $rules = [
        'title' => 'required',
        'facility_id' => 'required',
        'provider_id' => 'required',
        'dos' => 'required',
        'document_categories_id' => 'required',
            //'description' 	=> 'required'
    ];

    public static function messages() {
        return [
            'title.required' => Lang::get("common.validation.title"),
            'facility_id.required' => Lang::get("practice/patients/clinical_notes.validation.facility"),
            'provider_id.required' => Lang::get("practice/patients/clinical_notes.validation.provider"),
            'dos.required' => Lang::get("practice/patients/clinical_notes.validation.dos"),
            'filefield.mimes' => Lang::get("common.validation.upload_valid"),
            'filefield.required' => Lang::get("common.validation.upload"),
            'document_categories_id.required' => Lang::get("common.validation.category")
        ];
    }

    public function user() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function claim() {
        return $this->belongsTo('App\Models\Payments\ClaimInfoV1', 'claim_id', 'id');
    }

    public function category_type() {
        return $this->belongsTo('App\Models\Document_categories', 'document_categories_id', 'id');
    }

    //
}
