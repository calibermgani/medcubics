<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFacilityaddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('facilityaddresses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('facilityid');
			$table->string('address1', 50)->nullable();
			$table->string('address2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 2)->nullable();
			$table->string('pay_zip5', 5)->nullable();
			$table->string('pay_zip4', 4)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('phoneext', 4)->nullable();
			$table->string('fax', 20)->nullable();
			$table->string('email', 100)->nullable();
			$table->timestamps();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
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
		Schema::drop('facilityaddresses');
	}

}
