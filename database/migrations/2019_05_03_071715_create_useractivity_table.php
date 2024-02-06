<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

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
			$table->bigInteger('userid')->unsigned();
			$table->enum('action', array('add','edit','delete','export'));
			$table->string('url', 200)->nullable();
			$table->string('main_directory', 150)->nullable();
			$table->string('module', 70)->nullable();
			$table->enum('usertype', array('medcubics','practice'));
			$table->text('user_activity_msg', 65535)->nullable();
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
