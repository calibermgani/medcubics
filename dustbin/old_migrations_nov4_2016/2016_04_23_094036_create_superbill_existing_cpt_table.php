<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuperbillExistingCptTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: superbill_existing_cpt
         */
        Schema::create('superbill_existing_cpt', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->text('cpt_ids');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
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
            
                Schema::drop('superbill_existing_cpt');
         }

}