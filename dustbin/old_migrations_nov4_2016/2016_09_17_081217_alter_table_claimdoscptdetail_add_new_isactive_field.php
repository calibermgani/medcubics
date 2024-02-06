<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableClaimdoscptdetailAddNewIsactiveField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `insurance_id`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` DROP `is_active`;");
	}

}
