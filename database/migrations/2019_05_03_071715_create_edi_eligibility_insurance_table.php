<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEdiEligibilityInsuranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_eligibility_insurance', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('edi_eligibility_id')->unsigned();
			$table->string('name', 200)->nullable();
			$table->string('payer_type', 50)->nullable();
			$table->string('payer_type_label', 200)->nullable();
			$table->integer('insurance_id');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edi_eligibility_insurance');
	}

}
