<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimAmbulanceBillingsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: claim_ambulance_billings
         */
        Schema::create('claim_ambulance_billings', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->boolean('is_emergency');
                $table->bigInteger('patient_id');
                $table->bigInteger('claim_id');
                $table->string('patient_weight', 255);
                $table->string('tr_distance', 255);
                $table->string('tr_code', 255);
                $table->string('tr_reason_code', 255);
                $table->string('drop_location', 255);
                $table->string('drop_addr1', 255);
                $table->string('drop_addr2', 255);
                $table->string('drop_city', 255);
                $table->string('drop_state', 255);
                $table->integer('drop_zip4');
                $table->integer('drop_zip5');
                $table->string('pick_addr1', 255);
                $table->string('pick_addr2', 255);
                $table->string('pick_city', 255);
                $table->string('pick_state', 255);
                $table->integer('pick_zip4');
                $table->integer('pick_zip5');
                $table->text('strecher_purpose');
                $table->text('ambulance_cert');
                $table->text('medical_note');
                $table->text('round_trip');
                $table->text('business_note');
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
            
                Schema::drop('claim_ambulance_billings');
         }

}