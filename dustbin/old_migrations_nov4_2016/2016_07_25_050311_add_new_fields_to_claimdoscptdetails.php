<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToClaimdoscptdetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` ADD `insurance_balance` DOUBLE(10,2) NOT NULL AFTER `patient_balance`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` DROP COLUMN `insurance_balance`;");
	}

}
