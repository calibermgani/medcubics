<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsurance extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance` CHANGE `category` `category` ENUM('Primary','Secondary','Tertiary','Workerscomp','Liability','Autoaccident','Attorney','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
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
			DB::statement("ALTER TABLE `patient_insurance` CHANGE `category` `category` ENUM('Primary','Secondary','Tertiary','Workerscomp','Liability','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		});
	}

}