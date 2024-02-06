<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticeApiListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('practice_api_list', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('api_id')->unsigned();
			$table->enum('status', array('Active','Inactive'));
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
		Schema::drop('practice_api_list');
	}

}
