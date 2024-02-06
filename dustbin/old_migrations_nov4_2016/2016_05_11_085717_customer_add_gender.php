<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CustomerAddGender extends Migration {

	public function up()
	{
		Schema::table('customers', function($table)
		{
			$table->Enum('gender',array('Male','Female','Other'))->after('email');
		});
	}

	public function down()
	{
		Schema::table('customers', function(Blueprint $table)
		{
			$table->dropColumn('gender');
		});
	}

}
