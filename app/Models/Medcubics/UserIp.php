<?php namespace App\Models\Medcubics;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Medcubics\Practice as Practice;
use Auth;
use Lang;
use Config;
class UserIp extends Model 
{
	protected $fillable=[
				'user_id','ip_address','approved','security_code','security_code_attempt', 'browser_name', 'device_name','login_info','first_login', 'created_at', 'updated_at'];
	protected $connection = 'responsive';	
	protected $table = 'user_ip';
	
	public function user()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','user_id','id')->with('customer');
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
	
