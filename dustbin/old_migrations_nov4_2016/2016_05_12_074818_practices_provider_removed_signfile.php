<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PracticesProviderRemovedSignfile extends Migration {

	
	public function up()
	{
		DB::statement("ALTER TABLE `providers` DROP `sign_file`");
	}

	public function down()
	{
		DB::statement("ALTER TABLE `providers` DROP `sign_file`");	
	}

}
