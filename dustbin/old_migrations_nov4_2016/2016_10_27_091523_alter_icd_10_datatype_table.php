<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIcd10DatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('icd_10', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `icd_10` CHANGE `created_by` `created_by` BIGINT NOT NULL DEFAULT '1';");
			DB::statement("ALTER TABLE `icd_10` CHANGE `updated_by` `updated_by` BIGINT NULL DEFAULT '0';");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('icd_10', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `icd_10` CHANGE `created_by` `created_by` INT NOT NULL DEFAULT '1';");
			DB::statement("ALTER TABLE `icd_10` CHANGE `updated_by` `updated_by` INT NULL DEFAULT '0';");
		});
	}

}
