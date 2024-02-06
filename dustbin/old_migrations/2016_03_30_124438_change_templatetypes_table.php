<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTemplatetypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('templatetypes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `templatetypes` CHANGE `modified_at` `updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('templatetypes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `templatetypes` CHANGE `updated_at` `modified_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ");
		});
	}

}
