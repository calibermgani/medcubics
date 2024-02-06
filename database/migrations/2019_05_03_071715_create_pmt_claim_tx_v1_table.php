<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePmtClaimTxV1Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pmt_claim_tx_v1', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('payment_id')->unsigned();
			$table->bigInteger('claim_id')->unsigned();
			$table->enum('pmt_method', array('','Insurance','Addwallet','Patient'));
			$table->enum('pmt_type', array('Payment','Refund','Adjustment','Credit Balance'));
			$table->bigInteger('patient_id')->unsigned();
			$table->integer('payer_insurance_id');
			$table->integer('claim_insurance_id');
			$table->decimal('total_allowed', 10);
			$table->decimal('total_deduction', 10);
			$table->decimal('total_copay', 10);
			$table->decimal('total_coins', 10);
			$table->decimal('total_withheld', 10);
			$table->decimal('total_writeoff', 10);
			$table->decimal('total_paid', 10);
			$table->date('posting_date');
			$table->string('ins_category', 20);
			$table->timestamps();
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
		Schema::drop('pmt_claim_tx_v1');
	}

}
