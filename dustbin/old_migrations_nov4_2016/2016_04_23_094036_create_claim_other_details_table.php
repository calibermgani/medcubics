<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimOtherDetailsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: claim_other_details
         */
        Schema::create('claim_other_details', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->string('family_plan', 255);
                $table->integer('patient_id');
                $table->string('original_reference', 255);
                $table->string('reference_id', 255);
                $table->string('non_avaiability', 255);
                $table->string('sponsor_status', 255);
                $table->string('sponsor_grade', 255);
                $table->string('disability_percent', 255);
                $table->string('service_status', 255);
                $table->string('serive_card_effective', 255);
                $table->string('handicaped_program', 255);
                $table->string('therapy_type', 255);
                $table->string('class_finding', 255);
                $table->string('nature_of_condition', 255);
                $table->date('date_of_last_xray')->nullable();
                $table->string('total_disability', 255);
                $table->string('hospitalization', 255);
                $table->date('prescription_date')->nullable();
                $table->string('month_treated', 255);
                $table->string('epsdt', 255);
                $table->string('ambulatory_service_req', 255);
                $table->string('levels_of_submission', 255);
                $table->string('weight_unit', 255);
                $table->string('pregnant', 255);
                $table->string('referal_item', 255);
                $table->string('last_menstrual_period', 255);
                $table->string('resubmission_no', 255);
                $table->string('medicalid_referral_no', 255);
                $table->string('service_auth_exception', 255);
                $table->string('branch_of_service', 255);
                $table->string('special_program', 255);
                $table->date('effective_start')->nullable();
                $table->date('effective_end')->nullable();
                $table->string('service_grade', 255);
                $table->string('non_available_statement', 255);
                $table->string('systemic_condition', 255);
                $table->string('complication_indicator', 255);
                $table->date('consultations_dates');
                $table->string('partial_disability', 255);
                $table->string('assumed_relinquished_care', 255);
                $table->date('date_of_last_visit')->nullable();
                $table->date('date_of_manifestation')->nullable();
                $table->string('third_party_liability', 255);
                $table->string('birth_weight', 255);
                $table->date('estimated_dob')->nullable();
                $table->string('findings', 255);
                $table->string('referal_code', 255);
                $table->string('note', 255);
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
            
                Schema::drop('claim_other_details');
         }

}