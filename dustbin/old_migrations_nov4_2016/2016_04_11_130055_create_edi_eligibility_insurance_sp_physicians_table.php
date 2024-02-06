<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityInsuranceSpPhysiciansTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: edi_eligibility_insurance_sp_physicians
         */
        Schema::create('edi_eligibility_insurance_sp_physicians', function($table) {
                $table->increments('id');
                $table->integer('edi_eligibility_insurance_id');
                $table->string('insurance_type', 200);
                $table->string('eligibility_code', 200);
                $table->enum('primary_care', array('Unknown','true','false'));
                $table->enum('restricted', array('Unknown','true','false'));
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
            
                Schema::drop('edi_eligibility_insurance_sp_physicians');
         }

}