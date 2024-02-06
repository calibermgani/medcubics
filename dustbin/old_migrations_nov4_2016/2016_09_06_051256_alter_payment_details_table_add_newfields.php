<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentDetailsTableAddNewfields extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_details` ADD `billing_provider_id` BIGINT NULL AFTER `patient_id`;");

		DB::statement("ALTER TABLE `payment_details` ADD `insurance_id` INT NULL AFTER `billing_provider_id`;");

		DB::statement("ALTER TABLE `payment_details` ADD `reference` VARCHAR(20) NULL AFTER `adjustment_reason_id`;");

		DB::statement("ALTER TABLE `payment_details` CHANGE `payment_randomid` `paymentnumber` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		DB::statement("ALTER TABLE `payment_details` DROP `money_order_no`;");		

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payment_details` DROP `billing_provider_id`");

		DB::statement("ALTER TABLE `payment_details` DROP `insurance_id`");

		DB::statement("ALTER TABLE `payment_details` DROP `reference`");

		DB::statement("ALTER TABLE `payment_details` CHANGE `paymentnumber` `payment_randomid` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		DB::statement("ALTER TABLE `payment_details` ADD `money_order_no` VARCHAR(20) NULL;");
	}

}
