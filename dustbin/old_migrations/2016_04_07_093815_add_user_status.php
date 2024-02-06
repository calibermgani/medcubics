<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserStatus extends Migration {

	public function up()
	{
		Schema::table('users', function ($table) 
		{
		    $table->enum('status',['Active','Inactive'])->default('Active')->after('designation');
		});	
	}

	
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('status');
            
		});
	}

}
