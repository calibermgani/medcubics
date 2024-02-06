<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserLoginHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_login_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('session_id')->nullable();
			$table->string('ip_address', 25)->nullable();
			$table->string('logitude', 25)->nullable();
			$table->string('latitude', 25)->nullable();
			$table->string('browser_name', 25)->nullable();
			$table->string('login_time', 25)->nullable();
			$table->string('logout_time', 25)->nullable();
			$table->bigInteger('user_id')->unsigned();
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
		Schema::drop('user_login_histories');
	}

}
