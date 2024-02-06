<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facilityoverride extends Model 
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
    
	public function insurance()
	{
		return $this->belongsTo('App\Models\Insurance','insurances_id','id');
	}
	
	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','providers_id','id');		
	}
	
	public function id_qualifier()
	{
		return $this->belongsTo('App\Models\IdQualifier','id_qualifiers_id','id');		
	}
	
	protected $fillable = array('practice_id','facilities_id', 'insurances_id','providers_id','tax_id', 'npi','provider_id','id_qualifiers_id');
	 
	public static $rules = [
		'facilities_id' => 'required',
		'insurances_id' => 'required',			
		'providers_id' => 'required',
		'provider_id' => 'required|alpha_num|max:15',						
		'id_qualifiers_id' => 'required',	
	];
	
	public static $messages = [
		'facilities_id.required' => 'Select your facility!',
		'insurances_id.required' => 'Select your insurance!',
		'insurances_id.not_in' => 'Sasdfasdfnce!',
		'providers_id.required' => 'Select your billing provider!',
		'provider_id.required' => 'Enter your provider ID!',
		'provider_id.alpha_num' => 'Provider ID should contain only letters and numbers!',
		'provider_id.min' => 'Provider ID should be 15 digits!',
		'provider_id.max' => 'Provider ID should be 15 digits!',
		'id_qualifiers_id.required' => 'Select your ID type!',
	];
}