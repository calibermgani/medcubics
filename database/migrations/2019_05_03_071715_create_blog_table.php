<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blog', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('user_id')->unsigned();
			$table->string('title', 150)->nullable();
			$table->text('description', 65535)->nullable();
			$table->enum('privacy', array('Private','Public','Group','User'));
			$table->string('user_list', 150)->nullable();
			$table->string('attachment', 100)->nullable();
			$table->string('url', 150)->nullable();
			$table->enum('status', array('Active','Inactive'));
			$table->integer('comment_count');
			$table->integer('up_count');
			$table->integer('down_count');
			$table->timestamps();
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
		Schema::drop('blog');
	}

}
