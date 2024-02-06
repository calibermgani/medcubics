<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCptsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: cpts
         */
        Schema::create('cpts', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('short_description', 28);
                $table->string('medium_description', 100);
                $table->text('long_description');
                $table->string('statement_description', 255);
                $table->boolean('print_shortdesc');
                $table->boolean('print_mediumdesc');
                $table->boolean('print_longdesc');
                $table->boolean('print_statedesc');
                $table->string('cpt_hcpcs', 30)->nullable();
                $table->string('code_type', 50);
                $table->string('type_of_service', 50);
                $table->integer('pos_id');
                $table->string('applicable_sex', 255)->nullable()->default("Both");
                $table->string('age_limit', 3);
                $table->string('medicare_allowable', 100);
                $table->decimal('allowed_amount', 15,2)->nullable();
                $table->decimal('billed_amount', 15,2)->nullable();
                $table->string('modifier', 3);
                $table->string('revenue_code', 100);
                $table->string('drug_name', 255);
                $table->string('ndc_number', 100);
                $table->string('min_units', 10);
                $table->string('max_units', 10);
                $table->string('anesthesia_unit', 100);
                $table->string('service_id_qualifier', 100);
                $table->string('medicare_global_period', 3);
                $table->enum('required_clia_id', array('Yes','No'));
                $table->string('clia_id', 100);
                $table->string('icd', 100);
                $table->string('work_rvu', 6);
                $table->string('facility_practice_rvu', 6);
                $table->string('nonfacility_practice_rvu', 6);
                $table->string('pli_rvu', 5);
                $table->string('total_facility_rvu', 6);
                $table->string('total_nonfacility_rvu', 6);
                $table->date('effectivedate');
                $table->date('terminationdate');
                $table->boolean('onlypatient');
                $table->boolean('hipaa');
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
            
                Schema::drop('cpts');
         }

}