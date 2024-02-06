<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePateintWallet extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pateint_wallet', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->bigInteger('patient_id');
			$table->decimal('payment_amt', 10,2);
			$table->decimal('amt_used', 10,2);
			$table->decimal('balance', 10,2);
			$table->enum('type', ['scheduler', 'charge', 'posting']);
			$table->bigInteger('type_id');
			$table->enum('payment_mode',['Check', 'Cash', 'Money Order', 'Credit']);
			$table->string('check_no',25);
			$table->date('check_date');
			$table->string('bankname', 25);
			$table->string('bank_branch', 25);
			$table->string('card_type', 20);
			$table->Integer('card_no');
			$table->string('name_on_card', 25);
			$table->date('cardexpiry_date');
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
		Schema::drop('pateint_wallet');
	}

}
