<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patients
         */
        Schema::create('patients', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('account_no', 100);
                $table->enum('is_self_pay', array('No','Yes'));
                $table->string('last_name', 50);
                $table->string('middle_name', 1);
                $table->string('first_name', 50);
                $table->string('title', 5);
                $table->string('address1', 50);
                $table->string('address2', 50);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('zip5', 5);
                $table->string('zip4', 4);
                $table->integer('country_id');
                $table->enum('gender', array('Male','Female','Others'));
                $table->string('ssn', 20);
                $table->date('dob')->default("1901-01-01");
                $table->string('age', 3);
                $table->string('phone', 20);
                $table->string('work_phone', 20);
                $table->string('work_phone_ext', 4);
                $table->string('mobile', 20);
                $table->string('email', 50);
                $table->string('driver_license', 15);
                $table->integer('ethnicity_id');
                $table->enum('race', array('Asian','Aslakan Eskimo','Black','Native American','Pacific Islander','Patient Declined','Unknown','White'))->nullable()->default("Unknown");
                $table->integer('language_id');
                $table->string('guarantor_first_name', 50);
                $table->string('guarantor_last_name', 50);
                $table->string('guarantor_middle_name', 1);
                $table->enum('guarantor_relationship', array('Others','Brother','Sister','Spouse','Child','Father','Friend','Grand Child','Grand Father','Grand Mother','Guardian','Mother','Neighbour'))->nullable();
                $table->enum('employment_status', array('Employed','Self Employed','Unemployed','Retired','Student(Full Time)','Student(Part Time)','Unknown','Student'))->nullable();
                $table->string('employer_name', 50);
                $table->enum('marital_status', array('Single','Married','Divorced','Partnered','Unknown'))->default("Unknown");
                $table->enum('student_status', array('Full Time','Part Time','Unknown'))->default("Unknown");
                $table->integer('provider_id');
                $table->integer('facility_id');
                $table->enum('email_notification', array('Yes','No'));
                $table->enum('phone_reminder', array('Yes','No'));
                $table->enum('preferred_communication', array('Text Message','Voice Calls','Regular Mail','Email','Unknown'))->default("Unknown");
                $table->enum('statements', array('Yes','No','Hold','Insurance Only','Unknown'))->default("Unknown");
                $table->enum('statements_sent', array('0','1','2','3','Pre Collection','Unknown'))->nullable()->default("Unknown");
                $table->enum('bill_cycle', array('A - G','H - M','N - S','T - Z'));
                $table->date('deceased_date')->default("0000-00-00");
                $table->string('medical_chart_no', 10);
                $table->enum('eligibility_verification', array('None','Active','Inactive','Error'));
                $table->enum('demographic_status', array('Complete','Incomplete'));
                $table->enum('status', array('Active','Inactive'));
                $table->integer('percentage');
                $table->string('avatar_name', 50);
                $table->string('avatar_ext', 5);
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
            
                Schema::drop('patients');
         }

}