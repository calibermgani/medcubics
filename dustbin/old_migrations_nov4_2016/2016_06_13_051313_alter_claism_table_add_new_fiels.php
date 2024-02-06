<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClaismTableAddNewFiels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('claims', function(Blueprint $table)
		{
			$table->decimal('total_adjusted', 10, 2);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('claims', function(Blueprint $table)
		{
			$table->dropColumn('total_adjusted');
		});
	}

}
