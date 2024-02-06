<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPracticeRegistrationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('practice_registration', function(Blueprint $table)
		{
			$table->dropColumn('group_id');
			DB::statement("ALTER TABLE `practice_registration` CHANGE `group_name` `group_name_id` ENUM('1','0') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('practice_registration', function(Blueprint $table)
		{
			$table->enum('group_id', array('0','1'));
			DB::statement("ALTER TABLE `practice_registration` CHANGE `group_name_id` `group_name` ENUM('1','0') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'");
		});
	}

}
