<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentClaimCtpDetails extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_claim_ctp_details', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->BigInteger('payment_id');
			$table->BigInteger('claim_id');
			$table->bigInteger('pateint_id');
			$table->Integer('insurance_id');
			$table->string('pateint_wallet_id', 50);
			$table->bigInteger('paymnt_claim_detail_id');
			$table->string('claim_no',25);
			$table->string('posting_type',25);				
			$table->date('dos');
			$table->decimal('billed_amt', 10,2);
			$table->decimal('allowed_amt', 10,2);
			$table->decimal('paid_amt', 10,2);
			$table->decimal('balance_amt', 10,2);
			$table->enum('category',['Co-pay', 'Co-Insurance', 'Detuctible','Patient Due']);
			$table->decimal('patient_paid', 10,2);
			$table->decimal('patient_balance',10,2);
			$table->decimal('detuctible', 10,2);
			$table->decimal('co_pay', 10,2);
			$table->decimal('co_ins', 10,2);
			$table->decimal('with_held', 10,2);
			$table->decimal('adjustment', 10,2);
			$table->decimal('paid', 10,2);
			$table->string('remark_code');
			$table->string('cpt', 10);
			$table->enum('status', ['Denied', 'Paid', 'P.paid']);
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
		Schema::drop('payment_claim_ctp_details');
	}

}
