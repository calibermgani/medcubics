<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogCommentsTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blog_comments
         */
        Schema::create('blog_comments', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->bigInteger('blog_id');
                $table->string('comments', 250);
                $table->string('attachment', 100);
                $table->integer('order');
                $table->integer('up_count');
                $table->integer('down_count');
                $table->dateTime('datetime');
                $table->dateTime('deleted_at');
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('blog_comments');
         }

}