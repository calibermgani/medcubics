<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPatientAutorization extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `patient_authorizations` CHANGE `authorization_no` `authorization_no` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `patient_authorizations` CHANGE `authorization_no` `authorization_no` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
