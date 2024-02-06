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
		DB::statement("ALTER TABLE `patient_insurance` DROP `category_changed_date`, DROP `insurance_notes`");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `patient_insurance` ADD `category_changed_date` TIMESTAMP NOT NULL AFTER `updated_at`, ADD `insurance_notes` LONGTEXT NOT NULL AFTER `category_changed_date`");
	}

}
