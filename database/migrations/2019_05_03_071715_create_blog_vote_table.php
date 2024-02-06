<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogVoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blog_vote', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('blog_id');
			$table->bigInteger('user_id')->unsigned();
			$table->integer('up');
			$table->integer('down');
			$table->dateTime('datetime');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('blog_vote');
	}

}
