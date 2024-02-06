<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClearingHouse extends Migration {

	public function up()
	{
		Schema::table('clearing_house', function(Blueprint $table)
		{
			$table->string('edi_report_folder', 200)->after('ftp_folder');
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
			$table->dropColumn('edi_report_folder');
		});
	}

}
