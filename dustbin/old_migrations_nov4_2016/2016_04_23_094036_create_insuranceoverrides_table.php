<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsuranceoverridesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: insuranceoverrides
         */
        Schema::create('insuranceoverrides', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('insurance_id');
                $table->bigInteger('facility_id');
                $table->bigInteger('providers_id');
                $table->bigInteger('provider_id');
                $table->integer('id_qualifiers_id');
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
            
                Schema::drop('insuranceoverrides');
         }

}