<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

class Modifierstype extends Model 
{
	public function modifier()
	{
		return $this->hasMany('App\Models\Medcubics\Modifier','modifiers_type_id','id');
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
