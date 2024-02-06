<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviderNameLength extends Migration {

	public function up()
	{
		DB::statement('ALTER TABLE `providers` CHANGE `short_name` `short_name` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
		DB::statement('ALTER TABLE `providers` CHANGE `last_name` `last_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `first_name` `first_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `middle_name` `middle_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
		
	}

	public function down()
	{
		DB::statement('ALTER TABLE `providers` CHANGE `short_name` `short_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
		DB::statement('ALTER TABLE `providers` CHANGE `last_name` `last_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `first_name` `first_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, CHANGE `middle_name` `middle_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;');
	}

}
