<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class EmailTemplate extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];	
	protected $fillable=['tamplate_for','subject','content','created_by','updated_by'];
		
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
		'subject' 		=> 'required',
		'content' 		=> 'required'			
	];
		
	public static function messages(){
		return [
			'subject.required' 		=> Lang::get("practice/practicemaster/reason.validation.subject"),
			'content.required' 		=> Lang::get("common.validation.content")
		];
	}
}