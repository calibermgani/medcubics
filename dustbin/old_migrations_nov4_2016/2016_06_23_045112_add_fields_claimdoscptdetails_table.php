<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsClaimdoscptdetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` ADD `insurance_paid` DECIMAL(10,2) NOT NULL AFTER `patient_paid`;");
		DB::statement("ALTER TABLE `claimdoscptdetails` CHANGE `status` `status` ENUM('Pending','Paid','Open','P.Paid','Denied') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;"); 
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` DROP COLUMN `insurance_paid`;");
		DB::statement("ALTER TABLE `claimdoscptdetails` CHANGE `status` `status` ENUM('Pending','Paid','Open','P.Paid') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;"); 
	}

}