<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldOnPaymentTransactionHistories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `pateint_wallet_id` `payment_detail_id` BIGINT(20) NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE  `payment_detail_id` `pateint_wallet_id` BIGINT(20) NOT NULL;");
	}

}
