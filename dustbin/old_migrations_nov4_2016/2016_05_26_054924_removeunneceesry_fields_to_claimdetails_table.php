<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveunneceesryFieldsToClaimdetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claim_details` ADD `claim_id` BIGINT(20) NOT NULL AFTER `patient_id`;");

		DB::statement("ALTER TABLE `claim_details` DROP `reserved_nucc_box8`, DROP `reserved_nucc_box9b`, DROP `reserved_nucc_box9c`, DROP `reserved_nucc_box30`;");

		DB::statement("ALTER TABLE `claim_details` CHANGE `facility_mrn` `facility_mrn` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `autoaccident_state` `autoaccident_state` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `other_claim_id` `other_claim_id` VARCHAR(28) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `provider_otherid` `provider_otherid` VARCHAR(17) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `claim_code` `claim_code` VARCHAR(19) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `illness_box14` `illness_box14` DATE NOT NULL, CHANGE `facility_otherid` `facility_otherid` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `resubmission_code` `resubmission_code` ENUM('','7','8','') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `original_ref_no` `original_ref_no` VARCHAR(18) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `box_23` `box_23` VARCHAR(29) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claim_details` DROP `claim_id`;");

		DB::statement("ALTER TABLE `claim_details` ADD `reserved_nucc_box8`, ADD `reserved_nucc_box9b`, ADD `reserved_nucc_box9c`, ADD `reserved_nucc_box30`;");

		DB::statement("ALTER TABLE `claim_details` CHANGE `facility_mrn` `facility_mrn` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `autoaccident_state` `autoaccident_state` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `other_claim_id` `other_claim_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `provider_otherid` `provider_otherid` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `claim_code` `claim_code` VARCHAR(19) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `illness_box14` `illness_box14` DATE NOT NULL, CHANGE `facility_otherid` `facility_otherid` VARCHAR(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `resubmission_code` `resubmission_code` ENUM('','7','8','') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `original_ref_no` `original_ref_no` VARCHAR(18) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `box_23` `box_23` VARCHAR(29) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
