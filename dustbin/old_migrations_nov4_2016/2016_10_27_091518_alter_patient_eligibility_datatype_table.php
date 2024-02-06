<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientEligibilityDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_eligibility', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_eligibility` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_eligibility', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_eligibility` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

}
