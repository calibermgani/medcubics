<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: claims
         */
        Schema::create('claims', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->bigInteger('provider_id');
                $table->integer('template_id');
                $table->date('date_of_service');
                $table->enum('charge_add_type', array('esuperbill','bhr','manual','billing'));
                $table->string('claim_number', 320);
                $table->text('icd_codes');
                $table->text('cpt_codes');
                $table->text('cpt_codes_icd');
                $table->text('notes');               
                $table->bigInteger('rendering_provider_id');
                $table->bigInteger('refering_provider_id');
                $table->bigInteger('billing_provider_id');
                $table->bigInteger('claim_detail_id');
                $table->bigInteger('claim_other_detail_id');
                $table->bigInteger('ambulance_billing_id');
                $table->bigInteger('facility_id');
                $table->integer('insurance_id');
                $table->enum('self_pay', array('','Yes','No'));
                $table->string('insurance_category', 20);
                $table->string('auth_no', 50);
                $table->string('patient_type', 255);
                $table->string('bill_cycle', 10);
                $table->integer('employer_id');
                $table->string('pos_name', 255);
                $table->integer('pos_code');
                $table->enum('copay', array('','Cash','Cheque','Credit Card','Others','Moneyorder'));
                $table->integer('copay_amt');
                $table->string('copay_detail', 255);
                $table->string('alert', 255);
                $table->string('icd_order', 255);
                $table->string('batch_no', 255);
                $table->date('batch_date');
                $table->dateTime('doi');
                $table->date('admit_date')->nullable();
                $table->date('discharge_date');
                $table->string('anesthesia_start', 255);
                $table->string('anesthesia_stop', 255);
                $table->string('anesthesia_minute', 255);
                $table->integer('anesthesia_unit');
                $table->decimal('total_charge', 10,2);
                $table->boolean('is_hold');
                $table->integer('hold_reason_id');
                $table->enum('status', array('E-bill','Hold','Ready to submit','Patient'));
                $table->string('claim_ids', 255);
                $table->string('cmsform', 255);
                $table->string('document_path', 255);
                $table->string('document_domain', 255);
                $table->string('localfilename', 255);
                $table->enum('claim_type', array('electronic','paper'));
                $table->date('submited_date');
                $table->date('last_submited_date');
                $table->decimal('paid_amt', 10,2);
                $table->decimal('adjust_amt', 10,2);
                $table->enum('payment_type', array('self','insurance'));
                $table->enum('payment_mode', array('Cheque','Cash','EFT','Credit'));
                $table->string('cheque_no', 50);
                $table->date('cheque_date');
                $table->date('cheque_amt');
                $table->date('payment_date');
                $table->date('deposit_date');
                $table->decimal('total_due', 10,2);
                $table->decimal('unupplied', 10,2);
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
            
                Schema::drop('claims');
         }

}