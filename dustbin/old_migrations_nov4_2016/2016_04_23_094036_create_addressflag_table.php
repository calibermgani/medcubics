<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddressflagTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: addressflag
         */
        Schema::create('addressflag', function($table) {
                $table->increments('id')->unsigned();
                $table->enum('address_company', array('usps'));
                $table->enum('type', array('patients','practice','facility','provider','insurance','employer','adminuser'))->nullable();
                $table->integer('type_id');
                $table->enum('type_category', array('pay_to_address','primary_address','mailling_address','billing_service','general_information','appeal_address','personal_info_address','patient_contact_address','patient_insurance_address'))->nullable();
                $table->string('address2', 25);
                $table->string('city', 25);
                $table->string('state', 2);
                $table->integer('zip5');
                $table->integer('zip4');
                $table->enum('is_address_match', array('Yes','No'));
                $table->string('error_message', 50);
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
            
                Schema::drop('addressflag');
         }

}