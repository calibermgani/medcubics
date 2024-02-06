<?php

namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Provider_type extends Model
{
	
	use SoftDeletes;

	protected $dates = ['deleted_at'];

	public static function get_provider_types_name($provider_types_id)
	{
		return Provider_type::where('id',$provider_types_id)->pluck('name')->first();
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
