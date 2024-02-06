<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeschedulesDetail extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{ 
		Schema::drop('feeschedules');
            Schema::create('feeschedules',function(Blueprint $table)
			{
                $table->increments('id');
				$table->string('file_name',100);
				$table->string('choose_year',10);
				$table->string('conversion_factor',100);
				$table->string('percentage',100);
				$table->string('saved_file_name',100);
				$table->timestamp('created_at');
				$table->timestamp('updated_at');
				$table->integer('created_by');
                $table->integer('updated_by');
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
            Schema::drop('feeschedules');
	}

}
