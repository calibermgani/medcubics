<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdjustmentReasonsTable extends Migration {

	public function up()
	{
		Schema::create('adjustment_reasons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('adjustment_type', array('Insurance','Patient'));
			$table->text('adjustment_reason');
			$table->enum('status', array('Active','Inactive'));
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->timestamp('deleted_at')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('adjustment_reasons');
	}

}
