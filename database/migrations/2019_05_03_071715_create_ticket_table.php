<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
			$table->increments('id');
			$table->bigInteger('ticket_id');
			$table->string('name', 100)->nullable();
			$table->string('email_id', 100)->nullable();
			$table->string('title', 100)->nullable();
			$table->enum('status', array('Open','Closed'));
			$table->enum('notification_sent', array('Yes','No'));
			$table->bigInteger('assigned');
			$table->bigInteger('assignedby');
			$table->date('assigneddate');
			$table->integer('read');
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
		Schema::drop('ticket');
	}

}
