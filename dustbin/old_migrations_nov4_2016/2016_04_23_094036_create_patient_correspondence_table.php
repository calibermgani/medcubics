<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientCorrespondenceTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_correspondence
         */
        Schema::create('patient_correspondence', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->string('template_id', 25);
                $table->string('email_id', 25);
                $table->text('message');
                $table->text('subject');
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
            
                Schema::drop('patient_correspondence');
         }

}