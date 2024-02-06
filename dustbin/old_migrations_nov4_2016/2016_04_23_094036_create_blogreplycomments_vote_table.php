<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogreplycommentsVoteTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blogreplycomments_vote
         */
        Schema::create('blogreplycomments_vote', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('user_id');
                $table->bigInteger('parentcomment_id');
                $table->bigInteger('comment_id');
                $table->integer('up');
                $table->integer('down');
                $table->timestamp('datetime')->default("0000-00-00 00:00:00");
            });


         }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
         public function down()
         {
            
                Schema::drop('blogreplycomments_vote');
         }

}