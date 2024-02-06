<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsuranceoverridesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('insuranceoverrides', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('insurance_id');
			$table->bigInteger('facility_id');
			$table->bigInteger('providers_id');
			$table->bigInteger('provider_id');
			$table->integer('id_qualifiers_id');
			$table->timestamps();
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('insuranceoverrides');
	}

}
