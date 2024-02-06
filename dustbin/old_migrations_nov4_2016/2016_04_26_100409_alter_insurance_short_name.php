<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInsuranceShortName extends Migration {

	public function up()
	{
		Schema::table('insurances', function($table)
		{
			$table->string('short_name',255)->after('insurance_name');
		});
	}

	public function down()
	{
		Schema::table('insurances', function(Blueprint $table)
		{
			$table->dropColumn('short_name');
		});
	}

}
