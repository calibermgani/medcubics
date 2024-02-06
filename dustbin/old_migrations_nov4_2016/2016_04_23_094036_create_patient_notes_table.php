<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientNotesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_notes
         */
        Schema::create('patient_notes', function($table) {
                $table->increments('id')->unsigned();
                $table->string('title', 100);
                $table->text('content');
                $table->enum('notes_type', array('patient','eligibility'));
                $table->enum('patient_notes_type', array('alert_notes','insurance_notes','patient_notes','billing_notes','claim_notes'))->nullable();
                $table->integer('notes_type_id');
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
            
                Schema::drop('patient_notes');
         }

}