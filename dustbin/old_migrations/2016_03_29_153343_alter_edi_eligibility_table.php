<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `edi_eligibility` ADD `error_message` VARCHAR(250) NOT NULL AFTER `service_type`');
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
			DB::statement("ALTER TABLE `edi_eligibility` DROP COLUMN `error_message`");
		});
	}

}
