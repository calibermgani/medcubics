<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerAddressflagTable extends Migration {
	public function up()
	{
			DB::statement("ALTER TABLE `addressflag` CHANGE `type` `type` ENUM('patients','practice','facility','provider','insurance','employer','adminuser','customer','customerusers')");
	}

	public function down()
	{
			DB::statement("ALTER TABLE `addressflag` CHANGE `type` `type` ENUM('patients','practice','facility','provider','insurance','employer','adminuser')");
	}

}
