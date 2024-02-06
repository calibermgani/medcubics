<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientAppointmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_appointments', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->integer('facility_id');
			$table->bigInteger('provider_id')->unsigned();
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('provider_scheduler_id')->unsigned();
			$table->date('scheduled_on');
			$table->string('appointment_time', 100);
			$table->enum('is_new_patient', array('No','Yes'));
			$table->integer('reason_for_visit');
			$table->enum('status', array('Scheduled','Complete','Rescheduled','Canceled','No Show','Encounter'))->nullable()->default('Scheduled');
			$table->string('checkin_time', 20)->nullable();
			$table->string('checkout_time', 20)->nullable();
			$table->enum('copay_option', array('Cash','CC','Check','Money Order','Others'));
			$table->string('copay', 250)->nullable();
			$table->enum('non_billable_visit', array('No','Yes'));
			$table->string('rescheduled_from')->nullable();
			$table->string('rescheduled_reason')->nullable();
			$table->string('cancel_delete_reason')->nullable();
			$table->string('copay_details', 200)->nullable();
			$table->string('copay_check_number', 25)->nullable();
			$table->enum('copay_card_type', array('Visa Card','Master Card','Maestro Card','Gift Card'))->nullable();
			$table->date('copay_date')->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->timestamps();
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
		Schema::drop('patient_appointments');
	}

}
