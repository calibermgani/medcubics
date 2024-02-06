<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClinicalDetailsDocumentsTable extends Migration {

	public function up()
	{
		Schema::table('documents', function($table)
		{
			$table->enum('clinical_note', array("No","Yes"))->default("No")->after('document_sub_type');
			$table->bigInteger('claim_id')->nullable()->after('temp_type_id');
			$table->bigInteger('facility_id')->nullable()->after('claim_id');
			$table->bigInteger('provider_id')->nullable()->after('facility_id');
			$table->date('dos')->nullable()->after('provider_id');
			$table->bigInteger('document_categories_id')->nullable()->after('category');
		});
	}

	public function down()
	{
		Schema::table('documents', function(Blueprint $table)
		{
			Schema::drop('clinical_note');
			Schema::drop('claim_id');
			Schema::drop('facility_id');
			Schema::drop('provider_id');
			Schema::drop('dos');
			Schema::drop('document_categories_id');
		});
	}

}
