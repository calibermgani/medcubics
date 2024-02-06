<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
			$table->bigIncrements('id');
			$table->string('source', 50);
			$table->string('module', 50);
			$table->bigInteger('record_id');
			$table->string('filename', 50);
			$table->string('filepath', 150);
			$table->enum('mode', ['Amazon', 'Local']);
			$table->integer('created_by');
			$table->integer('updated_by');
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
