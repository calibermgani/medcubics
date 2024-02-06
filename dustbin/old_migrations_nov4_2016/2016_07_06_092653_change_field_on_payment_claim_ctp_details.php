<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldOnPaymentClaimCtpDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `pateint_wallet_id` `payment_detail_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `payment_detail_id` `pateint_wallet_id`  VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
