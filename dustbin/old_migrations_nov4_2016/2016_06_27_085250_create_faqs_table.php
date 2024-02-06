<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsTable extends Migration {

	/*** Table create ***/
	 
	public function up()
	{
		Schema::create('faqs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('question', 250);
			$table->text('answer');
			$table->enum('status', array('Active','Inactive'));
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->timestamp('deleted_at')->nullable();
		});
	}

	/*** table roll back ***/
	public function down()
	{
		Schema::drop('faqs');
	}

}
