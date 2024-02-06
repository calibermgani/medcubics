<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAppDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users_app_details', function(Blueprint $table)
		{
			$table->timestamp('last_login_time')->default("0000-00-00 00:00:00")->after('authentication_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users_app_details', function(Blueprint $table)
		{
			$table->dropColumn('last_login_time');
		});
	}

}
