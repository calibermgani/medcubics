<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStatsDetailTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: stats_detail
         */
        Schema::create('stats_detail', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->string('module_name', 25);
                $table->integer('position');
                $table->integer('stats_id');
                $table->string('class_name', 25);
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
            
                Schema::drop('stats_detail');
         }

}