<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;

class ClaimOtherDetail extends Model {

	protected $fillable = [
		'family_plan',
		'original_reference',
		'reference_id',
		'non_avaiability',
		'sponsor_status',
		'sponsor_grade',
		'disability_percent',
		'service_status',
		'serive_card_effective',
		'handicaped_program',
		'therapy_type',
		'class_finding',
		'nature_of_condition',
		'date_of_last_xray',
		'total_disability',
		'hospitalization',
		'prescription_date',
		'month_treated',
		'epsdt',
		'ambulatory_service_req',
		'levels_of_submission',
		'weight_unit',
		'pregnant',
		'referal_item',
		'last_menstrual_period',
		'resubmission_no',
		'medicalid_referral_no',
		'service_auth_exception',
		'branch_of_service',
		'special_program',
		'effective_start',
		'effective_end',
		'service_grade',
		'non_available_statement',
		'systemic_condition',
		'complication_indicator',
		'consultations_dates',
		'partial_disability',
		'assumed_relinquished_care',
		'date_of_last_visit',
		'date_of_manifestation',
		'third_party_liability',
		'birth_weight',
		'estimated_dob',
		'findings',
		'referal_code',
		'note',
		'created_by',
		'updated_by',
		'patient_id'
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

}