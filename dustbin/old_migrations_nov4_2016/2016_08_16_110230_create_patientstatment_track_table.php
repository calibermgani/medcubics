<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientstatmentTrackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patientstatement_track', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->BigInteger('patient_id');
			$table->string('store_filename','100');	
			$table->string('store_path','150');
			$table->enum('type_for', ['Sendstatement', 'Emailstatement']);
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->bigInteger('created_by');
            $table->bigInteger('updated_by');
			$table->timestamp('deleted_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('patientstatement_track');
	}

}
