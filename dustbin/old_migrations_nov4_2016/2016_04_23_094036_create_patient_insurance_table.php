<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientInsuranceTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_insurance
         */
        Schema::create('patient_insurance', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->integer('insurance_id');
                $table->enum('category', array('Primary','Secondary','Tertiary','Workerscomp','Liability','Others'))->nullable();
                $table->enum('relationship', array('Self','Spouse','Child','Father','Mother','Son','Daughter'));
                $table->string('last_name', 50);
                $table->string('first_name', 50);
                $table->string('middle_name', 1);
                $table->string('insured_ssn', 20);
                $table->date('insured_dob')->default("1901-01-01");
                $table->string('insured_address1', 50);
                $table->string('insured_address2', 50);
                $table->string('insured_city', 50);
                $table->string('insured_state', 2);
                $table->string('insured_zip5', 5);
                $table->string('insured_zip4', 4);
                $table->string('policy_id', 20);
                $table->string('group_name', 100);
                $table->string('group_id', 20);
                $table->date('effective_date')->default("0000-00-00");
                $table->date('termination_date')->default("0000-00-00");
                $table->timestamp('category_changed_date')->default("0000-00-00 00:00:00");
                $table->string('adjustor_ph', 20);
                $table->string('adjustor_fax', 20);
                $table->longText('insurance_notes');
                $table->integer('orderby_category');
                $table->integer('document_save_id');
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
            
                Schema::drop('patient_insurance');
         }

}