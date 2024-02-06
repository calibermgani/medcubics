<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInsuranceappealaddressTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('insuranceappealaddress', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('insurance_id');
			$table->string('address_1', 50)->nullable();
			$table->string('address_2', 50)->nullable();
			$table->string('city', 50)->nullable();
			$table->string('state', 50)->nullable();
			$table->string('zipcode5', 5)->nullable();
			$table->string('zipcode4', 4)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('phoneext', 4)->nullable();
			$table->string('fax', 20)->nullable();
			$table->string('email', 70)->nullable();
			$table->bigInteger('created_by');
			$table->bigInteger('updated_by');
			$table->timestamps();
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
		Schema::drop('insuranceappealaddress');
	}

}
