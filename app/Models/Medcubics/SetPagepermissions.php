<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class SetPagepermissions extends Model {
	
	public function roles()
	{		
		return $this->belongsTo('App\Models\Medcubics\Roles','role_id','id');
	}

	protected $fillable = ['role_id', 'page_permission_id'];
	
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
