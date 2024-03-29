<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIcdcategoryDatatypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('icdcategory', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `icdcategory` CHANGE `created_by` `created_by` BIGINT UNSIGNED NOT NULL;");
			DB::statement("ALTER TABLE `icdcategory` CHANGE `updated_by` `updated_by` BIGINT UNSIGNED NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('icdcategory', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `icdcategory` CHANGE `created_by` `created_by` INT NOT NULL;");
			DB::statement("ALTER TABLE `icdcategory` CHANGE `updated_by` `updated_by` INT NOT NULL;");
		});
	}

}
