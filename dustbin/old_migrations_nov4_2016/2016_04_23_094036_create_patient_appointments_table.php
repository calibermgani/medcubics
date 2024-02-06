<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientAppointmentsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_appointments
         */
        Schema::create('patient_appointments', function($table) {
                $table->increments('id')->unsigned();
                $table->integer('facility_id');
                $table->integer('provider_id');
                $table->integer('patient_id');
                $table->integer('provider_scheduler_id');
                $table->date('scheduled_on');
                $table->string('appointment_time', 100);
                $table->enum('is_new_patient', array('No','Yes'));
                $table->text('reason_for_visit');
                $table->enum('status', array('Scheduled','Confirmed','Not Confirmed','Arrived','In Session','Complete','Rescheduled','Cancelled','No Show'))->nullable()->default("Scheduled");
                $table->string('checkin_time', 20);
                $table->string('checkout_time', 20);
                $table->enum('copay_option', array('Cash','CC','Cheque','Money Order','Others'));
                $table->string('copay', 250);
                $table->enum('non_billable_visit', array('No','Yes'));
                $table->string('rescheduled_from', 255);
                $table->string('rescheduled_reason', 255);
                $table->string('copay_details', 200);
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->timestamp('deleted_at')->nullable();               
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('patient_appointments');
         }

}