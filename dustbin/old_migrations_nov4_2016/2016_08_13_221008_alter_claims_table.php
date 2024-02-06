<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('claims', function(Blueprint $table)
		{
			$table->string('claim_armanagement_status',320)->after('claim_submit_count');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('claims', function(Blueprint $table)
		{
			$table->dropColumn('claim_armanagement_status');
		});
	}

}
