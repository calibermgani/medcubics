<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTablePaymentClaimCptDetailsNewField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `posting_type` `posting_type` ENUM('','Insurance','Patient') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` DROP `claim_no`, DROP `dos`, DROP `category`, DROP `cpt`, DROP `status`;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `paymnt_claim_detail_id` `payment_claim_detail_id` BIGINT(20) NOT NULL;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` DROP `payment_detail_id`;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `pateint_id` `patient_id` BIGINT(20) NOT NULL;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` ADD `description` VARCHAR(50) NULL AFTER `remark_code`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `posting_type` `posting_type` ENUM(Insurance','Patient') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` ADD `claim_no` VARCHAR(20) NULL, ADD `dos` VARCHAR(50) NULL, ADD `category` VARCHAR(10) NULL, ADD `cpt` VARCHAR(10) NULL, ADD `status` VARCHAR(10) NULL;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `payment_claim_detail_id` `paymnt_claim_detail_id` BIGINT(20) NOT NULL;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` DROP `payment_detail_id`;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` CHANGE `patient_id` `pateint_id` BIGINT(20) NOT NULL;");

		DB::statement("ALTER TABLE `payment_claim_ctp_details` DROP `description`");
	}

}
