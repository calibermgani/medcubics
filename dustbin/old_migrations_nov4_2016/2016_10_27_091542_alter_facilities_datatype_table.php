<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFacilitiesDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('facilities', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `facilities` CHANGE `default_provider_id` `default_provider_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('facilities', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `facilities` CHANGE `default_provider_id` `default_provider_id` INT NOT NULL;");
		});
	}

}
