<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatsList extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('stats_list',function(Blueprint $table){
                $table->increments('id');
				$table->string('name',25);
                $table->string('class_name',25);
                $table->integer('created_by');
                $table->integer('updated_by');
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
            Schema::drop('stats_list');
	}

}
