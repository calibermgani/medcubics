<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPatientAuthorizations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_authorizations', function(Blueprint $table)
		{
			$table->decimal('alert_amt', 10,2)->after('amt_remaining');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_authorizations', function(Blueprint $table)
		{
			$table->dropColumn('alert_amt');
		});
	}

}
