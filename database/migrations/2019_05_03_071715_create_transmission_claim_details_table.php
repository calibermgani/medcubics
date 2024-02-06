<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransmissionClaimDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transmission_claim_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('edi_transmission_id');
			$table->bigInteger('claim_id')->unsigned();
			$table->string('claim_type', 30)->nullable();
			$table->text('icd', 65535)->nullable();
			$table->bigInteger('insurance_id')->nullable();
			$table->bigInteger('referring_provider_id')->unsigned()->nullable();
			$table->decimal('total_billed_amount', 10);
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
		Schema::drop('transmission_claim_details');
	}

}
