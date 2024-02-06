<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibilityInsuranceDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `edi_eligibility_insurance` CHANGE `id` `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT;");
			DB::statement("ALTER TABLE `edi_eligibility_insurance` CHANGE `edi_eligibility_id` `edi_eligibility_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('edi_eligibility_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `edi_eligibility_insurance` CHANGE `id` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT;");
			DB::statement("ALTER TABLE `edi_eligibility_insurance` CHANGE `edi_eligibility_id` `edi_eligibility_id` INT NOT NULL;");
		});
	}

}
