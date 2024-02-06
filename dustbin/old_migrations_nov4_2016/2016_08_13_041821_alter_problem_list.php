<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProblemList extends Migration {

	public function up()
	{
		Schema::table('problem_lists', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `problem_lists` CHANGE `priority` `priority` ENUM('High','Moderate','Low') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

	public function down()
	{
		//No need it
	}

}
