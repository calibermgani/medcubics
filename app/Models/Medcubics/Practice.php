<?php

namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use Config;

class Practice extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $connection = "responsive";
    protected $table = 'practices'; //protected $table = 'practices';

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

    /* public function __construct(){
      $this->table = Config::get('siteconfigs.connection_database').".practices";
      } */

    public function speciality_details() {
        return $this->belongsTo('App\Models\Medcubics\Speciality', 'speciality_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'created_by', 'id');
    }

    public function update_user() {
        return $this->belongsTo('App\User', 'updated_by', 'id');
    }

    public function taxanomy_details() {
        return $this->belongsTo('App\Models\Medcubics\Taxanomy', 'taxanomy_id', 'id');
    }

    public function languages_details() {
        return $this->belongsTo('App\Models\Medcubics\Language', 'language_id', 'id');
    }

    protected $fillable = [
        'bcbs_id', 'customer_id', 'practice_name', 'practice_description', 'phone', 'phoneext', 'fax', 'email', 'website', 'facebook', 'twitter', 'practice_link', 'doing_business_s', 'entity_type', 'billing_entity', 'tax_id', 'speciality_id', 'taxanomy_id', 'language_id', 'entity_type', 'tax_id', 'group_tax_id', 'npi', 'group_npi', 'medicare_ptan', 'medicaid', 'mail_add_1', 'mail_add_2', 'mail_city', 'mail_state', 'mail_zip5', 'mail_zip4', 'hostname', 'hostpassword', 'ipaddress', 'pay_add_1', 'pay_add_2', 'pay_city', 'pay_state', 'pay_zip5', 'pay_zip4', 'primary_add_1', 'primary_add_2', 'primary_city', 'primary_state', 'primary_zip5', 'primary_zip4', 'monday_forenoon', 'tuesday_forenoon', 'wednesday_forenoon', 'thursday_forenoon', 'friday_forenoon', 'saturday_forenoon', 'sunday_forenoon', 'monday_afternoon', 'tuesday_afternoon', 'wednesday_afternoon', 'thursday_afternoon', 'friday_afternoon', 'saturday_afternoon', 'sunday_afternoon', 'practice_db_id', 'status','timezone','backDate'
    ];
    public static $rules = [
        'practice_description' => 'required',
        'mail_add_1' => 'required|regex:/^[A-Za-z0-9\s]+$/i',
        'mail_city' => 'required|regex:/^[A-Za-z0-9\s]+$/i',
        'mail_state' => 'required|regex:/^[A-Za-z]+$/i',
        'mail_zip5' => 'required|numeric|min:5',
        'mail_zip4' => 'nullable|numeric',
        'pay_add_1' => 'required|regex:/^[A-Za-z0-9\s]+$/i',
        'pay_city' => 'required|regex:/^[A-Za-z0-9\s]+$/i',
        'pay_state' => 'required|regex:/^[A-Za-z]+$/i',
        'pay_zip5' => 'required|numeric|min:5',
        'pay_zip4' => 'nullable|numeric',
        'primary_add_1' => 'nullable|regex:/^[A-Za-z0-9\s]+$/i',
        'primary_city' => 'nullable|regex:/^[A-Za-z0-9\s]+$/i',
        'primary_state' => 'nullable|regex:/^[A-Za-z]+$/i',
        'primary_zip5' => 'nullable|numeric',
        'primary_zip4' => 'nullable|numeric',
        'practice_description' => 'required',
        'doing_business_s' => 'required|max:100',
        'speciality_id' => 'required|not_in:0',
        'taxanomy_id' => 'required|not_in:0',
        'billing_entity' => 'required',
        'entity_type' => 'required'
    ];

    public static function messages() {
        return [
            'practice_name.required' => Lang::get("admin/practice.validation.practice_name"),
            'practice_description.required' => Lang::get("admin/practice.validation.practice_description"),
            'doing_business_s.required' => Lang::get("admin/practice.validation.doingbusinessus"),
            'doing_business_s.max' => Lang::get("admin/practice.validation.doingbusinessus_limit"),
            'speciality_id.required' => Lang::get("admin/practice.validation.speciality"),
            'speciality_id.not_in' => Lang::get("admin/practice.validation.speciality"),
            'taxanomy_id.required' => Lang::get("admin/practice.validation.taxanomy"),
            'taxanomy_id.not_in' => Lang::get("admin/practice.validation.taxanomy"),
            'billing_entity.required' => Lang::get("admin/practice.validation.billing_entity"),
            'tax_id.required' => Lang::get("admin/practice.validation.taxid"),
            'tax_id.digits' => Lang::get("common.validation.numeric"),
            'npi.required' => Lang::get("common.validation.npi"),
            'npi.digits' => Lang::get("common.validation.numeric"),
            'group_tax_id.required' => Lang::get("admin/practice.validation.group_tax_id"),
            'group_tax_id.digits' => Lang::get("common.validation.numeric"),
            'group_npi.required' => Lang::get("admin/practice.validation.group_npi"),
            'group_npi.digits' => Lang::get("common.validation.numeric"),
            'medicare_ptan.required' => Lang::get("admin/practice.validation.medicare_ptan"),
            'medicare_ptan.digits' => Lang::get("common.validation.numeric"),
            'medicaid.required' => Lang::get("admin/practice.validation.medicaid"),
            'medicaid.digits' => Lang::get("common.validation.numeric"),
            'entity_type.required' => Lang::get("admin/practice.validation.entity_type"),
            'mail_add_1.required' => Lang::get("common.validation.address1_required"),
            'mail_add_1.regex' => Lang::get("common.validation.alphanumericspac"),
            'mail_city.required' => Lang::get("common.validation.city_required"),
            'mail_city.regex' => Lang::get("common.validation.alphanumericspac"),
            'mail_state.required' => Lang::get("common.validation.state_required"),
            'mail_state.regex' => Lang::get("common.validation.alpha"),
            'mail_zip5.required' => Lang::get("common.validation.zipcode5_required"),
            'mail_zip5.numeric' => Lang::get("common.validation.zipcode5_limit"),
            'mail_zip5.min' => Lang::get("common.validation.zipcode5_limit"),
            'pay_add_1.required' => Lang::get("common.validation.address1_required"),
            'pay_add_1.regex' => Lang::get("common.validation.alphanumericspac"),
            'pay_city.required' => Lang::get("common.validation.city_required"),
            'pay_city.regex' => Lang::get("common.validation.alphanumericspac"),
            'pay_state.required' => Lang::get("common.validation.state_required"),
            'pay_state.regex' => Lang::get("common.validation.alpha"),
            'pay_zip5.required' => Lang::get("common.validation.zipcode5_required"),
            'pay_zip5.numeric' => Lang::get("common.validation.zipcode5_limit"),
            'pay_zip5.min' => Lang::get("common.validation.zipcode5_limit"),
            'primary_add_1.regex' => Lang::get("common.validation.alphanumericspac"),
            'primary_add_2.regex' => Lang::get("common.validation.alphanumericspac"),
            'primary_city.regex' => Lang::get("common.validation.alphanumericspac"),
            'primary_state.regex' => Lang::get("common.validation.alpha"),
            'primary_zip5.numeric' => Lang::get("common.validation.zipcode5_limit"),
            'primary_zip5.min' => Lang::get("common.validation.zipcode5_limit"),
            'image.mimes' => Config::get('siteconfigs.customer_image.defult_image_message')
        ];
    }

    public static function getPracticeName() {
        $practice = Practice::first();
        return $practice->practice_name;
    }

    public static function getProviderCount() {
        return '33';
    }

    public static function getFacilityCount($practice_id) {
        return '10';
    }

    public static function getPatientrCount($practice_id) {
        return '1530';
    }

    public static function getVistiCount($practice_id) {
        return '1256';
    }

    public static function getClaimCount($practice_id) {
        return '896';
    }

    public static function getCollectionCount($practice_id) {
        return '$2,55,460';
    }

    public static function practiceID($id) {
        $practice_id = Practice::where('practice_name', $id)->first();
        return $practice_id;
    }

}
