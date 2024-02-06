<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsClaims extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` CHANGE `status` `status` ENUM('E-bill','Hold','Ready to submit','Patient') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` CHANGE `status` `status` ENUM('E-bill','Hold','Ready to submit') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
");
	}

}
