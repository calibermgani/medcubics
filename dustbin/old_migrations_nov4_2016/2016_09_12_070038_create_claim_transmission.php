<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClaimTransmission extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edi_transmissions', function($table) {
            $table->bigIncrements('id')->unsigned();
            $table->enum('transmission_type', array('Electronic','Paper'));          
            $table->integer('total_claims');
            $table->decimal('total_billed_amount', 10,2);
            $table->string('file_path', 250)->nullable();
			$table->enum('is_transmitted', array('No','Yes'));
            $table->timestamp('created_at')->default("0000-00-00 00:00:00");
            $table->timestamp('updated_at')->default("0000-00-00 00:00:00");
            $table->bigInteger('created_by');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edi_transmissions');
	}
}