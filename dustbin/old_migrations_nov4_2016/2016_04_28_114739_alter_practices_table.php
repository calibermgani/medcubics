<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPracticesTable extends Migration {

	public function up()
	{
		Schema::table('practices', function($table)
		{
			$table->string('api_ids',255)->after('practice_db_id');
		});
	}

	
	public function down()
	{
		Schema::table('practices', function(Blueprint $table)
		{
			$table->dropColumn('api_ids');
		});
	}

}
