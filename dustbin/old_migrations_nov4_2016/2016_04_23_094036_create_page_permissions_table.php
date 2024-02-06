<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagePermissionsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: page_permissions
         */
        Schema::create('page_permissions', function($table) {
                $table->increments('id')->unsigned();
                $table->string('menu', 255);
                $table->string('submenu', 255);
                $table->string('title', 255);
                $table->string('title_url', 255);
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('page_permissions');
         }

}