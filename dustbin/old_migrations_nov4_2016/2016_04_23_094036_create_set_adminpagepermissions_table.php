<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSetAdminpagepermissionsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: set_adminpagepermissions
         */
        Schema::create('set_adminpagepermissions', function($table) {
                $table->increments('id')->unsigned();
                $table->integer('role_id');
                $table->longText('page_permission_id');
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
            
                Schema::drop('set_adminpagepermissions');
         }

}