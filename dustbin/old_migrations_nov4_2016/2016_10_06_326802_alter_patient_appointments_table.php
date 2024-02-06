<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientAppointmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_appointments', function(Blueprint $table)
		{
			$table->string('copay_check_number', 25)->after('copay_details');
			$table->enum('copay_card_type', ['Visa Card','Master Card','Maestro Card','Gift Card'])->after('copay_check_number');
			$table->date('copay_date')->after('copay_card_type');
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
			$table->dropColumn('copay_check_number');
			$table->dropColumn('copay_card_type');
			$table->dropColumn('copay_date');
		});
	}

}
