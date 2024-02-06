<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomerRemovePassword extends Migration {

	public function up()
	{
		DB::statement("ALTER TABLE `customers` DROP `password`");
	}

	public function down()
	{
		DB::statement("ALTER TABLE `customers` DROP `password`");
	}

}
