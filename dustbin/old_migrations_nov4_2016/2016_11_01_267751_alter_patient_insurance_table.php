<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsuranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			$table->timestamp('active_from')->default("0000-00-00 00:00:00")->after('same_patient_address');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			$table->dropColumn('active_from');
		});
	}

}
