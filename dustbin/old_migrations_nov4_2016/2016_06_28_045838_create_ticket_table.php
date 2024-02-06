<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->string('name','100');	
			$table->string('email_id','100');	
			$table->string('title','100');
			$table->string('type','100');
			$table->enum('status', ['Open', 'Closed']);
			$table->enum('notification_sent', ['Yes', 'No']);
			$table->Integer('created_by');
			$table->Integer('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
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
		Schema::drop('ticket');
	}

}
