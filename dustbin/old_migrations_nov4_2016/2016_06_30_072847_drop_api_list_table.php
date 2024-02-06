<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropApiListTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('api_list', function(Blueprint $table)
		{
			Schema::dropIfExists('api_list');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('api_list', function(Blueprint $table)
		{
			Schema::dropIfExists('api_list');
		});
	}

}
