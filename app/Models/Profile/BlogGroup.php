<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogGroup extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];
protected $table = "blog_group";

public $timestamps = false;
	public function user()
	{
		return $this->hasMany('App\Models\Medcubics\Users','name','id');
	} 

protected $fillable=array(
			  'id','group_name', 'group_users','datetime','created_by', 'updated_by' , 'status'
       );
	public static $rules = [
		  'group_users' => 'required',
		  'group_name' => 'required',
		  'status' =>'required'
			];
	public static $messages = [
			'group_users.required' => 'Select the group users!',
			'group_name.required' => 'Enter the group name!',
			'status.required' => 'Select the status!'
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
