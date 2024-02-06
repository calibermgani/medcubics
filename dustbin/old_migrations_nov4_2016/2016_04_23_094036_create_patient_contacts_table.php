<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientContactsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_contacts
         */
        Schema::create('patient_contacts', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->enum('category', array('Guarantor','Emergency Contact','Employer','Attorney'));
                $table->string('guarantor_last_name', 50);
                $table->string('guarantor_middle_name', 1);
                $table->string('guarantor_first_name', 50);
                $table->enum('guarantor_relationship', array('Others','Child','Father','Mother','Spouce','Neighbour','Grandmother','Grandfather','Grandchild','Friend','Brother','Sister','Guardian'))->nullable();
                $table->string('guarantor_home_phone', 20);
                $table->string('guarantor_cell_phone', 20);
                $table->string('guarantor_email', 25);
                $table->string('guarantor_address1', 50);
                $table->string('guarantor_address2', 50);
                $table->string('guarantor_city', 50);
                $table->string('guarantor_state', 2);
                $table->string('guarantor_zip5', 5);
                $table->string('guarantor_zip4', 4);
                $table->string('emergency_last_name', 50);
                $table->string('emergency_middle_name', 1);
                $table->string('emergency_first_name', 50);
                $table->enum('emergency_relationship', array('Child','Father','Mother','Spouce','Neighbour','Grandmother','Grandfather','Grandchild','Friend','Brother','Sister','Guardian','Others'));
                $table->string('emergency_home_phone', 20);
                $table->string('emergency_cell_phone', 20);
                $table->string('emergency_email', 25);
                $table->string('emergency_address1', 50);
                $table->string('emergency_address2', 50);
                $table->string('emergency_city', 50);
                $table->string('emergency_state', 2);
                $table->string('emergency_zip5', 5);
                $table->string('emergency_zip4', 4);
                $table->enum('employer_status', array('Employed','Self Employed','Unemployed','Retired','Active Military Duty','Employed(Full Time)','Employed(Part Time)','Unknown','Student'))->nullable();
                $table->string('employer_name', 50);
                $table->string('employer_work_phone', 20);
                $table->string('employer_phone_ext', 5);
                $table->string('employer_address1', 50);
                $table->string('employer_address2', 50);
                $table->string('employer_city', 50);
                $table->string('employer_state', 2);
                $table->string('employer_zip5', 5);
                $table->string('employer_zip4', 4);
                $table->string('attorney_adjuster_name', 50);
                $table->date('attorney_doi');
                $table->string('attorney_claim_num', 20);
                $table->string('attorney_work_phone', 20);
                $table->string('attorney_phone_ext', 5);
                $table->string('attorney_fax', 20);
                $table->string('attorney_email', 25);
                $table->string('attorney_address1', 50);
                $table->string('attorney_address2', 50);
                $table->string('attorney_city', 50);
                $table->string('attorney_state', 2);
                $table->string('attorney_zip5', 5);
                $table->string('attorney_zip4', 4);
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
            
                Schema::drop('patient_contacts');
         }

}