<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('patient_id')->unsigned();
			$table->bigInteger('billing_provider_id')->unsigned()->nullable();
			$table->integer('insurance_id')->nullable();
			$table->integer('adjustment_reason_id');
			$table->string('reference', 20)->nullable();
			$table->decimal('payment_amt', 10);
			$table->decimal('amt_used', 10);
			$table->decimal('balance', 10);
			$table->enum('type', array('scheduler','charge','posting','addwallet','refundwallet'));
			$table->bigInteger('type_id')->unsigned();
			$table->enum('payment_method', array('Insurance','Patient',''));
			$table->enum('payment_type', array('Payment','Refund','Adjustment','Credit Balance'));
			$table->string('paymentnumber', 20)->nullable();
			$table->enum('payment_mode', array('Check','Cash','Money Order','Credit','EFT'));
			$table->string('check_no', 25)->nullable();
			$table->date('check_date');
			$table->date('deposite_date')->nullable();
			$table->string('bankname', 25)->nullable();
			$table->string('bank_branch', 25)->nullable();
			$table->enum('card_type', array('Visa Card','Master Card','Maestro Card','Gift Card'));
			$table->integer('card_no')->nullable();
			$table->string('name_on_card', 25)->nullable();
			$table->date('cardexpiry_date');
			$table->boolean('void_check')->nullable();
			$table->bigInteger('created_by')->unsigned();
			$table->bigInteger('updated_by')->unsigned();
			$table->timestamps();
			$table->date('deleted_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('payments');
	}

}
