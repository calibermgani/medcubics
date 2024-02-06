<?php
namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class SearchFields extends Model
{
	protected $fillable = [ 'page_name', 'search_fields' ];
	protected $table = 'search_fields';
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