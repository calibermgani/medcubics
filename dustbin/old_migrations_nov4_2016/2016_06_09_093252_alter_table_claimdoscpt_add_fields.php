<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableClaimdoscptAddFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` ADD `patient_paid` DECIMAL(10,2) NOT NULL AFTER `balance`, ADD `patient_balance` DECIMAL(10,2) NOT NULL AFTER `patient_paid`;");
		DB::statement("ALTER TABLE `claimdoscptdetails` ADD `payment_id` BIGINT(20) NOT NULL AFTER `patient_id`;");		
		DB::statement("ALTER TABLE `claimdoscptdetails` CHANGE `status` `status` ENUM('Pending','Paid','Open','P.Paid') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` DROP 'patient_paid'");
		DB::statement("ALTER TABLE `claimdoscptdetails` DROP 'patient_balance'");
		DB::statement("ALTER TABLE `claimdoscptdetails` DROP 'payment_id'");
		DB::statement("ALTER TABLE `claimdoscptdetails` CHANGE `status` `status` ENUM('','Pending','Paid','Open','P.Paid') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

	}

}
