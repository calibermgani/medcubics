<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSuperbillTemplateTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: superbill_template
         */
        Schema::create('superbill_template', function($table) {
                $table->increments('id')->unsigned();
                $table->string('template_name', 50);
                $table->integer('provider_id');
                $table->text('header_list');
                $table->enum('status', array('Active','Inactive'));
                $table->text('get_list_order');
                $table->text('order_header');
                $table->text('header_style');
                $table->text('office_visit');
                $table->text('office_procedures');
                $table->text('laboratory');
                $table->text('well_visit');
                $table->text('medicare_preventive_services');
                $table->text('skin_procedures');
                $table->text('consultation_preop_clearance');
                $table->text('vaccines');
                $table->text('medications');
                $table->text('other_services');
                $table->text('skin_procedures_units');
                $table->text('medications_units');
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
            
                Schema::drop('superbill_template');
         }

}