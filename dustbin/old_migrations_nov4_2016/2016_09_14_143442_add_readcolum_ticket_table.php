<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReadcolumTicketTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ticket', function(Blueprint $table)
		{
			$table->date('assigneddate')->after('assignedby');
			$table->Integer('read')->after('assigneddate');
			$table->dropColumn('type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ticket', function(Blueprint $table)
		{
			$table->dropColumn('assigneddate');
			$table->dropColumn('read');
			$table->string('type',100)->after('title');
			
		});
	}

}
