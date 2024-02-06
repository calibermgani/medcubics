<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_details', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->BigInteger('ticket_id');
			$table->text('description');
			$table->string('attach_details','150');
			$table->enum('posted_by', ['User', 'Admin']);			
			$table->Integer('postedby_id');
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
		Schema::drop('ticket_details');
	}

}
