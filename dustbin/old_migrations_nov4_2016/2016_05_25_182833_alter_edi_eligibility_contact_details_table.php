<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibilityContactDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility_contact_details', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `edi_eligibility_contact_details` CHANGE `details_for` `details_for` ENUM("insurance_sp","medicare") NOT NULL DEFAULT "insurance_sp" ');
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
			DB::statement('ALTER TABLE `edi_eligibility_contact_details` CHANGE `details_for` `details_for` ENUM("insurance_sp") NOT NULL DEFAULT "insurance_sp" ');
		});
	}
}
