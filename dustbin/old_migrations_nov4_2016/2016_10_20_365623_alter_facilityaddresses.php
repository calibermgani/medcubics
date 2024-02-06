<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFacilityaddresses extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `facilityaddresses` CHANGE `phoneext` `phoneext` VARCHAR(4) NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `facilityaddresses` CHANGE `phoneext` `phoneext` INT(11) NOT NULL");
	}

}
