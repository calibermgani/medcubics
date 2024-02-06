<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;

class Facilitymanagecare extends Model 
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
		return $this->belongsTo('App\Models\Insurance','insurance_id','id');
	}
	
	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','providers_id','id');		
	}

	protected $fillable = array('facilities_id','insurance_id','enrollment','entitytype','provider_id', 'providers_id','effectivedate',
	 'terminationdate', 'feeschedule');
		
	public static $rules = [
		'insurance_id' => 'required|not_in:0',
		'providers_id' => 'nullable|not_in:0',
		'enrollment' => 'required',
		'entitytype' => 'required',
		'terminationdate' => 'nullable|after:effectivedate'
	];

	public static function messages(){
		return [
			'insurance_id.required' => Lang::get("common.validation.insurance_required"),
			'insurance_id.not_in' => Lang::get("common.validation.insurance_required"),
			'providers_id.required' => Lang::get("common.validation.provider_required"),
			'providers_id.not_in' => Lang::get("common.validation.provider_required"),
			'enrollment.required' => Lang::get("practice/practicemaster/managecare.validation.enrollment"),
			'entitytype.required' => Lang::get("practice/practicemaster/managecare.validation.entitytype"),
			//'effectivedate.required' => 'Enter effective date!',
			'terminationdate.after' => Lang::get("common.validation.terminationdate")	
		];
	 }
}