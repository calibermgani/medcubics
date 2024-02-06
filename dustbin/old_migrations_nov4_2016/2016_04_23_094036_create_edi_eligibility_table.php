<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: edi_eligibility
         */
        Schema::create('edi_eligibility', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('patient_eligibility_id');
                $table->string('edi_eligibility_id', 20);
                $table->dateTime('edi_eligibility_created');
                $table->bigInteger('patient_id');
                $table->bigInteger('provider_id');
                $table->integer('provider_npi');
                $table->bigInteger('insurance_id');
                $table->date('dos');
                $table->date('dos_from');
                $table->date('dos_to');
                $table->integer('service_type');
                $table->string('error_message', 250);
                $table->bigInteger('temp_patient_id');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
                $table->bigInteger('created_by');
                $table->dateTime('deleted_at');
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('edi_eligibility');
         }

}