<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionnariesOptionTable extends Migration 
{    
	/*** Creating a questionnaries_option table ***/
	public function up()
	{
		Schema::create('questionnaries_option', function($table) 
		{
			$table->bigIncrements('id')->unsigned();
			$table->bigInteger('template_id')->unsigned();
			$table->bigInteger('questionnaries_template_id')->unsigned();
			$table->string('option',255);
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->timestamp('deleted_at')->nullable();
		});
	}
	
	/*** Drop questionnaries_option table ***/
	public function down()
	{
		Schema::drop('questionnaries_option');
	}
}