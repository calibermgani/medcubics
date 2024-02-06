<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldOnPayments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payments` CHANGE `pateint_wallet_id` `payment_detail_id` BIGINT(20) NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payments` CHANGE  `payment_detail_id` `pateint_wallet_id` BIGINT(20) NOT NULL;");
	}

}
