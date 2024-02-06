<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogcommentsVoteTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blogcomments_vote
         */
        Schema::create('blogcomments_vote', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->bigInteger('blog_id');
                $table->bigInteger('comment_id');
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
            
                Schema::drop('blogcomments_vote');
         }

}