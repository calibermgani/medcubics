<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnEdiEligibilityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			$table->date('plan_begin_date')->after('plan_name');
			$table->date('plan_end_date')->after('plan_begin_date');
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
			$table->dropColumn('plan_begin_date');
			$table->dropColumn('plan_end_date');
		});
	}

}
