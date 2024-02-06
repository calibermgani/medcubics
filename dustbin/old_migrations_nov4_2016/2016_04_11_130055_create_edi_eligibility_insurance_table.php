<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityInsuranceTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: edi_eligibility_insurance
         */
        Schema::create('edi_eligibility_insurance', function($table) {
                $table->increments('id');
                $table->integer('edi_eligibility_id');
                $table->string('name', 200);
                $table->string('payer_type', 50);
                $table->string('payer_type_label', 200);
                $table->integer('insurance_id');
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
            
                Schema::drop('edi_eligibility_insurance');
         }

}