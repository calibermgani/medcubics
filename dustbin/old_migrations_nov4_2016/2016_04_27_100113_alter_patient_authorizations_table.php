<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientAuthorizationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_authorizations', function($table)
		{
			$table->integer('document_save_id')->after('authorization_notes');
		});
	}

	
	public function down()
	{
		Schema::table('patient_authorizations', function(Blueprint $table)
		{
			$table->dropColumn('document_save_id');
		});
	}

}
