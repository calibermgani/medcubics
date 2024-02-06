<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Practicedocument extends Model {

	use SoftDeletes;

	protected $dates = ['deleted_at'];

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
    
	protected $fillable=[
		'title','category', 'description'
	];

	public static $rules = [
		'title' => 'required',
		'category' => 'required',
		'description' => 'required',
		'filefield' => 'required|mimes:pdf,jpg,png,gif,jpeg,doc',
	];
	
	public static $messages = [
		'title.required' => 'Enter title!',
		'category.required' => 'Select category!',
		'description.required' => 'Enter description!',
		'filefield.required' => 'Attachment missing!',
		'filefield.required' => 'Choose any file!',
		'filefield.mimes' => 'Select valid file!',
	];
}