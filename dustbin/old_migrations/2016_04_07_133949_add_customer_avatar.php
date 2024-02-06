<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerAvatar extends Migration {

	
	public function up()
	{
		Schema::table('customers', function ($table) 
		{
		    $table->string('avatar_name',50)->after('status');
			$table->string('avatar_ext',50)->after('avatar_name');
		});	
	}

	
	public function down()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->dropColumn('avatar_name');
            $table->dropColumn('avatar_ext');
            
		});
	}

}
