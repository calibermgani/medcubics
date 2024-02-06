<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClaimFormatFacility extends Migration {

	public function up()
	{
		Schema::table('facilities', function($table)
		{
			DB::statement('ALTER TABLE facilities CHANGE `claim_format` `claim_format` INT NOT NULL');
		});
	}

	public function down()
	{
		Schema::table('facilities', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE facilities CHANGE `claim_format` `claim_format` ENUM('Professional','Institutional','Dental') NOT NULL");
		});
	}
}
