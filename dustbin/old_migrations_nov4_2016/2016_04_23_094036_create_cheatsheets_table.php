<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCheatsheetsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: cheatsheets
         */
        Schema::create('cheatsheets', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('resource_id');
                $table->bigInteger('facility_id');
                $table->bigInteger('provider_id');
                $table->string('visit_type_id', 255);
                $table->string('cpt', 50);
                $table->string('icd', 50);
                $table->string('claimstatus', 50);
                $table->string('feeschedules', 50);
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
            
                Schema::drop('cheatsheets');
         }

}