<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogreplycommentsVoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blogreplycomments_vote', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('parentcomment_id');
			$table->integer('comment_id');
			$table->integer('up');
			$table->integer('down');
			$table->timestamp('datetime');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('blogreplycomments_vote');
	}

}
