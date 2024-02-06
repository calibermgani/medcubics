<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePatientstatementTrackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('patientstatement_track', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_id')->unsigned();
			$table->date('send_statement_date');
			$table->date('pay_by_date');
			$table->decimal('balance', 10);
			$table->decimal('latest_payment_amt', 15);
			$table->date('latest_payment_date');
			$table->integer('statements');
			$table->enum('type_for', array('Paper','Email'));
			$table->bigInteger('created_by')->unsigned();
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
		Schema::drop('patientstatement_track');
	}

}
