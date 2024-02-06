<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Useractivity extends Model {

	protected $table = 'useractivity';

	protected $dates = ['deleted_at'];

	protected $connection = 'responsive';	

	public $timestamps = false;

	protected $fillable = ['userid','action','usertype','user_activity_msg','activity_date'];

	public function user() {
	    return $this->belongsTo('App\User','userid','id');
	}
	
	public function practice() {
	    return $this->belongsTo('App\Models\Practice','main_directory','id');
	}
	
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
	
