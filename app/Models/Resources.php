<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resources extends Model {

	use SoftDeletes;

	protected $dates = ['deleted_at'];

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

	public function facility()
	{
		return $this->belongsTo('App\Models\Facility','resource_location_id','id');		
	}

	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','default_provider_id','id');		
	}

	protected $fillable=[
							'resource_name','resource_location_id','resource_code','phone_number','default_provider_id'
	        			];

	public static $rules = [						
							'resource_name' => 'required',
							'resource_location_id' => 'required',
							'resource_code' => 'required',
							'phone_number' => 'required',
							'default_provider_id' => 'required',
							];
							
	public static $messages = [
							'resource_name.required' => 'Enter resource name!',
							'resource_location_id.required' => 'Select resource location!',
							'resource_code.required' => 'Enter recource code',
							'phone_number.required' => 'Enter phone number!',	
							'default_provider_id.required' => 'Select default provider!',
							];

}