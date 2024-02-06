<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInsurancesoverride extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('insuranceoverrides', function ($table) {
		    $table->renameColumn('insurancemaster_id', 'insurance_id');
		});		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('insuranceoverrides', function ($table) {
		    $table->renameColumn('insurance_id', 'insurancemaster_id');
		});
	}

}
