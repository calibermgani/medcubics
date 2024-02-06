<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatsDetail extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{ 
            Schema::create('stats_detail',function(Blueprint $table)
			{
                $table->increments('id');
				$table->integer('user_id');
				$table->string('module_name',25);
				$table->integer('position');
				$table->integer('stats_id');
                $table->string('class_name',25);
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
            Schema::drop('stats_detail');
	}

}
