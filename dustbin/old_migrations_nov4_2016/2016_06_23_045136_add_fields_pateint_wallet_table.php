<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsPateintWalletTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `pateint_wallet` CHANGE `payment_mode` `payment_mode` ENUM('Check','Cash','Money Order','Credit','EFT') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `pateint_wallet` CHANGE `payment_mode` `payment_mode` ENUM('Check','Cash','Money Order','Credit') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
