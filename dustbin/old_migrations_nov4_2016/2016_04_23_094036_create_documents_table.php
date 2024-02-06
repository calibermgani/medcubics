<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: documents
         */
        Schema::create('documents', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('practice_id');
                $table->integer('type_id');
                $table->enum('document_type', array('practice','facility','provider','insurance','cpt','patients','patient_document'))->nullable();
                $table->enum('document_sub_type', array('','managed_care','overrides','insurance','Authorization'));
                $table->integer('main_type_id');
                $table->string('temp_type_id', 320);
                $table->enum('upload_type', array('webcam','browse','scanner'))->nullable();
                $table->string('document_path', 300);
                $table->string('document_extension', 100);
                $table->string('document_domain', 300);
                $table->string('title', 100);
                $table->string('description', 255);
                $table->string('category', 320)->nullable();
                $table->string('filename', 255);
                $table->double('filesize', 10,4);
                $table->string('user_email', 100);
                $table->string('mime', 50);
                $table->string('original_filename', 255);
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
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
            
                Schema::drop('documents');
         }

}