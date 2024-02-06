<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PatientsInsuranceAddInsuredphoneInsuredgender extends Migration {

	public function up()
	{
		Schema::table('patient_insurance', function($table)
		{
			$table->string('insured_phone',15)->after('relationship');
			$table->Enum('insured_gender', array('Male','Female','Others'))->after('insured_phone');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			$table->dropColumn('insured_phone');
			$table->dropColumn('insured_gender');
		});	
	}

}
