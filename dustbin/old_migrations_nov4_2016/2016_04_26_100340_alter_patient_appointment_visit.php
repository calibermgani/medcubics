<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientAppointmentVisit extends Migration {

	public function up()
	{
		Schema::table('patient_appointments', function($table)
		{
			DB::statement('ALTER TABLE `patient_appointments` CHANGE `reason_for_visit` `reason_for_visit` INT NOT NULL');
		});
	}

	public function down()
	{
		Schema::table('patient_appointments', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_appointments` CHANGE `reason_for_visit` `reason_for_visit` TEXT NOT NULL");
		});
	}

}
