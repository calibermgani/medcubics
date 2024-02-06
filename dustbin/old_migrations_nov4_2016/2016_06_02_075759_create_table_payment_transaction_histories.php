<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentTransactionHistories extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_transaction_histories', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->BigInteger('claim_id');
			$table->BigInteger('payment_id');
			$table->BigInteger('patient_id');
			$table->BigInteger('pateint_wallet_id');
			$table->BigInteger('paymentcpt_detail_id');
			$table->enum('posting_type', ['Payment', 'Refund', 'Adjustment', 'Credit Balance']);
			$table->enum('type', ['scheduler', 'charge', 'posting']);
			$table->string('description', 150);
			$table->BigInteger('type_id');
			$table->Integer('created_by');
			$table->Integer('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->date('deleted_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payment_transaction_histories');
	}

}
