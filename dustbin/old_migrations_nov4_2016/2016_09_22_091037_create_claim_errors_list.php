<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimErrorsList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('claims', function($table)
		{
			$table->integer('no_of_issues')->after('status');
            $table->text('error_message')->nullable()->after('no_of_issues');	    
		});		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('claims', function($table)
		{
		    $table->dropColumn('no_of_issues');
		    $table->dropColumn('error_message');
		});
	}

}
