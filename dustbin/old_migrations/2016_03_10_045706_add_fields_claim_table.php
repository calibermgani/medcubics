<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsClaimTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` ADD `admit_date` DATE NULL AFTER `doi`;");
		DB::statement("ALTER TABLE `claims` ADD `discharge_date` DATE NOT NULL AFTER `admit_date`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

		DB::statement("ALTER TABLE `claims` DROP COLUMN `admit_date`, `discharge_date`;");

	}

}
