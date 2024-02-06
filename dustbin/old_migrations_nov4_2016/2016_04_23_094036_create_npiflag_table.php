<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNpiflagTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: npiflag
         */
        Schema::create('npiflag', function($table) {
                $table->increments('id')->unsigned();
                $table->string('company_name', 20);
                $table->enum('type', array('Practice','Facility','Provider'));
                $table->integer('type_id');
                $table->enum('type_category', array('Group','Individual'));
                $table->string('location_address_1', 50);
                $table->string('location_address_2', 50);
                $table->string('location_address_type', 10);
                $table->string('location_city', 50);
                $table->string('location_state', 2);
                $table->string('location_country_code', 2);
                $table->string('location_country_name', 25);
                $table->string('location_postal_code', 10);
                $table->string('location_telephone_number', 20);
                $table->string('location_fax_number', 20);
                $table->string('mailling_address_1', 50);
                $table->string('mailling_address_2', 50);
                $table->string('mailling_address_type', 10);
                $table->string('mailling_city', 50);
                $table->string('mailling_state', 2);
                $table->string('mailling_country_code', 2);
                $table->string('mailling_country_name', 25);
                $table->string('mailling_postal_code', 10);
                $table->string('mailling_telephone_number', 20);
                $table->string('mailling_fax_number', 20);
                $table->string('basic_credential', 5);
                $table->string('basic_first_name', 25);
                $table->string('basic_last_name', 25);
                $table->string('basic_middle_name', 25);
                $table->enum('basic_gender', array('M','F'));
                $table->string('basic_name_prefix', 5);
                $table->enum('basic_sole_proprietor', array('Yes','No'));
                $table->string('basic_authorized_official_credential', 20);
                $table->string('basic_authorized_official_first_name', 200);
                $table->string('basic_authorized_official_last_name', 150);
                $table->string('basic_authorized_official_name_prefix', 20);
                $table->string('basic_authorized_official_telephone_number', 25);
                $table->string('basic_authorized_official_title_or_position', 30);
                $table->text('basic_organization_name');
                $table->string('basic_organizational_subpart', 100);
                $table->string('basic_status', 5);
                $table->date('basic_enumeration_date');
                $table->date('basic_last_updated');
                $table->bigInteger('created_epoch');
                $table->string('enumeration_type', 15);
                $table->string('identifiers_code', 10);
                $table->string('identifiers_desc', 25);
                $table->string('identifiers_identifier', 10);
                $table->string('identifiers_issuer', 25);
                $table->string('identifiers_state', 5);
                $table->bigInteger('last_updated_epoch');
                $table->integer('number');
                $table->string('taxonomies_code', 20);
                $table->string('taxonomies_desc', 25);
                $table->string('taxonomies_license', 20);
                $table->string('taxonomies_primary', 5);
                $table->string('taxonomies_state', 2);
                $table->enum('is_valid_npi', array('Yes','No'));
                $table->text('npi_error_message');
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
            
                Schema::drop('npiflag');
         }

}