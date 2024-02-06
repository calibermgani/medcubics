<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSsnPatientInsuranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance` CHANGE `insured_ssn` `insured_ssn` INT NOT NULL;");
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
			DB::statement("ALTER TABLE `patient_insurance` CHANGE `insured_ssn` `insured_ssn` VARCHAR(20) NOT NULL;");
		});
	}

}
