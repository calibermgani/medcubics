<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogUrlTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blog_url', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('blog_id');
			$table->string('url', 100)->nullable();
			$table->string('image', 200)->nullable();
			$table->string('title', 200)->nullable();
			$table->string('description', 250)->nullable();
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
		Schema::drop('blog_url');
	}

}
