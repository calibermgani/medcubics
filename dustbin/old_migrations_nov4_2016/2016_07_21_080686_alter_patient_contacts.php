<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientContacts extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_contacts', function($table)
		{
			$table->string('employer_organization_name', 60)->after('employer_status');
			$table->string('employer_occupation', 60)->after('employer_organization_name');
			$table->enum('employer_student_status', array('Unknown','Full Time','Part Time'))->after('employer_occupation');
		});
	}

	
	public function down()
	{
		Schema::table('patient_contacts', function(Blueprint $table)
		{
			$table->dropColumn('employer_organization_name');
			$table->dropColumn('employer_occupation');
			$table->dropColumn('employer_student_status');
		});
	}

}
