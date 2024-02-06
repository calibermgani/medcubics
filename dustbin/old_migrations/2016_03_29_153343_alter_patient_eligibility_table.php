<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientEligibilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_eligibility', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `patient_eligibility` CHANGE `filename` `edi_file_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
			DB::statement('ALTER TABLE `patient_eligibility` CHANGE `file_path` `edi_file_path` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
			DB::statement('ALTER TABLE `patient_eligibility` ADD `bv_filename` VARCHAR(250) NOT NULL AFTER `edi_file_path`');
			DB::statement('ALTER TABLE `patient_eligibility` ADD `bv_file_path` VARCHAR(255) NOT NULL AFTER `bv_filename`');
			DB::statement("ALTER TABLE `patient_eligibility` CHANGE `is_manual_atatched` `is_manual_atatched` TINYINT(1) NOT NULL DEFAULT '0'");
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
			DB::statement('ALTER TABLE `patient_eligibility` CHANGE `edi_file_name` `filename` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
			DB::statement('ALTER TABLE `patient_eligibility` CHANGE `edi_file_path` `file_path` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
			DB::statement("ALTER TABLE `patient_eligibility` DROP COLUMN `bv_filename`");
			DB::statement("ALTER TABLE `patient_eligibility` DROP COLUMN `bv_file_path`");
			DB::statement("ALTER TABLE `patient_eligibility` CHANGE `is_manual_atatched` `is_manual_atatched` TINYINT(1) NOT NULL DEFAULT '1'");
		});
	}

}
