<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviderLengthChange extends Migration {

	public function up()
	{
		Schema::table('providers', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `providers` CHANGE `short_name` `short_name` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `last_name` `last_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `first_name` `first_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `middle_name` `middle_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

	public function down()
	{
		Schema::table('providers', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `providers` CHANGE `short_name` `short_name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `last_name` `last_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `first_name` `first_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `middle_name` `middle_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

}
