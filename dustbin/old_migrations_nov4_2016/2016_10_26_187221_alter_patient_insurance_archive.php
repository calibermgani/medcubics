<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsuranceArchive extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `patient_insurance_archive` DROP `insurance_notes`, CHANGE `address1` `insured_address1` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `address2` `insured_address2` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `city` `insured_city` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `state` `insured_state` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `zip5` `insured_zip5` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `zip4` `insured_zip4` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `from` `active_from` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00', CHANGE `to` `active_to` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00', ADD `insured_phone` VARCHAR(15) NOT NULL AFTER `relationship`, ADD `insured_gender` ENUM('Male', 'Female', 'Other') NOT NULL AFTER `insured_phone`, ADD `document_save_id` BIGINT(30) NOT NULL AFTER `adjustor_fax`, ADD `eligibility_verification` ENUM('None', 'Active', 'Inactive', 'Error') NOT NULL AFTER `document_save_id`, ADD `same_patient_address` ENUM('no', 'yes') NOT NULL AFTER `eligibility_verification`, CHANGE `middle_name` `middle_name` VARCHAR(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `group_name` `group_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `adjustor_ph` `adjustor_ph` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `adjustor_fax` `adjustor_fax` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `patient_insurance_archive` ADD `insurance_notes` LONGTEXT NOT NULL AFTER `category_changed_date`, CHANGE `insured_address1` `address1` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `insured_address2` `address2` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `insured_city` `city` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `insured_state` `state` VARCHAR(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `insured_zip5` `zip5` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `insured_zip4` `zip4` VARCHAR(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `active_from` `from` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00', CHANGE `active_to` `to` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00', DROP `insured_phone`, DROP `insured_gender`, DROP `document_save_id`, DROP `eligibility_verification`, DROP `same_patient_address`");
	}

}
