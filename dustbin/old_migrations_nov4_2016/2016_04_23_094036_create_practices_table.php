<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: practices
         */
        Schema::create('practices', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('customer_id');
                $table->string('practice_name', 100);
                $table->text('practice_description');
                $table->string('email', 50);
                $table->string('website', 50);
                $table->string('facebook', 100);
                $table->string('twitter', 100);
                $table->string('phone', 20);
                $table->string('phoneext', 4);
                $table->string('fax', 20);
                $table->string('avatar_name', 20);
                $table->string('avatar_ext', 5);
                $table->string('practice_link', 150);
                $table->enum('default_practice', array('Yes','No'));
                $table->string('doing_business_s', 100);
                $table->integer('speciality_id');
                $table->integer('taxanomy_id');
                $table->integer('language_id');
                $table->integer('timezone_id');
                $table->enum('entity_type', array('Group','Individual'));
                $table->enum('billing_entity', array('No','Yes'));
                $table->integer('tax_id');
                $table->integer('group_tax_id');
                $table->integer('npi');
                $table->integer('group_npi');
                $table->string('medicare_ptan', 20);
                $table->string('medicaid', 20);
                $table->string('bcbs_id', 15);
                $table->string('mail_add_1', 50);
                $table->string('mail_add_2', 50);
                $table->string('mail_city', 50);
                $table->string('mail_state', 2);
                $table->string('mail_zip5', 5);
                $table->string('mail_zip4', 4);
                $table->string('pay_add_1', 50);
                $table->string('pay_add_2', 50);
                $table->string('pay_city', 50);
                $table->string('pay_state', 2);
                $table->string('pay_zip5', 5);
                $table->string('pay_zip4', 4);
                $table->string('primary_add_1', 50);
                $table->string('primary_add_2', 50);
                $table->string('primary_city', 50);
                $table->string('primary_state', 2);
                $table->string('primary_zip5', 5);
                $table->string('primary_zip4', 4);
                $table->string('monday_forenoon', 10);
                $table->string('monday_afternoon', 10);
                $table->string('tuesday_forenoon', 10);
                $table->string('tuesday_afternoon', 10);
                $table->string('wednesday_forenoon', 10);
                $table->string('wednesday_afternoon', 10);
                $table->string('thursday_forenoon', 10);
                $table->string('thursday_afternoon', 10);
                $table->string('friday_forenoon', 10);
                $table->string('friday_afternoon', 10);
                $table->string('saturday_forenoon', 10);
                $table->string('saturday_afternoon', 10);
                $table->string('sunday_forenoon', 10);
                $table->string('sunday_afternoon', 10);
                $table->bigInteger('practice_db_id');
                $table->enum('status', array('In Progress','Active','Inactive'));
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
            
                Schema::drop('practices');
         }

}