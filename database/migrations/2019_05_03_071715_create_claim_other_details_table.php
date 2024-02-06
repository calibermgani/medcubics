<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimOtherDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_other_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->string('family_plan')->nullable();
			$table->bigInteger('patient_id')->unsigned();
			$table->string('original_reference')->nullable();
			$table->string('reference_id')->nullable();
			$table->string('non_avaiability')->nullable();
			$table->string('sponsor_status')->nullable();
			$table->string('sponsor_grade')->nullable();
			$table->string('disability_percent')->nullable();
			$table->string('service_status')->nullable();
			$table->string('serive_card_effective')->nullable();
			$table->string('handicaped_program')->nullable();
			$table->string('therapy_type')->nullable();
			$table->string('class_finding')->nullable();
			$table->string('nature_of_condition')->nullable();
			$table->date('date_of_last_xray')->nullable();
			$table->string('total_disability')->nullable();
			$table->string('hospitalization')->nullable();
			$table->date('prescription_date')->nullable();
			$table->string('month_treated')->nullable();
			$table->string('epsdt')->nullable();
			$table->string('ambulatory_service_req')->nullable();
			$table->string('levels_of_submission')->nullable();
			$table->string('weight_unit')->nullable();
			$table->string('pregnant')->nullable();
			$table->string('referal_item')->nullable();
			$table->string('last_menstrual_period')->nullable();
			$table->string('resubmission_no')->nullable();
			$table->string('medicalid_referral_no')->nullable();
			$table->string('service_auth_exception')->nullable();
			$table->string('branch_of_service')->nullable();
			$table->string('special_program')->nullable();
			$table->date('effective_start')->nullable();
			$table->date('effective_end')->nullable();
			$table->string('service_grade')->nullable();
			$table->string('non_available_statement')->nullable();
			$table->string('systemic_condition')->nullable();
			$table->string('complication_indicator')->nullable();
			$table->date('consultations_dates');
			$table->string('partial_disability')->nullable();
			$table->string('assumed_relinquished_care')->nullable();
			$table->date('date_of_last_visit')->nullable();
			$table->date('date_of_manifestation')->nullable();
			$table->string('third_party_liability')->nullable();
			$table->string('birth_weight')->nullable();
			$table->date('estimated_dob')->nullable();
			$table->string('findings')->nullable();
			$table->string('referal_code')->nullable();
			$table->string('note')->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('claim_other_details');
	}

}
