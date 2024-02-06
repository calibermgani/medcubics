<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Practiceoverride extends Model {

	use SoftDeletes;

	protected $dates = ['deleted_at'];

	protected $fillable = array('practice_id','providers_id','provider_id','id_qualifiers_id');

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
    
	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','providers_id','id');		
	}
	
	public function id_qualifier()
	{
		return $this->belongsTo('App\Models\IdQualifier','id_qualifiers_id','id');		
	}
	
	public static $rules = [            
			'providers_id' => 'required|not_in:0',
			'provider_id' => 'required|alpha_num|max:15',						
			'id_qualifiers_id' => 'required|not_in:0',									
		];
		
	public static $messages = [
			'providers_id.required' => 'Select your billing provider!',
			'providers_id.not_in' => 'Select any billing provider!',
			'provider_id.required' => 'Enter your provider id!',
			'provider_id.min' => 'Enter valid provider id!',
			'provider_id.alpha_num' => 'Enter valid provider id!',
			'id_qualifiers_id.required' => 'Select your id type!',	
			'id_qualifiers_id.not_in' => 'Select any id type!',	
		];
		
	public static function checkHasOverrideOrNot()
	{
		return Practiceoverride::count();
	}
}