<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentClaimDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasTable('payment_claim_details'))
		{
			Schema::drop('payment_claim_details');   
		}
		Schema::create('payment_claim_details', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('payment_id');
			$table->bigInteger('claim_id');
			$table->bigInteger('patient_id');
			$table->Integer('insurance_id');
			$table->enum('payment_type', ['', 'Insurance', 'Addwallet', 'Patient']);
			$table->decimal('patient_paid_amt', 10, 2);
			$table->decimal('insurance_paid_amt', 10, 2);
			$table->decimal('balance_amt', 10, 2);
			$table->decimal('patient_due', 10, 2);
			$table->decimal('insurance_due', 10, 2);
			$table->decimal('total_allowed', 10, 2);
			$table->decimal('total_adjusted', 10, 2);
			$table->decimal('total_withheld', 10, 2);
			$table->string('reference', 20);
			$table->string('description', 50);
			$table->Integer('created_by');
			$table->Integer('updated_by');
			$table->timestamp('created_at');
			$table->timestamp('updated_at');
			$table->timestamp('deleted_at');					
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('payment_claim_details', function(Blueprint $table)
		{
			Schema::drop('payment_claim_details');
		});
	}

}
