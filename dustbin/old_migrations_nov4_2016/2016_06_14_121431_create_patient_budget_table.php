<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientBudgetTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patient_budget', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->BigInteger('patient_id');
			$table->enum('plan', ['Weekly', 'Biweekly', 'Monthly', 'Bimonthly']);
			$table->decimal('budget_amt', 15,2);
			$table->date('statement_start_date');		
			$table->decimal('budget_balance', 15,2);
			$table->date('budget_period');		
			$table->string('budget_count','100');		
			$table->date('last_statement_sent_date')->nullable();		
			$table->Integer('created_by');
			$table->Integer('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->timestamp('deleted_at')->nullable();	
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('patient_budget');
	}

}
