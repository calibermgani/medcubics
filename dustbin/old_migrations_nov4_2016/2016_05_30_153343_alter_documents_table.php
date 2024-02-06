<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDocumentsTable extends Migration {

	public function up()
	{
		Schema::table('documents', function($table)
		{
			DB::statement('ALTER TABLE `documents` CHANGE `type_id` `type_id` BIGINT(30) NOT NULL, CHANGE `main_type_id` `main_type_id` BIGINT(30) NOT NULL');
		});
	}

	public function down()
	{
		Schema::table('documents', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE `documents` CHANGE `type_id` `type_id` INT(11) NOT NULL, CHANGE `main_type_id` `main_type_id` INT(11) NOT NULL');
		});
	}

}
