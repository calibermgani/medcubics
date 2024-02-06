<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsurancesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: insurances
         */
        Schema::create('insurances', function($table) {
                $table->increments('id')->unsigned();
                $table->string('insurance_name', 50);
                $table->string('insurance_desc', 255);
                $table->string('avatar_name', 20);
                $table->string('avatar_ext', 5);
                $table->string('address_1', 50);
                $table->string('address_2', 50);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('zipcode5', 5);
                $table->string('zipcode4', 4);
                $table->string('phone1', 20);
                $table->string('phoneext', 4);
                $table->string('fax', 20);
                $table->string('email', 100);
                $table->string('website', 100);
                $table->enum('enrollment', array('Unknown','Yes','No'));
                $table->integer('insurancetype_id');
                $table->integer('insuranceclass_id');
                $table->string('managedcareid', 50);
                $table->string('medigapid', 50);
                $table->string('payerid', 50);
                $table->string('era_payerid', 50);
                $table->string('eligibility_payerid', 50);
                $table->string('feeschedule', 50);
                $table->string('primaryfiling', 3);
                $table->string('secondaryfiling', 3);
                $table->string('appealfiling', 3);
                $table->enum('claimtype', array('Unknown','Electronic','Paper'));
                $table->enum('claimformat', array('Unknown','Professional','Institutional','Dental'));
                $table->string('claim_ph', 20);
                $table->string('claim_ext', 5);
                $table->string('eligibility_ph', 20);
                $table->string('eligibility_ext', 5);
                $table->string('eligibility_ph2', 20);
                $table->string('eligibility_ext2', 5);
                $table->string('enrollment_ph', 20);
                $table->string('enrollment_ext', 5);
                $table->string('prior_ph', 20);
                $table->string('prior_ext', 5);
                $table->string('claim_fax', 20);
                $table->string('eligibility_fax', 20);
                $table->string('eligibility_fax2', 20);
                $table->string('enrollment_fax', 20);
                $table->string('prior_fax', 20);
                $table->enum('status', array('Active','Inactive'));
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
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
            
                Schema::drop('insurances');
         }

}