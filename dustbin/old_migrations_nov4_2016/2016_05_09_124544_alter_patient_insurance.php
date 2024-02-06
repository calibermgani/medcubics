<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsurance extends Migration {

	public function up()
	{
			DB::statement("ALTER TABLE `patient_insurance` CHANGE `relationship` `relationship` ENUM('Self','Spouse','Child','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	
	public function down()
	{
		DB::statement("ALTER TABLE `patient_insurance` CHANGE `relationship` `relationship` ENUM('Self','Spouse','Child','Others') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
