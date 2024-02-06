<?php
namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class SearchUserData extends Model
{
	protected $fillable = [ 'user_id','search_fields_id','search_fields_data','more_field_data','created_at','updated_at','deleted_at' ];
	protected $table = 'search_user_data';
	public static $rules = [];
	
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