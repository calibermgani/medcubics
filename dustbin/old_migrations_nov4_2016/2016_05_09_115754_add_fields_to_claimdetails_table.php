<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFieldsToClaimdetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claim_details` CHANGE `additional_claim_info` `additional_claim_info` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claim_details` ADD `provider_qualifier` ENUM('','0B','1G','G2','LU') NOT NULL AFTER `other_claim_id`, ADD `provider_otherid` VARCHAR(20) NOT NULL AFTER `provider_qualifier`, ADD `lab_charge` DECIMAL(10,2) NOT NULL AFTER `provider_otherid`;");
		DB::statement("ALTER TABLE `claim_details` ADD `service_facility_qual` ENUM('','0B','G2','LU') NOT NULL AFTER `other_date`;");
		DB::statement("ALTER TABLE `claim_details` ADD `facility_otherid` VARCHAR(20) NOT NULL AFTER `service_facility_qual`;");
		DB::statement("ALTER TABLE `claim_details` ADD `billing_provider_qualifier` ENUM('','0B','G2','ZZ') NOT NULL AFTER `facility_otherid`, ADD `billing_provider_otherid` VARCHAR(20) NOT NULL AFTER `billing_provider_qualifier`;");
		DB::statement('ALTER TABLE `claims` ADD `referingprovidertypeid` BIGINT(20) NOT NULL AFTER `cheque_date`;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `claim_details` DROP COLUMN provider_qualifier');
		DB::statement('ALTER TABLE `claim_details` DROP COLUMN provider_otherid');
		DB::statement('ALTER TABLE `claim_details` DROP COLUMN service_facility_qual');
		DB::statement('ALTER TABLE `claim_details` DROP COLUMN lab_charge');
		DB::statement('ALTER TABLE `claim_details` DROP COLUMN referingprovidertypeid');
		DB::statement("ALTER TABLE `claim_details` ALTER COLUMN change additional_claim_info string(50)");
	}

}
