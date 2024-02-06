<?php

namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Lang;

class Provider_degree extends Model
{
	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
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
	
	public function userupdate()
	{
		return $this->belongsTo('App\User','updated_by','id');
	}

	protected $fillable = [
						'degree_name'
						];
	public static $rules = [
						'degree_name' => 'required|unique:provider_degrees'
							];
	public static function messages() { 
			return 
					[
						'degree_name.required' 	=> Lang::get("admin/providerdegree.validation.providerdegree"),
						'degree_name.unique' 	=> Lang::get("admin/providerdegree.validation.providerdegree_unique"),
					];
	}
}




