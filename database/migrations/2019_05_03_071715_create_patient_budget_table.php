<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
			$table->increments('id');
			$table->bigInteger('patient_id')->unsigned();
			$table->enum('plan', array('Weekly','Biweekly','Monthly','Bimonthly'));
			$table->decimal('budget_amt', 15);
			$table->date('statement_start_date');
			$table->decimal('budget_balance', 15);
			$table->date('last_statement_sent_date')->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->timestamps();
			$table->softDeletes();
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
