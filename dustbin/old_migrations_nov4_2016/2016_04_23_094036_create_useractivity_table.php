<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUseractivityTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: useractivity
         */
        Schema::create('useractivity', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('userid');
                $table->enum('action', array('add','edit','delete','export'));
                $table->string('url', 200);
                $table->string('main_directory', 150);
                $table->string('module', 70);
                $table->enum('usertype', array('medcubics','practice'));
                $table->text('user_activity_msg');
                $table->dateTime('activity_date');
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('useractivity');
         }

}