<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IcdCategoryModel extends Model 
{

	use SoftDeletes;
    protected $dates = ['deleted_at'];
	protected $table = 'icdcategory';
	protected $fillable=['text_code','description','created_by','updated_by'];
	
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
		'text_code' 	=> 	'required'
	];
		
	public static $messages = [
		'text_code.required' 		=> 	'Text code required!',
		'description.required' 		=> 	'description required!'
	];
}