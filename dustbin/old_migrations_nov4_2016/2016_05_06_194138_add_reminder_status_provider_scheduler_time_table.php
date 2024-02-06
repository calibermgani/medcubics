<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReminderStatusProviderSchedulerTimeTable extends Migration {

	public function up()
	{
		Schema::table('provider_scheduler_time', function($table)
		{
			 $table->enum('sms_reminder_status', array('Yes','No'))->default("No")->after('updated_by');
			 $table->enum('phone_reminder_status', array('Yes','No'))->default("No")->after('sms_reminder_status');
			 $table->enum('email_reminder_status', array('Yes','No'))->default("No")->after('phone_reminder_status');
		});		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('provider_scheduler_time', function(Blueprint $table)
		{
			$table->dropColumn('sms_reminder_status');
			$table->dropColumn('phone_reminder_status');
			$table->dropColumn('email_reminder_status');
		});
	}

}
