<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransmissionCptDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transmission_cpt_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('edi_transmission_id');
			$table->bigInteger('transmission_claim_id');
			$table->string('cpt', 10)->nullable();
			$table->text('icd_pointers', 65535)->nullable();
			$table->decimal('billed_amount', 10);
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
		Schema::drop('transmission_cpt_details');
	}

}
