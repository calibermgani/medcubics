<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentsChangeCardNo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `payments` CHANGE `card_no` `card_no` INT(11) NULL DEFAULT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `payments` CHANGE `card_no` `card_no` INT(11) NOT NULL DEFAULT NULL;");
	}

}
