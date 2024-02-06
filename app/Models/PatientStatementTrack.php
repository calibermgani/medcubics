<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientStatementTrack extends Model {
	protected $table 	= 'patientstatement_track';
	
	public $timestamps = false;
	
	protected $fillable	=	[
		'patient_id','claim_id_collection','total_ar_collection','send_statement_date','pay_by_date','balance','latest_payment_amt','latest_payment_date', 'statements','type_for','created_by'
	];

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
	
	public function patient_detail()
	{
		return $this->belongsTo('App\Models\Patients\Patient','patient_id','id');
	}
	
	public function user_detail()
	{
		return $this->belongsTo('App\Models\Medcubics\Users','created_by','id');
	}

}