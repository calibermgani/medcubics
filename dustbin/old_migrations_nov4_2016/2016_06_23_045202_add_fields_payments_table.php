<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsPaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payments` ADD `billing_provider_id` BIGINT(20) NOT NULL AFTER `pateint_wallet_id`;");
		DB::statement("ALTER TABLE `payments` ADD `insurance_id` BIGINT(20) NOT NULL AFTER `billing_provider_id`;");
		DB::statement("ALTER TABLE `payments` ADD `insurance_cat` VARCHAR(10) NOT NULL AFTER `insurance_id`;");

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payments` DROP COLUMN `billing_provider_id`;");
		DB::statement("ALTER TABLE `payments` DROP COLUMN `insurance_id`;");
		DB::statement("ALTER TABLE `payments` DROP COLUMN `insurance_cat`;");
	}

}
