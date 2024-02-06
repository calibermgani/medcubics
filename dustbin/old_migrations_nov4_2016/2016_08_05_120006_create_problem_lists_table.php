<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProblemListsTable extends Migration {

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
			$table->enum('priority', array('High','Miderate','low'));
			$table->text('description');
			$table->Integer('created_by'); 
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('problem_lists');
	}

}
