<?php namespace App\Models\Profile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BlogReplyComments extends Model 
{
	use SoftDeletes;    
	protected $table = "blog_comments_reply";
	protected $dates = ['deleted_at'];
	public $timestamps = false;
	protected $fillable=array(
			  'id','user_id', 'blog_id','comment_id','comments','up_count','down_count','created_at'
		   );
		
	public function user()
	{
		 return $this->belongsTo('App\User', 'user_id', 'id');
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
