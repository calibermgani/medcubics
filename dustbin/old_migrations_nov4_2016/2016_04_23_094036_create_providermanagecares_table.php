<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProvidermanagecaresTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: providermanagecares
         */
        Schema::create('providermanagecares', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('insurance_id');
                $table->bigInteger('providers_id');
                $table->enum('enrollment', array('Par','Non-Par'));
                $table->enum('entitytype', array('Group','Individual'));
                $table->bigInteger('provider_id');
                $table->date('effectivedate');
                $table->date('terminationdate');
                $table->string('feeschedule', 20);
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
            
                Schema::drop('providermanagecares');
         }

}