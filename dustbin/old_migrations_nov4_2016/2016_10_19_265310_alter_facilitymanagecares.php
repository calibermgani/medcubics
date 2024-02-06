<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFacilitymanagecares extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `facilitymanagecares` CHANGE `provider_id` `provider_id` VARCHAR(20) NOT NULL;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `facilitymanagecares` CHANGE `provider_id` `provider_id` INT(11) NOT NULL;");
	}

}
