<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMedicalSecondaryTable extends Migration {
	/**
	* Run the migrations.
	*
	* @return void
	*/
	public function up()
	{
		/**
		* Table: medical_secondary
		*/
		Schema::dropIfExists('medical_secandary');
		Schema::dropIfExists('medical_secondary');
		Schema::create('medical_secondary', function($table){
			$table->bigIncrements('id')->unsigned();
			$table->integer('code');
			$table->string('description', 255);
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->timestamp('deleted_at')->nullable();
		});
	}
	/**
	* Reverse the migrations.
	*
	* @return void
	*/
	public function down()
	{
		Schema::drop('medical_secondary');
	}

}