<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPracticeApiConfigTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('practice_api_configs', function(Blueprint $table)
		{
			if(Schema::hasColumn('practice_api_configs', 'token'))  
			{}
			else
			{
				$table->string('token',50)->after('usps_user_id');
			}
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('practice_api_configs', function(Blueprint $table)
		{
			$table->dropColumn('token');
		});
	}

}
