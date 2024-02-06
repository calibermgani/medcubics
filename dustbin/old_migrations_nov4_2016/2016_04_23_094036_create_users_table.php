<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: users
         */
        Schema::create('users', function($table) {
                $table->bigIncrements('id')->unsigned();
                $table->bigInteger('customer_id');
                $table->integer('role_id');
                $table->string('name', 100);
                $table->string('email', 100);
                $table->string('password', 60);
                $table->enum('user_type', array('Practice','Medcubics'));
                $table->dateTime('last_access_date');
                $table->enum('practice_user_type', array('customer','practice_admin','practice_user'));
                $table->text('admin_practice_id');
                $table->string('firstname', 100);
                $table->string('lastname', 100);
                $table->date('dob');
                $table->enum('gender', array('Male','Female','Others'));
                $table->string('designation', 100);
                $table->enum('status', array('Active','Inactive'))->default("Active");
                $table->string('department', 100);
                $table->integer('language_id');
                $table->integer('ethnicity_id');
                $table->string('addressline1', 50);
                $table->string('addressline2', 50);
                $table->string('city', 50);
                $table->string('state', 2);
                $table->string('zipcode5', 5);
                $table->string('zipcode4', 4);
                $table->string('phone', 20);
                $table->string('fax', 20);
                $table->string('facebook_ac', 250)->nullable();
                $table->string('twitter', 250)->nullable();
                $table->string('linkedin', 250)->nullable();
                $table->string('googleplus', 250)->nullable();
                $table->string('remember_token', 100);
                $table->double('maximum_document_uploadsize', 10,4);
                $table->string('avatar_name', 50);
                $table->string('avatar_ext', 50);
                $table->enum('is_logged_in', array('0','1'));
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
            
                Schema::drop('users');
         }

}