<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMcInvoiceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mc_invoice', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('invoice_no', 20)->nullable();
			$table->text('header')->nullable();
			$table->bigInteger('practice_id');
			$table->date('invoice_date');
			$table->date('invoice_start_date');
			$table->date('invoice_end_date');
			$table->float('invoice_amt', 10, 0);
			$table->float('tax', 10, 0)->nullable();
			$table->float('previous_amt_due', 10, 0);
			$table->float('total_amt', 10, 0);
			$table->text('notes', 65535)->nullable();
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
		Schema::drop('mc_invoice');
	}

}
