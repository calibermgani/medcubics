<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientNotesDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_notes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_notes` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;");
			DB::statement("ALTER TABLE `patient_notes` CHANGE `notes_type_id` `notes_type_id` BIGINT UNSIGNED NOT NULL;");
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
			DB::statement("ALTER TABLE `patient_notes` CHANGE `notes_type_id` `notes_type_id` INT NOT NULL;");
			DB::statement("ALTER TABLE `patient_notes` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

}
