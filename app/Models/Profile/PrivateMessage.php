<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;
use Config;
class PrivateMessage extends Model
{
    protected $table = 'private_message';
	use SoftDeletes;
    protected $dates = ['deleted_at'];
	
	protected $fillable = ['message_id','subject','message_body','recipient_users_id','send_user_id','attachment_file','draft_message','created_at','updated_at','deleted_at'];
	public static $rules = [
	//'attachment_file'=>Config::get("siteconfigs.file_Uplode.defult_file_message");
	//"required"
	];
	public function user(){
			return $this->belongsTo('App\User','send_user_id','id');
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
