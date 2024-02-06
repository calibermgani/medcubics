<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Note extends Model {

	use SoftDeletes;
    protected $dates 	= ['deleted_at'];
	protected $fillable = ['title', 'content','notes_type','notes_type_id','created_by'];

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

	public static $rules = [
		'title' => 'required',
		'content' => 'required'
	];
	
	public static function messages(){
		return [
			'title.required'	=> Lang::get("common.validation.title"),
			'content.required' 	=> Lang::get("common.validation.content"),
		];
	}
}