<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsuranceappealaddressTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: insuranceappealaddress
         */
        Schema::create('insuranceappealaddress', function($table) {
                $table->increments('id')->unsigned();
                $table->integer('insurance_id');
                $table->string('address_1', 50);
                $table->string('address_2', 50);
                $table->string('city', 50);
                $table->string('state', 50);
                $table->string('zipcode5', 5);
                $table->string('zipcode4', 4);
                $table->string('phone', 20);
                $table->string('phoneext', 4);
                $table->string('fax', 20);
                $table->string('email', 70);
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
            
                Schema::drop('insuranceappealaddress');
         }

}