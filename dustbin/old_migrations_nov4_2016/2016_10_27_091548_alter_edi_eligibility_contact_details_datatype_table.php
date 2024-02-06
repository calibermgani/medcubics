<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibilityContactDetailsDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility_contact_details', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `edi_eligibility_contact_details` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('edi_eligibility_contact_details', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `edi_eligibility_contact_details` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;");
		});
	}

}
