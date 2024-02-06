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
		Schema::table('patient_insurance', function($table)
		{
			$table->dropColumn('insurancetype_id');
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
			$table->integer('insurancetype_id')->after('eligibility_verification');
		});
	}

}
