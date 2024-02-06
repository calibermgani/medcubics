<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blog
         */
        Schema::create('blog', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->string('title', 150);
                $table->text('description');
                $table->enum('privacy', array('Private','Public','Group','User'));
                $table->string('user_list', 150);
                $table->string('attachment', 100);
                $table->string('url', 150);
                $table->enum('status', array('Active','Inactive'));
                $table->integer('comment_count');
                $table->integer('up_count');
                $table->integer('down_count');
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
            
                Schema::drop('blog');
         }

}