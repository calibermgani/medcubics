<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticeApiConfigsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: practice_api_configs
         */
        Schema::create('practice_api_configs', function($table) {
                $table->increments('id')->unsigned();
                $table->string('api_for', 50);
                $table->string('api_name', 50);
                $table->string('usps_user_id', 50);
                $table->string('host', 20);
                $table->string('port', 20);
                $table->enum('api_status', array('Active','Inactive'));
                $table->text('url');
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
            
                Schema::drop('practice_api_configs');
         }

}