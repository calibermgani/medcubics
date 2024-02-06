<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMedicalCategoryPatientInsuranceArchiveTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance_archive', function($table)
		{
			$table->integer('medical_secondary_code')->nullable()->after('insurance_id');
		});
	}

	
	public function down()
	{
		Schema::table('patient_insurance_archive', function(Blueprint $table)
		{
			$table->dropColumn('medical_secondary_code');
		});
	}

}
