<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFacilityaddressesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: facilityaddresses
         */
        Schema::create('facilityaddresses', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('facilityid');
                $table->string('address1', 50);
                $table->string('address2', 50);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('pay_zip5', 5);
                $table->string('pay_zip4', 4);
                $table->string('phone', 20);
                $table->integer('phoneext');
                $table->string('fax', 20);
                $table->string('email', 100);
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
            
                Schema::drop('facilityaddresses');
         }

}