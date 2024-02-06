<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransmissionClaimDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transmission_claim_details', function($table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('edi_transmission_id');
            $table->bigInteger('claim_id');
            $table->string('claim_type',30);
            $table->text('icd');
            $table->bigInteger('insurance_id')->nullable();
            $table->bigInteger('referring_provider_id')->nullable();       
            $table->decimal('total_billed_amount', 10,2);
            $table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
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
