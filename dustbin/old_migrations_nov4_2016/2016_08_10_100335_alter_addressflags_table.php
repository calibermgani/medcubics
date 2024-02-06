<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddressflagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addressflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `addressflag` CHANGE `type` `type` ENUM('patients','practice','facility','provider','insurance','employer','adminuser','customer','customerusers','patientstatementsettings') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addressflag', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `addressflag` CHANGE `type` `type` ENUM('patients','practice','facility','provider','insurance','employer','adminuser','customer','customerusers') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL");
		});
	}

}
