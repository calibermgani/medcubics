<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeeschedulesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: feeschedules
         */
        Schema::create('feeschedules', function($table) {
                $table->bigInteger('id')->unsigned();
                $table->string('file_name', 100);
                $table->string('choose_year', 10);
                $table->string('conversion_factor', 100);
                $table->string('percentage', 100);
                $table->string('saved_file_name', 100);
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
            
                Schema::drop('feeschedules');
         }

}