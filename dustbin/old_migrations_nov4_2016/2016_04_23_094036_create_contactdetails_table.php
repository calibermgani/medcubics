<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactdetailsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: contactdetails
         */
        Schema::create('contactdetails', function($table) {
                $table->increments('id')->unsigned();
                $table->string('practiceceo', 200);
                $table->string('mobileceo', 20);
                $table->string('phoneceo', 20);
                $table->integer('phoneceo_ext');
                $table->string('faxceo', 20);
                $table->string('emailceo', 100);
                $table->string('practicemanager', 200);
                $table->string('mobilemanager', 20);
                $table->string('phonemanager', 20);
                $table->integer('phonemanager_ext');
                $table->string('faxmanager', 20);
                $table->string('emailmanager', 100);
                $table->string('companyname', 100);
                $table->string('contactperson', 100);
                $table->string('address1', 50);
                $table->string('address2', 50)->nullable();
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('zipcode5', 5);
                $table->string('zipcode4', 4);
                $table->string('phone', 20);
                $table->integer('phone_ext');
                $table->string('fax', 20);
                $table->string('emailid', 100);
                $table->string('website', 100);
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
            
                Schema::drop('contactdetails');
         }

}