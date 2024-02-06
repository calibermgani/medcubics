<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Icd10FieldRemovedChange extends Migration {

	public function up()
	{
		DB::statement("ALTER TABLE `icd_10` DROP `print_shortdesc`, DROP `print_mediumdesc`, DROP `print_longdesc`, DROP `onlypatient`, DROP `hipaa`");
	}

	
	public function down()
	{
		
	}

}
