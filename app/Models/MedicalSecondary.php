<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class MedicalSecondary extends Model {
	use SoftDeletes;
	protected $table = 'medical_secondary';
	protected $fillable = ['code','description','created_by','updated_by'];
	
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

	public static $rules = [
		'code' 			=> 'required',
		'description' 	=> 'required'
	];
	
	public static function messages(){
		return [
			'code.required' 		=> Lang::get("common.validation.title"),
			'description.required' 	=> Lang::get("practice/patients/clinical_notes.validation.facility"),
		];
	}
}