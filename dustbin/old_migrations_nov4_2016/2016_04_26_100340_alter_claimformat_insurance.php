<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClaimFormatInsurance extends Migration {

	public function up()
	{
		Schema::table('insurances', function($table)
		{
			DB::statement('ALTER TABLE insurances CHANGE `claimformat` `claimformat` INT NOT NULL');
		});
	}

	public function down()
	{
		Schema::table('insurances', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE insurances CHANGE `claimformat` `claimformat` ENUM('Professional','Institutional','Dental') NOT NULL;");
		});
	}

}
