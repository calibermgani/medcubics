<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToClaimsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('claims', function(Blueprint $table)
		{
			$table->enum('claim_type',['electronic', 'paper']);
			$table->date('submited_date');
			$table->date('last_submited_date');
			$table->decimal('paid_amt', 10,2);
			$table->decimal('adjust_amt', 10,2);
			$table->enum('payment_type',['self', 'insurance']);
			$table->enum('payment_mode',['Cheque', 'Cash', 'EFT', 'Credit']);
			$table->string('cheque_no', 50);
			$table->date('cheque_date');
			$table->date('cheque_amt');
			$table->date('payment_date');
			$table->date('deposit_date');
			$table->decimal('total_due', 10,2);
			$table->decimal('unupplied', 10,2);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('claims', function(Blueprint $table)
		{
			 $table->dropColumn('claim_type');
			 $table->dropColumn('submited_date');
			 $table->dropColumn('last_submited_date');
			 $table->dropColumn('paid_amt');
			 $table->dropColumn('adjust_amt');
			 $table->dropColumn('payment_type');
			 $table->dropColumn('payment_mode');
			 $table->dropColumn('cheque_no');
			 $table->dropColumn('cheque_date');
			 $table->dropColumn('cheque_amt');			 
			 $table->dropColumn('payment_date');
			 $table->dropColumn('deposit_date');
			 $table->dropColumn('total_due');			 
			 $table->dropColumn('unupplied');
		});
	}

}
