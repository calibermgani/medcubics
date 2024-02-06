<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;
use App\Http\Helpers\Helpers as Helpers;

class UserLoginHistory extends Model {
	use SoftDeletes;
	protected $connection = 'responsive';
	protected $table = 'user_login_histories';
	protected $dates = ['deleted_at'];
	public function modifier(){
		return $this->belongsTo('App\User', 'id', 'user_id');
	}
	public function user(){
		return $this->belongsTo('App\User', 'user_id', 'id');
	}
	protected $fillable = [	
		 'ip_address', 'logitude', 'latitude', 'browser_name', 'mac_address', 'login_time', 'logout_time', 'session_id', 'user_id', 'created_by', 'updated_by', 'created_at', 'updated_at'
	];
	public static $rules = [
	
	];
	public static $messages = [
	
	];
	public static function ClearLoginHistorySession()
	{
		$get_all_logout_qry =	UserLoginHistory::where("logout_time","")->get()->toArray();
		foreach($get_all_logout_qry as $qry_key => $qry_val)
		{
			$lifetim = Config::get('session.lifetime'); //lifetime
			$last_access_timestamp = strtotime("+".$lifetim." minutes", strtotime($qry_val['updated_at']));
			$current_timestamp = time();
			if($current_timestamp >=$last_access_timestamp)
			{
				UserLoginHistory::where("id",$qry_val['id'])->update(['logout_time' => date("Y-m-d h:i:s", $last_access_timestamp)]);
			}
		}
	}
	public static function LogoutTime($date)
	{
		$logout_time = trim($date);
		if($logout_time !='') 
			$response	=	Helpers::dateFormat($logout_time,'time');
		else 
			$response	=	"Current User";
		return $response;
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
