<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldsOnClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` DROP `provider_id`, DROP `bill_cycle`, DROP `employer_id`, DROP `pos_name`, DROP `pos_code`, DROP `alert`, DROP `icd_order`, DROP `claim_ids`, DROP `localfilename`;");
		DB::statement("ALTER TABLE `claims` CHANGE `charge_add_type` `charge_add_type` ENUM('esuperbill','ehr','manual','billing') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` CHANGE `claim_number` `claim_number` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` CHANGE `self_pay` `self_pay` ENUM('No','Yes') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` CHANGE `anesthesia_start` `anesthesia_start` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `anesthesia_stop` `anesthesia_stop` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `anesthesia_minute` `anesthesia_minute` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` ADD `provider_id`, ADD `bill_cycle`, ADD `employer_id`, ADD `pos_name`, ADD `pos_code`, ADD `alert`, ADD `icd_order`, ADD `claim_ids`, ADD `localfilename`;");
		DB::statement("ALTER TABLE `claims` CHANGE `charge_add_type` `charge_add_type` ENUM('esuperbill','bhr','manual','billing') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` CHANGE `claim_number` `claim_number` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` CHANGE `self_pay` `self_pay` ENUM('', 'No','Yes') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claims` CHANGE `anesthesia_start` `anesthesia_start` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `anesthesia_stop` `anesthesia_stop` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `anesthesia_minute` `anesthesia_minute` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
