<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCodesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: codes
         */
        Schema::create('codes', function($table) {
                $table->increments('id')->unsigned();
                $table->integer('codecategory_id');
                $table->string('transactioncode_id', 100);
                $table->text('description');
                $table->enum('status', array('Active','Inactive','Deleted'));
                $table->date('start_date');
                $table->date('last_modified_date');
                $table->date('stop_date');
                $table->string('notes', 255);
                $table->text('alert');
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
            
                Schema::drop('codes');
         }

}