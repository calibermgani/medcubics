<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameInsurancesoverride extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('insuranceoverrides');
		Schema::rename('insurancemasteroverrides', 'insuranceoverrides');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::rename('insuranceoverrides', 'insurancemasteroverrides');
	}

}
