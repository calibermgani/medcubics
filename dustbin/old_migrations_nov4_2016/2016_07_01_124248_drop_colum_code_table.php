<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumCodeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('codes', function(Blueprint $table)
		{
			if (Schema::hasColumn('codes', 'stop_date'))
			{
				$table->dropColumn('stop_date');
			}	
			
			if (Schema::hasColumn('codes', 'notes'))
			{
				$table->dropColumn('notes');
			}

			if (Schema::hasColumn('codes', 'alert'))
			{
				$table->dropColumn('alert');
			}	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('codes', function(Blueprint $table)
		{
			$table->date('stop_date')->after('last_modified_date');
			$table->text('notes')->after('stop_date');
			$table->text('alert')->after('notes');
		});
	}
}