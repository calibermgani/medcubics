<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSetpracticeforusersTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: setpracticeforusers
         */
        Schema::create('setpracticeforusers', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->bigInteger('role_id');
                $table->bigInteger('practice_id');
                $table->text('page_permission_ids');
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
            
                Schema::drop('setpracticeforusers');
         }

}