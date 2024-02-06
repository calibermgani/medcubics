<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableClaimsPaymentTransactionHistoriesNewField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `payment_detail_id` `payment_claim_detail_id` BIGINT(20) NOT NULL;");

		DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `type` `type` ENUM('scheduler','charge','posting','responsibility') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `payment_claim_detail_id` `payment_detail_id` BIGINT(20) NOT NULL;");

		DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `type` `type` ENUM('scheduler','charge','posting') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
