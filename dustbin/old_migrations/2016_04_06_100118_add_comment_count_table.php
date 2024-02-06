<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentCountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('blog', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `blog` ADD `comment_count` int(11) NOT NULL AFTER `status`");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('blog', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `blog` DROP COLUMN `comment_count`");
		});
	}

}