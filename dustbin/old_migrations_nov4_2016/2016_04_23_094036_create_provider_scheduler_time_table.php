<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProviderSchedulerTimeTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: provider_scheduler_time
         */
        Schema::create('provider_scheduler_time', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('provider_scheduler_id');
                $table->bigInteger('facility_id');
                $table->bigInteger('provider_id');
                $table->date('schedule_date');
                $table->string('day', 10);
                $table->string('from_time', 10);
                $table->string('to_time', 10);
                $table->enum('schedule_type', array('Daily','Weekly','Monthly'));
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
            
                Schema::drop('provider_scheduler_time');
         }

}