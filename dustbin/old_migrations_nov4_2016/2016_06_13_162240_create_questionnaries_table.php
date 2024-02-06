<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQuestionnariesTable extends Migration 
{    
	/*** Creating a questionnaries table ***/
	public function up()
	{
		Schema::create('questionnaries', function($table) 
		{
			$table->bigIncrements('id')->unsigned();
			$table->bigInteger('facility_id')->unsigned();
			$table->bigInteger('provider_id')->unsigned();
			$table->bigInteger('template_id')->unsigned();
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->timestamp('deleted_at')->nullable();
		});
	}
	
	/*** Drop questionnaries table ***/
	public function down()
	{
		Schema::drop('questionnaries');
	}
}