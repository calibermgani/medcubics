<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserLoginHistoriesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: user_login_histories
         */
        Schema::create('user_login_histories', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('ip_address', 25);
                $table->string('logitude', 25);
                $table->string('latitude', 25);
                $table->string('browser_name', 25);
                $table->string('mac_address', 25);
                $table->string('login_time', 25);
                $table->string('logout_time', 25);
                $table->bigInteger('user_id');
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->timestamp('deleted_at')->default("0000-00-00 00:00:00");
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('user_login_histories');
         }

}