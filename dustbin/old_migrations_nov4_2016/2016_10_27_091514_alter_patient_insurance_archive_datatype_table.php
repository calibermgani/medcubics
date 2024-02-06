<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsuranceArchiveDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance_archive', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance_archive` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_insurance_archive', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance_archive` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

}
