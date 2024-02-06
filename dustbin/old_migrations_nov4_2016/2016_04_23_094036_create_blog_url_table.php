<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogUrlTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blog_url
         */
        Schema::create('blog_url', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('blog_id');
                $table->string('url', 100);
                $table->string('image', 200);
                $table->string('title', 200);
                $table->string('description', 250);
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
            
                Schema::drop('blog_url');
         }

}