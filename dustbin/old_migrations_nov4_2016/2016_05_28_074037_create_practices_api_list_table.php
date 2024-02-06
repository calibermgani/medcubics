<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePracticesApiListTable extends Migration {

        /**
         * Run the migrations.
         *
         * @return void
         */
         public function up()
         {
			Schema::create('practice_api_list', function($table) {
                $table->bigIncrements('id')->unsigned();
				$table->bigInteger('practice_id')->unsigned();
				$table->bigInteger('api_id')->unsigned();
				$table->Enum('status', array('Active','Inactive'));
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
                Schema::drop('practice_api_list');
         }

}