<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageTypeToTicketDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ticket_details', function(Blueprint $table)
		{
			$table->string('image_type', 50)->after('attach_details');
			$table->dropColumn('created_by');
			$table->dropColumn('updated_by');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ticket_details', function(Blueprint $table)
		{
			$table->dropColumn('image_type');
			$table->integer('created_by')->after('postedby_id');
			$table->integer('updated_by')->after('created_by');
		});
	}

}
