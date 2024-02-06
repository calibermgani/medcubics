<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientAppointmentsDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_appointments', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_appointments` CHANGE `patient_id` `patient_id` BIGINT UNSIGNED NOT NULL;");
			DB::statement("ALTER TABLE `patient_appointments` CHANGE `provider_id` `provider_id` BIGINT UNSIGNED NOT NULL;");
			DB::statement("ALTER TABLE `patient_appointments` CHANGE `provider_scheduler_id` `provider_scheduler_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_appointments', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_appointments` CHANGE `patient_id` `patient_id` INT NOT NULL;");
			DB::statement("ALTER TABLE `patient_appointments` CHANGE `provider_id` `provider_id` INT NOT NULL;");
			DB::statement("ALTER TABLE `patient_appointments` CHANGE `provider_scheduler_id` `provider_scheduler_id` INT NOT NULL;");
		});
	}

}
