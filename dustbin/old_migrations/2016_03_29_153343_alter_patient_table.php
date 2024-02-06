<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patients', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patients` ADD `eligibility_verification` ENUM('None','Active','Inactive','Error') NOT NULL AFTER `medical_chart_no`;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patients', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patients` DROP COLUMN `eligibility_verification`");
		});
	}

}
