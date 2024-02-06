<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerProvider extends Migration {

	public function up()
	{
		Schema::table('providers', function($table)
		{
			$table->string('host_username',50)->after('practice_db_provider_id');
			$table->string('host_password',100)->after('host_username');
			$table->string('host_ip',50)->after('host_password');
		});
	}

	public function down()
	{
		Schema::table('providers', function(Blueprint $table)
		{
			$table->dropColumn('host_username');
			$table->dropColumn('host_password');
			$table->dropColumn('host_ip');
		});
	}

}
