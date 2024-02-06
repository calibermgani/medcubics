<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provideroverride extends Model
{
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

	public function provider_override()
	{
		return $this->belongsTo('App\Models\Provider','provider_override_id','id');
	}
	
	public function id_qualifier()
	{
		return $this->belongsTo('App\Models\IdQualifier','id_qualifiers_id','id');		
	}

	protected $fillable = array (
		'providers_id',
        'provider_override_id',
		'provider_id',
		'id_qualifiers_id' 
	);
	
	public static $rules = [ 
		'provider_override_id' => 'required',
		'provider_id' => 'required|alpha_num|max:15',
		'id_qualifiers_id' => 'required' 
	];
	
	public static $messages = [
		'provider_override_id.required' => 'Select your provider!',
		'provider_id.required' => 'Enter your provider id!',
		'provider_id.min' => 'Enter valid provider id!',
		'provider_id.alpha_num' => 'Enter valid provider id!',
		'id_qualifiers_id.required' => 'Select your id type!',	
	];
	
	public static function checkHasOverrideOrNot($provider_id)
	{
		return Provideroverride::where('providers_id',$provider_id)->count();
	}
}