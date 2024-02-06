<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerFirstLastName extends Migration {

	public function up()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->string('lastname',50)->after('email');
			$table->string('firstname',50)->after('lastname');
		});
	}

	public function down()
	{
		
	}

}
