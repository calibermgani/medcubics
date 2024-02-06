<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserDetails extends Migration {

	
	public function up()
	{
		Schema::table('users', function($table)
		{
			$table->enum('useraccess', array('app','web'));
			$table->integer('practice_access_id');
			$table->integer('facility_access_id');
		});	
	}

	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('useraccess');
			$table->dropColumn('practice_access_id');
			$table->dropColumn('facility_access_id');
		});
	}

}
