<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsuranceRemovedClaimtype extends Migration {

	public function up()
	{
		DB::statement("ALTER TABLE `insurances` DROP `claimtype`");
	}

	public function down()
	{
		DB::statement("ALTER TABLE `insurances` DROP `claimtype`");
	}

}
