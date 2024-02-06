<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUseractivityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('useractivity', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `useractivity` ADD `url` VARCHAR(200) NOT NULL AFTER `action`");
			DB::statement("ALTER TABLE `useractivity` ADD `main_directory` VARCHAR(150) NOT NULL AFTER `url`");
			DB::statement("ALTER TABLE `useractivity` ADD `module` VARCHAR(70) NOT NULL AFTER `main_directory`");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('useractivity', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `useractivity` DROP COLUMN `url`");
			DB::statement("ALTER TABLE `useractivity` DROP COLUMN `main_directory`");
			DB::statement("ALTER TABLE `useractivity` DROP COLUMN `module`");
		});
	}
}
