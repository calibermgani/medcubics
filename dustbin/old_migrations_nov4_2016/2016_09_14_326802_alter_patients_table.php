<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patients', function(Blueprint $table)
		{
			$table->integer('demo_percentage')->after('percentage');
			$table->integer('ins_percentage')->after('demo_percentage');
			$table->integer('contact_percentage')->after('ins_percentage');
			$table->integer('auth_percentage')->after('contact_percentage');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patients', function(Blueprint $table)
		{
			$table->dropColumn('demo_percentage');
			$table->dropColumn('ins_percentage');
			$table->dropColumn('contact_percentage');
			$table->dropColumn('auth_percentage');
		});
	}

}
