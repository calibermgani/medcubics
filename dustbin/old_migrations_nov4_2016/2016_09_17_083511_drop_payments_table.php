<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("DROP TABLE `payments`;");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('payments', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->text('claim_id');
			$table->BigInteger('pateint_wallet_id');
			$table->enum('type', ['Patient', 'Insurance']);
			$table->enum('payment_type', ['Payment', 'Refund', 'Adjustment', 'Credit Balance']);
			$table->decimal('payment_amt', 10,2);
			$table->decimal('tot_billed_amt', 10,2);
			$table->decimal('tot_paid_amt', 10,2);
			$table->decimal('tot_balance_amt', 10,2);
			$table->Integer('created_by');
			$table->Integer('updated_by');
			$table->timestamp('created_at')->default("0000-00-00 00:00:00");
			$table->timestamp('updated_at')->default("0000-00-00 00:00:00");
			$table->date('deleted_at');		
		});	
	}

}
