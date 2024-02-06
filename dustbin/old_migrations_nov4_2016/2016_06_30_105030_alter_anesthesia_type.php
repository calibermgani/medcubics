<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAnesthesiaType extends Migration {

	public function up()
	{
		DB::statement("ALTER TABLE `modifiers` CHANGE `anesthesia_base_unit` `anesthesia_base_unit` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `modifiers` CHANGE `anesthesia_base_unit` `anesthesia_base_unit` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
	}

}
