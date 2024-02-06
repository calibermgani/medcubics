<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEdiEligibility extends Migration {

	public function up()
	{
		Schema::table('edi_eligibility', function($table)
		{
			$table->string('type',50)->after('temp_patient_id');
			$table->string('plan_type',50)->after('type');
			$table->string('plan_number',50)->after('plan_type');
			$table->string('plan_name',100)->after('plan_number');
			$table->string('coverage_status',50)->after('plan_name');
			$table->string('group_name',100)->after('coverage_status');
		});
	}

	
	public function down()
	{
		Schema::table('edi_eligibility', function(Blueprint $table)
		{
			$table->dropColumn('type');
			$table->dropColumn('plan_type');
			$table->dropColumn('plan_number');
			$table->dropColumn('plan_name');
			$table->dropColumn('coverage_status');
			$table->dropColumn('group_name');
		});
	}

}
