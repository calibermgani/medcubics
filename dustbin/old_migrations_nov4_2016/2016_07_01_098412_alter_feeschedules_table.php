<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFeeschedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('feeschedules', function(Blueprint $table)
		{
			DB::statement("TRUNCATE `feeschedules`;");
			DB::statement("ALTER TABLE `feeschedules` ADD PRIMARY KEY(`id`);");
			DB::statement("ALTER TABLE `feeschedules` CHANGE `id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feeschedules');
	}

}
