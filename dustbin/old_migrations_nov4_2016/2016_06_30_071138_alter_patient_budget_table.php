<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientBudgetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_budget', function(Blueprint $table)
		{
			if (Schema::hasColumn('patient_budget', 'budget_period'))
			{
				$table->dropColumn('budget_period');
			}	
			
			if (Schema::hasColumn('patient_budget', 'budget_count'))
			{
				$table->dropColumn('budget_count');
			}	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_budget', function(Blueprint $table)
		{
			$table->date('budget_period')->after('budget_balance');
			$table->string('budget_count',100)->after('budget_period');
		});
	}

}
