<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfileEventsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: profile_events
         */
        Schema::create('profile_events', function($table) {
                $table->increments('id')->unsigned();
                $table->integer('event_id');
                $table->string('title', 100);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('description', 255);
                $table->string('participants', 50)->nullable();
                $table->enum('reminder_type', array('one-time','repeat'))->nullable();
                $table->enum('reminder_type_repeat', array('on','never'))->nullable();
                $table->enum('repeated_by', array('Daily','Weekly','Monthly','yearly'))->nullable();
                $table->string('repeated_day', 255)->nullable();
                $table->string('reminder_days', 255)->nullable();
                $table->date('reminder_date')->nullable();
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
            
                Schema::drop('profile_events');
         }

}