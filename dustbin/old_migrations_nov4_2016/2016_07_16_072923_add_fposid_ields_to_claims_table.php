<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFposidIeldsToClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claims` ADD `pos_id` INT NOT NULL AFTER `insurance_id`;");
		DB::statement("ALTER TABLE `claims` ADD `entry_date` DATE NOT NULL AFTER `date_of_service`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claims` DROP `pos_id`;");
		DB::statement("ALTER TABLE `claims` DROP `entry_date`;");		
	}

}
