<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClaimOtherDetailsDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('claim_other_details', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `claim_other_details` CHANGE `patient_id` `patient_id` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('claim_other_details', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `claim_other_details` CHANGE `patient_id` `patient_id` INT NOT NULL;");
		});
	}

}
