<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTable extends Migration {

	
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->enum('is_logged_in',['0','1'])->default(0)->after('googleplus');
			$table->string('avatar_name',50)->after('is_logged_in');
			$table->string('avatar_ext',50)->after('avatar_name');
		});	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('is_logged_in');
            $table->dropColumn('avatar_name');
            $table->dropColumn('avatar_ext');
		});
	}

}
