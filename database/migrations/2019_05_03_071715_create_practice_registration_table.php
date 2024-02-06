<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticeRegistrationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('practice_registration', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('email_id', array('0','1'));
			$table->enum('driving_license', array('0','1'));
			$table->enum('ethnicity', array('0','1'));
			$table->enum('race', array('0','1'));
			$table->enum('preferred_language', array('0','1'));
			$table->enum('marital_status', array('0','1'));
			$table->enum('student_status', array('0','1'));
			$table->enum('primary_care_provider', array('0','1'));
			$table->enum('primary_facility', array('0','1'));
			$table->enum('send_email_notification', array('0','1'));
			$table->enum('auto_phone_call_reminder', array('0','1'));
			$table->enum('preferred_communication', array('0','1'));
			$table->enum('insured_ssn', array('0','1'));
			$table->enum('insured_dob', array('0','1'));
			$table->enum('group_name_id', array('0','1'));
			$table->enum('guarantor', array('0','1'));
			$table->enum('emergency_contact', array('0','1'));
			$table->enum('employer', array('0','1'));
			$table->enum('attorney', array('0','1'));
			$table->enum('requested_date', array('0','1'));
			$table->enum('contact_person', array('0','1'));
			$table->enum('alert_on_appointment', array('0','1'));
			$table->enum('allowed_visit', array('0','1'));
			$table->enum('visits_used', array('0','1'));
			$table->enum('alert_on_visit_remains', array('0','1'));
			$table->enum('visit_remaining', array('0','1'));
			$table->enum('work_phone', array('0','1'));
			$table->enum('alert_on_billing', array('0','1'));
			$table->enum('total_allowed_amount', array('0','1'));
			$table->enum('amount_used', array('0','1'));
			$table->enum('amount_remaining', array('0','1'));
			$table->enum('documents', array('0','1'));
			$table->enum('notes', array('0','1'));
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->softDeletes();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('practice_registration');
	}

}
