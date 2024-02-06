<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class PrivateMessageDetails extends Model
{
    protected $table = 'private_message_details';
	use SoftDeletes;
    protected $dates = ['deleted_at'];
	
	protected $fillable = [ 
                    'message_id','parent_message_id','send_user_id','user_id','recipient_read','recipient_read_time','recipient_deleted','sender_deleted','sender_deleted_time','recipient_deleted_time','label_list_type','send_category_id','recipient_category_id','trash_category_id','label_category_id','created_at','updated_at','deleted_at','recipient_stared','sender_stared'      
    ];
	
	public function user(){
			return $this->belongsTo('App\User','send_user_id','id');
	}
	
	public function PrivateMessage(){
			return $this->belongsTo('App\Models\Profile\PrivateMessage','message_id','message_id');
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
