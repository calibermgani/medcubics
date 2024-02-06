<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Insuranceoverride extends Model
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
	
	public function facility()
	{
		return $this->belongsTo('App\Models\Facility','facility_id','id');		
	}
	
	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','providers_id','id');		
	}
	
	public function id_qualifier()
	{
		return $this->belongsTo('App\Models\IdQualifier','id_qualifiers_id','id');		
	}
	
	protected $fillable = array (
		'insurance_id',
		'practice_id',
		'facility_id',
		'providers_id',
		'provider_id',
		'id_qualifiers_id' 
	);
	
	public static $rules = [ 
		'facility_id' => 'required',
		'providers_id' => 'required',
		'provider_id' => 'required',
		'id_qualifiers_id' => 'required' 
	];
	
	public static function messages(){
		return [
			'facility_id.required' 		=> Lang::get("practice/practicemaster/insurance.validation.facility_id"),
			'providers_id.required'		=> Lang::get("common.validation.provider_required"),
			'provider_id.required'		=> Lang::get("practice/practicemaster/insurance.validation.provider_id"),
			'id_qualifiers_id.required'	=> Lang::get("practice/practicemaster/insurance.validation.type_id"),
		];
	}
}