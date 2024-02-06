<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Lang;

class CheckReturn extends Model {
	use SoftDeletes;
	protected $table = 'check_returns';
	protected $fillable=['check_date','check_no','financial_charges','patient_id'];
	
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
		
	public function creator(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}

	public function modifier(){
		return $this->belongsTo('App\User', 'updated_by', 'id');
	}

	public static $rules = [
        'check_date' => 'required',
        //'check_no' => 'required',
        'financial_charges' => 'required',
    ];

	public static function messages()
	{
		return [
			'check_date.required' 				=> Lang::get("patient/checkReturn.validation.check_date"),
			'check_no.required'					=> Lang::get("patient/returnCheck.validation.check_no"),
			'financial_charges.required'		=> Lang::get("patient/returnCheck.validation.financial_charges"),			
		];
	}

}