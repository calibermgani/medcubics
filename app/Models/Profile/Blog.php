<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model {
    
use SoftDeletes;

protected $table = "blog";

protected $dates = ['deleted_at'];

protected $fillable=array(
          'id','user_id', 'title','description','privacy','attachment','url','status','up_count','down_count'
       );
		

	
public static $rules = [
                        'title' => 'required',
                        'description' => 'required',
                        //'attachment' => 'mimes:jpeg,jpg,png,txt,doc,docx,pdf',
                        'privacy'   => 'required',
                        'url'        => 'nullable|url'
                        ];
public static $messages = [
                        'title.required' => 'Enter the title!',
                        'description.required' => 'Enter the description!',
                        'privacy.required'     => 'Select the privacy', 
                        ];

public function user(){
     return $this->belongsTo('App\User', 'user_id', 'id');
}

public function Blog_favourite(){
    return $this->belongsTo('App\Models\Profile\BlogFavourite', 'id', 'blog_id');
}

public function Blog_favcount() {
    return $this->hasMany('App\Models\Profile\BlogFavourite', 'blog_id', 'id');
}

public function Blog_commentscount() {
    return $this->hasMany('App\Models\Profile\BlogComments', 'blog_id', 'id');
}

public function Blog_vote() {
    return $this->hasMany('App\Models\Profile\BlogVote', 'blog_id', 'id');
}

public function Blog_group() {
    return $this->belongsTo('App\Models\Profile\BlogGroup', 'user_list', 'id');
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
