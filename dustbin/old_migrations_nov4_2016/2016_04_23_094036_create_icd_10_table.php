<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIcd10Table extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: icd_10
         */
        Schema::create('icd_10', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('short_description', 100);
                $table->string('medium_description', 200);
                $table->text('long_description');
                $table->string('statement_description', 255);
                $table->boolean('print_shortdesc');
                $table->boolean('print_longdesc');
                $table->boolean('print_mediumdesc');
                $table->boolean('print_statedesc');
                $table->string('order', 100);
                $table->string('icdid', 20);
                $table->enum('header', array('V','H','C'));
                $table->string('icd_code', 20);
                $table->string('icd_type', 30)->nullable();
                $table->enum('sex', array('Both','Male','Female','Others'))->default("Both");
                $table->string('age_limit_lower', 10);
                $table->string('age_limit_upper', 10);
                $table->date('effectivedate');
                $table->date('inactivedate');
                $table->string('cpt_check', 100);
                $table->string('map_to_icd9', 100);
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
            
                Schema::drop('icd_10');
         }

}