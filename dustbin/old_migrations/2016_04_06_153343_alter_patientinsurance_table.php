<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPatientInsuranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			DB::statement("ALTER TABLE `patient_insurance` ADD `orderby_category` INT(11) NOT NULL AFTER `insurance_notes`");
			DB::statement("ALTER TABLE `patient_insurance` ADD `document_save_id` INT(11) NOT NULL AFTER `orderby_category`");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			$table->dropColumn('insurance_notes');
			$table->dropColumn('orderby_category');
		});
	}

}
