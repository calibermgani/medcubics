<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profile_events', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('event_id');
			$table->string('title', 100)->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->time('start_time')->nullable();
			$table->time('end_time')->nullable();
			$table->string('description')->nullable();
			$table->string('participants', 50)->nullable();
			$table->enum('reminder_type', array('one-time','repeat'))->nullable();
			$table->enum('reminder_type_repeat', array('on','never'))->nullable();
			$table->enum('repeated_by', array('Daily','Weekly','Monthly','yearly'))->nullable();
			$table->string('repeated_day')->nullable();
			$table->string('reminder_days')->nullable();
			$table->date('reminder_date')->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->softDeletes();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profile_events');
	}

}
