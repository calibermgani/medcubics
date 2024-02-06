<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientEligibilityTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_eligibility
         */
        Schema::create('patient_eligibility', function($table) {
                $table->increments('id')->unsigned();               
                $table->bigInteger('patient_insurance_id');
                $table->bigInteger('patients_id');
                $table->longText('content');
                $table->integer('template_id');
                $table->boolean('is_edi_atatched');
                $table->boolean('is_manual_atatched');
                $table->string('edi_filename', 255);
                $table->string('edi_file_path', 255);
                $table->string('bv_filename', 250);
                $table->string('bv_file_path', 255);
                $table->timestamp('dos')->default("0000-00-00 00:00:00");
                $table->bigInteger('temp_patient_id');               
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
            
                Schema::drop('patient_eligibility');
         }

}