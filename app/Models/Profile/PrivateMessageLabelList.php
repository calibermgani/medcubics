<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class PrivateMessageLabelList extends Model
{
    protected $table = 'private_message_label_list';
	use SoftDeletes;
    protected $dates = ['deleted_at'];
	
	protected $fillable = [ 
                    'user_id','label_name','label_image','label_color','label_id','created_at','updated_at','deleted_at'      
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
