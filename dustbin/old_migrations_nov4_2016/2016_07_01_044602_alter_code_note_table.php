<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCodeNoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('codes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `codes` CHANGE `notes` `notes` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
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
			DB::statement("ALTER TABLE `codes` CHANGE `notes` `notes` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;");
		});
	}

}
