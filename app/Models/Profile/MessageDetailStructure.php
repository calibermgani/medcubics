<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;
use Config;
class MessageDetailStructure extends Model
{
    protected $table = 'message_detail_structure';
	use SoftDeletes;
   
	
	protected $fillable = [ 
                    'message_id','sender_id','receiver_id','draft_message','attachment_file','parent_message_id'];
	
	public function message_detail(){ 
		return $this->hasOne('App\Models\Profile\PrivateMessage','id','message_id'); 
	}
	public function sender_details(){ 
		return $this->hasOne('App\Models\Medcubics\Users','id','sender_id'); 
	}
	public function receiver_details(){ 
		return $this->hasOne('App\Models\Medcubics\Users','id','receiver_id'); 
	}
	public function label_detail(){ 
		return $this->hasOne('App\Models\Profile\PrivateMessageLabelList','id','label_id'); 
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
