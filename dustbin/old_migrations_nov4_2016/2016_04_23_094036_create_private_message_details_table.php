<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrivateMessageDetailsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: private_message_details
         */
        Schema::create('private_message_details', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('message_id');
                $table->bigInteger('parent_message_id');
                $table->bigInteger('send_user_id');
                $table->bigInteger('user_id');
                $table->enum('recipient_read', array('0','1'));
                $table->dateTime('recipient_read_time');
                $table->enum('recipient_deleted', array('0','1','2'));
                $table->enum('sender_deleted', array('0','1','2'));
                $table->dateTime('sender_deleted_time');
                $table->dateTime('recipient_deleted_time');
                $table->integer('label_list_type');
                $table->enum('recipient_stared', array('0','1'));
                $table->enum('sender_stared', array('0','1'));
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
            
                Schema::drop('private_message_details');
         }

}