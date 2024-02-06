<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRolesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: roles
         */
        Schema::create('roles', function($table) {
                $table->increments('id')->unsigned();
                $table->string('role_name', 255);
                $table->enum('role_type', array('Medcubics','Practice'));
                $table->enum('status', array('Active','Inactive'));
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->timestamp('deleted_at')->nullable()->default("0000-00-00 00:00:00");
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('roles');
         }

}