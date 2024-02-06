<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssignTickethistoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('assign_tickethistory', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('ticket_id');
			$table->bigInteger('assigned')->unsigned();
			$table->bigInteger('assigned_by')->unsigned();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('assign_tickethistory');
	}

}
