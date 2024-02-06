<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFildsToClaimdetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claim_details` CHANGE `facility_id` `facility_mrn` VARCHAR(255) NOT NULL;");
		DB::statement("ALTER TABLE `claim_details` ADD `other_date_qualifier` ENUM('','454','304','453','439','455','471','090','091','444') NOT NULL AFTER `illness_box14`;");

		DB::statement("ALTER TABLE `claim_details` CHANGE `claim_code` `claim_code` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		DB::statement("ALTER TABLE `claim_details` CHANGE `print_signature_onfile_box12` `print_signature_onfile_box12` ENUM('Yes','No') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `print_signature_onfile_box13` `print_signature_onfile_box13` ENUM('Yes','No') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		DB::statement("ALTER TABLE `claim_details` CHANGE `emergency` `emergency` ENUM('','Yes','No') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");


		DB::statement("ALTER TABLE `claim_details` ADD `original_ref_no` VARCHAR(50) NOT NULL AFTER `resubmission_code`;");

		DB::statement("ALTER TABLE `claim_details` ADD `box23_type` ENUM('','referal_number','mamography','clia_no') NOT NULL AFTER `emergency`;");

		DB::statement("ALTER TABLE `claim_details` ADD `box_23` VARCHAR(50) NOT NULL AFTER `box23_type`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claim_details` DROP COLUMN `other_date_qualifier`;");
		DB::statement("ALTER TABLE `claim_details` DROP COLUMN `original_ref_no`;");
		DB::statement("ALTER TABLE `claim_details` DROP COLUMN `box23_type`;");
		DB::statement("ALTER TABLE `claim_details` DROP COLUMN `box_23`;");
		DB::statement("ALTER TABLE `claim_details` CHANGE `claim_code` `claim_code` VARCHAR(10);");
		DB::statement("ALTER TABLE `claim_details` CHANGE `facility_mrn` `facility_id` INT(10)';");
		DB::statement("ALTER TABLE `claim_details` CHANGE `emergency` `emergency` ENUM('Yes','No') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		DB::statement("ALTER TABLE `claim_details` CHANGE `print_signature_onfile_box12` `print_signature_onfile_box12` ENUM('', 'Yes','No') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `print_signature_onfile_box13` `print_signature_onfile_box13` ENUM('','Yes','No') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
