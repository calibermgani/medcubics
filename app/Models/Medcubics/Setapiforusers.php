<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Setapiforusers extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at']; 
	
	protected $table = 'setapiforusers';
	
	protected $connection = 'responsive';	
    
	protected $fillable=['practice_id','user_id','api_id','created_by','updated_by'];

	public static $rules = [];
	
	public static $messages = [];	

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
