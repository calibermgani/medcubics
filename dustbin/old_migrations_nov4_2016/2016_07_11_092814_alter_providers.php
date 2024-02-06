<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviders extends Migration {

	
	public function up()
	{
		DB::statement('ALTER TABLE `providers` CHANGE `ssn` `ssn` VARCHAR(9) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL');
	}

	public function down()
	{
		DB::statement('ALTER TABLE `providers` CHANGE `ssn` `ssn` INT(11) NOT NULL');
		
	}

}
