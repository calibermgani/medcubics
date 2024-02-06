<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToClaimdoscptdetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement('ALTER TABLE `claimdoscptdetails` ADD `co_pay` DECIMAL(10,2) NOT NULL AFTER `co_ins`;');
		DB::statement('ALTER TABLE `claimdoscptdetails` CHANGE `unit` `unit` VARCHAR(50) NOT NULL;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE `claimdoscptdetails` DROP COLUMN `co_pay`;');
		DB::statement('ALTER TABLE `claimdoscptdetails` CHANGE `unit` `unit` DECIMAL(10,2) NOT NULL;');
	}

}
