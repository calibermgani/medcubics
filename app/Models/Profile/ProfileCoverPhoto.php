<?php namespace App\Models\Profile;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileCoverPhoto extends Model {
    
use SoftDeletes;

protected $table = "profile_coverphoto";

protected $dates = ['deleted_at'];

protected $fillable=array(
				'id','userid', 'coverphoto','created_at','updated_at'
						);
	
public function user(){
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
