<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientEligibilityMoreTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_eligibility', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `patient_eligibility` CHANGE `edi_file_name` `edi_filename` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
			DB::statement('ALTER TABLE `patient_eligibility` ADD `temp_patient_id` BIGINT(20) NOT NULL AFTER `dos`');
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
			DB::statement('ALTER TABLE `patient_eligibility` CHANGE `edi_filename` `edi_file_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
			DB::statement("ALTER TABLE `patient_eligibility` DROP COLUMN `temp_patient_id`");
		});
	}

}
