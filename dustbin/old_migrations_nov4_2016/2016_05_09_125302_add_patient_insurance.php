<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPatientInsurance extends Migration {


	public function up()
	{
		Schema::table('patient_insurance', function($table)
		{
			$table->string('phone',15)->after('relationship');
			$table->Enum('gender', array('Male','Female','Others'))->after('phone');
		});
		
	}

	
	public function down()
	{
		Schema::table('patient_insurance', function(Blueprint $table)
		{
			$table->dropColumn('phone');
			$table->dropColumn('gender');
		});	
	}

}
