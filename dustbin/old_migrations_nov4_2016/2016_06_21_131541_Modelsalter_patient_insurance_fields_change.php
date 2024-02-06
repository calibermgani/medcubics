<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModelsalterPatientInsuranceFieldsChange extends Migration {

	
	public function up()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance` CHANGE `policy_id` `policy_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance` CHANGE `policy_id` `policy_id` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

}
