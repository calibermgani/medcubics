<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviderShortName extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('providers', function($table)
		{
			$table->string('short_name',255)->after('organization_name');
		});
	}

	
	public function down()
	{
		Schema::table('providers', function(Blueprint $table)
		{
			$table->dropColumn('short_name');
		});
	}

}
