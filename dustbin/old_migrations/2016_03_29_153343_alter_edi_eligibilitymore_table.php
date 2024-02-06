<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibilityMoreTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `edi_eligibility` ADD `temp_patient_id` BIGINT(20) NOT NULL AFTER `error_message`;');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `edi_eligibility` DROP COLUMN `temp_patient_id`");
		});
	}

}
