<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class Ethnicity extends Model {
	protected $table = 'ethnicity';
	protected $fillable = array('name');

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
