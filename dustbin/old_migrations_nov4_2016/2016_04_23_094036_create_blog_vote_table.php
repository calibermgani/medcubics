<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogVoteTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blog_vote
         */
        Schema::create('blog_vote', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('blog_id');
                $table->bigInteger('user_id');
                $table->integer('up');
                $table->integer('down');
                $table->dateTime('datetime');
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('blog_vote');
         }

}