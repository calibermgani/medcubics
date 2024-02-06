<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsuranceArchive extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance_archive', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance_archive` CHANGE `category` `category` ENUM('Primary','Secondary','Tertiary','Workerscomp','Liability','Autoaccident','Attorney','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
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
			DB::statement("ALTER TABLE `patient_insurance_archive` CHANGE `category` `category` ENUM('Primary','Secondary','Tertiary','Workerscomp','Liability','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		});
	}

}
