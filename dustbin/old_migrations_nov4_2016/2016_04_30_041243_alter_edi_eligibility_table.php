<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibilityTable extends Migration {

	public function up()
	{
		Schema::table('edi_eligibility', function($table)
		{
			$table->enum('category', array('Primary','Secondary','Tertiary','Workerscomp','Liability','Others'))->after('error_message');
		});		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			$table->dropColumn('category');
		});
	}

}
