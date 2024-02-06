<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityDemoTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: edi_eligibility_demo
         */
        Schema::create('edi_eligibility_demo', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('edi_eligibility_id');
                $table->enum('demo_type', array('subscriber','dependent'));
                $table->enum('gender', array('M','F'))->nullable();
                $table->string('member_id', 20)->nullable();
                $table->string('first_name', 100)->nullable();
                $table->string('last_name', 100)->nullable();
                $table->string('middle_name', 2)->nullable();
                $table->string('group_id', 25)->nullable();
                $table->string('group_name', 100)->nullable();
                $table->string('address1', 100)->nullable();
                $table->string('address2', 100)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('state', 2)->nullable();
                $table->integer('zip5')->nullable();
                $table->integer('zip4')->nullable();
                $table->date('dob')->nullable();
                $table->string('phone_number', 20);
                $table->string('relationship', 25)->nullable();
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('edi_eligibility_demo');
         }

}