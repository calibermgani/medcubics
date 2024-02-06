<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
			$table->bigInteger('user_id');
			$table->bigInteger('blog_id');
			$table->bigInteger('comment_id');
			$table->string('comments', 250)->nullable();
			$table->integer('up_count');
			$table->integer('down_count');
			$table->dateTime('created_at')->nullable();
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
