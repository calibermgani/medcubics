<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProblemListsDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('problem_lists', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `problem_lists` CHANGE `created_by` `created_by` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('problem_lists', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `problem_lists` CHANGE `created_by` `created_by` INT NOT NULL;");
		});
	}

}
