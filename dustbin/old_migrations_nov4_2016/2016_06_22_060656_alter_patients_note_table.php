<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientsNoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_notes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE patient_notes CHANGE `patient_notes_type` `patient_notes_type` ENUM('alert_notes','patient_notes','claim_notes') NOT NULL");
			$table->bigInteger('claim_id')->after('patient_notes_type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_notes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE patient_notes CHANGE `patient_notes_type` `patient_notes_type` ENUM('alert_notes','insurance_notes','patient_notes','billing_notes') NOT NULL");
			$table->dropColumn('claim_id');
		});
	}

}
