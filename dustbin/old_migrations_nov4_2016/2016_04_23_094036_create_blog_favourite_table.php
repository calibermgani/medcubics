<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogFavouriteTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
            
        /**
         * Table: blog_favourite
         */
        Schema::create('blog_favourite', function($table) {
                $table->increments('id')->unsigned();
                $table->bigInteger('blog_id');
                $table->bigInteger('user_id');
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
            
                Schema::drop('blog_favourite');
         }

}