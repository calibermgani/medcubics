<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPracticeApiConfigsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('practice_api_configs', function(Blueprint $table)
		{
			$table->string('category',100)->after('api_name');
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
			$table->dropColumn('category');
		});
	}

}
