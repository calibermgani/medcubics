<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmployersTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: employers
         */
        Schema::create('employers', function($table) {
                $table->increments('id')->unsigned();
                $table->string('employer_name', 50);
                $table->text('description');
                $table->string('address_line_1', 50);
                $table->string('address_line_2', 50);
                $table->string('employer_city', 50);
                $table->string('employer_state', 2);
                $table->string('employer_zip_code_5', 5);
                $table->string('employer_zip_code_4', 4);
                $table->string('employer_phone', 20);
                $table->string('employer_phone_ext', 4);
                $table->string('employer_fax', 20);
                $table->string('employer_email', 100);
                $table->string('avatar_name', 20);
                $table->string('avatar_ext', 5);
                $table->string('contact_person', 50);
                $table->string('designation', 50);
                $table->string('contact_email', 100);
                $table->string('contact_phone', 20);
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
            
                Schema::drop('employers');
         }

}