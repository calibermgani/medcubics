<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

class BlogUrl extends Model {

protected $table = "blog_url";

public $timestamps = false;

protected $fillable=array(
          'id','blog_id','image','title','description','datetime'
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
