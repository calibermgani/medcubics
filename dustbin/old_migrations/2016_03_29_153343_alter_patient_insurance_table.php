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
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance` ADD `eligibility_verification` ENUM('None','Active','Inactive','Error') NOT NULL AFTER `document_save_id`");
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
			DB::statement("ALTER TABLE `patient_insurance` DROP COLUMN `eligibility_verification`");
		});
	}

}
