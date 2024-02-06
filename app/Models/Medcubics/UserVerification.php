<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Medcubics\Practice as Practice;
use Auth;
use Lang;
use Request;
use Config;
class UserVerification extends Model 
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

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

	protected $fillable=[
				'user_id','email_is_verified'];
	protected $connection = 'responsive';			
	protected $table = 'users_verification';			
									
}
	
