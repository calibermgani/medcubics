<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModifiersTable extends Migration {

	public function up()
	{
		Schema::drop('modifiers');
		Schema::create('modifiers', function(Blueprint $table)
		{		
				$table->increments('id')->unsigned();
                $table->integer('modifiers_type_id');
                $table->string('code', 2);
                $table->text('description');
                $table->string('name', 255);
                $table->string('anesthesia_base_unit', 100);
                $table->enum('status', array('Active','Inactive','Deleted'));
               // $table->timestamp('created_at')->default("0000-00-00 00:00:00");
                //$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
                $table->bigInteger('created_by');
                $table->bigInteger('updated_by');
                $table->timestamp('deleted_at')->nullable();
				$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('modifiers');
	}

}
