<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cheatsheet extends Model {
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public function facility()
	{
		return $this->belongsTo('App\Models\Facility','facility_id','id');		
	}

	public function provider()
	{
		return $this->belongsTo('App\Models\Provider','provider_id','id');		
	}

	public function resources()
	{
		return $this->belongsTo('App\Models\Resources','resource_id','id');		
	}

	protected $fillable = ['resource_id','facility_id','provider_id','visit_type_id','cpt','icd','claimstatus','feeschedules'];

	public static $rules = [						
		'resource_id' => 'required',
		'facility_id' => 'required',
		'provider_id' => 'required',
		'visit_type_id' => 'required',
		'cpt' => 'required',
		'icd' => 'required|max:7',
		'claimstatus' => 'required',
		'feeschedules' => 'required',
	];

	public static $messages = [
		'resource_id.required' => 'Select resource!',
		'facility_id.required' => 'Select facility!',
		'provider_id.required' => 'Select provider',
		'visit_type_id.required' => 'Select visit type!',	
		'cpt.required' => 'Procedure code is required field. Please enter the same.',
		'icd.required' => 'Diagnosis code is required field. Please enter the same.',
		'icd.max' => 'Enter valid ICD!',
		'claimstatus.required' => 'Enter claimstatus!',
		'feeschedules.required' => 'Enter feeschedules!',
	];
	/*public static function messages(){
		return [
			'adjustment_reason.required'=> Lang::get("practice/practicemaster/adjustmentreason.validation.adjustmentreason"),
			'adjustment_type.required' 	=> Lang::get("practice/practicemaster/adjustmentreason.validation.adjustmenttype")
		];
	}*/

}
