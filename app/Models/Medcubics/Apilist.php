<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiList extends Model {
	use SoftDeletes;
	protected $table = 'api_list';
	protected $dates = ['deleted_at'];
	
	public function created_by(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}
	
	public function updated_by(){
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}
	
	protected $fillable=['api_name','status','created_by','updated_by'];
	
	
	public static $rules = [
			'status' 	=> 'required'
	];
		
	public static $messages = [
			'api_name.required' 			=> 'API required!',
			'api_name.unique' 				=> 'API Already exit!',
			'status.required' 				=> 'Status required!'
	];
	
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