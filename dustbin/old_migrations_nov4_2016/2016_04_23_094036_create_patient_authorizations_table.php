<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientAuthorizationsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: patient_authorizations
         */
        Schema::create('patient_authorizations', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('patient_id');
                $table->string('authorization_no', 10);
                $table->date('requested_date')->default("0000-00-00");
                $table->string('authorization_contact_person', 100);
                $table->enum('alert_appointment', array('Yes','No'));
                $table->string('allowed_visit', 3);
                $table->string('visits_used', 3);
                $table->string('alert_visit_remains', 3);
                $table->string('visit_remaining', 3);
                $table->integer('insurance_id');
                $table->integer('pos_id');
                $table->date('start_date')->default("0000-00-00");
                $table->date('end_date')->default("0000-00-00");
                $table->string('authorization_phone', 20);
                $table->string('authorization_phone_ext', 4);
                $table->enum('alert_billing', array('Yes','No'));
                $table->decimal('allowed_amt', 10,2);
                $table->decimal('amt_used', 10,2);
                $table->decimal('amt_remaining', 10,2);
                $table->text('authorization_notes');
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
            
                Schema::drop('patient_authorizations');
         }

}