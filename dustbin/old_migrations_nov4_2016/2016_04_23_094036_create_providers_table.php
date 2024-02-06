<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProvidersTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: providers
         */
        Schema::create('providers', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('customer_id');
                $table->bigInteger('practice_id');
                $table->text('provider_name');
                $table->text('organization_name');
                $table->string('last_name', 100);
                $table->string('first_name', 100);
                $table->string('middle_name', 100);
                $table->text('description');
                $table->string('avatar_name', 20);
                $table->string('avatar_ext', 5);
                $table->integer('provider_types_id');
                $table->date('provider_dob');
                $table->enum('gender', array('Male','Female','Others'));
                $table->string('ssn', 20);
                $table->integer('provider_degrees_id');
                $table->string('job_title', 150);
                $table->string('address_1', 50);
                $table->string('address_2', 50);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('zipcode5', 5);
                $table->string('zipcode4', 4);
                $table->string('phone', 15);
                $table->string('phoneext', 4);
                $table->string('fax', 15);
                $table->string('email', 100);
                $table->string('website', 100);
                $table->enum('etin_type', array('SSN','TAX ID'));
                $table->string('etin_type_number', 20);
                $table->string('npi', 15);
                $table->string('tax_id', 15);
                $table->integer('speciality_id');
                $table->integer('taxanomy_id');
                $table->integer('speciality_id2');
                $table->integer('taxanomy_id2');
                $table->string('statelicense', 100);
                $table->string('state_1', 2);
                $table->string('statelicense_2', 100);
                $table->string('state_2', 2);
                $table->string('specialitylicense', 100);
                $table->string('state_speciality', 2);
                $table->string('deanumber', 50);
                $table->string('state_dea', 50);
                $table->string('upin', 25);
                $table->string('state_upin', 25);
                $table->string('tat', 25);
                $table->string('mammography', 25);
                $table->string('careplan', 25);
                $table->string('medicareptan', 15);
                $table->string('medicaidid', 15);
                $table->string('bcbsid', 15);
                $table->string('aetnaid', 25);
                $table->string('uhcid', 25);
                $table->string('otherid', 25);
                $table->string('otherid_ins', 25);
                $table->string('otherid2', 25);
                $table->string('otherid_ins2', 25);
                $table->string('otherid3', 25);
                $table->string('otherid_ins3', 25);
                $table->enum('req_super', array('Yes','No'));
                $table->string('super_pro', 255);
                $table->string('def_billprov', 255);
                $table->integer('def_facility');
                $table->enum('stmt_add', array('Pay to Address','Mailing Address','Primary Location'));
                $table->enum('hospice_emp', array('Yes','No'));
                $table->enum('sign_file', array('Yes','No'));
                $table->string('digital_sign', 255);
                $table->string('digital_sign_name', 20);
                $table->string('digital_sign_ext', 5);
                $table->enum('status', array('Active','Inactive'));
                $table->integer('practice_db_provider_id');
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
            
                Schema::drop('providers');
         }

}