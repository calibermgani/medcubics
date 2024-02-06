<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerPassword extends Migration {

	public function up()
	{
		Schema::table('customers', function ($table) 
		{
		    $table->string('password',50)->after('status');
		});	
	}

	public function down()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->dropColumn('password');
            
		});
	}

}
