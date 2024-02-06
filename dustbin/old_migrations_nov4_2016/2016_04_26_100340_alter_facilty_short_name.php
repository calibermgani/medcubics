<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFaciltyShortName extends Migration {

	public function up()
	{
		Schema::table('facilities', function($table)
		{
			$table->string('short_name',255)->after('facility_name');
		});
	}

	public function down()
	{
		Schema::table('facilities', function(Blueprint $table)
		{
			$table->dropColumn('short_name');
		});
	}

}
