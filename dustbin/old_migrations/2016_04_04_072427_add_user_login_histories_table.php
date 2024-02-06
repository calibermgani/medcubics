<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserLoginHistoriesTable extends Migration {

	public function up()
	{
		Schema::table('user_login_histories', function(Blueprint $table)
		{
			$table->softDeletes()->after('updated_at');
		});	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_login_histories', function(Blueprint $table)
		{
			$table->dropColumn('deleted_at');
		});
	}

}
