<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favouritecpts extends Model {	
	//use SoftDeletes;

	//protected $dates = ['deleted_at'];
	
	public $timestamps = true;

	protected $fillable=['user_id', 'cpt_id', 'created_at', 'updated_at', 'created_by', 'updated_by'];

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