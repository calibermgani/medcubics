<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePrivateMessageTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: private_message
         */
        Schema::create('private_message', function($table) {
                $table->increments('id')->unsigned();
                $table->string('message_id', 240);
                $table->string('subject', 420);
                $table->text('message_body');
                $table->bigInteger('recipient_users_id');
                $table->bigInteger('send_user_id');
                $table->string('attachment_file', 420);
                $table->enum('draft_message', array('0','1'));
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
            
                Schema::drop('private_message');
         }

}