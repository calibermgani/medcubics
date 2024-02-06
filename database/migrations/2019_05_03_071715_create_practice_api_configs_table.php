<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticeApiConfigsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('practice_api_configs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('api_for', 50)->nullable();
			$table->string('api_name', 50)->nullable();
			$table->string('api_username', 120)->nullable();
			$table->string('api_password', 120)->nullable();
			$table->string('category', 50)->nullable();
			$table->string('usps_user_id', 50)->nullable();
			$table->string('token', 50)->nullable();
			$table->string('host', 20)->nullable();
			$table->string('port', 20)->nullable();
			$table->enum('api_status', array('Active','Inactive'));
			$table->text('url', 65535);
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
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
		Schema::drop('practice_api_configs');
	}

}
