<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEdiEligibilityInsuranceTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			$table->enum('insurance_type', array('Medicare','Others'))->default('Others')->after('group_name');
			$table->bigInteger('contact_detail')->after('insurance_type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			$table->dropColumn('insurance_type');
			$table->dropColumn('contact_detail');
		});
	}
}
