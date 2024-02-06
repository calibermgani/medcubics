<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToClaims extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` ADD `self_pay` ENUM('','Yes','No') NOT NULL AFTER `insurance_id`;");
		DB::statement("ALTER TABLE `claims` ADD `insurance_category` VARCHAR(20) NOT NULL AFTER `self_pay`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `claims` DROP COLUMN `self_pay`;');
		DB::statement('ALTER TABLE `claims` DROP COLUMN `insurance_category`;');
	}

}
