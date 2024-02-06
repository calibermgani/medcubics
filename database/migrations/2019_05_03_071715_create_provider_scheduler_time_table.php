<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProviderSchedulerTimeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('provider_scheduler_time', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('provider_scheduler_id')->unsigned();
			$table->bigInteger('facility_id');
			$table->bigInteger('provider_id')->unsigned();
			$table->date('schedule_date');
			$table->string('day', 10)->nullable();
			$table->string('from_time', 10)->nullable();
			$table->string('to_time', 10)->nullable();
			$table->enum('schedule_type', array('Daily','Weekly','Monthly'));
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->enum('sms_reminder_status', array('Yes','No'))->default('No');
			$table->enum('phone_reminder_status', array('Yes','No'))->default('No');
			$table->enum('email_reminder_status', array('Yes','No'))->default('No');
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
		Schema::drop('provider_scheduler_time');
	}

}
