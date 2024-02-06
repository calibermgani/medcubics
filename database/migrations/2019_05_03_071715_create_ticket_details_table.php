<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
			$table->increments('id');
			$table->bigInteger('ticket_id');
			$table->text('description', 65535);
			$table->string('attach_details', 150);
			$table->string('image_type', 50);
			$table->enum('posted_by', array('User','Admin'));
			$table->bigInteger('postedby_id')->unsigned();
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
		Schema::drop('ticket_details');
	}

}
