<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTemplatepairsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: templatepairs
         */
        Schema::create('templatepairs', function($table) {
                $table->increments('id')->unsigned();
                $table->integer('templatetypes_id')->nullable();
                $table->string('label', 50)->nullable();
                $table->string('input_types', 50)->nullable();
                $table->string('key', 255);
                $table->string('value', 255);
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
            
                Schema::drop('templatepairs');
         }

}