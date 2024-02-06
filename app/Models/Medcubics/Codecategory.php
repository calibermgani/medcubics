<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class Codecategory extends Model {

	protected $fillable=[
						'codecategory'
						];
						
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
