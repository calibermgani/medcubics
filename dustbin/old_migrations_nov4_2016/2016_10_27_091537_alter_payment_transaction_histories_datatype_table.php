<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentTransactionHistoriesDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('payment_transaction_histories', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `created_by` `created_by` BIGINT UNSIGNED NOT NULL;");
			DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `updated_by` `updated_by` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('payment_transaction_histories', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `created_by` `created_by` INT NOT NULL;");
			DB::statement("ALTER TABLE `payment_transaction_histories` CHANGE `updated_by` `updated_by` INT NOT NULL;");
		});
	}

}
