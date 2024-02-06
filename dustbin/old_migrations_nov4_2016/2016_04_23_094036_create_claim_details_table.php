<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimDetailsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: claim_details
         */
        Schema::create('claim_details', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->bigInteger('attorney_id');
                $table->string('facility_mrn', 255);
                $table->bigInteger('provider_id');
                $table->bigInteger('patient_id');
                $table->string('reserved_nucc_box8', 100);
                $table->string('reserved_nucc_box9b', 100);
                $table->string('reserved_nucc_box9c', 100);
                $table->enum('is_provider_employed', array('','Yes','No'));
                $table->enum('is_employment', array('','Yes','No'));
                $table->enum('is_autoaccident', array('','Yes','No'));
                $table->string('autoaccident_state', 10);
                $table->enum('is_otheraccident', array('','Yes','No'));
                $table->string('other_claim_id', 100);
                $table->string('claim_code', 255);
                $table->enum('print_signature_onfile_box12', array('Yes','No'));
                $table->enum('print_signature_onfile_box13', array('Yes','No'));
                $table->string('illness_box14', 100);
                $table->enum('other_date_qualifier', array('','454','304','453','439','455','471','090','091','444'));
                $table->date('other_date')->default("0000-00-00");
                $table->date('unable_to_work_from')->default("0000-00-00");
                $table->date('unable_to_work_to')->default("0000-00-00");
                $table->date('hospitalization_from')->default("0000-00-00");
                $table->date('hospitalization_to')->default("0000-00-00");
                $table->string('additional_claim_info', 100);
                $table->string('resubmission_code', 100);
                $table->string('original_ref_no', 50);
                $table->enum('emergency', array('','Yes','No'));
                $table->enum('box23_type', array('','referal_number','mamography','clia_no'));
                $table->string('box_23', 50);
                $table->enum('outside_lab', array('','Yes','No'));
                $table->enum('accept_assignment', array('','Yes','No'));
                $table->string('reserved_nucc_box30', 100);
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('deleted_at')->nullable();
                $table->enum('epsdt', array('','Yes','No'));
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('claim_details');
         }

}