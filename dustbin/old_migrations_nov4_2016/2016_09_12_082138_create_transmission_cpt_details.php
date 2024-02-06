<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransmissionCptDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transmission_cpt_details', function($table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('edi_transmission_id');
            $table->bigInteger('transmission_claim_id');
            $table->string('cpt',10);
            $table->text('icd_pointers');
            $table->decimal('billed_amount', 10,2);
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
		Schema::drop('transmission_cpt_details');
	}

}
