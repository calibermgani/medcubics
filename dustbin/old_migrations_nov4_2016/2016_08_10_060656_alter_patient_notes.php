<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientNotes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_notes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_notes` CHANGE `patient_notes_type` `patient_notes_type` ENUM('alert_notes','patient_notes','claim_notes','claim_denial_notes') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
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
			DB::statement("ALTER TABLE `patient_notes` CHANGE `patient_notes_type` `patient_notes_type` ENUM('alert_notes','patient_notes','claim_notes') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
		});
	}

}
