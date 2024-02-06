<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStaticpagesTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: staticpages
         */
        Schema::create('staticpages', function($table) {
                $table->increments('id')->unsigned();
                $table->enum('type', array('customer','insurance','insurance_types','modifiers','codes','cpt','icd','speciality','taxanomy','pos','id_qualifier','provider_degree','role','admin_user','user_activity','practice','facility','provider','edi','employer','codes','templates','fee_schedule','help','scheduler','registration','superbills','hold_option','charges','payments','claims','documents','reports','messages','patient_registration','patient_appointments','eligibility','e-superbills','billing','referral','ledger','problem_list','task_list','correspondence','patient_reports','user','note','patients'))->nullable();
                $table->string('title', 50);
                $table->string('slug', 50);
                $table->text('content');
                $table->enum('status', array('Active','Inactive'));
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
            
                Schema::drop('staticpages');
         }

}