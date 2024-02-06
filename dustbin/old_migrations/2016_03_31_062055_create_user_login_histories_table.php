<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLoginHistoriesTable extends Migration 
{
	public function up()
	{
		Schema::create('user_login_histories', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('ip_address',25);
			$table->string('logitude',25);
			$table->string('latitude',25);
			$table->string('browser_name',25);
			$table->string('mac_address',25);
			$table->string('login_time',25);
			$table->string('logout_time',25);
			$table->string('user_id',25);
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->timestamps();
		});
	}

	
	public function down()
	{
		Schema::drop('user_login_histories');
	}

}
