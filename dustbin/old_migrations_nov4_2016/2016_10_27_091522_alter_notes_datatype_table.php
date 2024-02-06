<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNotesDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('notes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `notes` CHANGE `notes_type_id` `notes_type_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('notes', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `notes` CHANGE `notes_type_id` `notes_type_id` INT NOT NULL;");
		});
	}

}
