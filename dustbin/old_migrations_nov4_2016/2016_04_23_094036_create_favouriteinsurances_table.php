<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFavouriteinsurancesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: favouriteinsurances
         */
        Schema::create('favouriteinsurances', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->integer('insurance_id');
                $table->enum('type', array('Master','Optum'));
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
            
                Schema::drop('favouriteinsurances');
         }

}