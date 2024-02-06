<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPracticeApiListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('practice_api_list', function(Blueprint $table)
		{
			$table->dropColumn('practice_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('practice_api_list', function(Blueprint $table)
		{
			$table->integer('practice_id')->after('id');
		});
	}

}
