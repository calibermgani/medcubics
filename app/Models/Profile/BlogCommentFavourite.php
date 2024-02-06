<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCommentFavourite extends Model {

protected $table = "blog_comments_favourite";

public $timestamps = false;

protected $fillable=array(
          'id','blog_id', 'user_id','comment_id','datetime'
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
