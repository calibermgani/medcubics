<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientInsuranceArchiveTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_insurance_archive
         */
        Schema::create('patient_insurance_archive', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->bigInteger('insurance_id');
                $table->enum('category', array('Primary','Secondary','Tertiary','Quaternary','Liability','Others'))->nullable();
                $table->enum('relationship', array('Self','Spouse','Child','Father','Mother'));
                $table->string('last_name', 50);
                $table->string('first_name', 50);
                $table->string('middle_name', 50);
                $table->string('insured_ssn', 20);
                $table->date('insured_dob')->default("0000-00-00");
                $table->string('address1', 50);
                $table->string('address2', 50);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('zip5', 5);
                $table->string('zip4', 4);
                $table->string('policy_id', 20);
                $table->string('group_name', 50);
                $table->string('group_id', 20);
                $table->date('effective_date')->default("0000-00-00");
                $table->date('termination_date')->default("0000-00-00");
                $table->string('adjustor_ph', 50);
                $table->string('adjustor_fax', 50);
                $table->longText('insurance_notes');
                $table->timestamp('from')->default("0000-00-00 00:00:00");
                $table->timestamp('to')->default("0000-00-00 00:00:00");
                $table->enum('created_reason', array('Changed','Deleted','Self Pay'));
                $table->bigInteger('created_by');
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
            
                Schema::drop('patient_insurance_archive');
         }

}