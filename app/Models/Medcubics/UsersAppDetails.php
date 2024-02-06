<?php 
namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class UsersAppDetails extends Model {

	protected $table = 'users_app_details';
	use SoftDeletes;
    protected $dates = ['deleted_at'];

	protected $fillable=[
			'user_id','mobile_id','authentication_id','last_login_time'
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
