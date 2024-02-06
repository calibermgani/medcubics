<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimdoscptdetailsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: claimdoscptdetails
         */
        Schema::create('claimdoscptdetails', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->bigInteger('claim_id');
                $table->date('dos_from');
                $table->date('dos_to');
                $table->string('cpt_code', 10);
                $table->string('modifier1', 2);
                $table->string('modifier2', 2);
                $table->string('modifier3', 2);
                $table->string('modifier4', 2);
                $table->string('cpt_icd_code', 255);
                $table->string('cpt_icd_map_key', 50);
                $table->decimal('unit', 10,2);
                $table->decimal('charge', 10,2);
                $table->decimal('cpt_allowed_amt', 10,2);
                $table->decimal('cpt_billed_amt', 10,2);
                $table->enum('status', array('','Paid','Pending','Open'));
                $table->decimal('paid_amt', 10,2);
                $table->decimal('co_ins', 10,2);
                $table->decimal('deductable', 10,2);
                $table->decimal('with_held', 10,2);
                $table->decimal('adjustment', 10,2);
                $table->decimal('balance', 10,2);
                $table->string('denial_code', 10);
                $table->integer('insurance_id');
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
            
                Schema::drop('claimdoscptdetails');
         }

}