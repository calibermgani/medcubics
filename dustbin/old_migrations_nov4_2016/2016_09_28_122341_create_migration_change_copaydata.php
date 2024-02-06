<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMigrationChangeCopaydata extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` CHANGE `copay` `copay` ENUM('','Cash','Check','Credit') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` CHANGE `copay` `copay` ENUM('','Cash','Cheque','Credit Card, Moneyorder') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
	}

}
