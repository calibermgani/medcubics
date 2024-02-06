<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsOnClaimdosCptDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` ADD `patient_id` BIGINT(20) NOT NULL AFTER `updated_at`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `claimdoscptdetails` ADD `patient_id` BIGINT(20) NOT NULL AFTER `updated_at`;");
	}

}
