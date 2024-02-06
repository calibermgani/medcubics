<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNpiflagDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('npiflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `npiflag` CHANGE `type_id` `type_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('npiflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `npiflag` CHANGE `type_id` `type_id` INT NOT NULL;");
		});
	}

}
