<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentDetailsTableAddPaymentTypefiels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_details` ADD `payment_type` ENUM('Payment','Refund','Adjustment','Credit Balance') NOT NULL AFTER `payment_method`;");
		DB::statement("ALTER TABLE `payment_details` CHANGE `payment_type` `payment_type` ENUM('','Payment','Adjustment','Credit Balance','Refund') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payment_details` drop `payment_type`;");
	}

}
