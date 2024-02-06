<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePracticesDefaultPractice extends Migration {

	public function up()
	{
		Schema::table('practices', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `practices` DROP `default_practice`");
		});
	}

	public function down()
	{
		Schema::table('practices', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `practices` ADD `default_practice` ENUM('Yes','No') NOT NULL AFTER `practice_link`");
		});
	}

}
