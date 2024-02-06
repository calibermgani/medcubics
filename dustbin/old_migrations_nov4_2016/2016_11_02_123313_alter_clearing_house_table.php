<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClearingHouseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clearing_house', function(Blueprint $table)
		{
			$table->Integer('ftp_port')->after('ftp_address');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('clearing_house', function(Blueprint $table)
		{
			$table->dropColumn('ftp_port');
		});
	}

}
