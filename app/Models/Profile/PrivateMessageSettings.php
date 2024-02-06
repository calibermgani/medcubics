<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App;

class PrivateMessageSettings extends Model
{
    protected $table = 'private_message_settings';
	use SoftDeletes;
    protected $dates = ['deleted_at'];
	
	protected $fillable = [ 
                    'user_id','signature','signature_content','created_at','updated_at','deleted_at'      
    ];
	
	public function PrivateMessageLabelList(){
			return $this->hasMany('App\Models\Profile\PrivateMessageLabelList','user_id','user_id');
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
