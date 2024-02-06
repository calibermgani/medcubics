<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PracticesAddHostDetails extends Migration {
	public function up()
	{
		Schema::table('practices', function($table)
		{
			$table->string('hostname',15)->after('email');
			$table->string('hostpassword',200)->after('hostname');
			$table->string('ipaddress',50)->after('hostpassword');
		});
	}

	
	public function down()
	{
		Schema::table('practices', function(Blueprint $table)
		{
			$table->dropColumn('hostname');
			$table->dropColumn('hostpassword');
			$table->dropColumn('ipaddress');
		});	
	}

}
