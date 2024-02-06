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
			$table->enum('same_patient_address', array('no','yes'))->after('attorney_zip4');
		});
	}

	
	public function down()
	{
		Schema::table('patient_contacts', function(Blueprint $table)
		{
			$table->dropColumn('same_patient_address');
		});
	}

}
