<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsurance extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance', function($table)
		{
			$table->enum('same_patient_address', array('no','yes'))->after('insurancetype_id');
		});
	}

	
	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			$table->dropColumn('same_patient_address');
		});
	}

}
