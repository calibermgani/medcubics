<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUseractivityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('useractivity', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->integer('userid');
                        $table->enum('action', ['add', 'edit', 'delete', 'export']);
                        $table->enum('usertype', ['medcubics', 'practice']);
                        $table->text('user_activity_msg');
                        $table->dateTime('activity_date');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('useractivity');
	}

}
