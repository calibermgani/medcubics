<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('codes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `codes` CHANGE `transactioncode_id` `transactioncode_id` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('codes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `codes` CHANGE `transactioncode_id` `transactioncode_id` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

}
