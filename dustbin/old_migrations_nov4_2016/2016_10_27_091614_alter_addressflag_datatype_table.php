<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddressflagDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addressflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `addressflag` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;");
			DB::statement("ALTER TABLE `addressflag` CHANGE `type_id` `type_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addressflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `addressflag` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;");
			DB::statement("ALTER TABLE `addressflag` CHANGE `type_id` `type_id` INT NOT NULL;");
		});
	}

}
