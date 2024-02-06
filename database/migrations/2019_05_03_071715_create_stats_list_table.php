<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatsListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stats_list', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 25)->nullable();
			$table->string('image_name', 25)->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
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
		Schema::drop('stats_list');
	}

}
