<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: customers
         */
        Schema::create('customers', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('customer_name', 255);
                $table->text('customer_desc');
                $table->enum('customer_type', array('Billing','Provider'));
                $table->string('contact_person', 50);
                $table->string('designation', 50);
                $table->string('email', 100);
                $table->string('addressline1', 50);
                $table->string('addressline2', 50);
                $table->string('phone', 20);
                $table->string('phoneext', 4);
                $table->string('mobile', 20);
                $table->string('fax', 20);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('zipcode5', 5);
                $table->string('zipcode4', 4);
                $table->enum('status', array('Active','Inactive'));
                $table->string('password', 50);
                $table->string('avatar_name', 50);
                $table->string('avatar_ext', 50);
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
            
                Schema::drop('customers');
         }

}