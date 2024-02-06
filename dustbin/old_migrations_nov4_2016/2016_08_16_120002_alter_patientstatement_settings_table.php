<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientstatementSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patientstatement_settings', function(Blueprint $table)
		{
			$table->enum('cpt_shortdesc', ['Claim', 'Lineitem'])->after('paymentmessage_3');
			$table->integer('primary_dx')->after('cpt_shortdesc');
			$table->integer('insserviceline')->after('primary_dx');
			$table->integer('patserviceline')->after('insserviceline');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patientstatement_settings', function(Blueprint $table)
		{
			$table->dropColumn(['cpt_shortdesc', 'primary_dx', 'insserviceline', 'patserviceline']);
		});
	}

}
