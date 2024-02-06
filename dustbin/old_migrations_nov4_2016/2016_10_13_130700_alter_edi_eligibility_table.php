<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			$table->string('policy_id', 20)->after('insurance_id');
			$table->dropColumn('category');
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
			$table->enum('category', array('Primary','Secondary','Tertiary','Workerscomp','Liability','Others'))->after('error_message');
			$table->dropColumn('policy_id');
		});
	}

}
