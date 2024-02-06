<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToPaymentClaimCtpDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_claim_ctp_details` ADD `insurance_balance` DECIMAL(10,2) NOT NULL AFTER `patient_balance`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payment_claim_ctp_details` DROP COLUMN `insurance_balance`;");
	}

}
