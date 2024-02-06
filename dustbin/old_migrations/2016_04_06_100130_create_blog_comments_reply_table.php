<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlogCommentsReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blog_comments_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('blog_id');
			$table->integer('comment_id');
			$table->string('comments',250);
			$table->integer('up_count');
			$table->integer('down_count');
			$table->timestamp('created_at');
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('blog_comments_reply');
	}

}
