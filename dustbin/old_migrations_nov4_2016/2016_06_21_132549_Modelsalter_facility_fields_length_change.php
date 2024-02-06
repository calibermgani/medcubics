<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModelsalterFacilityFieldsLengthChange extends Migration {

	
	public function up()
	{
		Schema::table('facilities', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `facilities` CHANGE `short_name` `short_name` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

	public function down()
	{
		Schema::table('facilities', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `facilities` CHANGE `short_name` `short_name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

}
