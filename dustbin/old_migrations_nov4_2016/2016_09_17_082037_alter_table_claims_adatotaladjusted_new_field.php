<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableClaimsAdatotaladjustedNewField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` CHANGE `pateint_paid` `patient_paid` DECIMAL(10,2) NOT NULL;");

	    DB::statement("ALTER TABLE `claims` ADD `total_withheld` DECIMAL(10,2) NOT NULL AFTER `total_adjusted`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` CHANGE `patient_paid` `pateint_paid` DECIMAL(10,2) NOT NULL;");

	    DB::statement("ALTER TABLE `claims` DROP `total_withheld`");
	}

}
