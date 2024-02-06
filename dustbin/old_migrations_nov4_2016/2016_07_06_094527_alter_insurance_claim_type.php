<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInsuranceClaimType extends Migration {

	public function up()
	{
		Schema::table('insurances', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `insurances` DROP `claimformat`");
			DB::statement("ALTER TABLE `insurances` ADD `claimtype` ENUM('Electronic','Paper') NOT NULL AFTER `claim_fax`");
		});
	}

	public function down()
	{
		Schema::table('insurances', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `insurances` ADD `claimformat` ENUM('Electronic','Paper') NOT NULL AFTER `claim_fax`;");
		});
	}

}
