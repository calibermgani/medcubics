<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Codecategory extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $fillable = ['codecategory'];

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
    
}