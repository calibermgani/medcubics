<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFacilityShortname extends Migration 
{
	public function up()
	{
		DB::statement('ALTER TABLE `facilities` CHANGE `short_name` `short_name` VARCHAR(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
		
	}
	public function down()
	{
		DB::statement('ALTER TABLE `facilities` CHANGE `short_name` `short_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL');
	}
}
