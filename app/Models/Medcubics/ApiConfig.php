<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class ApiConfig extends Model {
	protected $table = 'practice_api_configs';
	
	protected $connection = 'responsive';

	public function created_by(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}

	public function updated_by(){
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}

	protected $fillable = array('api_for','api_name','api_username','api_password','category','usps_user_id','token','host','port','api_status', 'url');

	 // protected $fillable = array('api_for', 'api_name','category', 'usps_user_id', 'token', 'host', 'port','url');
	 
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
