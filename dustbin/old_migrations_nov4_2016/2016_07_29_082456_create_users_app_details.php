<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersAppDetails extends Migration {

	public function up()
	{
		 Schema::create('users_app_details', function($table) {
			$table->BigIncrements('id');
			$table->BigInteger('user_id');
			$table->string('mobile_id','150');
			$table->string('authentication_id','150');
			$table->timestamp('deleted_at')->nullable();
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
		});
	}

	public function down()
	{
		Schema::drop('users_app_details');
	}

}
