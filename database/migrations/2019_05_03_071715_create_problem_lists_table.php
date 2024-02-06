<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProblemListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('problem_lists', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->bigInteger('assign_user_id')->unsigned();
			$table->date('fllowup_date');
			$table->enum('status', array('Assigned','Inprocess','Completed'));
			$table->enum('priority', array('High','Moderate','Low'));
			$table->text('description', 65535)->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('problem_lists');
	}

}
