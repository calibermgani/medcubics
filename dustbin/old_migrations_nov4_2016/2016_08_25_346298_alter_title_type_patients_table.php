<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTitleTypePatientsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patients', function($table)
		{
			DB::statement("ALTER TABLE `patients` CHANGE `title` `title` ENUM('Mr','Mrs','Ms','Sr','Jr','Dr') NULL DEFAULT NULL");
		});
	}

	
	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patients` CHANGE `title` `title` varchar(5) NOT NULL");
		});
	}

}
