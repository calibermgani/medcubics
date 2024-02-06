<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsuranceTable extends Migration {

	public function up()
	{
		Schema::table('patient_insurance', function($table)
		{
			DB::statement('ALTER TABLE `patient_insurance` CHANGE `document_save_id` `document_save_id` BIGINT(30) NOT NULL');
		});
	}

	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `patient_insurance` CHANGE `document_save_id` `document_save_id` INT(11) NOT NULL');
		});
	}

}
