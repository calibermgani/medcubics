<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document_categories extends Model
{
	use SoftDeletes;
	public function creator(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}
	
	protected $dates = ['deleted_at'];
	protected $fillable=['category_value','module_name'];

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
			'category_value' 	=> 'required'
	];

	public static $messages = [];
}