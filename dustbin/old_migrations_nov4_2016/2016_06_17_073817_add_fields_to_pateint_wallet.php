<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPateintWallet extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `pateint_wallet` CHANGE `card_type` `card_type` ENUM('Visa Card','Master Card','Maestro Card','Gift Card') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `pateint_wallet` CHANGE `card_type` `card_type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
