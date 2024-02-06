<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientNotes extends Migration {

	public function up()
	{
		Schema::table('patient_notes', function($table)
		{
			$table->text('follow_up_content')->after('content');
		});
	}

	
	public function down()
	{
		Schema::table('patient_notes', function(Blueprint $table)
		{
			$table->dropColumn('follow_up_content');
		});
	}

}
