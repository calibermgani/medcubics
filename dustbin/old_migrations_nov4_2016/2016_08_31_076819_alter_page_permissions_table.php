<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPagePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('page_permissions', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `page_permissions` CHANGE `title_url` `title_url` TEXT NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('page_permissions', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `page_permissions` CHANGE `title_url` `title_url` VARCHAR(255) NOT NULL;");
		});
	}

}
