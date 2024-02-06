<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentsTableAddPaymentFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payments` CHANGE `type` `type` ENUM('scheduler','charge','posting','addwallet','refundwallet') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
			DB::statement("ALTER TABLE `payments` CHANGE `type` `type` ENUM('scheduler','charge','posting','addwallet') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
");
	}

}
