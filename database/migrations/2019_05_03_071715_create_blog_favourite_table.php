<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogFavouriteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blog_favourite', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('blog_id');
			$table->bigInteger('user_id')->unsigned();
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
		Schema::drop('blog_favourite');
	}

}
