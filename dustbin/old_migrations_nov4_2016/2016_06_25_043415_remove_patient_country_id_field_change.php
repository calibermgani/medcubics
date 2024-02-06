<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePatientCountryIdFieldChange extends Migration {

	public function up()
	{
		DB::statement("ALTER TABLE `patients` DROP `country_id`");
	}

	public function down()
	{
		DB::statement("ALTER TABLE `patients` ADD `country_id` INT(11) NOT NULL AFTER `zip4`");
	}

}
