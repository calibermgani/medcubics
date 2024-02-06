<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogCommentsReplyTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blog_comments_reply
         */
        Schema::create('blog_comments_reply', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->bigInteger('blog_id');
                $table->bigInteger('comment_id');
                $table->string('comments', 250);
                $table->integer('up_count');
                $table->integer('down_count');
                $table->timestamp('created_at')->default("0000-00-00 00:00:00");
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
            
                Schema::drop('blog_comments_reply');
         }

}