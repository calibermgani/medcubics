<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticemanagecaresTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: practicemanagecares
         */
        Schema::create('practicemanagecares', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('practice_id');
                $table->integer('insurance_id');
                $table->bigInteger('providers_id');
                $table->enum('enrollment', array('Par','Non-Par'));
                $table->enum('entitytype', array('Group','Individual'));
                $table->string('provider_id', 20);
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
            
                Schema::drop('practicemanagecares');
         }

}