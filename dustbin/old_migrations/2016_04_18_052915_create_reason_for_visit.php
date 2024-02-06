<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReasonForVisit extends Migration 
{

	public function up()
	{
		Schema::create('reasons',function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('reason',255);
			$table->enum('status', ['Active', 'Inactive']);
			$table->timestamp('created_at');
			$table->timestamp('updated_at');
			$table->integer('created_by');
			$table->integer('updated_by');
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
		Schema::drop('reasons');
	}

}
