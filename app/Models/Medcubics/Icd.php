<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Lang;

class Icd extends Model {

	use SoftDeletes;
	protected $dates 	= ['deleted_at'];
	protected $table = 'icd_10';	
	protected $connection = "responsive";

	protected $fillable=[
		'long_description','medium_description','short_description','statement_description',
		'order','icdid','header','icd_code','icd_type','sex','age_limit_lower','age_limit_upper',
		'effectivedate','inactivedate','map_to_icd9','map_to_icd10'
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

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}
	public static $rules = [
		'medium_description' => 'required',
		'order' => 'nullable|max:5',
		'icdid' => 'nullable|max:15',
		'age_limit_lower' => 'nullable|max:2',
		'age_limit_upper' => 'nullable|max:3',
		'inactivedate' => 'nullable|after:effectivedate',
	];
	public static function messages(){
		return [
			'icd_code.required' 	=> Lang::get("admin/icd.validation.code"),
			'icd_code.unique' 		=> Lang::get("admin/icd.validation.unique"),
			'icd_code.max' 			=> Lang::get("admin/icd.validation.code_regex"),
			'medium_description.required'	=> Lang::get("admin/icd.validation.medium_des"),
			'order.max'				=> Lang::get("admin/icd.validation.order_regex"),
			'icdid.max' 			=> Lang::get("admin/icd.validation.id_regex"),
			'age_limit_lower.max' 	=> Lang::get("practice/practicemaster/icd.validation.lower_age_limit"),
			'age_limit_upper.max' 	=> Lang::get("practice/practicemaster/icd.validation.upper_age_limit"),
			'effectivedate.required'=> Lang::get("common.validation.eff_date_required"),
			'inactivedate.required' => Lang::get("common.validation.inactdate_required"),
			'inactivedate.after' 	=> Lang::get("common.validation.inactivedate"),
		];
	}
	public static $messages = [
		'short_description.max' => 'Short description should not exceed 48 characters!',
		'medium_description.max' => 'Medium description should not exceed 60 characters!',
		'order.max' => 'Enter proper Order!',
		'icdid.max' => 'Enter proper ID!',
		'icd_code.required' => 'Enter code!',
		'icd_code.unique' => 'The ICD code has already been taken!',
		'icd_code.max' => 'Enter proper code!',
		'age_limit_lower.max' => 'Enter proper age limit lower!',
		'age_limit_upper.max' => 'Enter proper age limit upper!',
		'effectivedate.required' => 'Select effective date!',
		'inactivedate.required' => 'Select inactive date!',
		'inactivedate.after' => 'Inactive date should be after effective date!',
	];
}
