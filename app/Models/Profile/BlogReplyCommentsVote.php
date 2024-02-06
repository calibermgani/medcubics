<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

class BlogReplyCommentsVote extends Model {

protected $table = "blogreplycomments_vote";

public $timestamps = false;

protected $fillable=array(
          'id','user_id', 'parentcomment_id','comment_id','up','down','datetime'
       );
	   
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
