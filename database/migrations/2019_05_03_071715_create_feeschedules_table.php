<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeeschedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feeschedules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('file_name', 100)->nullable();
			$table->string('choose_year', 10)->nullable();
			$table->string('conversion_factor', 100)->nullable();
			$table->string('percentage', 100)->nullable();
			$table->string('saved_file_name', 100)->nullable();
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
		Schema::drop('feeschedules');
	}

}
