<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `claims` CHANGE `cheque_amt` `cheque_amt` DECIMAL(10,2) NOT NULL;');
		DB::statement('ALTER TABLE `claims` ADD `patient_due` DECIMAL(10,2) NOT NULL AFTER `paid_amt`;');
		DB::statement('ALTER TABLE `claims` ADD `payment_batch_no` VARCHAR(50) NOT NULL AFTER `payment_mode`;');
		DB::statement('ALTER TABLE `claims` ADD `payment_batch_date` DATE NOT NULL AFTER `payment_batch_no`;');	
		DB::statement('ALTER TABLE `claims` ADD `reference_no` VARCHAR(50) NOT NULL AFTER `payment_batch_date`;');				
		DB::statement('ALTER TABLE `claims` ADD `balance_amt` DECIMAL(10,2) NOT NULL AFTER `adjust_amt`;');		
		DB::statement('ALTER TABLE `claims` ADD `insurance_due` DECIMAL(10,2) NOT NULL AFTER `patient_due`;');
		DB::statement('ALTER TABLE `claims` ADD `total_paid` DECIMAL(10,2) NOT NULL AFTER `cheque_amt`;');
		DB::statement('ALTER TABLE `claims` ADD `claim_submit_count` INT NOT NULL AFTER `unupplied`;');
		DB::statement("ALTER TABLE `claims` CHANGE `status` `status` ENUM('E-bill','Hold','Ready to submit','Patient','Submitted') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `claims` DROP COLUMN `payment_batch_no`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `payment_batch_date`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `reference_no`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `balance_amt`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `insurance_due`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `total_paid`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `claim_submit_count`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `patient_due`;');
		DB::statement("ALTER TABLE `claims` CHANGE `status` `status` ENUM('E-bill','Hold','Ready to submit','Patient') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");		
	}

}
