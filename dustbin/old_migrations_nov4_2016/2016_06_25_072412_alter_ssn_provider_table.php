<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSsnProviderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('providers', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `providers` CHANGE `ssn` `ssn` INT NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `etin_type_number` `etin_type_number` INT NOT NULL;");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('providers', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `providers` CHANGE `ssn` `ssn` VARCHAR(20) NOT NULL");
			DB::statement("ALTER TABLE `providers` CHANGE `etin_type_number` `etin_type_number` VARCHAR(20) NOT NULL;");
		});
	}

}
