<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPatientWalletAddnewfields extends Migration {

	public function up()
	{
		DB::statement("ALTER TABLE `pateint_wallet` CHANGE `type` `type` ENUM('scheduler','charge','posting','addwallet') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");

		DB::statement("ALTER TABLE `pateint_wallet` ADD `payment_method` ENUM('Insurance','Patient','','') NOT NULL AFTER `type_id`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `pateint_wallet` DROP COLUMN `payment_method`");
		DB::statement("ALTER TABLE `pateint_wallet` CHANGE `type` `type` ENUM('scheduler','charge','posting') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}


}
