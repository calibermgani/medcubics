<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientAppointments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_appointments', function($table)
		{
			$table->string('cancel_delete_reason',255)->after('rescheduled_reason');
		});
	}

	
	public function down()
	{
		Schema::table('patient_appointments', function(Blueprint $table)
		{
			$table->dropColumn('cancel_delete_reason');
		});
	}

}
