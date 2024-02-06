<?php

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
            
        /**
         * Table: provider_schedulers
         */
        Schema::create('provider_schedulers', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('facility_id');
                $table->bigInteger('provider_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('no_of_occurrence');
                $table->enum('end_date_option', array('on','after','never'));
                $table->enum('schedule_type', array('Daily','Weekly','Monthly'));
                $table->integer('repeat_every');
                $table->string('weekly_available_days', 150);
                $table->enum('monthly_visit_type', array('date','day','week'));
                $table->integer('monthly_visit_type_date');
                $table->integer('monthly_visit_type_day_week');
                $table->string('monthly_visit_type_day_dayname', 255);
                $table->integer('monthly_visit_type_week');
                $table->string('monday_selected_times', 120);
                $table->string('tuesday_selected_times', 120);
                $table->string('wednesday_selected_times', 120);
                $table->string('thursday_selected_times', 120);
                $table->string('friday_selected_times', 120);
                $table->string('saturday_selected_times', 120);
                $table->string('sunday_selected_times', 120);
                $table->enum('provider_reminder_sms', array('on','off'));
                $table->enum('provider_reminder_phone', array('on','off'));
                $table->enum('provider_reminder_email', array('on','off'));
                $table->enum('patient_reminder_sms', array('on','off'));
                $table->enum('patient_reminder_phone', array('on','off'));
                $table->enum('patient_reminder_email', array('on','off'));
                $table->text('notes');
                $table->enum('status', array('active','inactive'));
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->timestamp('deleted_at')->nullable();
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