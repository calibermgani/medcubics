<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticeRegistrationTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: practice_registration
         */
        Schema::create('practice_registration', function($table) {
                $table->increments('id')->unsigned();
                $table->enum('email_id', array('1','0'));
                $table->enum('driving_license', array('1','0'));
                $table->enum('ethnicity', array('1','0'));
                $table->enum('race', array('1','0'));
                $table->enum('preferred_language', array('1','0'));
                $table->enum('marital_status', array('1','0'));
                $table->enum('student_status', array('1','0'));
                $table->enum('primary_care_provider', array('1','0'));
                $table->enum('primary_facility', array('1','0'));
                $table->enum('send_email_notification', array('1','0'));
                $table->enum('auto_phone_call_reminder', array('1','0'));
                $table->enum('preferred_communication', array('1','0'));
                $table->enum('insured_ssn', array('1','0'));
                $table->enum('insured_dob', array('1','0'));
                $table->enum('group_name', array('1','0'));
                $table->enum('group_id', array('1','0'));
                $table->enum('adjustor_ph', array('1','0'));
                $table->enum('adjustor_fax', array('1','0'));
                $table->enum('guarantor', array('1','0'));
                $table->enum('emergency_contact', array('1','0'));
                $table->enum('employer', array('1','0'));
                $table->enum('attorney', array('1','0'));
                $table->enum('requested_date', array('1','0'));
                $table->enum('contact_person', array('1','0'));
                $table->enum('alert_on_appointment', array('1','0'));
                $table->enum('allowed_visit', array('1','0'));
                $table->enum('visits_used', array('1','0'));
                $table->enum('alert_on_visit_remains', array('1','0'));
                $table->enum('visit_remaining', array('1','0'));
                $table->enum('work_phone', array('1','0'));
                $table->enum('alert_on_billing', array('1','0'));
                $table->enum('total_allowed_amount', array('1','0'));
                $table->enum('amount_used', array('1','0'));
                $table->enum('amount_remaining', array('1','0'));
                $table->enum('documents', array('1','0'));
                $table->enum('notes', array('1','0'));
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('deleted_at')->nullable();
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('practice_registration');
         }

}