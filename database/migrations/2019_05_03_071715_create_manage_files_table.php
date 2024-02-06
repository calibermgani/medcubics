<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManageFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manage_files', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('source', 50)->nullable();
			$table->string('module', 50)->nullable();
			$table->bigInteger('record_id');
			$table->string('filename', 50)->nullable();
			$table->string('filepath', 150)->nullable();
			$table->enum('mode', array('Amazon','Local'));
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
		Schema::drop('manage_files');
	}

}
