<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProviderHostDetailsRemove extends Migration {

	public function up()
	{
		
		DB::statement("ALTER TABLE `providers` DROP `host_username`, DROP `host_password`, DROP `host_ip`");
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE `providers` DROP `host_username`, DROP `host_password`, DROP `host_ip`");
		
	}

}
