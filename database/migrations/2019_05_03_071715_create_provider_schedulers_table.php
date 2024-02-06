<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProviderSchedulersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('provider_schedulers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('facility_id');
			$table->bigInteger('provider_id')->unsigned();
			$table->date('start_date');
			$table->date('end_date');
			$table->integer('no_of_occurrence');
			$table->enum('end_date_option', array('on','after','never'));
			$table->enum('schedule_type', array('Daily','Weekly','Monthly'));
			$table->integer('repeat_every');
			$table->string('weekly_available_days', 150)->nullable();
			$table->enum('monthly_visit_type', array('date','day','week'));
			$table->integer('monthly_visit_type_date');
			$table->integer('monthly_visit_type_day_week');
			$table->string('monthly_visit_type_day_dayname')->nullable();
			$table->integer('monthly_visit_type_week');
			$table->string('monday_selected_times', 120)->nullable();
			$table->string('tuesday_selected_times', 120)->nullable();
			$table->string('wednesday_selected_times', 120)->nullable();
			$table->string('thursday_selected_times', 120)->nullable();
			$table->string('friday_selected_times', 120)->nullable();
			$table->string('saturday_selected_times', 120)->nullable();
			$table->string('sunday_selected_times', 120)->nullable();
			$table->enum('provider_reminder_sms', array('on','off'));
			$table->enum('provider_reminder_phone', array('on','off'));
			$table->enum('provider_reminder_email', array('on','off'));
			$table->enum('patient_reminder_sms', array('on','off'));
			$table->enum('patient_reminder_phone', array('on','off'));
			$table->enum('patient_reminder_email', array('on','off'));
			$table->text('notes', 65535)->nullable();
			$table->enum('status', array('active','inactive'));
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->timestamps();
			$table->softDeletes();
			$table->string('appointment_slot', 50);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('provider_schedulers');
	}

}
