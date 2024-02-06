<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionnariesAnswerTable extends Migration 
{    
	/*** Creating a questionnaries answer table ***/
	public function up()
	{
		Schema::create('questionnaries_answer', function($table) 
		{
			$table->bigIncrements('id')->unsigned();
			$table->bigInteger('template_id')->unsigned();
			$table->bigInteger('questionnaries_template_id')->unsigned();
			$table->bigInteger('questionnaries_option_id')->unsigned();
			$table->longText('answer');
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->timestamp('deleted_at')->nullable();
		});
	}
	
	/*** Drop questionnaries answer table ***/
	public function down()
	{
		Schema::drop('questionnaries_answer');
	}
}