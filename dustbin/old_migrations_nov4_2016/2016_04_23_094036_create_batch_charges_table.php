<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBatchChargesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: batch_charges
         */
        Schema::create('batch_charges', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->string('batch_no', 50);
                $table->date('batch_date');
                $table->integer('no_of_claims');
                $table->string('reference_no', 50);
                $table->text('claim_ids');
                $table->bigInteger('rendering_provider_id');
                $table->bigInteger('billing_provider_id');
                $table->bigInteger('facility_id');
                $table->enum('status', array('Open','Closed'));
                $table->date('created_at')->default("0000-00-00");
                $table->date('updated_at')->default("0000-00-00");
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->date('deleted_at')->nullable();
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('batch_charges');
         }

}