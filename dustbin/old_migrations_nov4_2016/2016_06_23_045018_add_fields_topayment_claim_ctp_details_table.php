<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsTopaymentClaimCtpDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `detuctible` `deductable` DECIMAL(10,2) NOT NULL;");
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `paid` `insurance_paid` DECIMAL(10,2) NOT NULL;");
		DB::statement("ALTER TABLE `payment_claim_ctp_details` ADD `claimdoscptdetail_id` BIGINT(20) NOT NULL AFTER `paymnt_claim_detail_id`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{  
		DB::statement("ALTER TABLE `payment_claim_ctp_details` DROP COLUMN `claimdoscptdetail_id`;");
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `deductable` `detuctible` DECIMAL(10,2) NOT NULL;");
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `insurance_paid` `paid` DECIMAL(10,2) NOT NULL;");
	}

}
