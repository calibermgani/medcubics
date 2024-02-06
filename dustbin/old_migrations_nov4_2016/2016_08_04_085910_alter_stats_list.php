<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatsList extends Migration {

	public function up()
	{
		Schema::table('stats_list', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `stats_list` CHANGE `class_name` `image_name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

	public function down()
	{
		Schema::table('stats_list', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `stats_list` CHANGE `image_name` `class_name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

}
