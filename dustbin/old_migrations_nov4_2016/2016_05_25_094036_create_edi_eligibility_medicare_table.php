<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityMedicareTable extends Migration {

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
        Schema::create('edi_eligibility_medicare', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('plan_type', 250);
                $table->string('plan_type_label', 250);
				$table->enum('active', array('true','false'))->nullable();
                $table->string('deductible', 250);
                $table->string('deductible_remaining', 250);
                $table->string('coinsurance_percent', 250);
                $table->string('copayment', 250);
                $table->string('payer_name', 250);
				$table->string('policy_number', 250);
				$table->bigInteger('contact_details')->unsigned();
                $table->string('insurance_type', 250);
                $table->string('insurance_type_label', 250);
                $table->string('mco_bill_option_code', 250);
                $table->string('mco_bill_option_label', 250);
				$table->enum('locked', array('true','false'))->nullable();
                $table->date('info_valid_till');
                $table->date('start_date');
                $table->date('end_date');
				$table->date('effective_date');
                $table->date('termination_date');
				$table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->timestamp('deleted_at')->default("0000-00-00 00:00:00");
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('edi_eligibility_medicare');
         }

}