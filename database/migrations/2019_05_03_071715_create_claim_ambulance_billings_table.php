<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClaimAmbulanceBillingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('claim_ambulance_billings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->boolean('is_emergency');
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->string('patient_weight')->nullable();
			$table->string('tr_distance')->nullable();
			$table->string('tr_code')->nullable();
			$table->string('tr_reason_code')->nullable();
			$table->string('drop_location')->nullable();
			$table->string('drop_addr1')->nullable();
			$table->string('drop_addr2')->nullable();
			$table->string('drop_city')->nullable();
			$table->string('drop_state')->nullable();
			$table->integer('drop_zip4');
			$table->integer('drop_zip5');
			$table->string('pick_addr1')->nullable();
			$table->string('pick_addr2')->nullable();
			$table->string('pick_city')->nullable();
			$table->string('pick_state')->nullable();
			$table->integer('pick_zip4');
			$table->integer('pick_zip5');
			$table->text('strecher_purpose', 65535);
			$table->text('ambulance_cert', 65535);
			$table->text('medical_note', 65535);
			$table->text('round_trip', 65535);
			$table->text('business_note', 65535);
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
		Schema::drop('claim_ambulance_billings');
	}

}
