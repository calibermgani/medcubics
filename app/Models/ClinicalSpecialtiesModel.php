<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
use Config;

class ClinicalSpecialtiesModel extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'clinicalspecialties';

	protected $fillable=['specialty_IT_lexical_code','description','created_by','updated_by'];
	
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
			'specialty_IT_lexical_code' 	=> 'required'
	];
		
	public static $messages = [
			'specialty_IT_lexical_code.required' 	=> 'specialty IT lexical code required!',
			'description.required' 			     	=> 'description required!'
	];
}