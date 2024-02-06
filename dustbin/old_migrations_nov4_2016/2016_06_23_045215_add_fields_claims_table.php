<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` ADD `insurance_paid` DECIMAL(10,2) NOT NULL AFTER `pateint_paid`;");
		DB::statement("ALTER TABLE `claims` ADD `patient_due` DECIMAL(10,2) NOT NULL AFTER `insurance_paid`, ADD `insurance_due` DECIMAL(10,2) NOT NULL AFTER `patient_due`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` DROP COLUMN `insurance_paid`, `patient_due`, `insurance_due`;");
	}

}
