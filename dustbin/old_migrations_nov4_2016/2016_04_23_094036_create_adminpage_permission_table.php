<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminpagePermissionTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: adminpage_permission
         */
        Schema::create('adminpage_permission', function($table) {
                $table->increments('id')->unsigned();
                $table->string('menu', 50);
                $table->string('submenu', 50);
                $table->string('title', 20);
                $table->text('title_url');
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
            
                Schema::drop('adminpage_permission');
         }

}