<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTicketDetailsDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ticket_details', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `ticket_details` CHANGE `postedby_id` `postedby_id` BIGINT UNSIGNED NOT NULL;");
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
			DB::statement("ALTER TABLE `ticket_details` CHANGE `postedby_id` `postedby_id` INT NOT NULL;");
		});
	}

}
