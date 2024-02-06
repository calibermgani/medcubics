<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatients extends Migration {

	public function up()
	{
		Schema::table('patients', function($table)
		{
			$table->string('organization_name',60)->after('marital_status');
			$table->string('occupation',60)->after('organization_name');
		});
	}

	
	public function down()
	{
		Schema::table('patients', function(Blueprint $table)
		{
			$table->dropColumn('organization_name');
			$table->dropColumn('occupation');
		});
	}

}
